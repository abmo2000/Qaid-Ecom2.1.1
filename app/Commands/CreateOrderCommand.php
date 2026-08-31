<?php
namespace App\Commands;

use App\Models\City;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\Product;
use App\Models\ProductTrial;
use App\Events\OrderCreated;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateOrderCommand{

    const RELATION_TYPES = [
        'product' => 'products',
        'package' => 'packages',
        'producttrial' => 'productTrials',
    ];

     public function handle(array $data, \Closure $next)
    {
        $order = DB::transaction(function () use ($data) {

            $user = (($data['customer_type'] ?? null) === 'user' && Auth::check())
                ? Auth::user()
                : null;

            $cartService = new CartService();

             $cartItems =  $cartService->getItems();
             $this->validateCartItemsAvailability($cartItems);

             $city = City::query()->findOrFail($data['city_id']);
             
                 if($user && is_null($user->city_id)){
                 $user->city_id = $city->id;
                 
             }
             
                 if($user && is_null($user->phone)){
                $user->phone = $data['phone'];
             }

                 if($user && is_null($user->address)){
                $user->address = $data['address'];
             }

                 if($user && array_key_exists('insta_account' , $data)){
                 $user->insta_account = $data['insta_account'];
             }

                 if($user){
                     $user->save();
                 }
        
$itemsSubtotal = $cartItems->sum(fn($item) => $item['price'] * $item['quantity']);
            $shippingCost = (float) $city->price;

             $data['delivery_price'] = $shippingCost;
             
            $grandTotal = $itemsSubtotal + $shippingCost;


            if(getBuisnessSettings('order_settings')?->has_delivery_option && $data['delivery_option'] === 'discuss'){
                $grandTotal = $itemsSubtotal;
                $data['delivery_price'] = 0;
                $shippingCost = 0;
            }


            if($user && getBuisnessSettings('order_settings')?->allow_first_order_for_free && ! $user->orders()->exists() ){
                $grandTotal = $itemsSubtotal;
                 $data['delivery_price'] = 0;
                 $shippingCost = 0;
            }

            // Apply coupon discount if a valid code was provided
            $couponId = null;
            $adminCreatorId = null;
            if (! empty($data['coupon_code'])) {
                $couponCode = strtoupper(trim((string) $data['coupon_code']));

                $coupon = Coupon::query()
                    ->whereRaw('UPPER(code) = ?', [$couponCode])
                    ->where('is_active', true)
                    ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
                    ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                    ->first();

                if ($coupon) {
                    $discount = round($itemsSubtotal * ($coupon->discount_percentage / 100), 2);
                    $grandTotal = max(0, ($itemsSubtotal + $shippingCost) - $discount);
                    $couponId = $coupon->id;
                    $adminCreatorId = $coupon->created_by;
                }
            }

            
            $order = Order::query()->create([
                'order_id' => Str::uuid()->toString(),
                'customer_id' => $data['customer_id'],
                'customer_type' => $data['customer_type'],
                'payment_method' => $data['payment_method'],
                'payment_proof_path' => $data['payment_proof_path'] ?? null,
                'payment_status' => $data['payment_status'] ?? 'not_paid',
                'customer_address' => $data['address'],
                'amount' => $grandTotal,
                'delivery_option' => $data['delivery_option'],
                'delivery_price' =>  $data['delivery_price'] ,
                'notes' => $data['notes'] ?? null,
                'coupon_id' => $couponId,
                'admin_creator_id' => $adminCreatorId,
                'status' => \App\Enums\OrderStatus::PENDING,
            ]);

            $this->storeItems($cartItems , $order);
              
              $cartService->clear();

               event(new OrderCreated($cartItems, $grandTotal, $order));

            return $order;
        });

         
        $data['order'] = $order;

        return $next($data);
    }


    private function validateCartItemsAvailability(Collection $cartItems): void
    {
        $cartItems->each(function ($item) {
            $product = $this->getProductByType($item['product_type'], $item['product_id']);

            if (! $product) {
                throw new \Exception('A product in your cart is no longer available.');
            }

            if ($product->getAttribute('in_stock') === false || ($product->getAttribute('stock') !== null && $product->getAttribute('stock') <= 0)) {
                throw new \Exception(sprintf('%s is out of stock.', $product->getCartName()));
            }

            $stock = $product->getAttribute('stock');
            if ($stock !== null && $item['quantity'] > $stock) {
                throw new \Exception(sprintf('Only %s unit(s) of %s are available.', $stock, $product->getCartName()));
            }
        });
    }

    private function getProductByType(string $type, int $id)
    {
        return match (strtolower($type)) {
            'product' => Product::find($id),
            'trial' => ProductTrial::find($id),
            'package' => Package::find($id),
            default => null,
        };
    }

    private function storeItems(Collection $items , Order $order){
        $items
            ->groupBy('product_type')
            ->each(function ($items, $type) use ($order) {
                if (! isset(self::RELATION_TYPES[$type])) {
                    return;
                }

                $arr = [];
                foreach ($items as $item) {
                    $product = $this->getProductByType($type, $item['product_id']);

                    if ($product && $product->getAttribute('stock') !== null) {
                        $product->decrement('stock', $item['quantity']);

                        if ($product->stock <= 0) {
                            $product->update(['in_stock' => false]);
                        }
                    }

                    $arr[$item['product_id']] = [
                        'amount' => $item['subtotal'],
                        'quantity' => $item['quantity'],
                    ];
                }

                $order->{self::RELATION_TYPES[$type]}()->attach($arr);
            });
    }
}