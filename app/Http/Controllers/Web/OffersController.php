<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\OfferService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OffersController extends Controller
{
    public function index(Request $request, OfferService $offerService): View
    {
        $discount = $request->query('discount');
        $discount = in_array((string) $discount, ['10', '25', '40'], true) ? (int) $discount : null;

        $categoryIds = $this->queryIds($request->query('categories', $request->query('category', [])));
        $brandIds = $this->queryIds($request->query('brands', []));

        $sort = in_array($request->query('sort'), ['discount', 'price_low', 'price_high', 'newest'], true)
            ? $request->query('sort')
            : 'discount';

        $selectedBrands = Brand::query()
            ->whereIn('id', $brandIds)
            ->get(['id', 'name', 'image'])
            ->keyBy('id');
        $brandNames = $selectedBrands->pluck('name')->all();

        $filteredOffers = function (bool $includeDiscount = true, bool $includeBrands = true, bool $includeCategories = true) use (
            $discount,
            $brandIds,
            $categoryIds,
            $brandNames,
        ): Builder {
            return Product::query()
                ->onSale()
                ->when($includeDiscount && $discount, function (Builder $query) use ($discount) {
                    $query->whereHas('sale', function (Builder $saleQuery) use ($discount) {
                        $saleQuery->whereRaw(
                            'ROUND((products.price - product_sales.sale_price) * 100.0 / products.price) >= ?',
                            [$discount],
                        );
                    });
                })
                ->when($includeBrands && $brandIds, fn (Builder $query) => $query->whereIn('products.brand', $brandNames))
                ->when($includeCategories && $categoryIds, fn (Builder $query) => $query->whereIn('products.category_id', $categoryIds));
        };

        $productsQuery = $filteredOffers()
            ->with(['translations', 'sale', 'category.translations']);

        match ($sort) {
            'price_low' => $productsQuery
                ->withAggregate('sale', 'sale_price')
                ->orderBy('sale_sale_price')
                ->orderBy('products.id'),
            'price_high' => $productsQuery
                ->withAggregate('sale', 'sale_price')
                ->orderByDesc('sale_sale_price')
                ->orderBy('products.id'),
            'newest' => $productsQuery
                ->orderByDesc('products.created_at')
                ->orderByDesc('products.id'),
            default => $productsQuery
                ->join('product_sales', 'product_sales.product_id', '=', 'products.id')
                ->select('products.*')
                ->orderByRaw('(products.price - product_sales.sale_price) * 1.0 / products.price DESC')
                ->orderBy('products.id'),
        };

        $products = $productsQuery
            ->paginate(12)
            ->withQueryString();

        $eligibleBrandNames = Product::query()
            ->onSale()
            ->whereNotNull('products.brand')
            ->where('products.brand', '<>', '')
            ->distinct()
            ->pluck('products.brand');
        $brandCounts = $filteredOffers(false, false)
            ->whereNotNull('products.brand')
            ->where('products.brand', '<>', '')
            ->selectRaw('products.brand, COUNT(*) as offers_count')
            ->groupBy('products.brand')
            ->pluck('offers_count', 'products.brand');
        $brands = Brand::query()
            ->whereIn('name', $eligibleBrandNames)
            ->get(['id', 'name', 'image'])
            ->map(function (Brand $brand) use ($brandCounts) {
                $brand->offers_count = (int) ($brandCounts[$brand->name] ?? 0);

                return $brand;
            })
            ->sort(function (Brand $left, Brand $right) {
                return ($right->offers_count <=> $left->offers_count)
                    ?: strcasecmp($left->name, $right->name);
            })
            ->values();

        $categoryCounts = $filteredOffers(false, true, false)
            ->selectRaw('products.category_id, COUNT(*) as offers_count')
            ->groupBy('products.category_id')
            ->pluck('offers_count', 'products.category_id');
        $categories = Category::query()
            ->select(['id'])
            ->with(['translations:id,category_id,locale,title'])
            ->whereHas('products', fn (Builder $query) => $query->onSale())
            ->get()
            ->map(function (Category $category) use ($categoryCounts) {
                $category->offers_count = (int) ($categoryCounts[$category->id] ?? 0);

                return $category;
            })
            ->sortByDesc('offers_count')
            ->values();

        $categoryLabels = $categories->mapWithKeys(function (Category $category) {
            $translatedTitle = $category->translate(app()->getLocale())?->title;
            $englishTitle = app()->isLocale('ar') ? null : $category->translate('en')?->title;

            return [$category->id => $translatedTitle ?: $englishTitle ?: __('offers.category_fallback')];
        });

        $totalOffers = $filteredOffers()->count();
        $discountAggregates = $filteredOffers(false)
            ->join('product_sales', 'product_sales.product_id', '=', 'products.id')
            ->selectRaw('COUNT(*) as all_count')
            ->selectRaw('SUM(CASE WHEN ROUND((products.price - product_sales.sale_price) * 100.0 / products.price) >= 10 THEN 1 ELSE 0 END) as discount_10_count')
            ->selectRaw('SUM(CASE WHEN ROUND((products.price - product_sales.sale_price) * 100.0 / products.price) >= 25 THEN 1 ELSE 0 END) as discount_25_count')
            ->selectRaw('SUM(CASE WHEN ROUND((products.price - product_sales.sale_price) * 100.0 / products.price) >= 40 THEN 1 ELSE 0 END) as discount_40_count')
            ->first();
        $allDiscountCount = (int) $discountAggregates->all_count;
        $discountCounts = collect([
            10 => (int) $discountAggregates->discount_10_count,
            25 => (int) $discountAggregates->discount_25_count,
            40 => (int) $discountAggregates->discount_40_count,
        ]);

        $seoData = getPageSeo('offers', [
            'meta_title' => __('offers.seo_title'),
            'meta_description' => __('offers.seo_description'),
            'meta_keywords' => __('offers.seo_keywords'),
        ]);
        $seoData['canonical'] = route('offers.index');

        return view('web.pages.offers.index', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'categoryLabels' => $categoryLabels,
            'selectedBrands' => $selectedBrands,
            'activeBrandIds' => $brandIds,
            'activeCategoryIds' => $categoryIds,
            'activeDiscount' => $discount,
            'activeSort' => $sort,
            'totalOffers' => $totalOffers,
            'allDiscountCount' => $allDiscountCount,
            'discountCounts' => $discountCounts,
            'maximumDiscount' => $offerService->maximumDiscountPercentage(),
            'seoData' => $seoData,
        ]);
    }

    /**
     * @return array<int>
     */
    private function queryIds(mixed $value): array
    {
        $values = is_array($value) ? $value : [$value];

        return collect($values)
            ->map(fn ($id) => filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: null)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
