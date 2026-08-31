<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Routine;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $category = Category::query()->first() ?: Category::query()->create([
            'image' => 'categories/default.jpg',
            'title' => 'Sample Category',
        ]);

        $routine = Routine::query()->first() ?: Routine::query()->create([
            'image' => 'routines/default.jpg',
            'title' => 'Sample Routine',
            'description' => 'Sample routine description',
        ]);

        return [
            'image' => 'products/default.jpg',
            'price' => 1000,
            'in_stock' => true,
            'featured' => false,
            'category_id' => $category->id,
            'routine_id' => $routine->id,
            'slug' => $this->faker->unique()->slug(),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'stock' => 10,
            'brand' => 'Sample Brand',
        ];
    }
}
