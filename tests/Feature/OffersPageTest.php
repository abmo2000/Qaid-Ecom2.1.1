<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductSale;
use App\Models\Brand;
use App\Models\Category;
use App\Services\OfferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

class OffersPageTest extends TestCase
{
    use RefreshDatabase;

    private ?int $categoryId = null;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget(Product::OFFERS_MAX_DISCOUNT_CACHE_KEY);
    }

    public function test_offers_page_lists_only_published_in_stock_products_with_a_real_discount(): void
    {
        $offer = $this->createProduct('Valid offer', 1000, 750);
        $unpublished = $this->createProduct('Unpublished', 1000, 500, ['is_published' => false]);
        $outOfStockFlag = $this->createProduct('Stock flag false', 1000, 500, ['in_stock' => false]);
        $zeroStock = $this->createProduct('No stock units', 1000, 500, ['stock' => 0]);
        $samePrice = $this->createProduct('No discount', 1000, 1000);
        $higherPrice = $this->createProduct('Not a discount', 1000, 1200);
        $zeroSale = $this->createProduct('Zero sale', 1000, 0);

        $this->get(route('offers.index'))
            ->assertOk()
            ->assertSee($offer->name)
            ->assertSee('-25%')
            ->assertSee('You save 250 EGP')
            ->assertSee('<link rel="canonical" href="'.route('offers.index').'">', false)
            ->assertDontSee('offers.title')
            ->assertSee('data-offers-filter-details', false)
            ->assertDontSee($unpublished->name)
            ->assertDontSee($outOfStockFlag->name)
            ->assertDontSee($zeroStock->name)
            ->assertDontSee($samePrice->name)
            ->assertDontSee($higherPrice->name)
            ->assertDontSee($zeroSale->name);
    }

    public function test_discount_range_sort_category_filter_and_pagination_preserve_query_values(): void
    {
        $tenPercent = $this->createProduct('Ten percent', 1000, 900);
        $twentyFivePercent = $this->createProduct('Thirty percent', 1000, 700);
        $fiftyPercent = $this->createProduct('Fifty percent', 1000, 500);

        $this->get(route('offers.index', ['discount' => 40]))
            ->assertOk()
            ->assertSee($fiftyPercent->name)
            ->assertDontSee($tenPercent->name)
            ->assertDontSee($twentyFivePercent->name);

        $this->get(route('offers.index', ['discount' => 10]))
            ->assertOk()
            ->assertSee($tenPercent->name)
            ->assertSee($twentyFivePercent->name)
            ->assertSee($fiftyPercent->name);

        $this->get(route('offers.index', ['discount' => 25, 'sort' => 'price_low']))
            ->assertOk()
            ->assertSeeInOrder([$fiftyPercent->name, $twentyFivePercent->name])
            ->assertSee($twentyFivePercent->name)
            ->assertSee($fiftyPercent->name)
            ->assertDontSee($tenPercent->name);

        $this->get(route('offers.index', ['discount' => 25, 'sort' => 'price_high']))
            ->assertOk()
            ->assertSeeInOrder([$twentyFivePercent->name, $fiftyPercent->name]);

        $discountSorted = $this->get(route('offers.index', ['sort' => 'discount']));
        $discountSorted
            ->assertOk()
            ->assertSeeInOrder([$fiftyPercent->name, $twentyFivePercent->name, $tenPercent->name]);

        $recent = $this->createProduct('Newest offer', 1000, 800);
        $this->get(route('offers.index', ['sort' => 'newest']))
            ->assertOk()
            ->assertSee($recent->name);

        $otherCategory = Category::create();
        $otherCategory->translateOrNew('en')->fill(['locale' => 'en', 'title' => 'Other Category'])->save();
        $otherCategory->translateOrNew('ar')->fill(['locale' => 'ar', 'title' => 'فئة أخرى'])->save();
        $otherCategoryOffer = $this->createProduct('Other category offer', 1000, 600, [
            'category_id' => $otherCategory->id,
        ]);

        $this->get(route('offers.index', ['category' => $otherCategory->id]))
            ->assertOk()
            ->assertSee($otherCategoryOffer->name)
            ->assertDontSee($tenPercent->name)
            ->assertSee('category='.$otherCategory->id);

        $combinedFilters = $this->get(route('offers.index', [
            'discount' => 25,
            'category' => $otherCategory->id,
            'sort' => 'newest',
        ]));
        $combinedFilters
            ->assertOk()
            ->assertSee($otherCategoryOffer->name)
            ->assertSee('Active filters')
            ->assertSee('discount=25')
            ->assertSee('category='.$otherCategory->id)
            ->assertSee('href="'.e(route('offers.index', [
                'category' => $otherCategory->id,
                'sort' => 'newest',
            ])).'"', false)
            ->assertSee('href="'.e(route('offers.index', [
                'discount' => 25,
                'sort' => 'newest',
            ])).'"', false)
            ->assertSee('href="'.route('offers.index').'"', false);

        for ($index = 0; $index < 10; $index++) {
            $this->createProduct('Paginated offer '.$index, 1000, 800);
        }

        $this->get(route('offers.index', ['discount' => 10, 'sort' => 'newest']))
            ->assertOk()
            ->assertSee('page=2')
            ->assertSee('discount=10')
            ->assertSee('sort=newest');
    }

    public function test_offer_page_supports_arabic_and_renders_the_best_offers_navigation_badge(): void
    {
        $this->createProduct('Arabic offer', 1000, 600);

        $this->withSession(['locale' => 'ar'])
            ->get(route('offers.index'))
            ->assertOk()
            ->assertSee('lang="ar" dir="rtl"', false)
            ->assertSee('أفضل العروض')
            ->assertSee('خصم يصل إلى')
            ->assertSee('<bdi dir="ltr">40٪</bdi>', false)
            ->assertSee('عرض واحد')
            ->assertDontSee('offers.title');
    }

    public function test_arabic_offer_counts_and_missing_category_translation_are_localized(): void
    {
        $product = $this->createProduct('Gaming Mice offer', 1000, 750);
        $product->category->translateOrNew('en')->fill([
            'locale' => 'en',
            'title' => 'Gaming Mice Category',
        ])->save();
        $product->category->translations()->where('locale', 'ar')->delete();

        $this->withSession(['locale' => 'ar'])
            ->get(route('offers.index'))
            ->assertOk()
            ->assertSee('فئة')
            ->assertDontSee('Gaming Mice Category')
            ->assertSee('عرض واحد');

        $this->createProduct('Second offer', 1000, 750);

        $this->withSession(['locale' => 'ar'])
            ->get(route('offers.index'))
            ->assertSee('عرضان');

        $this->createProduct('Third offer', 1000, 750);

        $this->withSession(['locale' => 'ar'])
            ->get(route('offers.index'))
            ->assertSee('عروض')
            ->assertSee('<bdi dir="ltr">3</bdi>', false);
    }

    public function test_brand_and_category_filters_use_or_within_each_group_and_and_between_groups(): void
    {
        $categoryOne = $this->createCategory('Gaming', 'ألعاب');
        $categoryTwo = $this->createCategory('Accessories', 'ملحقات');
        $brandAlpha = Brand::create(['name' => 'Brand Alpha']);
        $brandBeta = Brand::create(['name' => 'Brand Beta']);
        $brandGamma = Brand::create(['name' => 'Brand Gamma']);
        $brandDelta = Brand::create(['name' => 'Brand Delta']);

        $alphaCategoryOne = $this->createProduct('Alpha gaming offer', 1000, 700, [
            'brand' => $brandAlpha->name,
            'category_id' => $categoryOne->id,
        ]);
        $betaCategoryOne = $this->createProduct('Beta gaming offer', 1000, 600, [
            'brand' => $brandBeta->name,
            'category_id' => $categoryOne->id,
        ]);
        $gammaCategoryOne = $this->createProduct('Gamma gaming offer', 1000, 500, [
            'brand' => $brandGamma->name,
            'category_id' => $categoryOne->id,
        ]);
        $this->createProduct('Delta accessories offer', 1000, 500, [
            'brand' => $brandDelta->name,
            'category_id' => $categoryTwo->id,
        ]);
        $alphaCategoryTwo = $this->createProduct('Alpha accessory offer', 1000, 650, [
            'brand' => $brandAlpha->name,
            'category_id' => $categoryTwo->id,
        ]);

        $response = $this->get(route('offers.index', [
            'brands' => [$brandAlpha->id, $brandBeta->id],
            'categories' => [$categoryOne->id],
            'discount' => 25,
        ]));

        $response
            ->assertOk()
            ->assertSee($alphaCategoryOne->name)
            ->assertSee($betaCategoryOne->name)
            ->assertDontSee($gammaCategoryOne->name)
            ->assertDontSee($alphaCategoryTwo->name)
            ->assertSee('name="brands[]" value="'.$brandAlpha->id.'"', false)
            ->assertSee('name="brands[]" value="'.$brandBeta->id.'"', false)
            ->assertSee('name="categories[]" value="'.$categoryOne->id.'"', false)
            ->assertSee('checked', false)
            ->assertSee('value="'.$brandDelta->id.'"', false)
            ->assertSee('data-brand-name="brand delta"', false)
            ->assertSee('disabled', false)
            ->assertSee('Brand Alpha')
            ->assertSee('Brand Beta')
            ->assertSee('aria-label="Brands"', false)
            ->assertSee('Active filters')
            ->assertSee('href="'.e(route('offers.index', [
                'categories' => [$categoryOne->id],
                'discount' => 25,
                'brands' => [$brandBeta->id],
            ])).'"', false);
    }

    public function test_brand_filter_search_and_show_more_controls_render_for_long_lists(): void
    {
        for ($index = 1; $index <= 9; $index++) {
            $brand = Brand::create(['name' => 'Brand '.$index]);
            $this->createProduct('Brand offer '.$index, 1000, 700, ['brand' => $brand->name]);
        }

        $this->get(route('offers.index'))
            ->assertOk()
            ->assertSee('data-brand-search', false)
            ->assertSee('data-brand-toggle', false)
            ->assertSee(__('offers.show_more'))
            ->assertSee(__('offers.show_less'));
    }

    public function test_offer_page_shows_empty_state_when_there_are_no_eligible_sales(): void
    {
        $this->get(route('offers.index'))
            ->assertOk()
            ->assertSee(__('offers.empty_title'))
            ->assertSee(route('shop'));

        $this->get(route('offers.index', ['brands' => [999999]]))
            ->assertOk()
            ->assertSee(__('offers.empty_title'))
            ->assertSee(__('offers.brand_fallback'))
            ->assertSee(__('offers.clear_all_filters'));
    }

    public function test_maximum_discount_cache_is_cleared_when_a_sale_changes(): void
    {
        $product = $this->createProduct('Cached offer', 1000, 600);
        $service = app(OfferService::class);

        $this->assertSame(40, $service->maximumDiscountPercentage());

        $product->sale->update(['sale_price' => 800]);

        $this->assertSame(20, $service->maximumDiscountPercentage());

        $product->update(['price' => 900]);

        $this->assertSame(11, $service->maximumDiscountPercentage());
    }

    private function createProduct(string $name, int $price, int $salePrice, array $attributes = []): Product
    {
        $category = $this->categoryId
            ? Category::findOrFail($this->categoryId)
            : Category::create();
        $this->categoryId = $category->id;

        $product = Product::create(array_merge([
            'image' => 'products/default.jpg',
            'price' => $price,
            'featured' => false,
            'category_id' => $category->id,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(8)),
            'brand' => 'Sample Brand',
            'is_published' => true,
            'in_stock' => true,
            'stock' => 5,
        ], $attributes));

        $category->translateOrNew('en')->fill(['locale' => 'en', 'title' => 'Test Category'])->save();
        $category->translateOrNew('ar')->fill(['locale' => 'ar', 'title' => 'فئة تجريبية'])->save();

        $product->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => $name, 'description' => $name],
        );
        $product->translations()->updateOrCreate(
            ['locale' => 'ar'],
            ['name' => 'عرض '.$name, 'description' => $name],
        );
        ProductSale::create([
            'product_id' => $product->id,
            'sale_price' => $salePrice,
        ]);

        return $product->fresh();
    }

    private function createCategory(string $englishTitle, string $arabicTitle): Category
    {
        $category = Category::create();
        $category->translateOrNew('en')->fill(['locale' => 'en', 'title' => $englishTitle])->save();
        $category->translateOrNew('ar')->fill(['locale' => 'ar', 'title' => $arabicTitle])->save();

        return $category;
    }
}
