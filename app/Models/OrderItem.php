<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphPivot;

class OrderItem extends MorphPivot
{
     protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'typeable_id',
        'typeable_type',
        'quantity',
        'amount',
    ];

    public function typeable()
    {
        return $this->morphTo();
    }


    public function order()
    {
        return $this->belongsTo(Order::class);
    }


    public function subtotal(): float
    {
        return $this->quantity * $this->price;
    }
}
