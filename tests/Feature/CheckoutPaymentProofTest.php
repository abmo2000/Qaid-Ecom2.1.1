<?php

namespace Tests\Feature;

use App\Http\Requests\OrderCreateReq;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CheckoutPaymentProofTest extends TestCase
{
    use RefreshDatabase;

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

        CartItem::create([
            'session_id' => session()->getId(),
            'product_type' => 'product',
            'product_id' => 1,
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
