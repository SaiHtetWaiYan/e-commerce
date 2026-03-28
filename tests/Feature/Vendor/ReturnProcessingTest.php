<?php

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\ReturnRequestItem;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('vendor can approve a fully owned pending return', function () {
    Mail::fake();

    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $customer = User::factory()->create();
    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'stock_quantity' => 5,
    ]);

    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'payment_status' => PaymentStatus::Paid,
        'subtotal' => 45.00,
        'total' => 45.00,
    ]);

    $orderItem = OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'product_name' => $product->name,
        'quantity' => 1,
        'unit_price' => 45.00,
        'subtotal' => 45.00,
    ]);

    $returnRequest = ReturnRequest::factory()->create([
        'order_id' => $order->id,
        'user_id' => $customer->id,
        'status' => ReturnStatus::Pending,
        'refund_amount' => 45.00,
    ]);

    ReturnRequestItem::factory()->create([
        'return_request_id' => $returnRequest->id,
        'order_item_id' => $orderItem->id,
        'quantity' => 1,
        'subtotal' => 45.00,
    ]);

    $response = $this->actingAs($vendor)->patch(route('vendor.returns.approve', $returnRequest), [
        'refund_amount' => 40.00,
        'notes' => 'Approved by vendor.',
    ]);

    $response->assertRedirect()
        ->assertSessionHas('status', 'Return approved successfully.');

    expect($returnRequest->fresh()->status)->toBe(ReturnStatus::Refunded)
        ->and((float) $returnRequest->fresh()->refund_amount)->toBe(40.0)
        ->and($returnRequest->fresh()->admin_notes)->toBe('Approved by vendor.')
        ->and($product->fresh()->stock_quantity)->toBe(6)
        ->and($order->fresh()->status)->toBe(OrderStatus::Refunded)
        ->and($order->fresh()->payment_status)->toBe(PaymentStatus::Refunded);
});

test('vendor can only see relevant returns and cannot process mixed vendor returns', function () {
    Mail::fake();

    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $otherVendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $customer = User::factory()->create();

    $vendorProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Vendor Return Product',
    ]);

    $otherVendorProduct = Product::factory()->create([
        'vendor_id' => $otherVendor->id,
        'name' => 'Other Vendor Product',
    ]);

    $sharedOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'order_number' => 'ORD-SHARED-001',
    ]);

    $vendorItem = OrderItem::factory()->create([
        'order_id' => $sharedOrder->id,
        'product_id' => $vendorProduct->id,
        'vendor_id' => $vendor->id,
        'product_name' => $vendorProduct->name,
        'subtotal' => 25.00,
    ]);

    $otherVendorItem = OrderItem::factory()->create([
        'order_id' => $sharedOrder->id,
        'product_id' => $otherVendorProduct->id,
        'vendor_id' => $otherVendor->id,
        'product_name' => $otherVendorProduct->name,
        'subtotal' => 30.00,
    ]);

    $mixedReturn = ReturnRequest::factory()->create([
        'order_id' => $sharedOrder->id,
        'user_id' => $customer->id,
        'status' => ReturnStatus::Pending,
        'refund_amount' => 55.00,
    ]);

    ReturnRequestItem::factory()->create([
        'return_request_id' => $mixedReturn->id,
        'order_item_id' => $vendorItem->id,
        'subtotal' => 25.00,
    ]);

    ReturnRequestItem::factory()->create([
        'return_request_id' => $mixedReturn->id,
        'order_item_id' => $otherVendorItem->id,
        'subtotal' => 30.00,
    ]);

    $otherOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'order_number' => 'ORD-OTHER-002',
    ]);

    $otherOnlyItem = OrderItem::factory()->create([
        'order_id' => $otherOrder->id,
        'product_id' => $otherVendorProduct->id,
        'vendor_id' => $otherVendor->id,
        'product_name' => $otherVendorProduct->name,
        'subtotal' => 18.00,
    ]);

    $otherOnlyReturn = ReturnRequest::factory()->create([
        'order_id' => $otherOrder->id,
        'user_id' => $customer->id,
        'status' => ReturnStatus::Pending,
        'refund_amount' => 18.00,
    ]);

    ReturnRequestItem::factory()->create([
        'return_request_id' => $otherOnlyReturn->id,
        'order_item_id' => $otherOnlyItem->id,
        'subtotal' => 18.00,
    ]);

    $this->actingAs($vendor)
        ->get(route('vendor.returns.index'))
        ->assertSuccessful()
        ->assertSee('ORD-SHARED-001')
        ->assertDontSee('ORD-OTHER-002');

    $this->actingAs($vendor)
        ->get(route('vendor.returns.show', $mixedReturn))
        ->assertSuccessful()
        ->assertSee('Vendor Return Product')
        ->assertDontSee('Other Vendor Product')
        ->assertSee('handled by an admin');

    $this->actingAs($vendor)
        ->patch(route('vendor.returns.reject', $mixedReturn), [
            'notes' => 'This should not be allowed.',
        ])
        ->assertForbidden();
});
