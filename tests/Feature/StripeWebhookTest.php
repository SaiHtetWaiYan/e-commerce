<?php

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\StripeService;
use Stripe\Event;
use Stripe\Exception\SignatureVerificationException;

test('stripe webhook marks order as paid on checkout session completed', function () {
    $order = Order::factory()->create([
        'payment_method' => 'card',
        'payment_status' => PaymentStatus::Pending,
        'payment_reference' => 'cs_test_session123',
    ]);

    $event = Event::constructFrom([
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'metadata' => ['order_id' => (string) $order->id],
                'payment_intent' => 'pi_test_intent123',
            ],
        ],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event): void {
        $mock->shouldReceive('constructWebhookEvent')
            ->once()
            ->andReturn($event);
    });

    $response = $this->postJson('/api/stripe/webhook', [], [
        'Stripe-Signature' => 'test_signature',
    ]);

    $response->assertOk();
    $response->assertJson(['message' => 'Webhook handled.']);

    $order->refresh();

    expect($order->payment_status)->toBe(PaymentStatus::Paid)
        ->and($order->payment_reference)->toBe('pi_test_intent123')
        ->and($order->paid_at)->not->toBeNull();
});

test('stripe webhook handles duplicate events idempotently', function () {
    $order = Order::factory()->create([
        'payment_method' => 'card',
        'payment_status' => PaymentStatus::Paid,
        'payment_reference' => 'pi_test_already_paid',
        'paid_at' => now()->subMinutes(5),
    ]);

    $event = Event::constructFrom([
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'metadata' => ['order_id' => (string) $order->id],
                'payment_intent' => 'pi_test_already_paid',
            ],
        ],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event): void {
        $mock->shouldReceive('constructWebhookEvent')
            ->once()
            ->andReturn($event);
    });

    $response = $this->postJson('/api/stripe/webhook', [], [
        'Stripe-Signature' => 'test_signature',
    ]);

    $response->assertOk();

    $order->refresh();
    expect($order->payment_status)->toBe(PaymentStatus::Paid);
});

test('stripe webhook returns 401 for invalid signature', function () {
    $this->mock(StripeService::class, function ($mock): void {
        $mock->shouldReceive('constructWebhookEvent')
            ->once()
            ->andThrow(new SignatureVerificationException('Invalid signature'));
    });

    $response = $this->postJson('/api/stripe/webhook', [], [
        'Stripe-Signature' => 'bad_signature',
    ]);

    $response->assertStatus(401);
    $response->assertJson(['message' => 'Invalid signature.']);
});

test('stripe webhook marks order as failed on session expired', function () {
    $order = Order::factory()->create([
        'payment_method' => 'card',
        'payment_status' => PaymentStatus::Pending,
        'payment_reference' => 'cs_test_expired_session',
    ]);

    $event = Event::constructFrom([
        'type' => 'checkout.session.expired',
        'data' => [
            'object' => [
                'metadata' => ['order_id' => (string) $order->id],
            ],
        ],
    ]);

    $this->mock(StripeService::class, function ($mock) use ($event): void {
        $mock->shouldReceive('constructWebhookEvent')
            ->once()
            ->andReturn($event);
    });

    $response = $this->postJson('/api/stripe/webhook', [], [
        'Stripe-Signature' => 'test_signature',
    ]);

    $response->assertOk();

    $order->refresh();
    expect($order->payment_status)->toBe(PaymentStatus::Failed);
});
