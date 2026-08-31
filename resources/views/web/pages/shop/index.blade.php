@extends('web.layouts.main')

@section('title')
    shop
@endsection

@section('content')
    <x-navbar></x-navbar>

    <section class="py-16 md:py-24 bg-[#0d1b2a]" x-data="productShop()">

        {{-- Agreement Modal --}}
        <div x-show="showAgreementModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#0d1b2a]/95 p-4">
            <div class="max-w-3xl w-full rounded-3xl border border-[#d8b35a]/50 bg-[#0d1b2a] p-8 text-left shadow-2xl">
                <h2 class="text-3xl font-black uppercase tracking-wide text-[#f7d879] mb-4">
                    {{ app()->getLocale() === 'ar' ? 'الشروط والأحكام' : 'Terms and Conditions' }}
                </h2>
                <p class="text-white/65 mb-4">
                    {{ app()->getLocale() === 'ar' ? 'يرجى الموافقة على شروطنا وسياسة الاستبدال والاسترجاع قبل البدء في التسوق.' : 'Please agree to our terms and refund/exchange policy before you start shopping.' }}
                </p>
                <div class="space-y-4 text-white/80 prose prose-invert max-w-none">
                    <ul class="list-disc ms-6">
                        <li>{{ app()->getLocale() === 'ar' ? 'ستستخدم الموقع فقط بعد قبول الشروط.' : 'You can use the shop only after agreeing to the terms.' }}
                        </li>
                        <li>{{ app()->getLocale() === 'ar' ? 'يمكنك قراءة النص الكامل للسياسة.' : 'You may read the full policy.' }}
                        </li>
                    </ul>
                </div>
                <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <button type="button" @click="acceptTerms()"
                        class="inline-flex items-center justify-center rounded-full bg-[#d8b35a] px-6 py-3 font-black uppercase tracking-wider text-[#0d1b2a] hover:bg-[#f7d879] transition">
                        {{ app()->getLocale() === 'ar' ? 'أوافق وأبدأ التسوق' : 'I Agree and Start Shopping' }}
                    </button>
                    <a href="{{ route('terms') }}" target="_blank"
                        class="inline-flex items-center justify-center rounded-full border border-[#d8b35a]/40 px-6 py-3 font-black uppercase tracking-wider text-white hover:border-[#d8b35a] hover:text-[#f7d879] transition">
                        {{ app()->getLocale() === 'ar' ? 'قراءة الشروط' : 'Read Terms' }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="container mx-auto px-4" :class="showAgreementModal ? 'opacity-40 pointer-events-none select-none' : ''">

            <h2 class="text-center font-black uppercase tracking-wide text-[#f7d879] text-4xl mb-2">
                {{ trans('shop.products') }}
            </h2>
            <p class="text-white/65 text-center mb-12">{{ trans('shop.discover-products') }}</p>

            <div class="flex flex-col lg:flex-row gap-8">

                {{-- Sidebar --}}
                <aside class="lg:w-72 flex-shrink-0">

                    {{-- Mobile toggle --}}
                    <button type="button" @click="showMobileFilters = !showMobileFilters"
                        class="lg:hidden w-full flex items-center justify-between border border-[#d8b35a]/35 bg-white/5 text-white py-3 px-5 rounded-xl font-semibold text-sm hover:bg-white/10 transition-all mb-3">
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#d8b35a]" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 8h10M11 12h2" />
                            </svg>
                            {{ trans('shop.filters') }}
                        </span>
                        <span class="text-white/40 text-lg leading-none" x-text="showMobileFilters ? '−' : '+'"></span>
                    </button>

                    <div class="rounded-2xl border border-[#d8b35a]/35 bg-white/5 overflow-hidden lg:sticky lg:top-4"
                        :class="showMobileFilters ? 'block' : 'hidden lg:block'">

                        {{-- Sidebar Header --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-[#d8b35a]/25">
                            <h3 class="text-[#f7d879] font-black text-base tracking-wide">{{ trans('shop.filters') }}</h3>
                            <button @click="resetFilters()"
                                class="text-xs text-[#d8b35a] hover:text-[#f7d879] font-semibold transition-colors">
                                {{ trans('shop.reset_filters') }}
                            </button>
                        </div>

                        {{-- Category Filter --}}
                        <div class="px-5 py-5 border-b border-[#d8b35a]/25">
                            <h4 class="text-white/40 text-xs font-semibold uppercase tracking-widest mb-4">
                                {{ trans('shop.categories') }}
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="category in categories" :key="category.id">
                                    <button type="button"
                                        :class="selectedCategories.includes(category.id) ?
                                            'bg-[#d8b35a] border-[#d8b35a] text-[#0d1b2a] font-semibold' :
                                            'bg-white/5 border-[#d8b35a]/25 text-white/60 hover:border-[#d8b35a]/60 hover:text-white'"
                                        @click="
                                            selectedCategories.includes(category.id)
                                                ? selectedCategories = selectedCategories.filter(c => c !== category.id)
                                                : selectedCategories.push(category.id);
                                            applyFilters()
                                        "
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm border transition-all duration-150">
                                        <span x-text="category.title"></span>
                                        <span x-show="category.products_count !== undefined"
                                            x-text="category.products_count"
                                            :class="selectedCategories.includes(category.id) ?
                                                'bg-[#0d1b2a]/20 text-[#0d1b2a]' : 'bg-white/10 text-white/40'"
                                            class="text-xs px-1.5 py-0.5 rounded-full leading-none">
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Brand Filter --}}
                        <div class="px-5 py-5 border-b border-[#d8b35a]/25">
                            <h4 class="text-white/40 text-xs font-semibold uppercase tracking-widest mb-4">
                                {{ trans('shop.brand') }}
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="brand in brands" :key="brand">
                                    <button type="button"
                                        :class="selectedBrands.includes(brand) ?
                                            'bg-[#d8b35a] border-[#d8b35a] text-[#0d1b2a] font-semibold' :
                                            'bg-white/5 border-[#d8b35a]/25 text-white/60 hover:border-[#d8b35a]/60 hover:text-white'"
                                        @click="
                                            selectedBrands.includes(brand)
                                                ? selectedBrands = selectedBrands.filter(b => b !== brand)
                                                : selectedBrands.push(brand);
                                            applyFilters()
                                        "
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm border transition-all duration-150">
                                        <span x-text="brand"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Active Filters Summary --}}
                        <template x-if="selectedCategories.length > 0 || selectedBrands.length > 0">
                            <div class="px-5 pb-5 pt-4 border-t border-[#d8b35a]/25 flex flex-wrap gap-2 items-center">
                                <span class="text-xs text-white/30">Active:</span>
                                <template x-for="id in selectedCategories" :key="'c-' + id">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#d8b35a]/10 border border-[#d8b35a]/30 text-[#d8b35a]">
                                        <span x-text="categories.find(c => c.id === id)?.title"></span>
                                        <button type="button"
                                            @click="selectedCategories = selectedCategories.filter(c => c !== id); applyFilters()"
                                            class="text-[#d8b35a] hover:text-[#f7d879] leading-none ml-0.5">×</button>
                                    </span>
                                </template>
                                <template x-for="brand in selectedBrands" :key="'b-' + brand">
                                    <span
                                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full bg-[#d8b35a]/10 border border-[#d8b35a]/30 text-[#d8b35a]">
                                        <span x-text="brand"></span>
                                        <button type="button"
                                            @click="selectedBrands = selectedBrands.filter(b => b !== brand); applyFilters()"
                                            class="text-[#d8b35a] hover:text-[#f7d879] leading-none ml-0.5">×</button>
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>
                </aside>

                {{-- Products Grid --}}
                <div class="flex-1 min-w-0">

                    {{-- Top bar: tabs + search --}}
                    <div class="mb-2">
                        <div
                            class="flex flex-wrap sm:flex-nowrap items-center gap-2 border border-[#d8b35a]/35 bg-white/5 rounded-xl p-1.5">

                            {{-- Pill Tabs --}}
                            <div class="inline-flex p-0.5 gap-1 shrink-0 w-full sm:w-auto">
                                <button @click="productType = 'products'; applyFilters()"
                                    :class="productType === 'products'
                                        ?
                                        'bg-[#d8b35a] text-[#0d1b2a]' :
                                        'text-white/50 hover:text-white hover:bg-white/10'"
                                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-black text-sm transition-all duration-200 whitespace-nowrap uppercase tracking-wide">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="7" height="7" rx="1" />
                                        <rect x="14" y="3" width="7" height="7" rx="1" />
                                        <rect x="3" y="14" width="7" height="7" rx="1" />
                                        <rect x="14" y="14" width="7" height="7" rx="1" />
                                    </svg>
                                    {{ trans('shop.products') }}
                                </button>
                                <button @click="productType = 'packages'; applyFilters()"
                                    :class="productType === 'packages'
                                        ?
                                        'bg-[#d8b35a] text-[#0d1b2a]' :
                                        'text-white/50 hover:text-white hover:bg-white/10'"
                                    class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-black text-sm transition-all duration-200 whitespace-nowrap uppercase tracking-wide">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M20 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2" />
                                        <line x1="12" y1="12" x2="12" y2="12.01"
                                            stroke-width="2.5" />
                                    </svg>
                                    {{ trans('shop.packages') }}
                                </button>
                            </div>

                            {{-- Divider --}}
                            <div class="hidden sm:block w-px h-6 bg-[#d8b35a]/25 shrink-0"></div>

                            {{-- Search --}}
                            <div class="relative flex-1 min-w-full sm:min-w-0">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/30 pointer-events-none"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="11" cy="11" r="8" />
                                    <path stroke-linecap="round" d="m21 21-4.35-4.35" />
                                </svg>
                                <input id="shop-search" type="search" x-model="searchQuery"
                                    @keyup.debounce.500ms="applyFilters()"
                                    placeholder="{{ trans('shop.search_products') }}..."
                                    class="w-full bg-transparent text-white placeholder-white/30 text-sm pl-9 pr-4 py-2 focus:outline-none" />
                            </div>
                        </div>

                        {{-- Count --}}
                        <p class="text-white/30 text-xs px-1 mt-2 mb-5 text-end">
                            {{ trans('shop.showing') }}
                            <span class="text-white/60" x-text="((currentPage - 1) * perPage) + 1"></span>–<span
                                class="text-white/60" x-text="Math.min(currentPage * perPage, totalProducts)"></span>
                            {{ trans('shop.of') }}
                            <span class="text-[#d8b35a] font-medium" x-text="totalProducts"></span>
                            <span
                                x-text="productType === 'packages' ? ' {{ trans('shop.packages') }}' : ' {{ trans('shop.products') }}'"></span>
                        </p>
                    </div>

                    {{-- Loading --}}
                    <div x-show="loading" class="text-center py-20">
                        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-[#d8b35a]"></div>
                        <p class="text-white/50 mt-4">Loading...</p>
                    </div>

                    {{-- Products Grid --}}
                    <div x-show="!loading && products.length > 0"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <template x-for="(product, index) in products" :key="product.id || index">
                            <div x-html="renderProduct(product)"></div>
                        </template>
                    </div>

                    {{-- Empty state --}}
                    <div x-show="!loading && products.length === 0" class="text-center py-20">
                        <p class="text-white/50 text-xl">No <span
                                x-text="productType === 'packages' ? 'packages' : 'products'"></span> found matching your
                            filters.</p>
                    </div>

                    {{-- Pagination --}}
                    <div x-show="!loading && totalPages > 1" class="mt-12 w-full min-w-0">

                        {{-- Mobile: simple Prev / counter / Next --}}
                        <nav class="flex sm:hidden items-center justify-between gap-3 w-full">
                            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/10'"
                                class="px-4 py-2 border border-[#d8b35a]/30 text-white/60 rounded-lg transition-colors">
                                Previous
                            </button>

                            <span class="text-white/50 text-sm shrink-0">
                                <span class="text-[#d8b35a] font-semibold" x-text="currentPage"></span>
                                /
                                <span x-text="totalPages"></span>
                            </span>

                            <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/10'"
                                class="px-4 py-2 border border-[#d8b35a]/30 text-white rounded-lg transition-colors">
                                Next
                            </button>
                        </nav>

                        {{-- Desktop / tablet: full dotted pagination --}}
                        <nav class="hidden sm:flex items-center justify-center gap-2 w-full min-w-0">
                            <button @click="changePage(currentPage - 1)" :disabled="currentPage === 1"
                                :class="currentPage === 1 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/10'"
                                class="px-4 py-2 border border-[#d8b35a]/30 text-white/60 rounded-lg transition-colors shrink-0">
                                Previous
                            </button>

                            <template x-for="(page, idx) in paginationItems()" :key="idx">
                                <button @click="page !== '...' && changePage(page)" :disabled="page === '...'"
                                    :class="page === '...' ?
                                        'border-transparent text-white/30 cursor-default' :
                                        (page === currentPage ?
                                            'bg-[#d8b35a] text-[#0d1b2a] font-black border-[#d8b35a]' :
                                            'bg-white/5 border-[#d8b35a]/25 text-white/60 hover:bg-white/10')"
                                    class="px-4 py-2 border rounded-lg transition-colors shrink-0" x-text="page">
                                </button>
                            </template>

                            <button @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white/10'"
                                class="px-4 py-2 border border-[#d8b35a]/30 text-white rounded-lg transition-colors shrink-0">
                                Next
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
    <script>
        function productShop() {
            return {
                products: [],
                categories: @json($categories ?? []),
                brands: @json($brands ?? []),
                selectedCategories: [],
                selectedBrands: [],
                searchQuery: '',
                productType: 'products',
                currentPage: 1,
                perPage: 9,
                showMobileFilters: false,
                showAgreementModal: false,
                totalProducts: 0,
                totalPages: 0,
                loading: true,

                init() {
                    this.termsAccepted = localStorage.getItem('qaid_terms_accepted') === 'true';
                    this.showAgreementModal = !this.termsAccepted;

                    if (!this.showAgreementModal) {
                        this.applyInitialFiltersFromQuery();
                        this.fetchProducts();
                    }
                },

                acceptTerms() {
                    localStorage.setItem('qaid_terms_accepted', 'true');
                    this.showAgreementModal = false;
                    this.applyInitialFiltersFromQuery();
                    this.fetchProducts();
                },

                normalizeFilterLabel(value) {
                    return (value ?? '')
                        .toString()
                        .trim()
                        .toLowerCase()
                        .replace(/[_-]+/g, ' ')
                        .replace(/\s+/g, ' ');
                },

                applyInitialFiltersFromQuery() {
                    const params = new URLSearchParams(window.location.search);

                    if (params.has('search')) {
                        this.searchQuery = params.get('search') ?? '';
                    }

                    const page = Number.parseInt(params.get('page'), 10);
                    if (!Number.isNaN(page) && page > 0) {
                        this.currentPage = page;
                    }

                    const perPage = Number.parseInt(params.get('per_page'), 10);
                    if (!Number.isNaN(perPage) && perPage > 0) {
                        this.perPage = perPage;
                    }

                    const categoryId = Number.parseInt(params.get('category_id'), 10);
                    if (!Number.isNaN(categoryId)) {
                        this.selectedCategories = [categoryId];
                        return;
                    }

                    const categoryIds = params
                        .getAll('categories[]')
                        .map((value) => Number.parseInt(value, 10))
                        .filter((value) => !Number.isNaN(value));

                    if (categoryIds.length > 0) {
                        this.selectedCategories = categoryIds;
                        return;
                    }

                    const categoryLabel = params.get('category');
                    if (!categoryLabel) {
                        return;
                    }

                    const normalizedLabel = this.normalizeFilterLabel(categoryLabel);

                    const matchedCategory = this.categories.find((category) => {
                        const translationTitles = Array.isArray(category.translations) ?
                            category.translations.map((translation) => translation?.title) : [];

                        const possibleTitles = [category.title, ...translationTitles]
                            .filter(Boolean)
                            .map((title) => this.normalizeFilterLabel(title));

                        return possibleTitles.includes(normalizedLabel);
                    });

                    if (matchedCategory) {
                        this.selectedCategories = [matchedCategory.id];
                    }
                },

                async fetchProducts() {
                    this.loading = true;

                    try {
                        const params = new URLSearchParams({
                            page: this.currentPage,
                            per_page: this.perPage,
                        });

                        if (this.selectedCategories.length > 0) {
                            this.selectedCategories.forEach(cat => {
                                params.append('categories[]', cat);
                            });
                        }

                        if (this.selectedBrands.length > 0) {
                            this.selectedBrands.forEach(brand => {
                                params.append('brands[]', brand);
                            });
                        }

                        if (this.searchQuery.trim() !== '') {
                            params.set('search', this.searchQuery.trim());
                        }

                        this.updateUrl();

                        let url = this.productType === 'products' ?
                            `/api/products/false/?${params.toString()}` :
                            `/api/packages?${params.toString()}`;

                        const response = await fetch(url);
                        const data = await response.json();

                        this.products = data.data ?? [];

                        this.totalProducts = data.total || 0;
                        this.totalPages = Math.ceil(this.totalProducts / this.perPage);

                    } catch (error) {
                        console.error('Error fetching:', error);
                        this.products = [];
                    } finally {
                        this.loading = false;
                    }
                },

                applyFilters() {
                    this.currentPage = 1;
                    this.fetchProducts();
                },

                resetFilters() {
                    this.selectedCategories = [];
                    this.selectedBrands = [];
                    this.searchQuery = '';
                    this.currentPage = 1;
                    this.fetchProducts();
                },

                renderProduct(product) {
                    if (product.html) {
                        return product.html;
                    }
                },

                changePage(page) {
                    if (page >= 1 && page <= this.totalPages) {
                        this.currentPage = page;
                        this.fetchProducts();
                    }
                },
                paginationItems() {
                    const total = this.totalPages;
                    const current = this.currentPage;
                    const delta = 1; // pages shown around current
                    const range = [];

                    for (let i = 1; i <= total; i++) {
                        if (
                            i === 1 ||
                            i === total ||
                            (i >= current - delta && i <= current + delta)
                        ) {
                            range.push(i);
                        }
                    }

                    // insert '...' where there are gaps
                    const withDots = [];
                    let prev = null;
                    for (const page of range) {
                        if (prev !== null && page - prev > 1) {
                            withDots.push('...');
                        }
                        withDots.push(page);
                        prev = page;
                    }

                    return withDots;
                },

                updateUrl() {
                    const params = new URLSearchParams();

                    if (this.currentPage > 1) {
                        params.set('page', this.currentPage);
                    }

                    if (this.perPage !== 9) {
                        params.set('per_page', this.perPage);
                    }

                    if (this.selectedCategories.length > 0) {
                        this.selectedCategories.forEach(cat => params.append('categories[]', cat));
                    }

                    if (this.selectedBrands.length > 0) {
                        this.selectedBrands.forEach(brand => params.append('brands[]', brand));
                    }

                    if (this.searchQuery.trim() !== '') {
                        params.set('search', this.searchQuery.trim());
                    }

                    const queryString = params.toString();
                    window.history.replaceState({}, '', queryString ? `${window.location.pathname}?${queryString}` : window
                        .location.pathname);
                },

            }
        }
    </script>
@endpush