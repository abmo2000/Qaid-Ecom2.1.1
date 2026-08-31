<?php

namespace App\Http\Controllers\Web;


use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CartAddRequest;
use App\Http\Requests\CartUpdateRequest;



class CartController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    /**
     * Display cart page
     */
    public function index()
    {
        $items = $this->cartService->getItems();
        $total = $this->cartService->getTotal();
       
        return view('web.pages.cart', compact('items', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(CartAddRequest $request)
    {
        
        try {
            $this->cartService->addProduct(
                    $request->type,
                $request->product_id,
                $request->quantity
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'data' => [
                    'cart_count' => $this->cartService->getCount(),
                    'cart_total' => $this->cartService->getTotal(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
          
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => []
            ], 400);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(CartUpdateRequest $request, int $productId): JsonResponse
    {
       
        try {
            $this->cartService->updateQuantity($productId, $request->quantity);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!',
                'data' => [
                    'cart_count' => $this->cartService->getCount(),
                    'cart_total' => $this->cartService->getTotal(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => []
            ], 400);
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(int $productId): JsonResponse
    {
        try {
            $this->cartService->removeProduct($productId);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart!',
                'data' => [
                    'cart_count' => $this->cartService->getCount(),
                    'cart_total' => $this->cartService->getTotal(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => []
            ], 400);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clear();
            
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully!',
                'data' => [
                    'cart_count' => 0,
                    'cart_total' => 0,
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => []
            ], 400);
        }
    }

    /**
     * Get cart data (AJAX endpoint)
     */
    public function getCart(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $this->cartService->getItems(),
                    'cart_count' => $this->cartService->getCount(),
                    'cart_total' => $this->cartService->getTotal(),
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => []
            ], 400);
        }
    }
}