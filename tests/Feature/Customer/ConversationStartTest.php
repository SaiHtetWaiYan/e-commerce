<?php

use App\Models\Conversation;
use App\Models\User;

test('customer can start a conversation via start endpoint', function () {
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->create();

    $response = $this->actingAs($customer)->post(route('customer.conversations.start'), [
        'vendor_id' => $vendor->id,
        'body' => 'Hi, I need help with my order.',
    ]);

    $conversation = Conversation::query()
        ->where('buyer_id', $customer->id)
        ->where('vendor_id', $vendor->id)
        ->first();

    expect($conversation)->not->toBeNull()
        ->and($conversation->messages()->count())->toBe(1)
        ->and($conversation->messages()->first()?->body)->toBe('Hi, I need help with my order.');

    $response->assertRedirect(route('customer.conversations.show', $conversation));
});

test('posting to start path does not get resolved as conversation id', function () {
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->create();

    $this->actingAs($customer)
        ->post('/customer/messages/start', [
            'vendor_id' => $vendor->id,
            'body' => 'Route conflict regression test.',
        ])
        ->assertRedirect();

    expect(Conversation::query()
        ->where('buyer_id', $customer->id)
        ->where('vendor_id', $vendor->id)
        ->exists())->toBeTrue();
});
