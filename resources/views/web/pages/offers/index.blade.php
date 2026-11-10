@extends('web.layouts.offers')

@section('offers-content')
    @php
        $activeFilterCount = (int) (bool) $activeDiscount + (int) (bool) $activeCategory;
        $clearAllQuery = request()->only('lang');
        $countTranslation = trans_choice('offers.products_count', $totalOffers, ['count' => '__OFFER_COUNT__']);
        $countLabel = str_replace('__OFFER_COUNT__', '<bdi dir="ltr">'.$totalOffers.'</bdi>', e($countTranslation));
        $activeCategoryLabel = $categoryLabels[$activeCategory] ?? __('offers.category_fallback');
    @endphp

    <section class="relative isolate flex min-h-32 items-center justify-between gap-2 overflow-hidden rounded-[1.5rem] border border-[#d8b35a]/35 bg-white/80 px-4 py-4 shadow-[0_14px_38px_rgba(13,27,42,.08)] sm:min-h-40 sm:gap-8 sm:px-8 sm:py-6">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-offer via-[#d8b35a] to-offer"></div>
        <div class="min-w-0 flex-1">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#9b762f] sm:text-[11px] sm:tracking-[0.24em]">
                {{ __('offers.eyebrow') }}
            </p>
            <h1 class="mt-1 font-sans text-2xl font-bold leading-tight text-[#0d1b2a] sm:text-4xl">
                {{ __('offers.title') }}
            </h1>
            <p class="mt-1 line-clamp-1 max-w-2xl text-xs leading-4 text-slate-600 sm:text-sm sm:leading-5">
                {{ __('offers.subtitle') }}
            </p>
        </div>

        <div class="flex shrink-0 items-center gap-2 rounded-2xl border border-offer/15 bg-white px-2.5 py-2.5 shadow-sm sm:min-w-52 sm:justify-center sm:gap-3 sm:px-4 sm:py-3">
            @if($maximumDiscount > 0)
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-offer/10 text-sm text-offer sm:h-11 sm:w-11 sm:text-base">
                    <i class="fas fa-tag" aria-hidden="true"></i>
                </span>
                <span class="min-w-0">
                    <span class="block text-[8px] font-black uppercase tracking-[0.08em] text-slate-500 sm:text-[10px] sm:tracking-[0.14em]">{{ __('offers.max_discount_label') }}</span>
                    <span class="mt-0.5 flex flex-wrap items-baseline gap-x-1 text-sm font-black leading-tight text-offer sm:text-xl">
                        <span>{{ __('offers.up_to_prefix') }}</span>
                        <bdi dir="ltr">{{ $maximumDiscount }}{{ app()->isLocale('ar') ? '٪' : '%' }}</bdi>
                        @if(__('offers.up_to_suffix'))
                            <span>{{ __('offers.up_to_suffix') }}</span>
                        @endif
                    </span>
                </span>
            @else
                <span class="text-sm font-bold text-slate-600">{{ __('offers.no_active_discount') }}</span>
            @endif
        </div>
    </section>

    <div id="offers-toolbar-sentinel" class="h-px" aria-hidden="true"></div>
    <section class="offers-toolbar mt-3 rounded-2xl border border-[#d8b35a]/25 bg-white/95 px-3 py-2.5 backdrop-blur-md sm:px-4" data-offers-toolbar aria-label="{{ __('offers.filters') }}">
        <div class="flex items-center justify-between gap-2 md:hidden">
            <details class="offers-filter-details min-w-0" data-offers-filter-details>
                <summary class="offers-filter-summary inline-flex min-h-11 items-center gap-2 rounded-full border border-[#d8b35a]/45 bg-white px-4 text-sm font-bold text-[#0d1b2a] transition duration-200 hover:border-[#d8b35a] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#d8b35a]/30" aria-label="{{ __('offers.open_filters') }}">
                    <i class="fas fa-sliders-h text-[#9b762f]" aria-hidden="true"></i>
                    <span>{{ __('offers.filters') }}</span>
                    @if($activeFilterCount > 0)
                        <bdi dir="ltr" class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#0d1b2a] px-1.5 text-[11px] font-black text-[#f7d879]">{{ $activeFilterCount }}</bdi>
                    @endif
                    <span class="offers-filter-close-icon" aria-hidden="true">&times;</span>
                </summary>

                <div class="offers-filter-panel rounded-t-[1.5rem] border border-[#d8b35a]/25 bg-[#fffdf8] p-5 shadow-[0_-16px_45px_rgba(13,27,42,.16)]">
                    <form method="GET" action="{{ route('offers.index') }}">
                        @if(request()->query('lang'))
                            <input type="hidden" name="lang" value="{{ request()->query('lang') }}">
                        @endif
                        <input type="hidden" name="sort" value="{{ $activeSort }}">
                        <fieldset>
                            <legend class="mb-2 text-xs font-black uppercase tracking-[0.14em] text-slate-500">{{ __('offers.discount_range') }}</legend>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="offers-filter-choice">
                                    <input class="peer offers-filter-radio" type="radio" name="discount" value="" @checked(! $activeDiscount)>
                                    <span>{{ __('offers.all_discount') }} <bdi dir="ltr">({{ $totalOffers }})</bdi></span>
                                </label>
                                @foreach([10, 25, 40] as $range)
                                    <label class="offers-filter-choice {{ $discountCounts[$range] === 0 ? 'opacity-55' : '' }}">
                                        <input class="peer offers-filter-radio" type="radio" name="discount" value="{{ $range }}" @checked($activeDiscount === $range)>
                                        <span>
                                            <bdi dir="ltr">{{ app()->isLocale('ar') ? '+'.$range.'٪' : $range.'%+' }}</bdi>
                                            <bdi dir="ltr">({{ $discountCounts[$range] }})</bdi>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        @if($categories->isNotEmpty())
                            <fieldset class="mt-5">
                                <legend class="mb-2 text-xs font-black uppercase tracking-[0.14em] text-slate-500">{{ __('offers.category') }}</legend>
                                <div class="grid max-h-40 grid-cols-2 gap-2 overflow-y-auto pe-1">
                                    <label class="offers-filter-choice">
                                        <input class="peer offers-filter-radio" type="radio" name="category" value="" @checked(! $activeCategory)>
                                        <span>{{ __('offers.all_categories') }}</span>
                                    </label>
                                    @foreach($categories as $category)
                                        <label class="offers-filter-choice">
                                            <input class="peer offers-filter-radio" type="radio" name="category" value="{{ $category->id }}" @checked($activeCategory === $category->id)>
                                            <span>{{ $categoryLabels[$category->id] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif

                        <div class="mt-5 flex items-center justify-between gap-3">
                            <a href="{{ route('offers.index', $clearAllQuery) }}" class="inline-flex min-h-11 items-center px-2 text-sm font-bold text-offer underline-offset-4 hover:underline">
                                {{ __('offers.clear_all') }}
                            </a>
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-full bg-[#0d1b2a] px-6 text-sm font-black text-[#f7d879] transition duration-200 hover:bg-[#13283d] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#d8b35a]/40">
                                {{ __('offers.apply_filters') }}
                            </button>
                        </div>
                    </form>
                </div>
            </details>

            <form method="GET" action="{{ route('offers.index') }}" class="flex min-w-0 flex-1 items-center justify-center gap-1">
                @if($activeDiscount)
                    <input type="hidden" name="discount" value="{{ $activeDiscount }}">
                @endif
                @if($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                @if(request()->query('lang'))
                    <input type="hidden" name="lang" value="{{ request()->query('lang') }}">
                @endif
                <label for="offers-sort-mobile" class="sr-only">{{ __('offers.sort_by') }}</label>
                <select id="offers-sort-mobile" name="sort" onchange="this.form.submit()" class="min-h-11 min-w-0 max-w-40 rounded-full border border-[#d8b35a]/35 bg-white px-3 text-xs font-bold text-[#0d1b2a] outline-none transition focus-visible:ring-4 focus-visible:ring-[#d8b35a]/30">
                    <option value="discount" @selected($activeSort === 'discount')>{{ __('offers.sort_discount') }}</option>
                    <option value="price_low" @selected($activeSort === 'price_low')>{{ __('offers.sort_price_low') }}</option>
                    <option value="price_high" @selected($activeSort === 'price_high')>{{ __('offers.sort_price_high') }}</option>
                    <option value="newest" @selected($activeSort === 'newest')>{{ __('offers.sort_newest') }}</option>
                </select>
                <noscript><button type="submit" class="text-xs font-bold text-[#0d1b2a]">{{ __('offers.apply_sort') }}</button></noscript>
            </form>

            <p class="shrink-0 text-xs font-bold text-slate-600" aria-live="polite">{!! $countLabel !!}</p>
        </div>

        <div class="hidden items-center gap-2 md:flex">
            <p class="shrink-0 text-xs font-bold text-slate-600" aria-live="polite">{!! $countLabel !!}</p>

            <nav class="flex shrink-0 items-center gap-1" aria-label="{{ __('offers.discount_range') }}">
                @php
                    $allDiscountQuery = request()->except(['discount', 'page']);
                @endphp
                <a href="{{ route('offers.index', $allDiscountQuery) }}"
                    @class(['offers-toolbar-chip', 'is-selected' => ! $activeDiscount]) @if(! $activeDiscount) aria-current="true" @endif>
                    {{ __('offers.all_discount') }} <bdi dir="ltr">({{ $totalOffers }})</bdi>
                </a>
                @foreach([10, 25, 40] as $range)
                    @php
                        $discountQuery = request()->except('page');
                        $discountQuery['discount'] = $range;
                    @endphp
                    <a href="{{ route('offers.index', $discountQuery) }}"
                        @class(['offers-toolbar-chip', 'is-selected' => $activeDiscount === $range, 'is-zero' => $discountCounts[$range] === 0])
                        aria-pressed="{{ $activeDiscount === $range ? 'true' : 'false' }}">
                        <bdi dir="ltr">{{ app()->isLocale('ar') ? '+'.$range.'٪' : $range.'%+' }}</bdi>
                        <bdi dir="ltr">({{ $discountCounts[$range] }})</bdi>
                    </a>
                @endforeach
            </nav>

            @if($categories->isNotEmpty() && $categories->count() <= 5)
                <nav class="offers-category-chips min-w-0 flex-1" aria-label="{{ __('offers.category') }}">
                    @php
                        $allCategoryQuery = request()->except(['category', 'page']);
                    @endphp
                    <a href="{{ route('offers.index', $allCategoryQuery) }}"
                        @class(['offers-toolbar-chip', 'is-selected' => ! $activeCategory]) @if(! $activeCategory) aria-current="true" @endif>
                        {{ __('offers.all_categories') }}
                    </a>
                    @foreach($categories as $category)
                        @php
                            $categoryQuery = request()->except('page');
                            $categoryQuery['category'] = $category->id;
                        @endphp
                        <a href="{{ route('offers.index', $categoryQuery) }}"
                            @class(['offers-toolbar-chip', 'is-selected' => $activeCategory === $category->id])
                            @if($activeCategory === $category->id) aria-current="true" @endif>
                            {{ $categoryLabels[$category->id] }}
                        </a>
                    @endforeach
                </nav>
            @elseif($categories->isNotEmpty())
                <form method="GET" action="{{ route('offers.index') }}" class="flex shrink-0 items-center gap-1">
                    @if($activeDiscount)
                        <input type="hidden" name="discount" value="{{ $activeDiscount }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ $activeSort }}">
                    <label for="offers-category" class="sr-only">{{ __('offers.category') }}</label>
                    <select id="offers-category" name="category" onchange="this.form.submit()" class="offers-toolbar-select max-w-32">
                        <option value="">{{ __('offers.all_categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected($activeCategory === $category->id)>{{ $categoryLabels[$category->id] }}</option>
                        @endforeach
                    </select>
                    @if(request()->query('lang'))
                        <input type="hidden" name="lang" value="{{ request()->query('lang') }}">
                    @endif
                    <noscript><button type="submit" class="text-xs font-bold text-[#0d1b2a]">{{ __('offers.apply_filters') }}</button></noscript>
                </form>
            @endif

            <form method="GET" action="{{ route('offers.index') }}" class="flex shrink-0 items-center gap-1">
                @if($activeDiscount)
                    <input type="hidden" name="discount" value="{{ $activeDiscount }}">
                @endif
                @if($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                @if(request()->query('lang'))
                    <input type="hidden" name="lang" value="{{ request()->query('lang') }}">
                @endif
                <label for="offers-sort" class="text-[11px] font-black uppercase tracking-wide text-slate-500">{{ __('offers.sort_by_short') }}</label>
                <select id="offers-sort" name="sort" onchange="this.form.submit()" class="offers-toolbar-select max-w-40">
                    <option value="discount" @selected($activeSort === 'discount')>{{ __('offers.sort_discount') }}</option>
                    <option value="price_low" @selected($activeSort === 'price_low')>{{ __('offers.sort_price_low') }}</option>
                    <option value="price_high" @selected($activeSort === 'price_high')>{{ __('offers.sort_price_high') }}</option>
                    <option value="newest" @selected($activeSort === 'newest')>{{ __('offers.sort_newest') }}</option>
                </select>
                <noscript><button type="submit" class="text-xs font-bold text-[#0d1b2a]">{{ __('offers.apply_sort') }}</button></noscript>
            </form>

            @if($activeFilterCount > 0)
                <a href="{{ route('offers.index', $clearAllQuery) }}" class="shrink-0 text-xs font-bold text-offer underline-offset-4 transition hover:underline">
                    {{ __('offers.clear_all') }}
                </a>
            @endif
        </div>
    </section>

    @if($activeFilterCount > 0)
        <div class="mt-2 flex flex-wrap items-center gap-2" aria-label="{{ __('offers.active_filters') }}">
            <span class="text-xs font-bold text-slate-500">{{ __('offers.active_filters') }}:</span>
            @if($activeDiscount)
                @php
                    $withoutDiscountQuery = request()->except(['discount', 'page']);
                @endphp
                <a href="{{ route('offers.index', $withoutDiscountQuery) }}" class="offers-active-chip">
                    <bdi dir="ltr">{{ app()->isLocale('ar') ? '+'.$activeDiscount.'٪' : $activeDiscount.'%+' }}</bdi>
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{{ __('offers.remove_filter', ['filter' => __('offers.discount_range')]) }}</span>
                </a>
            @endif
            @if($activeCategory)
                @php
                    $withoutCategoryQuery = request()->except(['category', 'page']);
                @endphp
                <a href="{{ route('offers.index', $withoutCategoryQuery) }}" class="offers-active-chip">
                    {{ $activeCategoryLabel }}
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">{{ __('offers.remove_filter', ['filter' => __('offers.category')]) }}</span>
                </a>
            @endif
        </div>
    @endif

    <section class="mt-4" aria-label="{{ __('offers.title') }}">
        @if($products->isEmpty())
            <div class="rounded-[1.5rem] border border-[#d8b35a]/30 bg-white px-6 py-10 text-center shadow-[0_14px_38px_rgba(13,27,42,.08)] sm:py-14">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f7f1e3] text-xl text-[#9b762f]">
                    <i class="fas fa-tags" aria-hidden="true"></i>
                </span>
                <h2 class="mt-4 text-xl font-bold text-[#0d1b2a]">{{ __('offers.empty_title') }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ __('offers.empty_description') }}</p>
                <a href="{{ route('offers.index', $clearAllQuery) }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-full bg-[#0d1b2a] px-6 py-3 text-sm font-black uppercase tracking-wider text-[#f7d879] transition duration-200 hover:bg-[#13283d] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#d8b35a]/30">
                    {{ __('offers.clear_all_filters') }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    <x-product :product="$product" layout="offer" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $products->appends(request()->query())->links('pagination::tailwind') }}
            </div>
        @endif
    </section>

    <script>
        (() => {
            const toolbar = document.querySelector('[data-offers-toolbar]');
            const sentinel = document.getElementById('offers-toolbar-sentinel');
            const filters = document.querySelector('[data-offers-filter-details]');
            const summary = filters?.querySelector('summary');

            if (toolbar && sentinel) {
                const stickyOffset = Number.parseFloat(getComputedStyle(toolbar).top) || 0;
                const updateStickyState = () => {
                    toolbar.classList.toggle('is-stuck', sentinel.getBoundingClientRect().top < stickyOffset);
                };
                window.addEventListener('scroll', updateStickyState, { passive: true });
                window.addEventListener('resize', updateStickyState);
                updateStickyState();
            }

            if (!filters || !summary) {
                return;
            }

            filters.classList.add('offers-filter-sheet-enhanced');
            filters.addEventListener('toggle', () => {
                const isOpen = filters.open;
                filters.setAttribute('role', isOpen ? 'dialog' : 'group');
                filters.setAttribute('aria-modal', isOpen ? 'true' : 'false');
                summary.setAttribute('aria-label', isOpen ? @json(__('offers.close_filters')) : @json(__('offers.open_filters')));

                summary.focus();
            });

            filters.addEventListener('click', (event) => {
                if (event.target === filters) {
                    filters.open = false;
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && filters.open) {
                    event.preventDefault();
                    filters.open = false;
                    return;
                }

                if (event.key !== 'Tab' || !filters.open) {
                    return;
                }

                const focusable = [...filters.querySelectorAll('summary, a[href], button:not([disabled]), input:not([disabled]), select:not([disabled])')]
                    .filter((element) => element.getClientRects().length > 0);
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last?.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first?.focus();
                }
            });
        })();
    </script>
@endsection
