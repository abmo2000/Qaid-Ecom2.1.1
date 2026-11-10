<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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

        $categoryId = filter_var($request->query('category'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]) ?: null;

        $sort = in_array($request->query('sort'), ['discount', 'price_low', 'price_high', 'newest'], true)
            ? $request->query('sort')
            : 'discount';

        $productsQuery = Product::query()
            ->onSale()
            ->with(['translations', 'sale', 'category.translations'])
            ->when($discount, function (Builder $query) use ($discount) {
                $query->whereHas('sale', function (Builder $saleQuery) use ($discount) {
                    $saleQuery->whereRaw(
                        'ROUND((products.price - product_sales.sale_price) * 100.0 / products.price) >= ?',
                        [$discount],
                    );
                });
            })
            ->when($categoryId, fn (Builder $query) => $query->where('products.category_id', $categoryId));

        match ($sort) {
            'price_low' => $productsQuery->withAggregate('sale', 'sale_price')->orderBy('sale_sale_price'),
            'price_high' => $productsQuery->withAggregate('sale', 'sale_price')->orderByDesc('sale_sale_price'),
            'newest' => $productsQuery->orderByDesc('products.created_at'),
            default => $productsQuery
                ->join('product_sales', 'product_sales.product_id', '=', 'products.id')
                ->select('products.*')
                ->orderByRaw('(products.price - product_sales.sale_price) * 1.0 / products.price DESC'),
        };

        $products = $productsQuery
            ->orderBy('products.id')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->select(['id'])
            ->with(['translations:id,category_id,locale,title'])
            ->whereHas('products', fn (Builder $query) => $query->onSale())
            ->orderBy('id')
            ->get();

        $categoryLabels = $categories->mapWithKeys(function (Category $category) {
            $translatedTitle = $category->translate(app()->getLocale())?->title;
            $englishTitle = app()->isLocale('ar') ? null : $category->translate('en')?->title;

            return [$category->id => $translatedTitle ?: $englishTitle ?: __('offers.category_fallback')];
        });

        $countQuery = Product::query()
            ->onSale()
            ->when($categoryId, fn (Builder $query) => $query->where('products.category_id', $categoryId));
        $totalOffers = (clone $countQuery)->count();
        $discountCounts = collect([10, 25, 40])->mapWithKeys(function (int $range) use ($countQuery) {
            $count = (clone $countQuery)->whereHas('sale', function (Builder $saleQuery) use ($range) {
                $saleQuery->whereRaw(
                    'ROUND((products.price - product_sales.sale_price) * 100.0 / products.price) >= ?',
                    [$range],
                );
            })->count();

            return [$range => $count];
        });

        $seoData = getPageSeo('offers', [
            'meta_title' => __('offers.seo_title'),
            'meta_description' => __('offers.seo_description'),
            'meta_keywords' => __('offers.seo_keywords'),
        ]);
        $seoData['canonical'] = route('offers.index');

        return view('web.pages.offers.index', [
            'products' => $products,
            'categories' => $categories,
            'categoryLabels' => $categoryLabels,
            'activeDiscount' => $discount,
            'activeCategory' => $categoryId,
            'activeSort' => $sort,
            'totalOffers' => $totalOffers,
            'discountCounts' => $discountCounts,
            'maximumDiscount' => $offerService->maximumDiscountPercentage(),
            'seoData' => $seoData,
        ]);
    }
}
