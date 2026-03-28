<?php

use App\Enums\PaymentStatus;
use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\StripeService;
use Stripe\Checkout\Session;

test('checkout screen can be rendered if cart has items', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => '123']);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
    ]);

    $this->actingAs($user)
        ->withSession(['id' => '123'])
        ->get('/checkout')
        ->assertStatus(200);
});

test('checkout redirects heavily if empty cart', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/checkout')
        ->assertRedirect('/cart');
});

test('user can place an order successfully', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => '123']);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
    ]);

    $response = $this->actingAs($user)
        ->withSession(['id' => '123'])
        ->post('/checkout', [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '1234567890',
            'shipping_address' => '123 Street',
            'shipping_city' => 'New York',
            'shipping_state' => 'NY',
            'shipping_country' => 'USA',
            'customer_email' => 'john@example.com',
            'payment_method' => 'cod',
        ]);

    $order = Order::first();
    expect($order)->not->toBeNull()
        ->and($order->user_id)->toBe($user->id)
        ->and($order->items()->count())->toBe(1)
        ->and($order->shipment()->exists())->toBeTrue();

    $response->assertRedirect(route('storefront.checkout.success', $order));

    // Cart should be empty after placing order
    expect(CartItem::count())->toBe(0);
});

test('checkout accepts legacy payment method aliases and normalizes them', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => '123']);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
    ]);

    $fakeSession = Session::constructFrom([
        'id' => 'cs_test_fake_session_id',
        'url' => 'https://checkout.stripe.com/pay/cs_test_fake',
    ]);

    $this->mock(StripeService::class, function ($mock) use ($fakeSession): void {
        $mock->shouldReceive('createCheckoutSession')
            ->once()
            ->andReturn($fakeSession);
    });

    $response = $this->actingAs($user)
        ->withSession(['id' => '123'])
        ->post('/checkout', [
            'shipping_name' => 'John Doe',
            'shipping_phone' => '1234567890',
            'shipping_address' => '123 Street',
            'shipping_city' => 'New York',
            'shipping_state' => 'NY',
            'shipping_country' => 'USA',
            'customer_email' => 'john@example.com',
            'payment_method' => 'credit_card',
        ]);

    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull()
        ->and($order->payment_method)->toBe('card')
        ->and($order->payment_status)->toBe(PaymentStatus::Pending)
        ->and($order->payment_reference)->toBe('cs_test_fake_session_id');

    $response->assertRedirect('https://checkout.stripe.com/pay/cs_test_fake');
});

test('checkout rejects address that does not belong to authenticated user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => '123']);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
    ]);

    $otherAddress = Address::query()->create([
        'user_id' => $otherUser->id,
        'label' => 'home',
        'full_name' => 'Other User',
        'phone' => '9999999',
        'street_address' => 'Other Street',
        'city' => 'Other City',
        'state' => 'Other State',
        'country' => 'Other Country',
        'is_default' => false,
    ]);

    $this->actingAs($user)
        ->withSession(['id' => '123'])
        ->post('/checkout', [
            'address_id' => $otherAddress->id,
            'customer_email' => 'john@example.com',
            'payment_method' => 'cod',
        ])
        ->assertInvalid(['address_id']);
});
