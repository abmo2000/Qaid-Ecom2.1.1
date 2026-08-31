<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_type', 
        'product_id',
        'quantity',
        'price',
        'attributes' 
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function product()
    {
        return $this->morphTo('product', 'product_type', 'product_id');
    }
}
