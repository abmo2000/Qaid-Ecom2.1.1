<?php

namespace App\Interfaces;

interface CartStorageInterface
{
    public function add(int $productId, int $quantity, float $price): bool;
    
    public function update(int $productId, int $quantity): bool;
    
    public function remove(int $productId): bool;
    
    public function get(): array;
    
    public function getItems(): \Illuminate\Support\Collection;
    
    public function getCount(): int;
    
    public function getTotal(): float;
    
    public function clear(): bool;
}