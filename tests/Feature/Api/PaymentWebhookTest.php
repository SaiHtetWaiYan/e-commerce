<?php

use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Support\Str;

test('payment webhook marks order as paid and is idempotent by event id', function () {
    $order = Order::factory()->create([
        'payment_status' => PaymentStatus::Pending,
    ]);

    $payload = [
        'event_id' => (string) Str::uuid(),
        'order_id' => $order->id,
        'status' => 'paid',
        'payment_reference' => 'CARD-TEST-000001',
    ];

    $this->postJson(route('api.payments.webhook'), $payload)
        ->assertSuccessful()
        ->assertJson([
            'message' => 'Payment webhook processed.',
            'order_id' => $order->id,
            'payment_status' => PaymentStatus::Paid->value,
        ]);

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Paid);

    $this->postJson(route('api.payments.webhook'), $payload)
        ->assertStatus(202)
        ->assertJson([
            'message' => 'Event already processed.',
        ]);
});

test('payment webhook rejects invalid signature when secret is configured', function () {
    config()->set('services.payments.webhook_secret', 'top-secret');

    $order = Order::factory()->create([
        'payment_status' => PaymentStatus::Pending,
    ]);

    $this->postJson(route('api.payments.webhook'), [
        'event_id' => (string) Str::uuid(),
        'order_id' => $order->id,
        'status' => 'paid',
        'payment_reference' => 'CARD-TEST-000002',
    ])->assertStatus(401);

    expect($order->fresh()->payment_status)->toBe(PaymentStatus::Pending);
});
