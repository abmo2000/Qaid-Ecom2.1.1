@extends('web.layouts.main')

@section('title')
    {{ $package->name }}
@endsection

@section('content')
    <x-navbar></x-navbar>

    @php
        $galleryImages = collect($package->products)
            ->filter(fn($product) => ! empty($product->image))
            ->pluck('image')
            ->map(fn($image) => storage_image_url($image))
            ->values()
            ->all();

        if (empty($galleryImages)) {
            $galleryImages = [storage_image_url($package->image ?? null) ?: asset('images/default-product.png')];
        }
    @endphp

    <section class="py-16 md:py-24 bg-[#0d1b2a]" x-data="productCard('package', {{ $package->id }})">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-start">
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
                    <div class="bg-[#fbf6e8] border border-[#d8b35a]/25 rounded-2xl overflow-hidden relative p-3 flex items-center justify-center h-[24rem] md:h-[28rem]">
                        <div class="absolute top-6 left-6 bg-[#9b1c1c] text-white px-4 py-2 rounded-full text-sm font-black uppercase tracking-wide shadow-lg z-10">
                            {{ trans('shop.packages') }}
                        </div>

                        <img x-show="images.length > 0" :src="images[activeImage]" alt="{{ $package->name }}"
                            class="w-full h-full object-contain drop-shadow-[0_24px_28px_rgba(13,27,42,.20)]">

                        <div x-show="images.length === 0" class="w-full h-full flex items-center justify-center text-[#0d1b2a]/30">
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
                                <img :src="image" alt="{{ $package->name }}" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <h1 class="text-white text-3xl md:text-4xl font-black mb-2">
                            {{ $package->name }}
                        </h1>
                    </div>

                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-4">
                            <span class="text-white/40 text-2xl line-through">{{ $package->original_price }} EGP</span>
                            <span class="text-[#f7d879] text-4xl font-black">{{ $package->price }} EGP</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="text-emerald-400 text-lg font-semibold">In stock</span>
                            <span class="text-white/40">{{ $package->products_count ?? $package->products->count() }} products included</span>
                        </div>
                    </div>

                    <div class="border-t border-[#d8b35a]/25 pt-6 text-white">
                        <h3 class="text-[#f7d879] text-lg font-black uppercase tracking-wide mb-3">{{ trans('shop.desc') }}</h3>
                        <div class="text-white/60 leading-relaxed">
                            {!! $package->description !!}
                        </div>
                    </div>

                    <div>
                        <div class="space-y-4">
                            <label class="block text-white font-semibold">{{ trans('shop.quantity') }}</label>

                            <div class="flex items-center border border-[#d8b35a]/30 rounded-lg overflow-hidden bg-white/5 w-fit">
                                <button type="button"
                                    class="px-4 py-3 text-white hover:bg-white/10 transition-colors disabled:opacity-50"
                                    @click="quantity--" :disabled="quantity <= 1">
                                    <i class="fas fa-minus"></i>
                                </button>

                                <input x-model.number="quantity" type="number" min="1"
                                    class="w-20 text-center bg-transparent text-white py-3 focus:outline-none border-x border-[#d8b35a]/30 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">

                                <button type="button"
                                    class="px-4 py-3 text-white hover:bg-white/10 transition-colors disabled:opacity-50"
                                    @click="quantity++" :disabled="quantity >= 99">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <button @click="addToCart()" :disabled="adding" type="submit"
                            class="w-full mt-5 bg-[#d8b35a] text-[#0d1b2a] py-4 rounded-xl font-black uppercase tracking-wide text-lg hover:bg-[#f7d879] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i>
                            <span x-show="!adding">{{ trans('shop.addtocart') }}</span>
                            <span x-show="adding">Adding...</span>
                        </button>

                        <button @click="buyNow()" :disabled="adding" type="button"
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

            <div class="mt-16">
                <h2 class="text-2xl md:text-3xl font-black text-white mb-6">
                    {{ trans('shop.package_includes') }}
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($package->products as $product)
                        <div class="bg-slate-800/70 border border-[#d8b35a]/15 rounded-2xl p-4 hover:-translate-y-1 transition-transform duration-300">
                            <div class="h-40 bg-[#fbf6e8] rounded-xl flex items-center justify-center mb-4 overflow-hidden">
                                <img src="{{ storage_image_url($product->image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                            </div>

                            <h3 class="text-white font-semibold mb-2">
                                {{ $product->name }}
                            </h3>

                            <p class="text-gray-400 text-sm">
                                Regular Price:
                                <span class="line-through">
                                    {{ $product->price }} EGP
                                </span>
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection