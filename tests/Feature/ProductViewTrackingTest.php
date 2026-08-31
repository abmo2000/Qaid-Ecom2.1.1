<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductView;
use App\Models\Routine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductViewTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_counts_unique_product_views_per_session(): void
    {
        $category = Category::query()->create([
            'title' => 'Sample Category',
            'image' => 'categories/default.jpg',
            'slug' => 'sample-category',
        ]);

        $routine = Routine::query()->create([
            'title' => 'Sample Routine',
            'description' => 'Sample routine description',
            'image' => 'routines/default.jpg',
            'slug' => 'sample-routine',
        ]);

        $product = Product::query()->create([
            'name' => 'Lenovo ThinkPad T460',
            'description' => 'Sample product description',
            'image' => 'products/default.jpg',
            'price' => 1000,
            'in_stock' => true,
            'stock' => 10,
            'featured' => false,
            'category_id' => $category->id,
            'routine_id' => $routine->id,
            'slug' => 'lenovo-thinkpad-t460',
            'brand' => 'Sample Brand',
        ]);

        $productSlug = $product->fresh()->slug;

        $response = $this->withSession(['_token' => 'abc123'])
            ->get(route('products.show', $productSlug));

        $this->assertSame(200, $response->getStatusCode());

        $response = $this->withSession(['_token' => 'abc123'])
            ->get(route('products.show', $productSlug));

        $this->assertSame(200, $response->getStatusCode());

        $this->assertSame(1, ProductView::query()->where('product_id', $product->id)->count());
        $this->assertSame(1, $product->fresh()->view_count);
    }
}
