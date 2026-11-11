<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_screens_render_with_localized_views(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(__('auth.no_account'))
            ->assertDontSee('auth.no_account')
            ->assertDontSee('admin_login_hint')
            ->assertDontSee('/admin/login');

        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee(__('auth.reset_password_instructions'));

        $this->get(route('password.reset', ['token' => 'sample-token']))
            ->assertOk()
            ->assertSee(__('auth.reset_password'));

        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee('login_success')
            ->assertDontSee('auth.');
    }

    public function test_failed_login_uses_generic_credentials_message(): void
    {
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'not-a-user@example.com',
                'password' => 'incorrect-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => __('auth.failed'),
            ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => __('auth.failed'),
            ]);
    }

    public function test_customer_can_sign_in_successfully(): void
    {
        $user = User::factory()->create([
            'role_name' => 'customer',
            'is_approved' => true,
        ]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }
}
