@props(['product' => (object)[], 'layout' => 'carousel'])
@php($type = $product->isTrial() ? 'trial' : 'product')
@php($stockValue = data_get($product, 'stock', 0))
@php($isProductInStock = data_get($product, 'in_stock', true) && ($stockValue === null || $stockValue > 0))
@php($maxProductStock = $stockValue === null ? 99 : $stockValue)
@php($cardClasses = $layout === 'grid'
    ? 'product-card group cursor-pointer w-full !overflow-hidden !rounded-[1.75rem] !border !border-[#d8b35a]/25 !bg-[#fbf6e8] !shadow-[0_24px_70px_rgba(0,0,0,.22)] transition duration-300 hover:!-translate-y-2 hover:!shadow-[0_32px_90px_rgba(0,0,0,.30)]'
    : 'product-card group cursor-pointer w-[78vw] sm:w-[300px] md:w-[280px] lg:w-[300px] xl:w-[320px] !overflow-hidden !rounded-[1.75rem] !border !border-[#d8b35a]/25 !bg-[#fbf6e8] !shadow-[0_24px_70px_rgba(0,0,0,.22)] transition duration-300 hover:!-translate-y-2 hover:!shadow-[0_32px_90px_rgba(0,0,0,.30)]')
@if($type === 'trial')
<div class="{{ $cardClasses }}" x-data="productCard('trial', {{ $product->id }}, {{ $isProductInStock ? 'true' : 'false' }}, {{ $maxProductStock }})">
@else
<div class="{{ $cardClasses }}" x-data="productCard('product', {{ $product->id }}, {{ $isProductInStock ? 'true' : 'false' }}, {{ $maxProductStock }})">
@endif
    <div class="relative m-3 overflow-hidden rounded-[1.35rem] bg-[#efe2c2]">
        <a
            href="{{ route('products.show', $product->slug) . ($product->isTrial() ? '?trial=trial' : '') }}"
            class="absolute inset-0 z-10 cursor-pointer"
            aria-label="{{ $product->name }}"
        ></a>

        @unless($isProductInStock)
            <div class="absolute inset-0 z-30 flex items-center justify-center bg-[#0d1b2a]/80 px-4 py-2 backdrop-blur-sm">
                <span class="inline-flex items-center gap-2 rounded-full border border-[#d8b35a]/45 bg-[#0d1b2a] px-6 py-3 text-xs font-black uppercase tracking-[0.18em] text-[#f7d879] shadow-2xl sm:text-sm">
                    <i class="fa-solid fa-ban"></i>
                    Out of stock
                </span>
            </div>
        @endunless

        @if($product->hasSale())
            <div class="pointer-events-none absolute start-4 top-4 z-20 rounded-full bg-[#9b1c1c] px-3 py-1.5 text-xs font-black uppercase tracking-[0.16em] text-white shadow-lg">
                {{ trans('shop.sale') }}
            </div>
        @endif

        <div class="flex h-52 items-center justify-center px-5 py-6 sm:h-56 md:h-60">
            <img
                class="max-h-full w-full object-contain drop-shadow-[0_24px_28px_rgba(13,27,42,.20)] transition duration-500 group-hover:scale-105 pointer-events-none"
                src="{{ storage_image_url($product->image) }}?v={{ optional($product->updated_at)->timestamp }}"
                alt="{{ $product->name }}"
            >
        </div>

        <div class="pointer-events-none absolute inset-0 bg-[#0d1b2a]/0 transition duration-300 md:group-hover:bg-[#0d1b2a]/10"></div>

        <div class="absolute end-4 top-4 z-20 flex flex-col gap-3 opacity-100 transition-opacity duration-300 md:opacity-0 md:group-hover:opacity-100">
            <button
                @click.stop="addToCart()"
                :disabled="adding || !inStock"
                class="relative flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-[#d8b35a]/35 bg-white text-[#0d1b2a] shadow-lg transition-all duration-300 hover:scale-110 hover:bg-[#0d1b2a] hover:text-[#f7d879] disabled:cursor-not-allowed disabled:opacity-50"
                aria-label="Add to cart"
            >
                <i class="fa-solid fa-cart-shopping" x-show="!adding"></i>
                <svg x-show="adding" class="h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            <button
                @click.stop="window.location.href='{{ route('products.show', $product->slug) . ($product->isTrial() ? '?trial=trial' : '') }}'"
                class="relative flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-[#d8b35a]/35 bg-white text-[#0d1b2a] shadow-lg transition-all duration-300 hover:scale-110 hover:bg-[#0d1b2a] hover:text-[#f7d879]"
                aria-label="View product"
            >
                <i class="fa-solid fa-eye"></i>
            </button>
        </div>

        <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="pointer-events-none absolute start-4 end-4 top-4 z-20 flex items-center gap-2 rounded-xl bg-[#176f43] px-4 py-2 text-white shadow-lg">
            <i class="fas fa-check-circle"></i>
            <span class="text-sm font-semibold">Added to cart!</span>
        </div>

        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="pointer-events-none absolute start-4 end-4 top-4 z-20 flex items-center gap-2 rounded-xl bg-[#9b1c1c] px-4 py-2 text-white shadow-lg">
            <i class="fas fa-exclamation-circle"></i>
            <span class="text-sm font-semibold" x-text="errorMessage"></span>
        </div>
    </div>

    <div class="px-5 pb-5 pt-2 text-center md:px-6">
        <a
            href="{{ route('products.show', $product->slug) . ($product->isTrial() ? '?trial=trial' : '') }}"
            class="relative z-10 block"
        >
            <h3 class="mx-auto line-clamp-2 min-h-[2.8rem] max-w-[15rem] text-base font-black leading-snug text-[#0d1b2a] transition-colors hover:text-[#9b762f] md:text-lg">
                {{ $product->name }}
            </h3>
        </a>

        <div class="mt-4 flex min-h-8 flex-wrap items-center justify-center gap-3">
            @if($product->hasSale())
                <span class="relative text-base font-bold text-[#7b8190]">
                    <span class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <span class="absolute h-[2px] w-full origin-center rotate-[-18deg] bg-[#7b8190]"></span>
                    </span>
                    {{ $product->price }} EGP
                </span>
                <span class="text-xl font-black text-[#9b1c1c]">{{ $product?->sale?->sale_price . ' EGP' }}</span>
            @elseif($product->isTrial())
                <span class="text-xl font-black text-[#9b762f]">{{ $product->price . ' EGP' }}</span>
            @else
                <span class="text-xl font-black text-[#0d1b2a]">{{ $product->price . ' EGP' }}</span>
            @endif
        </div>
    </div>
</div>
