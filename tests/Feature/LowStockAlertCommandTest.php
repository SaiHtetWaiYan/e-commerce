<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use App\Notifications\Vendor\LowStockNotification;
use Illuminate\Support\Facades\Notification;

it('sends low stock alerts to vendors with products below threshold', function () {
    Notification::fake();

    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    Product::factory()->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::Active,
        'stock_quantity' => 3,
        'low_stock_threshold' => 10,
    ]);

    $this->artisan('app:send-low-stock-alerts')
        ->assertSuccessful();

    Notification::assertSentTo($vendor, LowStockNotification::class);
});

it('does not send alerts when no products are low stock', function () {
    Notification::fake();

    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    Product::factory()->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::Active,
        'stock_quantity' => 100,
        'low_stock_threshold' => 10,
    ]);

    $this->artisan('app:send-low-stock-alerts')
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('supports dry run mode without sending notifications', function () {
    Notification::fake();

    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    Product::factory()->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::Active,
        'stock_quantity' => 2,
        'low_stock_threshold' => 5,
    ]);

    $this->artisan('app:send-low-stock-alerts --dry-run')
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('supports custom threshold override', function () {
    Notification::fake();

    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    Product::factory()->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::Active,
        'stock_quantity' => 15,
        'low_stock_threshold' => 5,
    ]);

    $this->artisan('app:send-low-stock-alerts --threshold=20')
        ->assertSuccessful();

    Notification::assertSentTo($vendor, LowStockNotification::class);
});
