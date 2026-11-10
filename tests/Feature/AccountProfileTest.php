<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_profile_renders_with_localized_account_navigation(): void
    {
        $city = $this->createCity();
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
            'city_id' => $city->id,
        ]);

        $this->actingAs($user)
            ->get(route('account.profile.edit'))
            ->assertOk()
            ->assertSee('account-page-body')
            ->assertSee(__('profile.personal_information'))
            ->assertSee(__('profile.contact_address'))
            ->assertSee(__('profile.security'))
            ->assertSee('name="city_id"', false)
            ->assertSee('lg:grid-cols-[17rem_minmax(0,1fr)]', false)
            ->assertSee('overflow-x-auto', false)
            ->assertSee($city->translate('en')->name)
            ->assertDontSee(__('profile.google_connected'))
            ->assertDontSee('Two-factor');

        $user->forceFill(['google_id' => 'google-account-id'])->save();

        $this->get(route('account.profile.edit'))
            ->assertOk()
            ->assertSee(__('profile.google_connected'));
    }

    public function test_customer_profile_renders_in_arabic_rtl(): void
    {
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
        ]);

        $this->actingAs($user)
            ->withSession(['locale' => 'ar'])
            ->get(route('account.profile.edit'))
            ->assertOk()
            ->assertSee('lang="ar" dir="rtl"', false)
            ->assertSee('المعلومات الشخصية')
            ->assertDontSee('profile.personal_information');
    }

    public function test_order_list_uses_short_reference_and_details_keep_full_order_id(): void
    {
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
        ]);
        $orderId = '8f04761e-aabb-4cc7-99ff-aabbccddeeff';
        $order = Order::create([
            'order_id' => $orderId,
            'customer_id' => $user->id,
            'customer_type' => 'user',
            'payment_method' => 'cash',
            'status' => 'pending',
            'amount' => 100,
        ]);

        $this->actingAs($user)
            ->get(route('account.orders.index'))
            ->assertOk()
            ->assertSee('#8F04761E')
            ->assertSee('title="'.$orderId.'"', false);

        $this->get(route('account.orders.show', $order))
            ->assertOk()
            ->assertSee($orderId);
    }

    public function test_customer_can_update_supported_profile_fields(): void
    {
        $city = $this->createCity();
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($user)
            ->put(route('user-profile-information.update'), [
                'name' => 'Updated Customer',
                'email' => $user->email,
                'phone' => '+201234567890',
                'address' => '12 Garden Street',
                'city_id' => $city->id,
                'insta_account' => '@qaid_customer',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'profile-information-updated');

        $this->get(route('account.profile.edit'))
            ->assertOk()
            ->assertSee(__('profile.updated'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Customer',
            'phone' => '+201234567890',
            'address' => '12 Garden Street',
            'city_id' => $city->id,
            'insta_account' => '@qaid_customer',
        ]);
    }

    public function test_profile_validation_errors_return_in_the_profile_error_bag(): void
    {
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
        ]);

        $this->actingAs($user)
            ->from(route('account.profile.edit'))
            ->put(route('user-profile-information.update'), [
                'name' => '',
                'email' => 'not-an-email',
            ])
            ->assertRedirect(route('account.profile.edit'))
            ->assertSessionHasErrorsIn('updateProfileInformation', ['name', 'email']);

        $this->get(route('account.profile.edit'))
            ->assertOk()
            ->assertSee(__('validation.required', ['attribute' => 'name']))
            ->assertDontSee('profile.name');
    }

    public function test_customer_can_update_password_from_account_security_card(): void
    {
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
        ]);

        $this->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'new-strong-password',
                'password_confirmation' => 'new-strong-password',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'password-updated');

        $this->assertTrue(Hash::check('new-strong-password', $user->fresh()->password));
    }

    private function createCity(): City
    {
        $city = City::create([
            'price' => 50,
            'has_discussion_for_delivery' => false,
        ]);

        $city->translateOrNew('en')->fill(['locale' => 'en', 'name' => 'Test City'])->save();
        $city->translateOrNew('ar')->fill(['locale' => 'ar', 'name' => 'مدينة تجريبية'])->save();

        return $city;
    }
}
