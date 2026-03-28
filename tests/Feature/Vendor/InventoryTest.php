<?php

namespace Tests\Feature\Vendor;

use App\Models\Product;
use App\Models\User;

test('vendor can view their inventory', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $product = Product::factory()->create(['vendor_id' => $vendor->id]);

    $response = $this->actingAs($vendor)->get(route('vendor.inventory.index'));

    $response->assertStatus(200);
    $response->assertSee($product->name);
});

test('vendor can update their product stock inline', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'stock_quantity' => 10,
        'low_stock_threshold' => 5,
    ]);

    $response = $this->actingAs($vendor)->patch(route('vendor.inventory.update-stock', $product), [
        'stock_quantity' => 20,
        'low_stock_threshold' => 10,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status', "Stock updated for \"{$product->name}\".");

    expect($product->fresh()->stock_quantity)->toBe(20)
        ->and($product->fresh()->low_stock_threshold)->toBe(10);
});

test('vendor cannot update someone elses product stock', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $otherVendor = User::factory()->vendor()->hasVendorProfile()->create();

    $product = Product::factory()->create([
        'vendor_id' => $otherVendor->id,
        'stock_quantity' => 10,
    ]);

    $response = $this->actingAs($vendor)->patch(route('vendor.inventory.update-stock', $product), [
        'stock_quantity' => 50,
        'low_stock_threshold' => 10,
    ]);

    $response->assertStatus(403);
    expect($product->fresh()->stock_quantity)->toBe(10);
});
