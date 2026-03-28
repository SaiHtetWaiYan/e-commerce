<?php

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use App\Models\User;

test('admin dashboard total revenue is reduced by partial refunds', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $deliveredOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'total' => 100.00,
    ]);

    $fullyRefundedOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Refunded,
        'total' => 60.00,
    ]);

    ReturnRequest::query()->create([
        'order_id' => $deliveredOrder->id,
        'user_id' => $customer->id,
        'reason' => 'Partial refund for damaged item.',
        'status' => ReturnStatus::Refunded,
        'refund_amount' => 20.00,
    ]);

    ReturnRequest::query()->create([
        'order_id' => $fullyRefundedOrder->id,
        'user_id' => $customer->id,
        'reason' => 'Full refund.',
        'status' => ReturnStatus::Refunded,
        'refund_amount' => 60.00,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Total Revenue')
        ->assertSee('$80.00');
});

test('admin reports total revenue is reduced by partial refunds', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    $deliveredOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'total' => 200.00,
    ]);

    ReturnRequest::query()->create([
        'order_id' => $deliveredOrder->id,
        'user_id' => $customer->id,
        'reason' => 'Partial refund requested.',
        'status' => ReturnStatus::Refunded,
        'refund_amount' => 50.00,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertSuccessful()
        ->assertSee('Total Revenue')
        ->assertSee('$150.00');
});

test('vendor dashboard total revenue is reduced by refunded return items', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $customer = User::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);

    $deliveredOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'total' => 100.00,
    ]);

    $deliveredOrderItem = OrderItem::factory()->create([
        'order_id' => $deliveredOrder->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'quantity' => 1,
        'unit_price' => 100.00,
        'subtotal' => 100.00,
    ]);

    $partialRefundRequest = ReturnRequest::query()->create([
        'order_id' => $deliveredOrder->id,
        'user_id' => $customer->id,
        'reason' => 'Partial refund for missing accessory.',
        'status' => ReturnStatus::Refunded,
        'refund_amount' => 25.00,
    ]);

    ReturnRequestItem::query()->create([
        'return_request_id' => $partialRefundRequest->id,
        'order_item_id' => $deliveredOrderItem->id,
        'quantity' => 1,
        'subtotal' => 25.00,
    ]);

    $fullyRefundedOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Refunded,
        'total' => 40.00,
    ]);

    $fullyRefundedOrderItem = OrderItem::factory()->create([
        'order_id' => $fullyRefundedOrder->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'quantity' => 1,
        'unit_price' => 40.00,
        'subtotal' => 40.00,
    ]);

    $fullRefundRequest = ReturnRequest::query()->create([
        'order_id' => $fullyRefundedOrder->id,
        'user_id' => $customer->id,
        'reason' => 'Full refund.',
        'status' => ReturnStatus::Refunded,
        'refund_amount' => 40.00,
    ]);

    ReturnRequestItem::query()->create([
        'return_request_id' => $fullRefundRequest->id,
        'order_item_id' => $fullyRefundedOrderItem->id,
        'quantity' => 1,
        'subtotal' => 40.00,
    ]);

    $this->actingAs($vendor)
        ->get(route('vendor.dashboard'))
        ->assertSuccessful()
        ->assertSee('Total Revenue')
        ->assertSee('$75.00');
});
