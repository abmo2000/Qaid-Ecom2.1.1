<?php

namespace Tests\Feature;

use App\Http\Requests\OrderCreateReq;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Order;
use App\Models\BuisnessSetting;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CheckoutPaymentProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_vacation_mode_blocks_order_submission_with_flash_message(): void
    {
        $setting = BuisnessSetting::create(['key' => 'vacation-settings']);
        $setting->translations()->createMany([
            ['locale' => 'en', 'value' => ['enabled' => true, 'message' => 'Back soon.']],
            ['locale' => 'ar', 'value' => ['enabled' => true, 'message' => 'سنعود قريبًا.']],
        ]);

        $this->assertTrue(getVacationSettings()->enabled);

        $city = City::create([
            'price' => 50,
            'has_discussion_for_delivery' => false,
        ]);

        $sessionId = session()->getId();
        session()->put('cart_session_id', $sessionId);

        CartItem::create([
            'session_id' => $sessionId,
            'product_type' => 'product',
            'product_id' => 1,
            'quantity' => 1,
            'price' => 100,
        ]);

        $response = $this->post('/order', [
            'email' => 'customer@example.com',
            'phone' => '+201234567890',
            'city_id' => $city->id,
            'payment_method' => 'cash',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Back soon.');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_public_storage_link_points_to_public_disk(): void
    {
        $this->assertSame(
            realpath(storage_path('app/public')),
            realpath(public_path('storage'))
        );
    }

    public function test_store_saves_payment_proof_for_instapay_orders(): void
    {
        Config::set('checkout.instapay_enabled', true);
        City::create([
            'price' => 50,
            'has_discussion_for_delivery' => false,
        ]);

        $product = $this->createProduct();
        $sessionId = session()->getId();
        session()->put('cart_session_id', $sessionId);
        CartItem::create([
            'session_id' => $sessionId,
            'product_type' => 'product',
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
        ]);

        $file = UploadedFile::fake()->image('transfer-proof.jpg', 600, 600);

        $response = $this->postJson('/order', [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'phone' => '+201234567890',
            'address' => 'Test address',
            'city_id' => City::first()->id,
            'delivery_option' => 'proceed',
            'payment_method' => 'instapay',
            'insta_account' => 'user@instapay',
            'notes' => 'Need guarantee proof',
            'payment_proof' => $file,
        ]);

        $response->assertOk();

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertNotNull($order->payment_proof_path);
        $this->assertTrue(Storage::disk('public')->exists($order->payment_proof_path));
        $this->assertNotNull($order->payment_proof_url);
    }

    public function test_instapay_requires_payment_proof_upload(): void
    {
        Config::set('checkout.instapay_enabled', true);
        City::create([
            'price' => 50,
            'has_discussion_for_delivery' => false,
        ]);

        $request = new OrderCreateReq([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'phone' => '+201234567890',
            'address' => 'Test address',
            'city_id' => City::first()->id,
            'delivery_option' => 'proceed',
            'payment_method' => 'instapay',
            'insta_account' => 'user@instapay',
            'notes' => 'Need guarantee proof',
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('payment_proof'));
    }

    public function test_instapay_is_rejected_when_the_feature_is_disabled(): void
    {
        Config::set('checkout.instapay_enabled', false);
        $city = City::create([
            'price' => 50,
            'has_discussion_for_delivery' => false,
        ]);
        $sessionId = session()->getId();
        session()->put('cart_session_id', $sessionId);
        CartItem::create([
            'session_id' => $sessionId,
            'product_type' => 'product',
            'product_id' => 1,
            'quantity' => 1,
            'price' => 100,
        ]);

        $response = $this->postJson('/order', [
            'email' => 'user@example.com',
            'phone' => '+201234567890',
            'city_id' => $city->id,
            'payment_method' => 'instapay',
            'insta_account' => 'user@instapay',
            'payment_proof' => UploadedFile::fake()->image('transfer-proof.jpg'),
        ]);

        $response->assertStatus(433)
            ->assertJsonValidationErrors('payment_method');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_coupon_validation_endpoint_is_disabled_by_default(): void
    {
        Config::set('checkout.coupons_enabled', false);
        $sessionId = session()->getId();
        session()->put('cart_session_id', $sessionId);
        CartItem::create([
            'session_id' => $sessionId,
            'product_type' => 'product',
            'product_id' => 1,
            'quantity' => 1,
            'price' => 100,
        ]);

        $this->postJson('/coupon/validate', ['code' => 'SAVE20'])
            ->assertNotFound()
            ->assertJsonPath('valid', false);
    }

    public function test_checkout_renders_cart_items_and_translated_checkout_copy(): void
    {
        $product = $this->createProduct();
        $this->addProductToCart($product);

        $this->get(route('checkout'))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee(__('checkout.secure_checkout'))
            ->assertSee(__('checkout.coming_soon'))
            ->assertSee(__('navbar.terms'))
            ->assertDontSee('checkout.secure_checkout')
            ->assertDontSee('navbar.terms');

        $this->withSession(['locale' => 'ar'])
            ->get(route('checkout'))
            ->assertOk()
            ->assertSee('lang="ar" dir="rtl"', false)
            ->assertSee(__('checkout.coming_soon'))
            ->assertSee(__('navbar.terms'))
            ->assertDontSee('navbar.terms');
    }

    public function test_forged_coupon_code_is_ignored_when_coupons_are_disabled(): void
    {
        Config::set('checkout.coupons_enabled', false);
        $city = City::create([
            'price' => 50,
            'has_discussion_for_delivery' => false,
        ]);
        $product = $this->createProduct();
        $this->addProductToCart($product);
        $couponOwner = User::factory()->create(['role_name' => 'customer']);
        Coupon::create([
            'code' => 'SAVE50',
            'discount_percentage' => 50,
            'is_active' => true,
            'created_by' => $couponOwner->id,
        ]);

        $this->postJson('/order', [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'phone' => '+201234567890',
            'address' => 'Test address',
            'city_id' => $city->id,
            'delivery_option' => 'proceed',
            'payment_method' => 'cash_on_delivery',
            'coupon_code' => 'SAVE50',
        ])->assertOk();

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertNull($order->coupon_id);
        $this->assertSame(150, (int) $order->amount);
    }

    private function createProduct(): Product
    {
        $category = Category::create();
        $product = Product::create([
            'image' => 'products/default.jpg',
            'price' => 100,
            'category_id' => $category->id,
            'brand' => 'Checkout Test',
            'is_published' => true,
            'in_stock' => true,
            'stock' => 5,
            'slug' => 'checkout-test-product',
        ]);
        $product->translations()->updateOrCreate(
            ['locale' => 'en'],
            ['name' => 'Checkout Test Product', 'description' => 'Test product'],
        );
        $product->translations()->updateOrCreate(
            ['locale' => 'ar'],
            ['name' => 'منتج الاختبار', 'description' => 'منتج للاختبار'],
        );

        return $product->fresh();
    }

    private function addProductToCart(Product $product): void
    {
        $sessionId = session()->getId();
        session()->put('cart_session_id', $sessionId);
        CartItem::create([
            'session_id' => $sessionId,
            'product_type' => 'product',
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
        ]);
    }
}
