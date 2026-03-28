<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'github']), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'github']), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Unable to authenticate using ' . ucfirst($provider) . '. Please try again.']);
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            // Update social provider details if it's matching by email but not set
            if (!$user->social_provider) {
                $user->update([
                    'social_provider' => $provider,
                    'social_id' => $socialUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
        } else {
            // Create a new customer user
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'password' => null, // Password is null for social login
                'social_provider' => $provider,
                'social_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(), // Assume social emails are verified
                'role' => UserRole::Customer,
                'status' => UserStatus::Active,
            ]);
        }

        Auth::login($user, true);

        // Redirect based on role
        $defaultRoute = match ($user->role) {
            UserRole::Admin => route('admin.dashboard'),
            UserRole::Vendor => route('vendor.dashboard'),
            UserRole::DeliveryAgent => route('delivery.dashboard'),
            default => route('storefront.home'),
        };

        return redirect()->intended($defaultRoute);
    }
}
