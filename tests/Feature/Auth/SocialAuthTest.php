<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Mockery;

test('redirects to social provider', function () {
    $response = $this->get(route('auth.social.redirect', 'google'));
    expect($response->status())->toBe(302);
});

test('handles social login callback for new user', function () {
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('1234567890');
    $abstractUser->shouldReceive('getName')->andReturn('Test User');
    $abstractUser->shouldReceive('getNickname')->andReturn(null);
    $abstractUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    $response = $this->get(route('auth.social.callback', 'google'));

    $user = User::where('email', 'test@example.com')->first();
    
    expect($user)->not->toBeNull()
        ->and($user->social_id)->toBe('1234567890')
        ->and($user->social_provider)->toBe('google')
        ->and($user->role)->toBe(UserRole::Customer);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('storefront.home'));
});

test('handles social login callback for existing user without social provider', function () {
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'role' => UserRole::Customer,
        'social_provider' => null,
        'social_id' => null,
    ]);

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('0987654321');
    $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('github')->andReturn($provider);

    $response = $this->get(route('auth.social.callback', 'github'));

    $user->refresh();
    
    expect($user->social_id)->toBe('0987654321')
        ->and($user->social_provider)->toBe('github');

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('storefront.home'));
});
