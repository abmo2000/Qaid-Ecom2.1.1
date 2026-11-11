<?php

namespace Tests\Feature\Filament\Packages;

use App\Enums\AdminRole;
use App\Filament\Resources\Packages\Pages\CreatePackages;
use App\Filament\Resources\Packages\Pages\EditPackages;
use App\Models\Category;
use App\Models\Package;
use App\Models\Product;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PackageProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_package_can_be_created_with_selected_products(): void
    {
        $product = $this->createProduct($this->createCategory('Care'), 'Daily Shampoo', 'شامبو يومي');

        Livewire::actingAs($this->admin())
            ->test(CreatePackages::class)
            ->fillForm([
                'en' => ['name' => 'Daily Set'],
                'ar' => ['name' => 'المجموعة اليومية'],
                'price' => 1500,
                'is_active' => true,
                'products' => [$product->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $package = Package::query()->firstOrFail();

        $this->assertDatabaseHas('package_products', [
            'package_id' => $package->id,
            'product_id' => $product->id,
        ]);
        $this->assertSame(1000, $package->original_price);
    }

    public function test_editing_a_package_updates_its_product_relationships(): void
    {
        $category = $this->createCategory('Care');
        $existingProduct = $this->createProduct($category, 'Existing Product', 'منتج حالي');
        $replacementProduct = $this->createProduct($category, 'Replacement Product', 'منتج بديل');
        $package = Package::query()->create([
            'price' => 1500,
            'original_price' => 1000,
            'is_active' => true,
        ]);
        $package->translateOrNew('en')->fill(['name' => 'Care Set'])->save();
        $package->translateOrNew('ar')->fill(['name' => 'مجموعة العناية'])->save();
        $package->products()->attach($existingProduct->id);

        Livewire::actingAs($this->admin())
            ->test(EditPackages::class, ['record' => $package->getKey()])
            ->fillForm([
                'en' => ['name' => 'Updated Care Set'],
                'ar' => ['name' => 'مجموعة العناية المحدثة'],
                'price' => 1600,
                'is_active' => true,
                'products' => [$replacementProduct->id],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseMissing('package_products', [
            'package_id' => $package->id,
            'product_id' => $existingProduct->id,
        ]);
        $this->assertDatabaseHas('package_products', [
            'package_id' => $package->id,
            'product_id' => $replacementProduct->id,
        ]);
    }

    public function test_products_can_be_searched_by_english_and_arabic_names(): void
    {
        $product = $this->createProduct($this->createCategory('Care'), 'Searchable Shampoo', 'شامبو قابل للبحث');
        $form = Livewire::actingAs($this->admin())->test(CreatePackages::class);
        $select = fn () => $form->instance()->form->getComponent('products');

        $this->assertArrayHasKey($product->id, $select()->getSearchResults('Searchable'));
        $this->assertArrayHasKey($product->id, $select()->getSearchResults('شامبو'));
    }

    public function test_category_filter_restricts_product_search_results(): void
    {
        $selectedCategory = $this->createCategory('Selected');
        $otherCategory = $this->createCategory('Other');
        $selectedProduct = $this->createProduct($selectedCategory, 'Matching Product A', 'منتج مطابق أ');
        $otherProduct = $this->createProduct($otherCategory, 'Matching Product B', 'منتج مطابق ب');

        $form = Livewire::actingAs($this->admin())->test(CreatePackages::class)
            ->set('data.category_filter', [$selectedCategory->id]);
        $results = $form->instance()->form->getComponent('products')->getSearchResults('Matching Product');

        $this->assertSame([$selectedProduct->id], array_keys($results));
        $this->assertArrayNotHasKey($otherProduct->id, $results);
    }

    public function test_product_search_is_limited_to_fifty_results(): void
    {
        $category = $this->createCategory('Bulk');

        for ($index = 1; $index <= 55; $index++) {
            $this->createProduct($category, "Bulk Match {$index}", "نتيجة جماعية {$index}");
        }

        $form = Livewire::actingAs($this->admin())->test(CreatePackages::class);
        $results = $form->instance()->form->getComponent('products')->getSearchResults('Bulk Match');

        $this->assertCount(50, $results);
    }

    public function test_package_title_price_and_products_are_required(): void
    {
        Livewire::actingAs($this->admin())
            ->test(CreatePackages::class)
            ->fillForm([
                'en' => ['name' => ''],
                'ar' => ['name' => ''],
                'price' => null,
                'products' => [],
            ])
            ->call('create')
            ->assertHasFormErrors([
                'en.name' => 'required',
                'ar.name' => 'required',
                'price' => 'required',
                'products' => 'required',
            ]);
    }

    private function admin(): User
    {
        return User::query()->create([
            'name' => 'Package Admin',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role_name' => AdminRole::SUPER_ADMIN->value,
            'is_approved' => true,
        ]);
    }

    private function createCategory(string $name): Category
    {
        $category = Category::query()->create([
            'image' => 'categories/test.jpg',
            'title' => $name,
        ]);
        $category->translateOrNew('ar')->fill(['title' => "فئة {$name}"])->save();

        return $category;
    }

    private function createProduct(Category $category, string $englishName, string $arabicName): Product
    {
        $product = Product::query()->create([
            'image' => 'products/test.jpg',
            'price' => 1000,
            'in_stock' => true,
            'featured' => false,
            'category_id' => $category->id,
            'is_published' => false,
        ]);
        $product->translateOrNew('en')->fill([
            'name' => $englishName,
            'description' => $englishName,
        ])->save();
        $product->translateOrNew('ar')->fill([
            'name' => $arabicName,
            'description' => $arabicName,
        ])->save();

        return $product;
    }
}
