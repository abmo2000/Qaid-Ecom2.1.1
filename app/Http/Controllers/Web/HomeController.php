<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = Category::query()
            ->with('translations')
            ->latest('id')
            ->get();

        $heroProducts = Product::query()
            ->where('featured', true)
            ->where('in_stock', true)
            ->where('is_published', true)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->with('category')
            ->latest('id')
            ->limit(5)
            ->get();

        if ($heroProducts->isEmpty()) {
            $heroProducts = Product::query()
                ->where('in_stock', true)
                ->where('is_published', true)
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->with('category')
                ->latest('id')
                ->limit(5)
                ->get();
        }

        $categorySections = Category::query()
      ->with('translations')
      ->get()
      ->map(function (Category $category) {
        $products = Product::query()
          ->where('category_id', $category->id)
          ->where('in_stock', true)
          ->where('is_published', true)
          ->with(['sale', 'translations'])
          ->latest('id')
          ->limit(5)
          ->get();

        return [
          'id' => $category->id,
          'title' => $category->title,
          'products' => $products,
        ];
      })
      ->filter(fn (array $section) => $section['products']->isNotEmpty())
      ->values();

    $seoData = getPageSeo('home', [
      'meta_title' => config('app.name'),
      'meta_description' => 'Discover amazing products and great deals on our online store.',
    ]);

    return view('web.pages.home')->with([
      'categories' => $categories,
      'categorySections' => $categorySections,
      'heroProducts' => $heroProducts,
      'seoData' => $seoData,
    ]);
    }
}
