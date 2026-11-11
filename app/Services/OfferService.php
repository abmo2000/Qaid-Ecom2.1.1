<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class OfferService
{
    public function maximumDiscountPercentage(): int
    {
        return Cache::remember(Product::OFFERS_MAX_DISCOUNT_CACHE_KEY, now()->addMinutes(10), function (): int {
            return (int) (Product::onSale()
                ->join('product_sales', 'product_sales.product_id', '=', 'products.id')
                ->selectRaw('MAX(ROUND(((products.price - product_sales.sale_price) * 100.0) / products.price)) AS maximum_discount')
                ->value('maximum_discount') ?? 0);
        });
    }
}
