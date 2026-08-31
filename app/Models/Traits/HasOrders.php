<?php

namespace   App\Models\Traits;

use App\Models\Order;
use App\Models\OrderItem;


trait HasOrders{

    public function orders()
    {
        return $this->morphToMany(Order::class, 'typeable', 'order_items')
            ->using(OrderItem::class)
            ->withPivot(['quantity', 'amount'])
            ->withTimestamps();
    }
}