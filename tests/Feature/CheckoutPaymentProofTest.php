<?php

namespace Tests\Feature;

use App\Http\Requests\OrderCreateReq;
use App\Models\BuisnessSetting;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
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
        City::create([
            'price' => 50,
            'has_discussion_for_delivery' => false,
        ]);

        $category = Category::query()->create([
            'image' => 'categories/test.jpg',
            'title' => 'Test category',
        ]);
        $product = Product::query()->create([
            'image' => 'products/test.jpg',
            'price' => 100,
            'in_stock' => true,
            'featured' => false,
            'category_id' => $category->id,
            'is_published' => true,
            'stock' => 10,
        ]);
        $product->translateOrNew('en')->fill([
            'name' => 'Test product',
            'description' => 'Test product description',
        ])->save();

        session()->put('cart_session_id', session()->getId());

        CartItem::create([
            'session_id' => session()->getId(),
            'product_type' => 'product',
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
        ]);

        $fakeImage = UploadedFile::fake()->image('transfer-proof.jpg', 600, 600);
        $file = UploadedFile::createFromBase(
            new SymfonyUploadedFile(
                $fakeImage->getPathname(),
                $fakeImage->getClientOriginalName(),
                $fakeImage->getClientMimeType(),
                UPLOAD_ERR_OK,
                true,
            ),
            test: true,
        );

        $response = $this->post('/order', [
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
}
