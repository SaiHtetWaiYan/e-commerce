<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\User;

test('customer can view return request form for delivered order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::Delivered,
    ]);

    $response = $this->actingAs($user)
        ->get("/customer/returns/create/{$order->id}");

    $response->assertStatus(200);
});

test('customer cannot return a pending order', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::Pending,
    ]);
    $item = OrderItem::factory()->create([
        'order_id' => $order->id,
    ]);

    $response = $this->actingAs($user)
        ->post("/customer/returns/{$order->id}", [
            'reason' => 'Defective',
            'order_item_ids' => [$item->id],
        ]);

    $response->assertSessionHasErrors(['order_item_ids' => 'Only delivered orders can be returned.']);
});

test('customer can submit a return request', function () {
    $user = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => OrderStatus::Delivered,
    ]);
    $firstItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'quantity' => 1,
        'subtotal' => 15.50,
    ]);
    OrderItem::factory()->create([
        'order_id' => $order->id,
        'quantity' => 1,
        'subtotal' => 8.50,
    ]);

    $response = $this->actingAs($user)
        ->post("/customer/returns/{$order->id}", [
            'reason' => 'Defective product',
            'order_item_ids' => [$firstItem->id],
        ]);

    $response->assertRedirect(route('customer.returns.index'));

    $returnObj = ReturnRequest::where('order_id', $order->id)->first();
    expect($returnObj)->not->toBeNull()
        ->and($returnObj->reason)->toBe('Defective product')
        ->and($returnObj->user_id)->toBe($user->id)
        ->and((float) $returnObj->refund_amount)->toBe(15.50)
        ->and($returnObj->items()->count())->toBe(1);
});
