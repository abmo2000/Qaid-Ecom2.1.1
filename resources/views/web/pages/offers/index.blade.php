@extends('web.layouts.offers')

@section('offers-content')
    @php
        $activeFilterCount = (int) (bool) $activeDiscount + count($activeBrandIds) + count($activeCategoryIds);
        $clearAllQuery = request()->only(['lang', 'sort']);
        $countTranslation = trans_choice('offers.products_count', $totalOffers, ['count' => '__OFFER_COUNT__']);
        $countLabel = str_replace('__OFFER_COUNT__', '<bdi dir="ltr">'.$totalOffers.'</bdi>', e($countTranslation));
        $applyCountTranslation = trans_choice('offers.show_results', $totalOffers, ['count' => '__OFFER_COUNT__']);
        $applyCountLabel = str_replace('__OFFER_COUNT__', '<bdi dir="ltr">'.$totalOffers.'</bdi>', e($applyCountTranslation));
    @endphp

    <section class="relative isolate flex min-h-32 items-center justify-between gap-2 overflow-hidden rounded-[1.5rem] border border-[#d8b35a]/35 bg-white/80 px-4 py-4 shadow-[0_14px_38px_rgba(13,27,42,.08)] sm:min-h-40 sm:gap-8 sm:px-8 sm:py-6">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-offer via-[#d8b35a] to-offer"></div>
        <div class="min-w-0 flex-1">
            <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#9b762f] sm:text-[11px] sm:tracking-[0.24em]">{{ __('offers.eyebrow') }}</p>
            <h1 class="mt-1 font-sans text-2xl font-bold leading-tight text-[#0d1b2a] sm:text-4xl">{{ __('offers.title') }}</h1>
            <p class="mt-1 line-clamp-1 max-w-2xl text-xs leading-4 text-slate-600 sm:text-sm sm:leading-5">{{ __('offers.subtitle') }}</p>
        </div>
        <div class="flex shrink-0 items-center gap-2 rounded-2xl border border-offer/15 bg-white px-2.5 py-2.5 shadow-sm sm:min-w-52 sm:justify-center sm:gap-3 sm:px-4 sm:py-3">
            @if($maximumDiscount > 0)
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-offer/10 text-sm text-offer sm:h-11 sm:w-11 sm:text-base"><i class="fas fa-tag" aria-hidden="true"></i></span>
                <span class="min-w-0">
                    <span class="block text-[8px] font-black uppercase tracking-[0.08em] text-slate-500 sm:text-[10px] sm:tracking-[0.14em]">{{ __('offers.max_discount_label') }}</span>
                    <span class="mt-0.5 flex flex-wrap items-baseline gap-x-1 text-sm font-black leading-tight text-offer sm:text-xl">
                        <span>{{ __('offers.up_to_prefix') }}</span>
                        <bdi dir="ltr">{{ $maximumDiscount }}{{ app()->isLocale('ar') ? '٪' : '%' }}</bdi>
                        @if(__('offers.up_to_suffix'))<span>{{ __('offers.up_to_suffix') }}</span>@endif
                    </span>
                </span>
            @else
                <span class="text-sm font-bold text-slate-600">{{ __('offers.no_active_discount') }}</span>
            @endif
        </div>
    </section>

    <div id="offers-toolbar-sentinel" class="h-px" aria-hidden="true"></div>
    <section class="offers-toolbar mt-3 rounded-2xl border border-[#d8b35a]/25 bg-white/95 px-3 py-2.5 sm:px-4" data-offers-toolbar aria-label="{{ __('offers.filters') }}">
        <div class="offers-mobile-toolbar lg:hidden">
            <details class="offers-filter-details" data-offers-filter-details>
                <summary class="offers-filter-summary" aria-label="{{ __('offers.open_filters') }}" aria-expanded="false">
                    <i class="fas fa-sliders-h text-[#9b762f]" aria-hidden="true"></i>
                    <span>{{ __('offers.filters') }}</span>
                    @if($activeFilterCount > 0)
                        <bdi dir="ltr" class="offers-filter-badge">{{ $activeFilterCount }}</bdi>
                    @endif
                </summary>
                <button class="offers-filter-backdrop" type="button" tabindex="-1" aria-hidden="true" data-offers-sheet-backdrop></button>
                <div class="offers-filter-panel" data-offers-filter-dialog role="dialog" aria-modal="true" aria-labelledby="offers-filter-title-mobile">
                    <span class="offers-sheet-handle" aria-hidden="true"></span>
                    <header class="offers-sheet-header">
                        <h2 id="offers-filter-title-mobile">{{ __('offers.filters') }}</h2>
                        <button class="offers-sheet-close" type="button" aria-label="{{ __('offers.close_filters') }}" data-offers-sheet-close>
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </header>
                    <form method="GET" action="{{ route('offers.index') }}" class="offers-sheet-form" data-offers-sheet-form>
                        @if(request()->query('lang'))<input type="hidden" name="lang" value="{{ request()->query('lang') }}">@endif
                        <input type="hidden" name="sort" value="{{ $activeSort }}">
                        <div class="offers-sheet-body">
                            @include('web.pages.offers.partials.filters', ['includeDiscount' => true, 'filterContext' => 'mobile'])
                        </div>
                        <footer class="offers-sheet-footer">
                            <button type="submit" class="offers-filter-submit offers-sheet-apply" data-offers-apply>
                                <span data-offers-apply-label>{!! $applyCountLabel !!}</span>
                            </button>
                            <a href="{{ route('offers.index', $clearAllQuery) }}" class="offers-sheet-clear">{{ __('offers.clear_all') }}</a>
                            <p class="offers-sheet-error" data-offers-count-error role="alert" hidden>{{ __('offers.count_unavailable') }}</p>
                        </footer>
                    </form>
                </div>
            </details>

            <form method="GET" action="{{ route('offers.index') }}" class="offers-mobile-sort-form">
                @foreach($activeBrandIds as $brandId)<input type="hidden" name="brands[]" value="{{ $brandId }}">@endforeach
                @foreach($activeCategoryIds as $categoryId)<input type="hidden" name="categories[]" value="{{ $categoryId }}">@endforeach
                @if($activeDiscount)<input type="hidden" name="discount" value="{{ $activeDiscount }}">@endif
                @if(request()->query('lang'))<input type="hidden" name="lang" value="{{ request()->query('lang') }}">@endif
                <label for="offers-sort-mobile" class="sr-only">{{ __('offers.sort_by') }}</label>
                <span class="offers-mobile-sort-wrap">
                    <select id="offers-sort-mobile" name="sort" onchange="this.form.submit()" class="offers-mobile-sort-select">
                        <option value="discount" @selected($activeSort === 'discount')>{{ __('offers.sort_discount_short') }}</option>
                        <option value="price_low" @selected($activeSort === 'price_low')>{{ __('offers.sort_price_low_short') }}</option>
                        <option value="price_high" @selected($activeSort === 'price_high')>{{ __('offers.sort_price_high_short') }}</option>
                        <option value="newest" @selected($activeSort === 'newest')>{{ __('offers.sort_newest') }}</option>
                    </select>
                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </span>
                <noscript><button type="submit" class="text-xs font-bold text-[#0d1b2a]">{{ __('offers.apply_sort') }}</button></noscript>
            </form>
            <p class="offers-mobile-result-count" data-offers-result-count aria-live="polite">{!! $countLabel !!}</p>
        </div>

        <div class="hidden offers-desktop-toolbar">
            <p class="shrink-0 text-xs font-bold text-slate-600" data-offers-result-count aria-live="polite">{!! $countLabel !!}</p>
            <nav class="offers-desktop-discount-chips" aria-label="{{ __('offers.discount_range') }}">
                @php
                    $allDiscountQuery = request()->except(['discount', 'page']);
                @endphp
                <a href="{{ route('offers.index', $allDiscountQuery) }}" @class(['offers-toolbar-chip', 'is-selected' => ! $activeDiscount]) @if(! $activeDiscount) aria-current="true" @endif>
                    {{ __('offers.all_discount') }} <bdi dir="ltr">({{ $allDiscountCount }})</bdi>
                </a>
                @foreach([10, 25, 40] as $range)
                    @php
                        $discountQuery = request()->except('page');
                        $discountQuery['discount'] = $range;
                    @endphp
                    <a href="{{ route('offers.index', $discountQuery) }}" @class(['offers-toolbar-chip', 'is-selected' => $activeDiscount === $range, 'is-zero' => $discountCounts[$range] === 0]) aria-pressed="{{ $activeDiscount === $range ? 'true' : 'false' }}">
                        <bdi dir="ltr">{{ app()->isLocale('ar') ? '+'.$range.'٪' : $range.'%+' }}</bdi> <bdi dir="ltr">({{ $discountCounts[$range] }})</bdi>
                    </a>
                @endforeach
            </nav>
            @if($activeFilterCount > 0)
                <a href="{{ route('offers.index', $clearAllQuery) }}" class="shrink-0 text-xs font-bold text-offer underline-offset-4 transition hover:underline">{{ __('offers.clear_all') }}</a>
            @endif
            <form method="GET" action="{{ route('offers.index') }}" class="offers-desktop-sort-form">
                @foreach($activeBrandIds as $brandId)<input type="hidden" name="brands[]" value="{{ $brandId }}">@endforeach
                @foreach($activeCategoryIds as $categoryId)<input type="hidden" name="categories[]" value="{{ $categoryId }}">@endforeach
                @if($activeDiscount)<input type="hidden" name="discount" value="{{ $activeDiscount }}">@endif
                @if(request()->query('lang'))<input type="hidden" name="lang" value="{{ request()->query('lang') }}">@endif
                <label for="offers-sort" class="text-[11px] font-black uppercase tracking-wide text-slate-500">{{ __('offers.sort_by_short') }}</label>
                <span class="offers-desktop-sort-wrap">
                    <select id="offers-sort" name="sort" onchange="this.form.submit()" class="offers-toolbar-select max-w-40">
                        <option value="discount" @selected($activeSort === 'discount')>{{ __('offers.sort_discount') }}</option>
                        <option value="price_low" @selected($activeSort === 'price_low')>{{ __('offers.sort_price_low') }}</option>
                        <option value="price_high" @selected($activeSort === 'price_high')>{{ __('offers.sort_price_high') }}</option>
                        <option value="newest" @selected($activeSort === 'newest')>{{ __('offers.sort_newest') }}</option>
                    </select>
                    <i class="fas fa-chevron-down" aria-hidden="true"></i>
                </span>
                <noscript><button type="submit" class="text-xs font-bold text-[#0d1b2a]">{{ __('offers.apply_sort') }}</button></noscript>
            </form>
        </div>
    </section>

    @if($activeFilterCount > 0)
        <div class="mt-2 flex flex-wrap items-center gap-2" aria-label="{{ __('offers.active_filters') }}">
            <span class="text-xs font-bold text-slate-500">{{ __('offers.active_filters') }}:</span>
            @if($activeDiscount)
                @php
                    $withoutDiscountQuery = request()->except(['discount', 'page']);
                @endphp
                <a href="{{ route('offers.index', $withoutDiscountQuery) }}" class="offers-active-chip"><bdi dir="ltr">{{ app()->isLocale('ar') ? '+'.$activeDiscount.'٪' : $activeDiscount.'%+' }}</bdi><span aria-hidden="true">&times;</span></a>
            @endif
            @foreach($activeBrandIds as $brandId)
                @php
                    $brand = $selectedBrands->get($brandId);
                    $brandLabel = $brand?->name ?? __('offers.brand_fallback');
                    $remainingBrandIds = array_values(array_diff($activeBrandIds, [$brandId]));
                    $withoutBrandQuery = request()->except(['brands', 'page']);
                    if ($remainingBrandIds) $withoutBrandQuery['brands'] = $remainingBrandIds;
                @endphp
                <a href="{{ route('offers.index', $withoutBrandQuery) }}" class="offers-active-chip">{{ $brandLabel }}<span aria-hidden="true">&times;</span></a>
            @endforeach
            @foreach($activeCategoryIds as $categoryId)
                @php
                    $remainingCategoryIds = array_values(array_diff($activeCategoryIds, [$categoryId]));
                    $withoutCategoryQuery = request()->except(['categories', 'category', 'page']);
                    if ($remainingCategoryIds) $withoutCategoryQuery['categories'] = $remainingCategoryIds;
                    $categoryLabel = $categoryLabels[$categoryId] ?? __('offers.category_fallback');
                @endphp
                <a href="{{ route('offers.index', $withoutCategoryQuery) }}" class="offers-active-chip">{{ $categoryLabel }}<span aria-hidden="true">&times;</span></a>
            @endforeach
        </div>
    @endif

    <div class="offers-results-layout mt-4">
        <aside class="offers-sidebar hidden lg:block" aria-label="{{ __('offers.filters') }}">
            <form method="GET" action="{{ route('offers.index') }}" data-offer-auto-submit>
                @if($activeDiscount)<input type="hidden" name="discount" value="{{ $activeDiscount }}">@endif
                <input type="hidden" name="sort" value="{{ $activeSort }}">
                @if(request()->query('lang'))<input type="hidden" name="lang" value="{{ request()->query('lang') }}">@endif
                <div class="offers-sidebar-card">
                    @include('web.pages.offers.partials.filters', ['includeDiscount' => false, 'filterContext' => 'desktop'])
                    @if($activeFilterCount > 0)
                        <a href="{{ route('offers.index', $clearAllQuery) }}" class="offers-sidebar-clear">{{ __('offers.clear_all') }}</a>
                    @endif
                    <button type="submit" class="offers-filter-submit offers-filter-submit-desktop">{{ __('offers.apply_filters') }}</button>
                </div>
            </form>
        </aside>

        <section class="min-w-0" aria-label="{{ __('offers.title') }}">
            @if($products->isEmpty())
                <div class="rounded-[1.5rem] border border-[#d8b35a]/30 bg-white px-6 py-10 text-center shadow-[0_14px_38px_rgba(13,27,42,.08)] sm:py-14">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#f7f1e3] text-xl text-[#9b762f]"><i class="fas fa-tags" aria-hidden="true"></i></span>
                    <h2 class="mt-4 text-xl font-bold text-[#0d1b2a]">{{ __('offers.empty_title') }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ __('offers.empty_description') }}</p>
                    <a href="{{ route('offers.index', $clearAllQuery) }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-full bg-[#0d1b2a] px-6 py-3 text-sm font-black uppercase tracking-wider text-[#f7d879] transition duration-200 hover:bg-[#13283d] focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#d8b35a]/30">{{ __('offers.clear_all_filters') }}</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 2xl:grid-cols-3">
                    @foreach($products as $product)
                        <x-product :product="$product" layout="offer" />
                    @endforeach
                </div>
                <div class="mt-8">{{ $products->appends(request()->query())->links('pagination::tailwind') }}</div>
            @endif
        </section>
    </div>

    <script>
        (() => {
            const toolbar = document.querySelector('[data-offers-toolbar]');
            const sentinel = document.getElementById('offers-toolbar-sentinel');
            const filterSheet = document.querySelector('[data-offers-filter-details]');
            const summary = filterSheet?.querySelector('summary');
            const sheetDialog = filterSheet?.querySelector('[data-offers-filter-dialog]');
            const sheetForm = filterSheet?.querySelector('[data-offers-sheet-form]');
            const sheetClose = filterSheet?.querySelector('[data-offers-sheet-close]');
            const sheetBackdrop = filterSheet?.querySelector('[data-offers-sheet-backdrop]');
            const applyButton = filterSheet?.querySelector('[data-offers-apply]');
            const applyLabel = filterSheet?.querySelector('[data-offers-apply-label]');
            const countError = filterSheet?.querySelector('[data-offers-count-error]');
            const mobileSort = document.querySelector('#offers-sort-mobile');
            const desktopBreakpoint = window.matchMedia('(min-width: 1024px)');
            const autoSubmitForms = document.querySelectorAll('[data-offer-auto-submit]');
            let submitTimer;
            let countPreviewTimer;
            let countPreviewController;
            let previousBodyOverflow = '';

            const updateMobileSortAvailability = () => {
                if (mobileSort) mobileSort.disabled = desktopBreakpoint.matches;
            };
            updateMobileSortAvailability();
            desktopBreakpoint.addEventListener('change', updateMobileSortAvailability);
            window.addEventListener('resize', updateMobileSortAvailability, { passive: true });

            if (toolbar && sentinel) {
                const stickyOffset = Number.parseFloat(getComputedStyle(toolbar).top) || 0;
                const updateStickyState = () => toolbar.classList.toggle('is-stuck', sentinel.getBoundingClientRect().top < stickyOffset);
                window.addEventListener('scroll', updateStickyState, { passive: true });
                window.addEventListener('resize', updateStickyState);
                updateStickyState();
            }

            document.body.classList.add('offers-autosubmit-enabled');
            autoSubmitForms.forEach((form) => form.addEventListener('change', (event) => {
                if (event.target.matches('[data-brand-search]')) return;
                window.clearTimeout(submitTimer);
                submitTimer = window.setTimeout(() => form.requestSubmit(), 250);
            }));

            document.querySelectorAll('[data-brand-list]').forEach((list) => {
                const form = list.closest('form');
                const search = form?.querySelector('[data-brand-search]');
                const toggle = form?.querySelector('[data-brand-toggle]');
                const items = [...list.querySelectorAll('[data-brand-item]')];
                let expanded = false;
                if (items.length > 8) list.classList.add('is-collapsed');

                search?.addEventListener('input', () => {
                    const term = search.value.trim().toLocaleLowerCase();
                    items.forEach((item) => item.hidden = term !== '' && !item.dataset.brandName.includes(term));
                    list.classList.toggle('is-searching', term !== '');
                });

                toggle?.addEventListener('click', () => {
                    expanded = !expanded;
                    list.classList.toggle('is-expanded', expanded);
                    toggle.textContent = expanded ? toggle.dataset.showLess : toggle.dataset.showMore;
                    toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                });
            });

            document.querySelectorAll('.offers-filter-accordion').forEach((details) => {
                const sectionSummary = details.querySelector('summary');
                sectionSummary?.setAttribute('aria-expanded', details.open ? 'true' : 'false');
                details.addEventListener('toggle', () => sectionSummary?.setAttribute('aria-expanded', details.open ? 'true' : 'false'));
            });

            if (!filterSheet || !summary || !sheetDialog || !sheetForm) return;
            filterSheet.classList.add('offers-filter-sheet-enhanced');
            filterSheet.addEventListener('toggle', () => {
                const isOpen = filterSheet.open;
                summary.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (isOpen) {
                    previousBodyOverflow = document.body.style.overflow;
                    document.body.style.overflow = 'hidden';
                    document.body.classList.add('offers-sheet-open');
                    requestAnimationFrame(() => sheetClose?.focus());
                } else {
                    document.body.style.overflow = previousBodyOverflow;
                    document.body.classList.remove('offers-sheet-open');
                    summary.focus();
                }
            });

            const closeSheet = () => {
                filterSheet.open = false;
            };
            sheetClose?.addEventListener('click', closeSheet);
            sheetBackdrop?.addEventListener('click', closeSheet);

            const updateOfferCount = () => {
                window.clearTimeout(countPreviewTimer);
                countPreviewController?.abort();
                countPreviewTimer = window.setTimeout(async () => {
                    const controller = new AbortController();
                    countPreviewController = controller;
                    const params = new URLSearchParams();
                    new FormData(sheetForm).forEach((value, key) => {
                        if (value !== '' && key !== 'page') params.append(key, value.toString());
                    });
                    const previewUrl = new URL(sheetForm.action, window.location.href);
                    previewUrl.search = params.toString();
                    if (applyButton) applyButton.setAttribute('aria-busy', 'true');
                    if (countError) countError.hidden = true;

                    try {
                        const response = await fetch(previewUrl, {
                            headers: { Accept: 'text/html', 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin',
                            signal: controller.signal,
                        });
                        if (!response.ok) throw new Error(`Offer count preview failed (${response.status})`);
                        const html = await response.text();
                        const previewDocument = new DOMParser().parseFromString(html, 'text/html');
                        const previewCount = previewDocument.querySelector('[data-offers-result-count]')?.innerHTML;
                        const previewApplyLabel = previewDocument.querySelector('[data-offers-apply-label]')?.innerHTML;
                        if (!previewCount || !previewApplyLabel) throw new Error('Offer count preview was missing its count.');

                        document.querySelectorAll('[data-offers-result-count]').forEach((element) => {
                            element.innerHTML = previewCount;
                        });
                        if (applyLabel) applyLabel.innerHTML = previewApplyLabel;
                    } catch (error) {
                        if (error.name === 'AbortError') return;
                        console.error('Unable to update the offer count preview.', error);
                        if (countError) countError.hidden = false;
                    } finally {
                        if (applyButton) applyButton.removeAttribute('aria-busy');
                    }
                }, 180);
            };

            sheetForm.addEventListener('change', (event) => {
                if (event.target.matches('input[name="discount"], input[name="brands[]"], input[name="categories[]"]')) {
                    updateOfferCount();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (!filterSheet.open) return;
                if (event.key === 'Escape') {
                    event.preventDefault();
                    closeSheet();
                } else if (event.key === 'Tab') {
                    const focusable = [...sheetDialog.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), summary')]
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
                }
            });
        })();
    </script>
@endsection
