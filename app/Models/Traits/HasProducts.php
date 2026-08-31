<?php

namespace App\Models\Traits;

use App\Models\Product;

trait HasProducts{

    public function products(){
        return $this->hasMany(Product::class);
    }
}