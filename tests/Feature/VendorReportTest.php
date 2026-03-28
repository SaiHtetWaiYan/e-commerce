<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

it('allows vendor to view reports page', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    $this->actingAs($vendor)
        ->get(route('vendor.reports.index'))
        ->assertSuccessful()
        ->assertSee('Sales Reports');
});

it('shows correct revenue for vendor within date range', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $customer = User::factory()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);

    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'created_at' => now()->subDays(5),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $vendor->id,
        'subtotal' => 150.00,
    ]);

    $this->actingAs($vendor)
        ->get(route('vendor.reports.index', ['from' => now()->subDays(10)->format('Y-m-d'), 'to' => now()->format('Y-m-d')]))
        ->assertSuccessful()
        ->assertSee('150.00');
});

it('allows vendor to export sales CSV', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    $this->actingAs($vendor)
        ->get(route('vendor.reports.export'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'text/csv; charset=utf-8');
});

it('prevents non-vendor from accessing reports', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('vendor.reports.index'))
        ->assertForbidden();
});
