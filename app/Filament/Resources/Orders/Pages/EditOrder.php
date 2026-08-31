<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\OrderItem;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }

    /**
     * Mutate form data before filling the form.
     * Pull customer relationship fields and order items into flat form fields.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $customer = $this->record->customer;

        $data['customer_name'] = $customer?->name ?? '';
        $data['customer_email'] = $customer?->email ?? '';
        $data['customer_phone'] = $customer?->phone ?? '';
        $data['customer_insta_account'] = $customer?->insta_account ?? '';

        // Load existing order items into the repeater format
        $data['order_items'] = $this->record->items()->with('typeable')->get()->map(function (OrderItem $item) {
            $unitPrice = $item->quantity > 0
                ? round($item->amount / $item->quantity, 2)
                : $item->amount;

            // Resolve category_id for products and product trials
            $categoryId = null;
            if ($item->typeable) {
                if ($item->typeable_type === 'product') {
                    $categoryId = $item->typeable->category_id ?? null;
                } elseif ($item->typeable_type === 'producttrial') {
                    $categoryId = $item->typeable->product?->category_id ?? null;
                }
            }

            return [
                'id' => $item->id,
                'typeable_type' => $item->typeable_type,
                'category_id' => $categoryId ? (string) $categoryId : null,
                'typeable_id' => (string) $item->typeable_id,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'amount' => $item->amount,
            ];
        })->toArray();

        return $data;
    }

    /**
     * After saving the order, update the related customer record and sync order items.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract customer fields
        $customerData = [
            'name' => $data['customer_name'] ?? null,
            'email' => $data['customer_email'] ?? null,
            'phone' => $data['customer_phone'] ?? null,
        ];

        if (isset($data['customer_insta_account'])) {
            $customerData['insta_account'] = $data['customer_insta_account'];
        }

        $this->record->customer?->update($customerData);

        // Remove virtual fields
        unset($data['customer_name'], $data['customer_email'], $data['customer_phone'], $data['customer_insta_account']);

        // Store order_items for afterSave, remove from data so Eloquent doesn't try to save it
        $this->orderItemsData = $data['order_items'] ?? [];
        unset($data['order_items']);

        // Recalculate items subtotal and grand total as subtotal + shipping
        $itemsSubtotal = collect($this->orderItemsData)->sum('amount');
        $shippingCost = (float) ($data['delivery_price'] ?? 0);
        $data['amount'] = $itemsSubtotal + $shippingCost;

        return $data;
    }

    /**
     * Temporary storage for order items data between mutateFormDataBeforeSave and afterSave.
     */
    protected array $orderItemsData = [];

    /**
     * After the order record is saved, sync the order items.
     */
    protected function afterSave(): void
    {
        $order = $this->record;
        $existingIds = $order->items()->pluck('id')->toArray();
        $keptIds = [];

        foreach ($this->orderItemsData as $itemData) {
            if (! empty($itemData['id']) && in_array($itemData['id'], $existingIds)) {
                // Update existing item
                OrderItem::where('id', $itemData['id'])->update([
                    'typeable_type' => $itemData['typeable_type'],
                    'typeable_id' => $itemData['typeable_id'],
                    'quantity' => $itemData['quantity'],
                    'amount' => $itemData['amount'],
                ]);
                $keptIds[] = $itemData['id'];
            } else {
                // Create new item
                OrderItem::create([
                    'order_id' => $order->id,
                    'typeable_type' => $itemData['typeable_type'],
                    'typeable_id' => $itemData['typeable_id'],
                    'quantity' => $itemData['quantity'],
                    'amount' => $itemData['amount'],
                ]);
            }
        }

        // Delete removed items
        $toDelete = array_diff($existingIds, $keptIds);
        if (! empty($toDelete)) {
            OrderItem::whereIn('id', $toDelete)->delete();
        }
    }
}
