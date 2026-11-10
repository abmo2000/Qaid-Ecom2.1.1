<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ProductSale extends Model
{
    protected $guarded = ['id' , 'created_at' , 'updated_at'];


    public function product(){
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(Product::OFFERS_MAX_DISCOUNT_CACHE_KEY));
        static::deleted(fn () => Cache::forget(Product::OFFERS_MAX_DISCOUNT_CACHE_KEY));
    }
}
