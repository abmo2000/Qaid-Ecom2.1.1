<?php


namespace App\Services;

use App\Models\Package;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\ProductTrial;

use Illuminate\Support\Collection;
use App\Models\Interfaces\Cartable;

use App\Managers\CartStorageManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService{
 public function __construct()
    {
    }

    /**
     * Add product to cart
     */
   public function addProduct(string $type, int $productId, int $quantity = 1, array $attributes = []): CartItem
    {
       
        $product = $this->getProductByType($type, $productId);
       
        if (!$product) {
            throw new \Exception("Product not found");
        }

        // Check if product implements Cartable interface
        if (!$product instanceof Cartable) {
            throw new \Exception("Product type does not support cart operations");
        }
         return  $this->updateOrCreateCartItem($product, (int)$quantity ,$attributes);
    }

    /**
     * Update quantity
     */
   public function updateQuantity(int $cartItemId, int $quantity): CartItem
    {
        $cartItem = $this->findCartItem($cartItemId);
        $product = $cartItem->product;
        
        
        if (!$product) {
            throw new \Exception("Product not found");
        }
        
        $cartItem->update([
            'quantity' => $quantity,
            'price' => $product->getCartPrice()
        ]);
        
        return $cartItem;
    }

    /**
     * Remove product
     */
   public function removeProduct(int $cartItemId): bool
    {
        $cartItem = $this->findCartItem($cartItemId);
        return $cartItem->delete();
    }

   
    public function getItems():Collection
    {
      
         return CartItem::query()
        ->with('product')  
        ->when(!Auth::check() , 
        fn($q)=>$q->where('session_id', $this->getSessionId()),
        fn($q) => $q->where('user_id', Auth::id()))
        ->get()
        ->map(function ($item) {
            $product = $item->product;
                
                if (!$product) {
                    return collect([]); 
                }

                $price = ($item->product_type === 'product' && $product->hasSale()) ? $product?->sale?->sale_price : $product->getCartPrice();

                $attributes = [
                    'cart_item_id' => $item->id,
                    'product_id' => $product->id,
                    'product_type' => $item->product_type,
                    'name' => $product->getCartName(),
                    'description' => $product->getCartDescription(),
                    'price' => $price,
                    'quantity' => $item->quantity,
                    'subtotal' => $price * $item->quantity,
                    'attributes' => $item->attributes ?? [],
                ];
                if(is_array($product->getCartAlbum())){
                    return array_merge(['images' => $product->getCartAlbum()] , $attributes);
                }

                 return array_merge(['image' => $product->getCartAlbum()] , $attributes);
            }); 
    }
    
    /**
     * Get cart count
     */
     public function getCount(): int
    {
        $query = CartItem::query()
        ->when(!Auth::check() , 
        fn($q)=>$q->where('session_id', $this->getSessionId()),
        fn($q) => $q->where('user_id', Auth::id()));

        return $query->sum('quantity');
    }

    /**
     * Get cart total
     */
    public function getTotal(): int
    {
        $items = $this->getItems();
        return (int) $items->sum('subtotal');
    }

    /**
     * Clear cart
     */
     public function clear(): bool
    {
        $query = CartItem::query()
        ->when(!Auth::check() , 
        fn($q)=>$q->where('session_id', $this->getSessionId()),
        fn($q) => $q->where('user_id', Auth::id()));

        return $query->delete() > 0;
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->getCount() === 0;
    }

      protected function updateOrCreateCartItem(Cartable $product, int $quantity , array $attributes = []): CartItem
    {
       
         $authConditions = !Auth::check() ? ['session_id' => $this->getSessionId()] : ['user_id' => Auth::id()];
          
       $cartItem =  CartItem::query()->firstOrCreate([
           'product_type' => strtolower(class_basename($product)),
           'product_id' => $product->id,
           ...$authConditions
         ] , [
             'quantity' => 0,
            'price' => $product->getCartPrice(),
            'attributes' => $attributes
         ]);
        
         $cartItem->increment('quantity' , $quantity);
    
         return $cartItem->refresh();

    }

      protected function findCartItem(int $cartItemId): CartItem
    {
        $cartItem = CartItem::query()
        ->where('id', $cartItemId)
        ->when(!Auth::check() , 
        fn($q)=>$q->where('session_id', $this->getSessionId()),
        fn($q) => $q->where('user_id', Auth::id()))
        ->first();

        if (!$cartItem) {
            throw new \Exception('Cart item not found or access denied');
        }

        return $cartItem;
    }

     protected function getProductByType(string $type, int $id): ?Cartable
    {
       
        return match($type) {
            'product' => Product::find($id),
            'trial' => ProductTrial::find($id),
            'package' => Package::find($id),
            default => null,
        };
    }

     public function transferGuestCartToUser(int $userId): void
    {
        $sessionId = $this->getSessionId();
        
        // Get guest cart items
        $guestItems = CartItem::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->get();

        foreach ($guestItems as $guestItem) {
            // Check if user already has this item in cart
            $userItem = CartItem::where('user_id', $userId)
                ->where('product_type', $guestItem->product_type)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($userItem) {
                // Merge quantities
                $product = $guestItem->product;
                
                 $newQuantity = $userItem->quantity + $guestItem->quantity;
                   
                
                $userItem->update(['quantity' => $newQuantity]);
                $guestItem->delete();
            } else {
                // Transfer to user
                $guestItem->update(['user_id' => $userId]);
            }
        }
    }

     protected function getSessionId(): string
    {
        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }
        
        return Session::get('cart_session_id');
    }

}