<?php

namespace App\Http\Controllers\Web\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

use function Laravel\Prompts\info;

class GoogleAuthController extends Controller
{
     public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

     public function handleGoogleCallback()
    {
        try {
            // Get user info from Google
            $googleUser = Socialite::driver('google')->user();
            
            // Find or create user
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                if (! $user->isCustomer()) {
                    return redirect('/login')->withErrors([
                        'email' => 'Admin accounts must sign in through the admin portal.',
                    ]);
                }

                // Update existing user with Google ID if not already set
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    $user->save();
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'role_name' => 'customer',
                    'password' => Hash::make(Str::random(24)), // Random password
                    'email_verified_at' => now(), // Auto-verify email for Google users
                ]);
            }
            
            // Log the user in
            Auth::login($user, true);
             $cartService = app(CartService::class);
             
             $cartService->transferGuestCartToUser(Auth::id());
            // Redirect to dashboard or intended page
            return redirect()->intended('/cart');
            
        } catch (\Exception $e) {
            info($e->getMessage());
            throw $e;
            return redirect('/login')->withErrors(['error' => 'Failed to authenticate with Google']);
        }
    }
}
