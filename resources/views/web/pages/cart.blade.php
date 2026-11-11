@extends('web.layouts.main')

@section('title')
    {{ __('cart.cart') }}
@endsection

@section('content')
<x-navbar></x-navbar>

<section class="qaid-page-background min-h-screen px-4 pb-16 pt-32 text-qaid-navy md:pb-24 md:pt-40" x-data="cartManager()">
    <div class="mx-auto max-w-7xl px-4">
        <h1 class="mb-10 text-center text-4xl font-black uppercase tracking-wide text-qaid-navy sm:text-5xl">
            {{ __('cart.cart') }}
        </h1>

        @if($items->isEmpty())
            <div class="mx-auto flex max-w-2xl flex-col items-center justify-center rounded-3xl border border-qaid-gold/25 bg-white/90 px-6 py-16 text-center shadow-xl shadow-qaid-navy/10 sm:px-12">
                <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full border border-qaid-gold/35 bg-qaid-cream text-4xl text-qaid-gold-dark">
                    <i class="fas fa-shopping-cart" aria-hidden="true"></i>
                </div>
                <div class="max-w-md">
                    <h2 class="mb-4 text-3xl font-black text-qaid-navy">{{ __('cart.cart_empty_title') }}</h2>
                    <p class="mb-8 text-lg text-slate-600">{{ __('cart.cart_empty_desc') }}</p>
                    <a href="{{ route('shop') }}"
                       class="inline-flex items-center gap-2 rounded-full bg-qaid-navy px-8 py-4 font-black text-qaid-gold-text shadow-xl shadow-qaid-navy/20 transition hover:-translate-y-1 hover:bg-qaid-navy-hover">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                        {{ __('cart.continue_shopping') }}
                    </a>
                </div>
            </div>
        @else
            @php
                $cartHasInvalidItems = collect($items)->contains(function ($item) {
                    return ($item['out_of_stock'] ?? false) || ($item['stock'] !== null && $item['quantity'] > $item['stock']);
                });
            @endphp

            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2 lg:max-h-[calc(100vh-250px)] lg:overflow-y-auto lg:pe-4 scrollbar-thin">
                    @foreach ($items as $item)
                        @php
                            $cartImage = $item['image'] ?? ($item['images'][0]->image ?? null);
                        @endphp
                        <article class="rounded-2xl border border-qaid-gold/20 bg-white p-5 shadow-lg shadow-qaid-navy/10 sm:p-6"
                            x-data="{ updating: false, removing: false }">
                            <div class="flex flex-col gap-5 sm:flex-row">
                                <div class="relative h-24 w-24 shrink-0 self-start overflow-hidden rounded-2xl border border-qaid-gold/20 bg-slate-100">
                                    @if($cartImage)
                                        <img src="{{ storage_image_url($cartImage) }}"
                                             alt=""
                                             class="absolute inset-0 h-full w-full object-cover"
                                             onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden')">
                                        <div class="absolute inset-0 hidden flex items-center justify-center bg-slate-100 text-3xl text-slate-400">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </div>
                                    @else
                                        <div class="absolute inset-0 flex items-center justify-center bg-slate-100 text-3xl text-slate-400">
                                            <i class="fas fa-image" aria-hidden="true"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1 text-start">
                                    <div class="mb-5 flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <h2 class="mb-2 break-words text-xl font-black text-qaid-navy">{{ $item['name'] }}</h2>
                                            <p class="text-sm text-slate-600">
                                                {{ __('cart.price') }}:
                                                <span class="font-bold text-qaid-navy">EGP{{ number_format($item['price'], 2) }}</span>
                                            </p>
                                        </div>
                                        <button type="button" @click="removeItem({{ $item['cart_item_id'] }})"
                                                :disabled="removing"
                                                aria-label="{{ __('cart.remove_item') }}"
                                                class="shrink-0 rounded-full p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600 disabled:opacity-50">
                                            <i class="fas fa-trash-alt" x-show="!removing" aria-hidden="true"></i>
                                            <i class="fas fa-spinner fa-spin" x-show="removing" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-4">
                                        <div class="inline-flex items-center overflow-hidden rounded-full border border-qaid-gold/40 bg-qaid-cream/60">
                                            <button type="button"
                                                    @click="decrementQuantity({{ $item['cart_item_id'] }}, {{ $item['quantity'] }})"
                                                    :disabled="updating || {{ $item['quantity'] }} <= 1"
                                                    aria-label="{{ __('cart.decrease_quantity') }}"
                                                    class="px-4 py-2 text-qaid-navy transition hover:bg-qaid-gold/20 disabled:cursor-not-allowed disabled:opacity-50">
                                                <i class="fas fa-minus" aria-hidden="true"></i>
                                            </button>
                                            <div class="w-12 py-2 text-center font-bold text-qaid-navy">
                                                <span x-show="!updating">{{ $item['quantity'] }}</span>
                                                <i class="fas fa-spinner fa-spin text-sm" x-show="updating" aria-hidden="true"></i>
                                            </div>
                                            <button type="button"
                                                    @click="incrementQuantity({{ $item['cart_item_id'] }}, {{ $item['quantity'] }})"
                                                    :disabled="updating || {{ $item['quantity'] }} >= {{ $item['stock'] ?? 99 }}"
                                                    aria-label="{{ __('cart.increase_quantity') }}"
                                                    class="px-4 py-2 text-qaid-navy transition hover:bg-qaid-gold/20 disabled:cursor-not-allowed disabled:opacity-50">
                                                <i class="fas fa-plus" aria-hidden="true"></i>
                                            </button>
                                        </div>

                                        <p class="text-xl font-black text-qaid-navy">
                                            <span class="text-sm font-bold text-qaid-gold-dark">EGP</span>
                                            {{ number_format($item['subtotal'], 2) }}
                                        </p>
                                    </div>

                                    @if(($item['out_of_stock'] ?? false) || ($item['stock'] !== null && $item['quantity'] > $item['stock']))
                                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                            @if($item['out_of_stock'] ?? false)
                                                This item is out of stock.
                                            @else
                                                Only {{ $item['stock'] }} item(s) are available for this product.
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="lg:col-span-1">
                    <div class="rounded-2xl border border-qaid-gold/20 bg-white p-6 shadow-xl shadow-qaid-navy/10 lg:sticky lg:top-28">
                        <h2 class="mb-6 text-2xl font-black text-qaid-navy">{{ __('cart.order_summary') }}</h2>

                        <div class="mb-6 space-y-4">
                            <div class="flex justify-between gap-4 text-slate-600">
                                <span>{{ __('cart.items') }} ({{ $items->count() }})</span>
                                <span class="font-semibold text-qaid-navy">EGP{{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between gap-4 text-slate-600">
                                <span>{{ __('cart.delivery') }}</span>
                                <span>{{ __('cart.delivery_checkout') }}</span>
                            </div>
                            <div class="border-t border-qaid-gold/30 pt-4">
                                <div class="flex justify-between gap-4 text-xl font-black text-qaid-navy">
                                    <span>{{ __('cart.total') }}</span>
                                    <span><span class="text-sm text-qaid-gold-dark">EGP</span> {{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        @if($cartHasInvalidItems)
                            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                Some cart items are no longer available in the requested quantity. Please update or remove them before checkout.
                            </div>
                        @endif
                        <button type="button" @click="window.location.href='{{ route('checkout') }}'"
                                class="mb-3 w-full rounded-full bg-qaid-navy px-6 py-4 font-black text-qaid-gold-text shadow-lg shadow-qaid-navy/20 transition hover:-translate-y-0.5 hover:bg-qaid-navy-hover {{ $cartHasInvalidItems ? 'cursor-not-allowed opacity-50' : '' }}"
                                {{ $cartHasInvalidItems ? 'disabled' : '' }}>
                            {{ __('cart.proceed_checkout') }}
                            <i class="fas fa-arrow-right ms-2" aria-hidden="true"></i>
                        </button>

                        <a href="{{ route('shop') }}"
                           class="mb-4 block w-full rounded-full border border-qaid-gold/40 bg-white px-6 py-3 text-center font-bold text-qaid-navy transition hover:border-qaid-gold hover:bg-qaid-cream">
                            <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>
                            {{ __('cart.continue_shopping') }}
                        </a>

                        <button type="button" @click="clearCart()"
                                :disabled="clearing"
                                class="w-full rounded-full border border-red-300 px-6 py-3 font-semibold text-red-600 transition hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-50">
                            <span x-show="!clearing">
                                <i class="fas fa-trash me-2" aria-hidden="true"></i>
                                {{ __('cart.clear_cart') }}
                            </span>
                            <span x-show="clearing">
                                <i class="fas fa-spinner fa-spin me-2" aria-hidden="true"></i>
                                {{ __('cart.clearing') }}
                            </span>
                        </button>
                    </div>
                </aside>
            </div>
        @endif

        <div x-show="showToast"
             x-transition
             @click="showToast = false"
             class="fixed bottom-8 end-8 z-50 max-w-sm cursor-pointer rounded-2xl border border-qaid-gold/25 bg-white p-4 shadow-2xl shadow-qaid-navy/15"
             :class="toastType === 'success' ? 'border-s-4 border-s-green-500' : 'border-s-4 border-s-red-500'">
            <div class="flex items-center gap-3">
                <i class="fas" :class="toastType === 'success' ? 'fa-check-circle text-green-600' : 'fa-exclamation-circle text-red-600'"></i>
                <p class="text-qaid-navy" x-text="toastMessage"></p>
            </div>
        </div>
    </div>
</section>

<script>
function cartManager() {
    return {
        clearing: false,
        showToast: false,
        toastMessage: '',
        toastType: 'success',
        updating: false,
        removing: false,

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
                console.error('Error removing cart item:', error);
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

            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }
}
</script>
@endsection
