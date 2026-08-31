<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Guest;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected array $orderItemsData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $guest = Guest::query()->create([
            'name' => $data['customer_name'] ?? null,
            'email' => $data['customer_email'],
            'phone' => $data['customer_phone'] ?? null,
            'insta_account' => $data['customer_insta_account'] ?? null,
        ]);

        $this->orderItemsData = $data['order_items'] ?? [];

        unset(
            $data['customer_name'],
            $data['customer_email'],
            $data['customer_phone'],
            $data['customer_insta_account'],
            $data['order_items']
        );

        $itemsSubtotal = collect($this->orderItemsData)->sum(fn (array $item): float => (float) ($item['amount'] ?? 0));
        $shippingCost = (float) ($data['delivery_price'] ?? 0);

        $data['customer_id'] = $guest->id;
        $data['customer_type'] = 'guest';
        $data['admin_creator_id'] = Auth::id();
        $data['amount'] = $itemsSubtotal + $shippingCost;
        $data['status'] = $data['status'] ?? 'pending';

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return Order::query()->create($data);
    }

    protected function afterCreate(): void
    {
        foreach ($this->orderItemsData as $itemData) {
            OrderItem::query()->create([
                'order_id' => $this->record->id,
                'typeable_type' => $itemData['typeable_type'],
                'typeable_id' => $itemData['typeable_id'],
                'quantity' => $itemData['quantity'],
                'amount' => $itemData['amount'],
            ]);
        }

        Notification::make()
            ->title('Order created successfully')
            ->success()
            ->send();
    }
}
