<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;

test('admin reports can be filtered by date range', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile([
        'store_name' => 'Reporting Vendor',
        'is_verified' => true,
    ])->create();

    $oldProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Old Report Product',
    ]);

    $inRangeProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'In Range Report Product',
    ]);

    $oldOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'order_number' => 'ORD-OLD-001',
        'subtotal' => 111.11,
        'total' => 111.11,
    ]);
    $oldOrder->forceFill([
        'created_at' => CarbonImmutable::parse('2025-12-20 10:00:00'),
        'updated_at' => CarbonImmutable::parse('2025-12-20 10:00:00'),
    ])->saveQuietly();

    OrderItem::factory()->create([
        'order_id' => $oldOrder->id,
        'product_id' => $oldProduct->id,
        'vendor_id' => $vendor->id,
        'product_name' => $oldProduct->name,
        'quantity' => 1,
        'unit_price' => 111.11,
        'subtotal' => 111.11,
    ]);

    $inRangeOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'order_number' => 'ORD-INRANGE-002',
        'subtotal' => 222.22,
        'total' => 222.22,
    ]);
    $inRangeOrder->forceFill([
        'created_at' => CarbonImmutable::parse('2026-01-15 12:00:00'),
        'updated_at' => CarbonImmutable::parse('2026-01-15 12:00:00'),
    ])->saveQuietly();

    OrderItem::factory()->create([
        'order_id' => $inRangeOrder->id,
        'product_id' => $inRangeProduct->id,
        'vendor_id' => $vendor->id,
        'product_name' => $inRangeProduct->name,
        'quantity' => 2,
        'unit_price' => 111.11,
        'subtotal' => 222.22,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reports.index', [
            'start_date' => '2026-01-10',
            'end_date' => '2026-01-20',
        ]))
        ->assertSuccessful()
        ->assertSee('$222.22')
        ->assertSee('ORD-INRANGE-002')
        ->assertDontSee('ORD-OLD-001')
        ->assertSee('In Range Report Product')
        ->assertDontSee('Old Report Product');
});

test('admin report csv export respects the selected date range', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile([
        'store_name' => 'CSV Vendor',
        'is_verified' => true,
    ])->create();

    $filteredProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Filtered Export Product',
    ]);

    $ignoredProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Ignored Export Product',
    ]);

    $ignoredOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'total' => 50.00,
    ]);
    $ignoredOrder->forceFill([
        'created_at' => CarbonImmutable::parse('2025-12-01 08:00:00'),
        'updated_at' => CarbonImmutable::parse('2025-12-01 08:00:00'),
    ])->saveQuietly();

    OrderItem::factory()->create([
        'order_id' => $ignoredOrder->id,
        'product_id' => $ignoredProduct->id,
        'vendor_id' => $vendor->id,
        'product_name' => $ignoredProduct->name,
        'quantity' => 1,
        'unit_price' => 50.00,
        'subtotal' => 50.00,
    ]);

    $filteredOrder = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
        'total' => 222.22,
    ]);
    $filteredOrder->forceFill([
        'created_at' => CarbonImmutable::parse('2026-01-16 09:30:00'),
        'updated_at' => CarbonImmutable::parse('2026-01-16 09:30:00'),
    ])->saveQuietly();

    OrderItem::factory()->create([
        'order_id' => $filteredOrder->id,
        'product_id' => $filteredProduct->id,
        'vendor_id' => $vendor->id,
        'product_name' => $filteredProduct->name,
        'quantity' => 2,
        'unit_price' => 111.11,
        'subtotal' => 222.22,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.reports.export', [
        'start_date' => '2026-01-10',
        'end_date' => '2026-01-20',
    ]));

    $handle = fopen('php://temp', 'r+');
    fputcsv($handle, ['Product', 'Quantity Sold', 'Revenue']);
    fputcsv($handle, ['Filtered Export Product', 2, '222.22']);
    rewind($handle);
    $expectedCsv = stream_get_contents($handle);
    fclose($handle);

    $response->assertStreamed()
        ->assertStreamedContent($expectedCsv);

    expect($response->headers->get('content-disposition'))->toContain('report-2026-01-10-to-2026-01-20.csv');
});
