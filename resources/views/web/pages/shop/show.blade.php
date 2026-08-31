@extends('web.layouts.main')

@section('title')
    {{ $product->meta_title ?: $product->name }}
@endsection

@push('meta')
    @php
        $baseProduct =
            isset($product) && method_exists($product, 'isTrial') && $product->isTrial() ? $product->product : $product;
        $seoTitle = $baseProduct->meta_title ?: $baseProduct->name;
        $seoDescription =
            $baseProduct->meta_description ?:
            \Illuminate\Support\Str::limit(strip_tags($baseProduct->description ?? ''), 160);
        $seoImage = $baseProduct->image ? storage_image_url($baseProduct->image) : null;
    @endphp
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    @if ($seoImage)
        <meta property="og:image" content="{{ $seoImage }}">
    @endif
    <meta property="og:url" content="{{ request()->url() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    @if ($seoImage)
        <meta name="twitter:image" content="{{ $seoImage }}">
    @endif
@endpush

@section('content')
    <x-navbar></x-navbar>

    @php
        $type = isset($product) && method_exists($product, 'isTrial') && $product->isTrial() ? 'trial' : 'product';
        $productId = isset($product->id) ? $product->id : null;
        $cacheVersion = optional($product->updated_at)->timestamp ?? time();
        $galleryImages = collect($product->gallery_images)
            ->map(fn(string $image) => storage_image_url($image) . '?v=' . $cacheVersion)
            ->values()
            ->all();

        $stockValue = data_get($product, 'stock', 0);
        $effectiveInStock = data_get($product, 'in_stock', true) && ($stockValue === null || $stockValue > 0);
        $effectiveMaxStock = $stockValue === null ? 99 : $stockValue;
    @endphp

    <section class="py-16 md:py-24 bg-[#0d1b2a]" x-data="productCard('{{ $type }}', {{ json_encode($productId) }}, {{ $effectiveInStock ? 'true' : 'false' }}, {{ $effectiveMaxStock }})" x-init="quantity = Math.max(1, Math.min(quantity, Math.max(maxStock, 1)))">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-start">
                <!-- Product Image - Left Side -->
                <div class="space-y-4 md:sticky md:top-20" x-data="{
                    images: @js($galleryImages),
                    activeImage: 0,
                    next() {
                        if (this.images.length < 2) {
                            return;
                        }

                        this.activeImage = (this.activeImage + 1) % this.images.length;
                    },
                    previous() {
                        if (this.images.length < 2) {
                            return;
                        }

                        this.activeImage = (this.activeImage - 1 + this.images.length) % this.images.length;
                    }
                }">
                    <!-- Main Image -->
                    <div
                        class="bg-[#fbf6e8] border border-[#d8b35a]/25 rounded-2xl overflow-hidden relative p-3 flex items-center justify-center h-[24rem] md:h-[28rem]">
                        <!-- Sale Badge -->
                        @if ($product->sale)
                            <div
                                class="absolute top-6 left-6 bg-[#9b1c1c] text-white px-4 py-2 rounded-full text-sm font-black uppercase tracking-wide shadow-lg z-10">
                                {{ trans('shop.sale') }}
                            </div>
                        @endif

                        <img x-show="images.length > 0" :src="images[activeImage]" alt="{{ $product->name }}"
                            class="w-full h-full object-contain drop-shadow-[0_24px_28px_rgba(13,27,42,.20)]">

                        <div x-show="images.length === 0"
                            class="w-full h-full flex items-center justify-center text-[#0d1b2a]/30">
                            <i class="fas fa-image text-4xl"></i>
                        </div>

                        <button type="button" x-show="images.length > 1" @click="previous()"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-[#0d1b2a]/70 text-[#f7d879] hover:bg-[#0d1b2a] transition-colors z-20">
                            <i class="fas fa-chevron-left"></i>
                        </button>

                        <button type="button" x-show="images.length > 1" @click="next()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-[#0d1b2a]/70 text-[#f7d879] hover:bg-[#0d1b2a] transition-colors z-20">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <div x-show="images.length > 1" class="grid grid-cols-5 gap-3">
                        <template x-for="(image, index) in images" :key="image + '-' + index">
                            <button type="button" @click="activeImage = index"
                                class="aspect-square rounded-lg overflow-hidden border-2 transition-colors bg-[#fbf6e8]"
                                :class="activeImage === index ? 'border-[#d8b35a]' : 'border-[#d8b35a]/20'">
                                <img :src="image" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Product Details - Right Side -->
                <div class="space-y-6">
                    <!-- Product Title -->
                    <div>
                        <h1 class="text-white text-3xl md:text-4xl font-black mb-2">
                            {{ $product->name }}
                        </h1>

                        <div class="flex items-center gap-2 text-sm text-white/70">
                            <i class="fas fa-eye text-[#f7d879]"></i>
                            <span>{{ number_format($product->view_count) }} {{ $product->view_count == 1 ? 'Product View' : 'product Views' }}</span>
                        </div>

                    </div>

                    <!-- Price -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4">
                            @if ($product->hasSale())
                                <!-- Old Price (Slashed) -->
                                <span class="text-white/40 text-2xl line-through">{{ $product->price . ' EGP' }}</span>
                                <!-- Sale Price -->
                                <span
                                    class="text-[#f7d879] text-4xl font-black">{{ $product?->sale?->sale_price . ' EGP' }}</span>
                                <!-- Discount Percentage (Optional) -->
                                @php
                                    $discount = round(
                                        (($product->price - $product->sale->sale_price) / $product->price) * 100,
                                    );
                                @endphp
                                <span class="bg-[#9b1c1c] text-white px-3 py-1 rounded-full text-sm font-black">
                                    -{{ $discount }}%
                                </span>
                            @else
                                <!-- Regular Price -->
                                <span class="text-white text-4xl font-black">{{ $product->price . ' EGP' }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span x-text="inStock ? 'In stock' : 'Out of stock'"
                                :class="inStock ? 'text-emerald-400' : 'text-red-400'" class="text-lg font-semibold"></span>
                            <span x-show="inStock && maxStock !== 99" x-text="`Only ${maxStock} left`"
                                class="text-white/40"></span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="border-t border-[#d8b35a]/25 pt-6 text-white">
                        <h3 class="text-[#f7d879] text-lg font-black uppercase tracking-wide mb-3">{{ trans('shop.desc') }}
                        </h3>
                        <p class="text-white/60 leading-relaxed">
                            {!! $product->description !!}
                        </p>
                    </div>

                    @if (!empty($brandImage))
                        <div class="border-t border-[#d8b35a]/25 pt-6 text-white">
                            <h3 class="text-[#f7d879] text-lg font-black uppercase tracking-wide mb-3">
                                {{ trans('shop.brand') }}</h3>
                            <div class="flex items-center">
                                <img src="{{ $brandImage }}" alt="{{ $brandName ?? 'Brand' }}"
                                    class="w-14 h-14 rounded-full object-cover ring-1 ring-[#d8b35a]/40">
                            </div>
                        </div>
                    @endif

                    <div>
                        <!-- Quantity Selector -->
                        <div class="space-y-4">
                            <label class="block text-white font-semibold">{{ trans('shop.quantity') }}</label>
                            <p class="text-sm text-white/40">
                                @if ($stockValue === null)
                                    {{ trans('shop.unlimited_stock') }}
                                @elseif($effectiveInStock)
                                    {{ trans('shop.stock_available', ['count' => $effectiveMaxStock]) }}
                                @else
                                    {{ trans('shop.out_of_stock') }}
                                @endif
                            </p>

                            <div
                                class="flex items-center border border-[#d8b35a]/30 rounded-lg overflow-hidden bg-white/5 w-fit">
                                <button type="button"
                                    class="px-4 py-3 text-white hover:bg-white/10 transition-colors disabled:opacity-50"
                                    @click="decrementQuantity()" :disabled="quantity <= 1">
                                    <i class="fas fa-minus"></i>
                                </button>

                                <input x-model.number="quantity" type="number" name="quantity" min="1"
                                    :max="maxStock"
                                    class="w-20 text-center bg-transparent text-white py-3 focus:outline-none border-x border-[#d8b35a]/30 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                    @input="quantity = Math.max(1, Math.min(maxStock, Number(quantity)))"
                                    @change="quantity = Math.max(1, Math.min(maxStock, Number(quantity)))">

                                <button type="button"
                                    class="px-4 py-3 text-white hover:bg-white/10 transition-colors disabled:opacity-50"
                                    @click="incrementQuantity()" :disabled="quantity >= maxStock">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button @click="addToCart()" :disabled="adding || !inStock" type="submit"
                            class="w-full mt-5 bg-[#d8b35a] text-[#0d1b2a] py-4 rounded-xl font-black uppercase tracking-wide text-lg hover:bg-[#f7d879] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span x-show="!adding">{{ trans('shop.addtocart') }}</span>
                            <span x-show="adding">Adding...</span>
                        </button>

                        <!-- Buy Now Button -->
                        <button @click="buyNow()" :disabled="adding || !inStock" type="button"
                            class="w-full mt-3 bg-white/5 border border-[#d8b35a]/35 text-white py-4 rounded-xl font-black uppercase tracking-wide text-lg hover:bg-white/10 hover:border-[#d8b35a] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fas fa-bolt text-[#f7d879]"></i>
                            <span>{{ trans('shop.buynow', [], app()->getLocale()) }}</span>
                        </button>

                        <div x-show="showSuccess" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="bg-[#176f43] text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 mt-4">
                            <i class="fas fa-check-circle"></i>
                            <span class="text-sm font-medium">{{ trans('shop.addtocart') }}</span>
                        </div>
                        <div x-show="errorMessage" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="bg-[#9b1c1c] text-white px-4 py-2 rounded-lg shadow-lg flex items-center gap-2 mt-4">
                            <i class="fas fa-exclamation-circle"></i>
                            <span class="text-sm font-medium" x-text="errorMessage"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection