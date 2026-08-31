<?php

namespace App\Http\Controllers\Web;

use App\Models\Coupon;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CouponValidateController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string|max:100']);

        $couponCode = strtoupper(trim((string) $request->input('code')));

        $coupon = Coupon::query()
            ->whereRaw('UPPER(code) = ?', [$couponCode])
            ->first();

        if (! $coupon) {
            return response()->json([
                'valid'   => false,
                'message' => __('Coupon code not found.'),
            ], 422);
        }

        if (! $coupon->is_active) {
            return response()->json([
                'valid'   => false,
                'message' => __('This coupon is inactive.'),
            ], 422);
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            return response()->json([
                'valid'   => false,
                'message' => __('This coupon will be active at :time (:timezone).', [
                    'time' => $coupon->starts_at->format('Y-m-d h:i A'),
                    'timezone' => config('app.timezone'),
                ]),
            ], 422);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json([
                'valid'   => false,
                'message' => __('This coupon has expired.'),
            ], 422);
        }

        // Scope check: if coupon targets a specific product/package/category
        if ($coupon->scopeable_type && $coupon->scopeable_id) {
            $cartItems = (new CartService())->getItems();

            if (! $this->appliesTo($coupon, $cartItems)) {
                return response()->json([
                    'valid'   => false,
                    'message' => __('This coupon does not apply to the items in your cart.'),
                ], 422);
            }
        }

        return response()->json([
            'valid'               => true,
            'coupon_id'           => $coupon->id,
            'discount_percentage' => $coupon->discount_percentage,
            'message'             => $coupon->discount_percentage . '% ' . __('discount applied!'),
        ]);
    }

    private function appliesTo(Coupon $coupon, Collection $cartItems): bool
    {
        // scopeable_type stores the morph map alias ('product', 'category', 'package')
        $type = $coupon->scopeable_type;
        $id   = (int) $coupon->scopeable_id;

        if ($type === 'product') {
            return $cartItems->contains(
                fn ($item) => $item['product_type'] === 'product' && (int) $item['product_id'] === $id
            );
        }

        if ($type === 'package') {
            return $cartItems->contains(
                fn ($item) => $item['product_type'] === 'package' && (int) $item['product_id'] === $id
            );
        }

        if ($type === 'category') {
            $productIds = $cartItems
                ->filter(fn ($item) => $item['product_type'] === 'product')
                ->pluck('product_id')
                ->unique();

            return Product::query()
                ->whereIn('id', $productIds)
                ->where('category_id', $id)
                ->exists();
        }

        return false;
    }
}
