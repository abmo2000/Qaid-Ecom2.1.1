<?php

namespace App\Models\Traits;

use App\Models\CartItem;


trait HasItems{

     public function cartItems()
    {
        return $this->morphMany(CartItem::class, 'product');
    }
}

