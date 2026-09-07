<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArtisanController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\OrderInvoiceController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\RoutineController;
use App\Http\Controllers\Web\PackagesController;
use App\Http\Controllers\Web\PackagesApiController;
use App\Http\Controllers\Web\ProductsApiCpntroller;
use App\Http\Controllers\Web\CustomerTrafficController;
use App\Http\Controllers\Web\CouponValidateController;
use App\Http\Controllers\Web\BuyerOrderController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\TermsController;
use App\Http\Controllers\Web\Auth\GoogleAuthController;
use App\Http\Controllers\Web\WholesaleController;

Route::middleware(['locale'])->group(function(){
     Route::get('/', HomeController::class)->name('home');

     Route::view('/contact' , 'web.pages.contact')->name('contact');
     Route::get('/contact-message', function () {
         return redirect()->route('contact');
     });
     Route::post('/contact-message' , ContactController::class)->name('contact-message');
     
     Route::resource('routines' , RoutineController::class)->only('show' , 'index');

     Route::get('/shop' , [ShopController::class, 'index'])->name('shop');
     Route::post('/customer-traffic', [CustomerTrafficController::class, 'store'])->middleware('auth')->name('customer.traffic.store');
     Route::get('api/products/{is_trial}' , ProductsApiCpntroller::class)->defaults('is_trial' , false);
     Route::get('api/packages' , PackagesApiController::class);

     Route::resource('products' , ProductController::class)->only(['show' , 'index']);
     Route::resource('packages' , PackagesController::class)->only(['show']);
     Route::prefix('cart')->group(function(){
          Route::get('' , [CartController::class , 'index'])->name('cart');
          Route::post('/add' , [CartController::class , 'add'])->name('cart.add');
          Route::patch('/update/{product_id}' , [CartController::class , 'update']);
          Route::delete('remove/{product_id}' , [CartController::class , 'remove']);
          Route::delete('clear' , [CartController::class , 'clear']);
     });

     Route::middleware('checkOut-checker')->group(function () {
          Route::get('checkout' , [OrderController::class , "index"])->name('checkout');
          Route::post('order' , [OrderController::class , 'store'])->name('checkout');
          Route::post('coupon/validate', CouponValidateController::class)->name('coupon.validate');
     });

     Route::get('orders/{order_id}/invoice', [OrderInvoiceController::class, 'show'])
          ->name('orders.invoice');

     Route::get('terms', TermsController::class)->name('terms');
     Route::get('wholesale-sales', [WholesaleController::class, 'index'])->name('wholesale-sales');
     Route::post('wholesale-sales/price-quote', [WholesaleController::class, 'submit'])->name('wholesale-sales.price-quote');
     Route::get('wholesale-sales/price-quote/download', [WholesaleController::class, 'downloadQuote'])->name('wholesale-sales.quote-download');
});

Route::get('locale/{locale}' , LocaleController::class)->name('lang-switch');
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('queue', ArtisanController::class)->middleware('secure');

Route::middleware('auth')->group(function () {
     Route::get('dashboard/orders/{order}/invoice', OrderInvoiceController::class)->name('dashboard.orders.invoice');

     Route::middleware(App\Http\Middleware\EnsureCustomer::class)->group(function () {
         Route::prefix('account')->name('account.')->group(function () {
             Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
             Route::get('orders', [BuyerOrderController::class, 'index'])->name('orders.index');
             Route::get('orders/{order}', [BuyerOrderController::class, 'show'])->name('orders.show');
             Route::post('orders/{order}/receive', [BuyerOrderController::class, 'receive'])->name('orders.receive');
         });
     });
});