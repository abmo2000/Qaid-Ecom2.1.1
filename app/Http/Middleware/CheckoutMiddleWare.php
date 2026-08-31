<?php

namespace App\Http\Middleware;

use App\Services\CartService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckoutMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $cartService = app(CartService::class);

        if($cartService->isEmpty()){
            if($request->expectsJson()){
                return response()->json([
                    'message' => 'cart is empty'
                ], 422);
            }

            return redirect()->route('cart');
        }

        return $next($request);
    }
}
