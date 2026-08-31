@extends('web.layouts.main')

@section('title')
    {{ $seoData['meta_title'] ?: config('app.name') }}
@endsection

@push('meta')
    <meta name="description" content="{{ $seoData['meta_description'] ?: 'Welcome to our e-commerce store' }}">
    @if ($seoData['meta_keywords'])
        <meta name="keywords" content="{{ $seoData['meta_keywords'] }}">
    @endif
    <meta name="robots" content="index, follow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $seoData['meta_title'] ?: config('app.name') }}">
    <meta property="og:description" content="{{ $seoData['meta_description'] ?: 'Welcome to our e-commerce store' }}">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $seoData['meta_title'] ?: config('app.name') }}">
    <meta name="twitter:description" content="{{ $seoData['meta_description'] ?: 'Welcome to our e-commerce store' }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
@endpush

@section('content')

    <x-navbar></x-navbar>

    <!-- QAID Hero -->
    <section class="relative isolate overflow-hidden bg-[#f7f1e3] text-[#0d1b2a]">
        <div class="absolute inset-0 -z-10">
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(216,179,90,.28),transparent_32%),radial-gradient(circle_at_84%_35%,rgba(13,27,42,.14),transparent_34%),linear-gradient(135deg,#fffaf0_0%,#f4ead4_55%,#e7d5ad_100%)]">
            </div>
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#d8b35a]/60 to-transparent">
            </div>
        </div>

        <div
            class="container mx-auto grid min-h-[calc(100vh-5rem)] grid-cols-1 items-center gap-8 px-4 py-20 lg:grid-cols-[.95fr_1.05fr] lg:gap-12">
            <!-- Text -->
            <div class="relative z-10 mx-auto max-w-2xl text-center lg:mx-0 lg:text-start">
                <div
                    class="mb-6 inline-flex items-center gap-3 rounded-full border border-[#d8b35a]/40 bg-white/60 px-4 py-2 shadow-sm backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-[#d8b35a] shadow-[0_0_18px_rgba(216,179,90,.85)]"></span>
                    <span class="text-xs font-black uppercase tracking-[0.32em] text-[#0d1b2a]">
                        Premium Tech Store
                    </span>
                </div>

                <h1
                    class="text-5xl font-black uppercase leading-[.85] tracking-wide text-[#0d1b2a] sm:text-6xl md:text-7xl xl:text-9xl">
                    QAID
                    <span class="mt-3 block text-transparent [-webkit-text-stroke:2px_#b8913f]">
                        Store
                    </span>
                </h1>

                <h2
                    class="mx-auto mt-7 max-w-xl text-base font-semibold leading-relaxed text-[#4d5562] md:text-xl lg:mx-0 lg:text-2xl">
                    {{ trans('home.hydrate-&-nourish-your-hair') }}
                </h2>

                <div class="mt-9 flex flex-wrap items-center justify-center gap-4 lg:justify-start">

                    <a href="{{ route('shop') }}"
                        class="inline-flex items-center justify-center rounded-full bg-[#0d1b2a] px-6 py-3.5 text-sm font-black uppercase tracking-wider text-[#f7d879] shadow-xl shadow-[#0d1b2a]/20 transition hover:-translate-y-1 hover:bg-[#13283d] md:px-8 md:py-4">
                        {{ trans('home.we-support-your-choice') }}
                    </a>


                    <a href="#fearured-products"
                        class="inline-flex items-center justify-center rounded-full border border-[#b8913f]/40 bg-white/70 px-6 py-3.5 text-sm font-black uppercase tracking-wider text-[#0d1b2a] shadow-sm backdrop-blur transition hover:border-[#b8913f] hover:text-[#9b762f] md:px-7 md:py-4">
                        Explore Products
                    </a>
                </div>
            </div>

            <!-- Product Carousel Inside Circle -->
            @php
                $heroItems = $heroProducts
                    ->filter(fn($product) => !empty($product->image))
                    ->map(function ($product) {
                        return [
                            'title' => $product->name ?? $product->title ?? 'Product',
                            'tag' => $product->category?->title ?? 'Product',
                            'image' => storage_image_url($product->image),
                        ];
                    })
                    ->values()
                    ->toArray();

                if (empty($heroItems)) {
                    $heroItems = [
                        ['title' => 'Mechanical Keyboard', 'tag' => 'Keyboard', 'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=80'],
                        ['title' => 'Gaming Mouse', 'tag' => 'Mouse', 'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=900&q=80'],
                        ['title' => 'Gaming Headphones', 'tag' => 'Audio', 'image' => '/Black-and-red-gaming-headphones-on-transparent-background-PNG.png'],
                        ['title' => 'Laptop Setup', 'tag' => 'Laptop', 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=80'],
                        ['title' => 'Gaming Controller', 'tag' => 'Controller', 'image' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?auto=format&fit=crop&w=900&q=80'],
                    ];
                }
            @endphp
            <div x-data='{
                active: 0,
                items: @json($heroItems),
                next() { this.active = (this.active + 1) % this.items.length; },
                prev() { this.active = this.active === 0 ? this.items.length - 1 : this.active - 1; },
                init() { setInterval(() => this.next(), 4200); }
            }'
                class="relative mx-auto flex aspect-square w-full max-w-[430px] items-center justify-center lg:min-h-[540px] lg:max-w-none">
                <div class="absolute inset-[7%] z-0 rounded-full border border-[#b8913f]/35"></div>
                <div class="absolute inset-[10%] z-0 rounded-full bg-[#d8b35a]/10"></div>
                <div class="absolute z-0 h-[50%] w-[50%] rounded-full bg-[#d8b35a]/25 blur-3xl"></div>

                <div class="relative z-10 flex h-[68%] w-[68%] items-center justify-center overflow-visible rounded-full">
                    <template x-for="(item, index) in items" :key="item.title">
                        <img x-show="active === index" x-transition:enter="transition ease-out duration-700"
                            x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-500"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            :src="item.image" :alt="item.title"
                            class="absolute h-[92%] w-[92%] object-contain drop-shadow-[0_34px_40px_rgba(13,27,42,.32)]">
                    </template>
                </div>

                <button type="button" @click="prev()"
                    class="absolute left-[7%] top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-[#d8b35a]/40 bg-white/90 text-[#0d1b2a] shadow-lg transition hover:bg-[#0d1b2a] hover:text-[#f7d879]">
                    <i class="fas fa-arrow-left text-sm"></i>
                </button>
                <button type="button" @click="next()"
                    class="absolute right-[7%] top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-[#d8b35a]/40 bg-white/90 text-[#0d1b2a] shadow-lg transition hover:bg-[#0d1b2a] hover:text-[#f7d879]">
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>

                <div class="absolute bottom-[7%] left-1/2 z-20 flex -translate-x-1/2 gap-2">
                    <template x-for="(item, index) in items" :key="index">
                        <button type="button" @click="active = index" class="h-2.5 rounded-full transition-all"
                            :class="active === index ? 'w-8 bg-[#d8b35a]' : 'w-2.5 bg-[#0d1b2a]/30'"></button>
                    </template>
                </div>

                <div
                    class="qaid-float absolute left-[3%] top-[16%] z-20 rounded-2xl border border-[#d8b35a]/35 bg-white/90 px-4 py-3 shadow-xl backdrop-blur">
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-[#9b762f]">QAID Picks</p>
                    <p class="text-sm font-black text-[#0d1b2a]">Premium Gear</p>
                </div>
                <div
                    class="qaid-float qaid-float-delay-1 absolute bottom-[18%] right-[3%] z-20 rounded-2xl border border-[#d8b35a]/35 bg-white/90 px-4 py-3 shadow-xl backdrop-blur">
                    <p class="text-[10px] font-black uppercase tracking-[0.22em] text-[#9b762f]">New Stock</p>
                    <p class="text-sm font-black text-[#0d1b2a]">Tech Essentials</p>
                </div>
            </div>
        </div>

        <!-- Clickable Marquee -->
        <div class="relative border-y border-[#d8b35a]/30 bg-[#0d1b2a] py-4 shadow-[0_-10px_30px_rgba(13,27,42,.08)]">
            <div class="qaid-marquee">
                <div class="qaid-marquee-track">
                    @for ($i = 0; $i < 2; $i++)
                        @foreach (['KEYBOARD', 'WEB CAM', 'HEADPHONE', 'CONTROLLER', 'SWITCHES', 'MOUSE', 'LAPTOP', 'ACCESSORIES'] as $item)
                            <a href="{{ route('shop') }}" class="qaid-marquee-link">{{ $item }}</a>
                            <span class="qaid-marquee-separator">//</span>
                        @endforeach
                    @endfor
                </div>
            </div>
        </div>
    </section>

<!-- Categories -->
<section class="bg-[#f7f1e3] py-14 text-[#0d1b2a] md:py-20">
    <div class="w-full px-4" x-data="categoryCarousel()">
        <div class="mb-8 flex flex-col gap-4 md:mb-10 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.32em] text-[#9b762f]">Shop by category</p>
                <h2 class="mt-3 text-3xl font-black uppercase leading-none tracking-wide text-[#0d1b2a] md:text-5xl">
                    Pick your setup</h2>
            </div>

            <div class="flex items-center gap-3">
                {{-- <a href="{{ route('shop') }}"
                    class="inline-flex w-fit items-center gap-3 rounded-full border border-[#b8913f]/35 bg-white/75 px-5 py-3 text-xs font-black uppercase tracking-wider text-[#0d1b2a] shadow-sm transition hover:-translate-y-0.5 hover:border-[#b8913f] hover:text-[#9b762f]">
                    View all
                    <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} text-[11px]"></i>
                </a> --}}

                {{-- Manual nav arrows — "prev" always points toward reading start, "next" toward reading end --}}
                <div class="hidden md:flex items-center gap-2">
                    <button type="button" @click="prev()" :disabled="!canScrollPrev"
                        :class="!canScrollPrev ? 'opacity-30 cursor-not-allowed' : 'hover:bg-[#0d1b2a] hover:text-[#f7d879]'"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-[#b8913f]/40 bg-white/75 text-[#0d1b2a] shadow-sm transition">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-right' : 'fa-arrow-left' }} text-sm"></i>
                    </button>
                    <button type="button" @click="next()" :disabled="!canScrollNext"
                        :class="!canScrollNext ? 'opacity-30 cursor-not-allowed' : 'hover:bg-[#0d1b2a] hover:text-[#f7d879]'"
                        class="flex h-11 w-11 items-center justify-center rounded-full border border-[#b8913f]/40 bg-white/75 text-[#0d1b2a] shadow-sm transition">
                        <i class="fas {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }} text-sm"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto scroll-smooth snap-x snap-mandatory [-webkit-overflow-scrolling:touch] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
            id="categories-strip-viewport" x-ref="viewport" @scroll.debounce.100ms="updateScrollState()">
            <div class="flex flex-nowrap items-stretch gap-5 md:gap-7" id="categories-strip-track">
                @forelse ($categories as $category)
                    <div class="category-slide-item snap-start w-[78vw] max-w-[330px] shrink-0 sm:w-[310px] md:w-[360px] md:max-w-none">
                        <a href="{{ route('shop', ['category' => $category->title]) }}"
                            class="group flex h-full min-h-[320px] flex-col overflow-hidden rounded-[2rem] border border-[#d8b35a]/35 bg-white shadow-[0_22px_55px_rgba(13,27,42,.10)] transition duration-300 hover:-translate-y-2 hover:border-[#b8913f]/70 hover:shadow-[0_30px_70px_rgba(13,27,42,.16)]">
                            <div class="relative m-4 mb-0 flex aspect-[4/3] items-center justify-center overflow-hidden rounded-[1.45rem] bg-[#efe2c2]">
                                <div class="absolute inset-0 border border-white/60"></div>
                                @if (!empty($category->image))
                                    <img src="{{ asset('storage/' . ltrim($category->image, '/')) }}"
                                        alt="{{ $category->title }}"
                                        class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="flex h-24 w-24 items-center justify-center rounded-full border border-[#d8b35a]/45 bg-white/85 text-5xl font-black text-[#9b762f] shadow-lg">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim($category->title ?? ''), 0, 1)) ?: '?' }}
                                    </div>
                                @endif
                                <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1.5 text-[10px] font-black uppercase tracking-[0.22em] text-[#9b762f] shadow-sm">QAID</span>
                            </div>
                            <div class="flex flex-1 flex-col justify-between p-5 md:p-6">
                                <h3 class="line-clamp-2 text-2xl font-black leading-tight text-[#0d1b2a] transition group-hover:text-[#9b762f] md:text-3xl">
                                    {{ $category->title }}</h3>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="w-full rounded-2xl border border-[#d8b35a]/35 bg-white/70 px-6 py-8 text-center font-semibold text-[#5f6670]">
                        No categories found.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
    <!-- Featured Products -->
    <section id="fearured-products" class="bg-[#0d1b2a] py-16 text-white md:py-24">
       <div class="w-full px-4">
            <h2 class="mb-3 text-center text-4xl font-black uppercase tracking-wide text-[#f7d879] md:text-5xl">
                {{ trans('home.featured-products') }}</h2>
            <p class="mx-auto mb-12 max-w-2xl text-center text-base font-semibold text-white/65">
                {{ trans('home.discover-products') }}</p>

            <div class="space-y-14">
                @forelse ($categorySections as $section)
                    <div>
                        <div class="mb-8 flex items-center justify-center gap-4 text-center">
                            <span class="hidden h-px w-16 bg-[#d8b35a]/40 sm:block"></span>
                            <h3
                                class="rounded-full border border-[#d8b35a]/35 bg-white/10 px-6 py-3 text-xl font-black uppercase tracking-[0.18em] text-[#f7d879] shadow-[0_18px_45px_rgba(0,0,0,.18)] backdrop-blur md:text-2xl">
                                {{ $section['title'] }}
                            </h3>
                            <span class="hidden h-px w-16 bg-[#d8b35a]/40 sm:block"></span>
                        </div>

                        <div class="swiper products-swiper mx-auto max-w-7xl pb-10">
                            <div class="swiper-wrapper pt-4">
                                @foreach ($section['products'] as $product)
                                    <div class="swiper-slide !h-auto !w-[78vw] sm:!w-[300px] lg:!w-[300px] xl:!w-[320px]">
                                        <x-product :product="$product" layout="grid"></x-product>
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination products-swiper-pagination"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-white/65">No products available right now.</p>
                @endforelse
            </div>
        </div>
    </section>

@endsection

@push('scripts_bottom')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        .products-swiper .swiper-pagination {
            position: static;
            margin-top: 1.25rem;
            line-height: 1;
        }

        .products-swiper .swiper-pagination-bullet {
            width: 0.625rem;
            height: 0.625rem;
            background: rgba(255, 255, 255, 0.32);
            opacity: 1;
            transition: width 0.25s ease, background-color 0.25s ease;
        }

        .products-swiper .swiper-pagination-bullet-active {
            width: 1.75rem;
            border-radius: 999px;
            background: #f7d879;
        }
    </style>
    <script>
    function categoryCarousel() {
    return {
        canScrollPrev: false,
        canScrollNext: true,
        isRTL: false,

        init() {
            this.isRTL = getComputedStyle(this.$refs.viewport).direction === 'rtl';
            this.$nextTick(() => this.updateScrollState());
            window.addEventListener('resize', () => this.updateScrollState());
        },

        getStep() {
            const track = this.$refs.viewport.querySelector('#categories-strip-track');
            const firstItem = track?.firstElementChild;
            if (!firstItem) return 0;
            const gap = parseInt(window.getComputedStyle(track).columnGap || '0', 10) || 0;
            return firstItem.getBoundingClientRect().width + gap;
        },

        // "next" = move toward the end of reading direction
        next() {
            const step = this.isRTL ? -this.getStep() : this.getStep();
            this.$refs.viewport.scrollBy({ left: step, behavior: 'smooth' });
        },

        // "prev" = move toward the start of reading direction
        prev() {
            const step = this.isRTL ? this.getStep() : -this.getStep();
            this.$refs.viewport.scrollBy({ left: step, behavior: 'smooth' });
        },

        // Normalizes scrollLeft across browsers so canScrollPrev/Next work identically in RTL
        getNormalizedScrollLeft(el) {
            if (!this.isRTL) return el.scrollLeft;

            // Chrome/Edge: negative scrollLeft in RTL (0 = start, negative = scrolled toward end)
            // Firefox: positive, starts at max and decreases toward 0
            // Safari: positive, starts at 0 and increases (like LTR)
            const raw = el.scrollLeft;
            if (raw <= 0) {
                // Chrome-style negative values
                return Math.abs(raw);
            }
            // Firefox-style: max value at rest, decreases as you scroll
            const maxScroll = el.scrollWidth - el.clientWidth;
            if (raw <= maxScroll + 1) {
                return maxScroll - raw;
            }
            // Safari-style, already correct
            return raw;
        },

        updateScrollState() {
            const el = this.$refs.viewport;
            const scrolled = this.getNormalizedScrollLeft(el);
            const maxScroll = el.scrollWidth - el.clientWidth;

            this.canScrollPrev = scrolled > 4;
            this.canScrollNext = scrolled < maxScroll - 4;
        },
    };
}

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.products-swiper').forEach(function(swiperEl) {
                const slideCount = swiperEl.querySelectorAll('.swiper-slide').length;

                new Swiper(swiperEl, {
                    slidesPerView: 'auto',
                    spaceBetween: 24,
                    speed: 850,
                    grabCursor: true,
                    watchOverflow: true,
                    autoHeight: false,
                    loop: slideCount > 1,
                    autoplay: slideCount > 1 ? {
                        delay: 2800,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true,
                    } : false,
                    pagination: {
                        el: swiperEl.querySelector('.swiper-pagination'),
                        clickable: true,
                    },
                    breakpoints: {
                        0: {
                            spaceBetween: 16
                        },
                        768: {
                            spaceBetween: 24
                        },
                    },
                });
            });
        });
    </script>
@endpush
