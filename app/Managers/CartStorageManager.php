<?php

namespace App\Managers;

use App\Factories\CartStorageFactory;
use App\Interfaces\CartStorageInterface;
use Illuminate\Support\Collection;

class CartStorageManager{
 private CartStorageInterface $storageInstance;

    public function __construct()
    {
        $this->storageInstance = CartStorageFactory::getInstance();
    }

    /**
     * Add item to cart
     */
    public function add(int $productId, int $quantity, float $price): bool
    {
        return $this->storageInstance->add($productId, $quantity, $price);
    }

    /**
     * Update item quantity
     */
    public function update(int $productId, int $quantity): bool
    {
        return $this->storageInstance->update($productId, $quantity);
    }

    /**
     * Remove item from cart
     */
    public function remove(int $productId): bool
    {
        return $this->storageInstance->remove($productId);
    }

    /**
     * Get raw cart data
     */
    public function get(): array
    {
        return $this->storageInstance->get();
    }

    /**
     * Get cart items with product details
     */
    public function getItems(): Collection
    {
        return $this->storageInstance->getItems();
    }

    /**
     * Get total items count
     */
    public function getCount(): int
    {
        return $this->storageInstance->getCount();
    }

    /**
     * Get cart total price
     */
    public function getTotal(): float
    {
        return $this->storageInstance->getTotal();
    }

    /**
     * Clear entire cart
     */
    public function clear(): bool
    {
        return $this->storageInstance->clear();
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return $this->getCount() === 0;
    }
    
}