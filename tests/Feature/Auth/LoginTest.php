<?php

use App\Enums\UserStatus;
use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('storefront.home'));
});

test('vendors get redirected to vendor dashboard upon login', function () {
    $user = User::factory()->vendor()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('vendor.dashboard'));
});

test('admins get redirected to admin dashboard upon login', function () {
    $user = User::factory()->admin()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

test('suspended vendor cannot access vendor dashboard', function () {
    $vendor = User::factory()
        ->vendor()
        ->hasVendorProfile(['is_verified' => true])
        ->create([
            'status' => UserStatus::Suspended,
        ]);

    $this->actingAs($vendor)
        ->get(route('vendor.dashboard'))
        ->assertStatus(403);
});
