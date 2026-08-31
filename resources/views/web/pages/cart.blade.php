@extends('web.layouts.main')

@section('title')
{{ __('cart.cart') }}
@endsection

@section('content')
<x-navbar></x-navbar>

<section class="py-16 md:py-24 bg-black min-h-screen" x-data="cartManager()">
    <h2 class="heading text-white text-center text-4xl font-bold mb-8 py-8">{{ __('cart.cart') }}</h2>
    <div class="container mx-auto px-4">
        @if($items->isEmpty())
            <!-- Empty Cart State -->
            <div class="flex flex-col items-center justify-center py-20">
                <div class="text-center max-w-md">
                    <!-- Empty Cart Icon -->
                    <div class="mb-8">
                        <i class="fas fa-shopping-cart text-gray-700 text-8xl"></i>
                    </div>
                    
                    <!-- Empty Cart Message -->
                    <h3 class="text-white text-3xl font-bold mb-4">{{ __('cart.cart_empty_title') }}</h3>
                    <p class="text-gray-400 text-lg mb-8">
                        {{ __('cart.cart_empty_desc') }}
                    </p>
                    
                    <!-- Continue Shopping Button -->
                    <a href="{{ route('shop') }}" 
                       class="inline-flex items-center gap-2 bg-orange-100 text-black font-bold px-8 py-4 rounded-lg hover:bg-orange-200 transition-all duration-300 hover:scale-105">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('cart.continue_shopping') }}
                    </a>
                </div>
            </div>
        @else
            <!-- Cart Items -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Side - Cart Items (Scrollable) -->
                <div class="lg:col-span-2">
                    @php
                        $cartHasInvalidItems = collect($items)->contains(function ($item) {
                            return ($item['out_of_stock'] ?? false) || ($item['stock'] !== null && $item['quantity'] > $item['stock']);
                        });
                    @endphp
                    <div class="max-h-[calc(100vh-250px)] overflow-y-auto pe-4 space-y-6 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">

                        @foreach ($items as $item)
                            <div class="rounded-lg bg-grey p-6" x-data="{ updating: false, removing: false }">
                                <div class="flex flex-col md:flex-row gap-6">
                                    <!-- Product Image -->
                                    @if($item['product_type'] === 'package')
                                           <div class="relative w-full md:w-48 h-40 md:h-32 flex items-center justify-center mx-auto">
                                                @foreach(array_slice($item['images'], 0, 3) as $idx => $image)
                                                    <div
                                                        class="absolute transition-all duration-300 hover:scale-105"
                                                        style="
                                                            left: {{ 50 + ($idx - 1) * 22 }}%;
                                                            transform: translateX(-50%) rotate({{ ($idx - 1) * 6 }}deg);
                                                            z-index: {{ $idx + 1 }};" >
                                                        <div class="bg-white rounded-lg shadow-lg p-2">
                                                            <img
                                                                src="{{ storage_image_url($image->image) }}"
                                                                alt="{{ $item['name'] }}"
                                                                class="w-20 h-28 md:w-16 md:h-24 object-contain"
                                                            >
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                    @else
                                    
                                    <div class="w-full md:w-32 h-32 flex-shrink-0">
                                        <img src="{{ storage_image_url($item['image']) }}" 
                                             class="w-full h-full object-cover rounded-lg" 
                                             alt="{{ $item['name'] }}">
                                    </div>

                                    @endif

                                    <!-- Product Details -->
                                    <div class="flex-grow">
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <a href="" 
                                                   class="text-white font-semibold text-xl hover:text-orange-100 transition-colors block mb-2">
                                                    {{ $item['name'] }}
                                                </a>
                                                <p class="text-gray-400 text-sm">{{ __('cart.price') }}: EGP{{ number_format($item['price'], 2) }}</p>
                                            </div>
                                            <button @click="removeItem({{ $item['cart_item_id'] }})" 
                                                    :disabled="removing"
                                                    class="text-gray-400 hover:text-red-500 transition-colors disabled:opacity-50">
                                                <i class="fas fa-trash-alt" x-show="!removing"></i>
                                                <i class="fas fa-spinner fa-spin" x-show="removing"></i>
                                            </button>
                                        </div>

                                        <!-- Price and Quantity -->
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                            <div class="flex items-center gap-6">
                                                <!-- Quantity Input -->
                                                <div class="flex items-center border border-gray-700 rounded-lg overflow-hidden">
                                                    <button type="button" 
                                                            @click="decrementQuantity({{ $item['cart_item_id'] }}, {{ $item['quantity'] }})"
                                                            :disabled="updating || {{ $item['quantity'] }} <= 1"
                                                            class="px-4 py-2 text-white hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <div class="w-16 text-center text-white py-2">
                                                        <span x-show="!updating">{{ $item['quantity'] }}</span>
                                                        <i class="fas fa-spinner fa-spin text-sm" x-show="updating"></i>
                                                    </div>
                                                    <button type="button" 
                                                            @click="incrementQuantity({{ $item['cart_item_id'] }}, {{ $item['quantity'] }})"
                                                            :disabled="updating || {{ $item['quantity'] }} >= {{ $item['stock'] ?? 99 }}"
                                                            class="px-4 py-2 text-white hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Subtotal -->
                                            <div class="text-end">
                                                <span class="text-orange-100 font-bold text-2xl">EGP{{ number_format($item['subtotal'], 2) }}</span>
                                            </div>
                                        </div>

                                        @if(($item['out_of_stock'] ?? false) || ($item['stock'] !== null && $item['quantity'] > $item['stock']))
                                            <div class="mt-4 rounded-lg bg-red-500/10 border border-red-500 px-4 py-3 text-red-100 text-sm">
                                                @if($item['out_of_stock'] ?? false)
                                                    This item is out of stock.
                                                @else
                                                    Only {{ $item['stock'] }} item(s) are available for this product.
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>    
                        @endforeach
                        
                    </div>
                </div>

                <!-- Right Side - Order Summary (Fixed/Sticky) -->
                <div class="lg:col-span-1">
                    <div class="rounded-lg bg-grey p-6 lg:sticky lg:top-24">
                        <h3 class="text-white text-2xl font-bold mb-6">{{ __('cart.order_summary') }}</h3>

                        <!-- Summary Items -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-400">
                                <span>{{ __('cart.items') }} ({{ $items->count() }})</span>
                                <span>EGP{{ number_format($total, 2) }}</span>
                            </div>
                            
                            <div class="flex justify-between text-gray-400">
                                <span>{{ __('cart.delivery') }}</span>
                                <span>{{ __('cart.delivery_checkout') }}</span>
                            </div>
                            
                            <div class="border-t border-gray-700 pt-4">
                                <div class="flex justify-between text-white text-xl font-bold">
                                    <span>{{ __('cart.total') }}</span>
                                    <span class="text-orange-100">EGP{{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        @if($cartHasInvalidItems)
                            <div class="rounded-lg bg-red-500/10 border border-red-500 px-4 py-3 text-red-100 text-sm mb-4">
                                Some cart items are no longer available in the requested quantity. Please update or remove them before checkout.
                            </div>
                        @endif
                        <button  @click="window.location.href='{{ route('checkout') }}'" class="w-full bg-orange-100 text-black font-bold py-4 rounded-lg hover:bg-orange-200 transition-colors mb-4 {{ $cartHasInvalidItems ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $cartHasInvalidItems ? 'disabled' : '' }}>
                            {{ __('cart.proceed_checkout') }}
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>

                        <!-- Continue Shopping Link -->
                        <a href="{{ route('shop') }}" 
                           class="block text-center text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-arrow-left me-2"></i>
                            {{ __('cart.continue_shopping') }}
                        </a>

                        <!-- Clear Cart Button -->
                        <button @click="clearCart()" 
                                :disabled="clearing"
                                class="w-full mt-4 border border-red-500 text-red-500 font-semibold py-3 rounded-lg hover:bg-red-500 hover:text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!clearing">
                                <i class="fas fa-trash me-2"></i>
                                {{ __('cart.clear_cart') }}
                            </span>
                            <span x-show="clearing">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                {{ __('cart.clearing') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Toast Notification -->
        <div x-show="showToast" 
             x-transition
             @click="showToast = false"
             class="fixed bottom-8 end-8 z-50 max-w-sm bg-grey border-l-4 p-4 rounded-lg shadow-2xl cursor-pointer"
             :class="toastType === 'success' ? 'border-green-500' : 'border-red-500'">
            <div class="flex items-center gap-3">
                <i class="fas" :class="toastType === 'success' ? 'fa-check-circle text-green-500' : 'fa-exclamation-circle text-red-500'"></i>
                <p class="text-white" x-text="toastMessage"></p>
            </div>
        </div>
    </div>
</section>
{{-- @include('web.pages.partials.checkout-modal'); --}}
<script>
function cartManager() {
    return {
        clearing: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        updating: false,
        removing:false,

        async incrementQuantity(productId, currentQuantity) {
            if (currentQuantity < 99) {
                this.updating = true;
                await this.updateQuantity(productId, currentQuantity + 1);
            }
        },

        async decrementQuantity(productId, currentQuantity) {
            
            if (currentQuantity > 1) {
                this.updating = true;
                await this.updateQuantity(productId, currentQuantity - 1);
            }
        },

        async updateQuantity(productId, quantity) {
            try {
                const response = await fetch(`/cart/update/${productId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quantity })
                });

                const data = await response.json();

                if (data.success) {
                   
                    this.showNotification(data.message, 'success');
                    // Reload after short delay to show notification
                    setTimeout(() => location.reload(), 100);
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error updating cart:', error);
                this.showNotification("{{ __('cart.update_failed') }}", 'error');
            }
        },

        async removeItem(productId) {
            if (!confirm("{{ __('cart.confirm_remove_item') }}")) {
                return;
            }
            this.removing = true;
            try {
                const response = await fetch(`/cart/remove/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 100);
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error removing item:', error);
                this.showNotification("{{ __('cart.remove_failed') }}", 'error');
            }
        },

        async clearCart() {
            if (!confirm("{{ __('cart.confirm_clear_cart') }}")) {
                return;
            }

            this.clearing = true;

            try {
                const response = await fetch('/cart/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    this.showNotification(data.message, 'error');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                this.showNotification("{{ __('cart.clear_failed') }}", 'error');
            } finally {
                this.clearing = false;
            }
        },

        showNotification(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;

            // Auto-hide after 3 seconds
            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }
}
</script>

@endsection