<?php

namespace App\Http\Controllers\Web;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Routine;
use Illuminate\Support\Facades\Session;

class ShopController extends Controller
{
    public function index(){

         $categories = Category::query()
                        ->select(['id'])
                        ->with(['translations:id,category_id,locale,title'])
                        ->get();

         $routines = Routine::query()->select(['id'])->get();

                 $brands = Product::query()
                        ->where('in_stock', true)
                        ->where('is_published', true)
                        ->whereNotNull('brand')
                        ->where('brand', '!=', '')
                        ->select('brand')
                        ->distinct()
                        ->orderBy('brand')
                        ->pluck('brand')
                        ->values();
          
          
                    return view('web.pages.shop.index')->with([
                        'categories' => $categories,
                        'routines' => $routines,
                        'brands' => $brands,
                    ]);
    }
}
