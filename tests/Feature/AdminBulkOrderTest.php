<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Notifications\Customer\ReviewRequestNotification;
use Illuminate\Support\Facades\Notification;

test('admin can bulk update order statuses', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $orders = Order::factory()->count(3)->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Processing,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.orders.bulk-status'), [
        'order_ids' => $orders->pluck('id')->toArray(),
        'status' => OrderStatus::Shipped->value,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');

    foreach ($orders as $order) {
        expect($order->fresh()->status)->toBe(OrderStatus::Shipped);
    }
});

test('admin bulk deliver sends review request notifications', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $orders = Order::factory()->count(2)->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Shipped,
    ]);

    $this->actingAs($admin)->post(route('admin.orders.bulk-status'), [
        'order_ids' => $orders->pluck('id')->toArray(),
        'status' => OrderStatus::Delivered->value,
    ]);

    Notification::assertSentTo($customer, ReviewRequestNotification::class);
});

test('admin can export orders as CSV', function () {
    $admin = User::factory()->admin()->create();

    Order::factory()->count(2)->create();

    $this->actingAs($admin)
        ->get(route('admin.orders.export'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'text/csv; charset=utf-8');
});
