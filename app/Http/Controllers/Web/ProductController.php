<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductTrial;
use App\Models\ProductView;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product, Request $request)
    {

        $isTrial = $request->query('trial') === 'trial';

        if ($isTrial) {
            $product = ProductTrial::query()->where('product_id', $product->id)->firstOrFail();
        }

        $baseProduct = $isTrial ? $product->product : $product;
          $brandName = $baseProduct?->brand;
          $brandImagePath = null;

          if (! empty($brandName)) {
                $brandImagePath = Brand::query()
                     ->where('name', trim($brandName))
                     ->value('image');
          }

          $trackProduct = $isTrial ? $baseProduct : $product;
          $userId = auth()->id();
          $viewKey = 'product_viewed_' . $trackProduct->id;

          $viewExists = ProductView::query()
              ->where('product_id', $trackProduct->id)
              ->when($userId, function ($query, $userId) {
                  $query->where('user_id', $userId);
              }, function ($query) {
                  $query->where('session_id', session()->getId());
              })
              ->exists();

          if (! $viewExists && ! session()->has($viewKey)) {
              ProductView::query()->create([
                  'product_id' => $trackProduct->id,
                  'user_id' => $userId,
                  'session_id' => session()->getId(),
              ]);

              session()->put($viewKey, true);
          }

              return view('web.pages.shop.show')->with([
                     'product' => $product,
                     'brandName' => $brandName,
                     'brandImage' => ! empty($brandImagePath) ? storage_image_url($brandImagePath) : null,
              ]);            
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
