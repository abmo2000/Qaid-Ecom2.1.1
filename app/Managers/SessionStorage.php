<?php

namespace App\Managers;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Interfaces\CartStorageInterface;

class SessionStorage implements CartStorageInterface{

    private const CART_KEY = 'cart';

    public function add(int $productId, int $quantity, float $price): bool
    {
        $cart = $this->get();
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
            ];
        }
        
        Session::put(self::CART_KEY, $cart);
        return true;
    }

    public function update(int $productId, int $quantity): bool
    {
        $cart = $this->get();
        
        if (!isset($cart[$productId])) {
            return false;
        }
        
        if ($quantity <= 0) {
            return $this->remove($productId);
        }
        
        $cart[$productId]['quantity'] = $quantity;
        Session::put(self::CART_KEY, $cart);
        
        return true;
    }

    public function remove(int $productId): bool
    {
        $cart = $this->get();
        unset($cart[$productId]);
        Session::put(self::CART_KEY, $cart);
        
        return true;
    }

    public function get(): array
    {
        return Session::get(self::CART_KEY, []);
    }

    public function getItems(): \Illuminate\Support\Collection
    {
        $cart = $this->get();
        $productIds = array_keys($cart);
        
        if (empty($productIds)) {
            return collect([]);
        }
        
        $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');
        
        return collect($cart)->map(function($item, $productId) use ($products) {
            $product = $products->get($productId);
            
            return [
                'product_id' => $productId,
                'name' => $product->name,
                'image' => $product->image,
                'quantity' => $item['quantity'],
                'price' => $product->price ?? $item['price'],
                'subtotal' => ($product->price ?? $item['price']) * $item['quantity']
            ];
        })->values();
    }

    public function getCount(): int
    {
        $cart = $this->get();
        return array_sum(array_column($cart, 'quantity'));
    }

    public function getTotal(): float
    {
        return $this->getItems()->sum('subtotal');
    }

    public function clear(): bool
    {
        Session::forget(self::CART_KEY);
        return true;
    }
}