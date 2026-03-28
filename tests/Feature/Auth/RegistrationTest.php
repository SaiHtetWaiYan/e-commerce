<?php

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Models\VendorProfile;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('vendor registration screen can be rendered', function () {
    $response = $this->get('/vendor/register');
    $response->assertStatus(200);
});

test('new customers can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('customer.dashboard'));

    $user = User::where('email', 'test@example.com')->first();
    expect($user->role)->toBe(UserRole::Customer)
        ->and($user->status)->toBe(UserStatus::Active);
});

test('new vendors can register', function () {
    $response = $this->post('/vendor/register', [
        'name' => 'Test Vendor',
        'email' => 'vendor@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'store_name' => 'Test Store',
        'store_description' => 'A great store',
        'phone' => '1234567890',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('storefront.home'));

    $user = User::where('email', 'vendor@example.com')->first();
    expect($user->role)->toBe(UserRole::Vendor)
        ->and($user->status)->toBe(UserStatus::Active)
        ->and($user->phone)->toBe('1234567890');

    $profile = VendorProfile::where('user_id', $user->id)->first();
    expect($profile)->not->toBeNull()
        ->and($profile->store_name)->toBe('Test Store')
        ->and($profile->store_description)->toBe('A great store')
        ->and($profile->is_verified)->toBeFalse();
});
