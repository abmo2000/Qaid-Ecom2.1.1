@if($includeDiscount)
    <fieldset class="offers-filter-section">
        <legend class="sr-only">{{ __('offers.discount_range') }}</legend>
        <details class="offers-filter-accordion" open>
            <summary class="offers-filter-heading" aria-expanded="true">
                <span>{{ __('offers.discount_range') }}</span>
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </summary>
            <div class="offers-filter-options offers-discount-options" role="group" aria-label="{{ __('offers.discount_range') }}">
                <label class="offers-filter-option offers-discount-option {{ $allDiscountCount === 0 ? 'is-zero' : '' }}">
                    <input class="offers-filter-checkbox" type="radio" name="discount" value="" @checked(! $activeDiscount)>
                    <span class="offers-checkbox-mark" aria-hidden="true"></span>
                    <span class="offers-option-label">{{ __('offers.all_discount') }}</span>
                    <bdi dir="ltr" class="offers-option-count">{{ $allDiscountCount }}</bdi>
                </label>
                @foreach([10, 25, 40] as $range)
                    <label class="offers-filter-option offers-discount-option {{ $discountCounts[$range] === 0 ? 'is-zero' : '' }}">
                        <input class="offers-filter-checkbox" type="radio" name="discount" value="{{ $range }}" @checked($activeDiscount === $range)>
                        <span class="offers-checkbox-mark" aria-hidden="true"></span>
                        <span class="offers-option-label"><bdi dir="ltr">{{ app()->isLocale('ar') ? '+'.$range.'٪' : $range.'%+' }}</bdi></span>
                        <bdi dir="ltr" class="offers-option-count">{{ $discountCounts[$range] }}</bdi>
                    </label>
                @endforeach
            </div>
        </details>
    </fieldset>
@endif

<fieldset class="offers-filter-section">
    <legend class="sr-only">{{ __('offers.brands') }}</legend>
    <details class="offers-filter-accordion" open>
        <summary class="offers-filter-heading" aria-expanded="true">
            <span>{{ __('offers.brands') }}</span>
            <i class="fas fa-chevron-down" aria-hidden="true"></i>
        </summary>
        <div class="offers-filter-content">
            @if($brands->count() > 8)
                <label class="sr-only" for="offers-brand-search-{{ $filterContext }}">{{ __('offers.search_brands') }}</label>
                <input id="offers-brand-search-{{ $filterContext }}" type="search" class="offers-brand-search" placeholder="{{ __('offers.search_brands') }}" data-brand-search>
            @endif
            <div class="offers-filter-options offers-brand-list" role="group" aria-label="{{ __('offers.brands') }}" data-brand-list>
                @forelse($brands as $brand)
                    @php
                        $brandWords = collect(preg_split('/\s+/', trim($brand->name)))->filter();
                        $brandInitials = $brandWords->take(2)->map(fn ($word) => mb_substr($word, 0, 1))->join('');
                    @endphp
                    <label class="offers-filter-option offers-brand-option {{ $brand->offers_count === 0 && ! in_array($brand->id, $activeBrandIds) ? 'is-zero' : '' }}"
                        data-brand-item data-brand-name="{{ mb_strtolower($brand->name) }}">
                        <input class="offers-filter-checkbox" type="checkbox" name="brands[]" value="{{ $brand->id }}"
                            @checked(in_array($brand->id, $activeBrandIds))
                            @disabled($brand->offers_count === 0 && ! in_array($brand->id, $activeBrandIds))>
                        <span class="offers-checkbox-mark" aria-hidden="true"></span>
                        <span class="offers-brand-avatar" aria-hidden="true">
                            {{ $brandInitials ?: mb_substr($brand->name, 0, 1) }}
                            @if($brand->image)
                                <img src="{{ storage_image_url($brand->image) }}" alt="" loading="lazy" onerror="this.remove()">
                            @endif
                        </span>
                        <span class="offers-option-label">{{ $brand->name }}</span>
                        <bdi dir="ltr" class="offers-option-count">{{ $brand->offers_count }}</bdi>
                    </label>
                @empty
                    <p class="py-2 text-sm text-slate-500">{{ __('offers.no_brands') }}</p>
                @endforelse
            </div>
            @if($brands->count() > 8)
                <button type="button" class="offers-brand-toggle" data-brand-toggle
                    data-show-more="{{ __('offers.show_more') }}" data-show-less="{{ __('offers.show_less') }}">
                    {{ __('offers.show_more') }}
                </button>
            @endif
        </div>
    </details>
</fieldset>

<fieldset class="offers-filter-section">
    <legend class="sr-only">{{ __('offers.category') }}</legend>
    <details class="offers-filter-accordion" open>
        <summary class="offers-filter-heading" aria-expanded="true">
            <span>{{ __('offers.category') }}</span>
            <i class="fas fa-chevron-down" aria-hidden="true"></i>
        </summary>
        <div class="offers-filter-options" role="group" aria-label="{{ __('offers.category') }}">
            @forelse($categories as $category)
                <label class="offers-filter-option {{ $category->offers_count === 0 && ! in_array($category->id, $activeCategoryIds) ? 'is-zero' : '' }}">
                    <input class="offers-filter-checkbox" type="checkbox" name="categories[]" value="{{ $category->id }}"
                        @checked(in_array($category->id, $activeCategoryIds))
                        @disabled($category->offers_count === 0 && ! in_array($category->id, $activeCategoryIds))>
                    <span class="offers-checkbox-mark" aria-hidden="true"></span>
                    <span class="offers-option-label">{{ $categoryLabels[$category->id] }}</span>
                    <bdi dir="ltr" class="offers-option-count">{{ $category->offers_count }}</bdi>
                </label>
            @empty
                <p class="py-2 text-sm text-slate-500">{{ __('offers.no_categories') }}</p>
            @endforelse
        </div>
    </details>
</fieldset>
