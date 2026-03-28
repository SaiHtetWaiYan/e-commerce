<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

test('vendor can update order to fulfillment statuses', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'status' => OrderStatus::Pending->value,
    ]);

    $this->actingAs($vendor)
        ->patch(route('vendor.orders.status', $order), [
            'status' => OrderStatus::Confirmed->value,
        ])
        ->assertSessionHas('status', 'Order status updated.');

    expect($order->fresh()->status)->toBe(OrderStatus::Confirmed)
        ->and($order->items()->first()?->status)->toBe(OrderStatus::Confirmed->value);
});

test('vendor cannot set admin intervention statuses', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'status' => OrderStatus::Pending->value,
    ]);

    $this->actingAs($vendor)
        ->patch(route('vendor.orders.status', $order), [
            'status' => OrderStatus::Cancelled->value,
        ])
        ->assertInvalid(['status']);

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
});

test('admin can set hold status', function () {
    $admin = User::factory()->admin()->create();
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.status', $order), [
            'status' => OrderStatus::Hold->value,
        ])
        ->assertSessionHas('status', 'Order status updated successfully.');

    expect($order->fresh()->status)->toBe(OrderStatus::Hold);
});

test('admin cannot set vendor fulfillment statuses', function () {
    $admin = User::factory()->admin()->create();
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    $this->actingAs($admin)
        ->patch(route('admin.orders.status', $order), [
            'status' => OrderStatus::Shipped->value,
        ])
        ->assertInvalid(['status']);

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
});

test('vendor order page shows refunded status and hides update form for refunded orders', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $customer = User::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);
    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Refunded,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'status' => OrderStatus::Refunded->value,
    ]);

    $this->actingAs($vendor)
        ->get(route('vendor.orders.show', $order))
        ->assertSuccessful()
        ->assertSee('Current Status:')
        ->assertSee('refunded')
        ->assertSee('can no longer be updated by vendor')
        ->assertDontSee('Update Status');
});
