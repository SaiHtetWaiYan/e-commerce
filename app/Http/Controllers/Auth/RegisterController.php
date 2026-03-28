<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showCustomerForm(): View
    {
        return view('auth.register');
    }

    public function registerCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => UserRole::Customer,
            'status' => UserStatus::Active,
        ]);

        event(new \Illuminate\Auth\Events\Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('customer.dashboard');
    }

    public function showVendorForm(): View
    {
        return view('auth.vendor-register');
    }

    public function registerVendor(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'store_name' => ['required', 'string', 'max:255'],
            'store_description' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'phone' => $validated['phone'] ?? null,
                'role' => UserRole::Vendor,
                'status' => UserStatus::Active,
            ]);

            VendorProfile::query()->create([
                'user_id' => $user->id,
                'store_name' => $validated['store_name'],
                'store_slug' => Str::slug($validated['store_name']).'-'.Str::random(4),
                'store_description' => $validated['store_description'] ?? null,
                'is_verified' => false,
                'commission_rate' => 10.00,
            ]);

            return $user;
        });

        event(new \Illuminate\Auth\Events\Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('storefront.home')
            ->with('status', 'Your vendor account has been created! Please wait for admin approval before you can start selling.');
    }
}
