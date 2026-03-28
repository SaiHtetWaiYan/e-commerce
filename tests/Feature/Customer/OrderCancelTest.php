<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

test('customer can cancel a pending order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::Pending,
    ]);

    $response = $this->actingAs($user)
        ->post("/customer/orders/{$order->id}/cancel", [
            'reason' => 'Changed my mind',
        ]);

    $response->assertRedirect(route('customer.orders.show', $order));
    $response->assertSessionHas('status', 'Order has been cancelled successfully.');

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
});

test('customer cannot cancel a shipped order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::Shipped,
    ]);

    $response = $this->actingAs($user)
        ->post("/customer/orders/{$order->id}/cancel", [
            'reason' => 'Too late',
        ]);

    $response->assertStatus(403);
    expect($order->fresh()->status)->toBe(OrderStatus::Shipped);
});

test('customer cannot cancel another users order', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $otherUser->id,
        'status' => OrderStatus::Pending,
    ]);

    $response = $this->actingAs($user)
        ->post("/customer/orders/{$order->id}/cancel", [
            'reason' => 'Hacking',
        ]);

    $response->assertStatus(403);
});
