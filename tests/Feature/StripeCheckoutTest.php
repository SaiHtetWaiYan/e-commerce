<?php

use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\StripeService;
use Stripe\Checkout\Session;
use Stripe\Exception\InvalidRequestException;

test('card checkout redirects to stripe checkout session', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => 'stripe-test']);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 25.00,
    ]);

    $fakeSession = Session::constructFrom([
        'id' => 'cs_test_abc123',
        'url' => 'https://checkout.stripe.com/pay/cs_test_abc123',
    ]);

    $this->mock(StripeService::class, function ($mock) use ($fakeSession): void {
        $mock->shouldReceive('createCheckoutSession')
            ->once()
            ->andReturn($fakeSession);
    });

    $response = $this->actingAs($user)
        ->withSession(['id' => 'stripe-test'])
        ->post('/checkout', [
            'shipping_name' => 'Jane Doe',
            'shipping_phone' => '5551234567',
            'shipping_address' => '456 Main St',
            'shipping_city' => 'San Francisco',
            'shipping_state' => 'CA',
            'shipping_country' => 'USA',
            'customer_email' => 'jane@example.com',
            'payment_method' => 'card',
        ]);

    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull()
        ->and($order->payment_method)->toBe('card')
        ->and($order->payment_status)->toBe(PaymentStatus::Pending)
        ->and($order->payment_reference)->toBe('cs_test_abc123');

    $response->assertRedirect('https://checkout.stripe.com/pay/cs_test_abc123');
});

test('non-card checkout does not use stripe', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => 'cod-test']);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 15.00,
    ]);

    $this->mock(StripeService::class, function ($mock): void {
        $mock->shouldNotReceive('createCheckoutSession');
    });

    $response = $this->actingAs($user)
        ->withSession(['id' => 'cod-test'])
        ->post('/checkout', [
            'shipping_name' => 'Jane Doe',
            'shipping_phone' => '5551234567',
            'shipping_address' => '456 Main St',
            'shipping_city' => 'San Francisco',
            'shipping_state' => 'CA',
            'shipping_country' => 'USA',
            'customer_email' => 'jane@example.com',
            'payment_method' => 'cod',
        ]);

    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull()
        ->and($order->payment_method)->toBe('cod');

    $response->assertRedirect(route('storefront.checkout.success', $order));
});

test('card checkout handles stripe api failure gracefully', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => 'fail-test']);
    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 20.00,
    ]);

    $this->mock(StripeService::class, function ($mock): void {
        $mock->shouldReceive('createCheckoutSession')
            ->once()
            ->andThrow(InvalidRequestException::factory('Stripe service unavailable'));
    });

    $response = $this->actingAs($user)
        ->withSession(['id' => 'fail-test'])
        ->post('/checkout', [
            'shipping_name' => 'Jane Doe',
            'shipping_phone' => '5551234567',
            'shipping_address' => '456 Main St',
            'shipping_city' => 'San Francisco',
            'shipping_state' => 'CA',
            'shipping_country' => 'USA',
            'customer_email' => 'jane@example.com',
            'payment_method' => 'card',
        ]);

    $order = Order::query()->latest('id')->first();

    expect($order)->not->toBeNull()
        ->and($order->payment_method)->toBe('card')
        ->and($order->payment_status)->toBe(PaymentStatus::Pending);

    $response->assertRedirect(route('storefront.checkout.success', $order));
    $response->assertSessionHas('stripe_error');
});
