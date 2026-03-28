<?php

namespace Tests\Feature\Admin;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;

test('admin can bulk approve pending products', function () {
    $admin = User::factory()->admin()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    $products = Product::factory()->count(3)->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::PendingReview,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.products.review.bulk-approve'), [
        'product_ids' => $products->pluck('id')->toArray(),
    ]);

    $response->assertRedirect(route('admin.products.review.index'));
    $response->assertSessionHas('status', '3 product(s) approved.');

    foreach ($products as $product) {
        expect($product->fresh()->status)->toBe(ProductStatus::Active);
    }
});

test('admin can bulk reject pending products', function () {
    $admin = User::factory()->admin()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    $products = Product::factory()->count(2)->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::PendingReview,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.products.review.bulk-reject'), [
        'product_ids' => $products->pluck('id')->toArray(),
    ]);

    $response->assertRedirect(route('admin.products.review.index'));
    foreach ($products as $product) {
        expect($product->fresh()->status)->toBe(ProductStatus::Rejected);
    }
});

test('vendor can bulk archive their products', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    $products = Product::factory()->count(2)->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::Active,
    ]);

    $response = $this->actingAs($vendor)->post(route('vendor.products.bulk-status'), [
        'product_ids' => $products->pluck('id')->toArray(),
        'action' => 'archive',
    ]);

    $response->assertRedirect(route('vendor.products.index'));
    foreach ($products as $product) {
        expect($product->fresh()->status)->toBe(ProductStatus::Archived);
    }
});

test('vendor cannot bulk update other vendors products', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $otherVendor = User::factory()->vendor()->hasVendorProfile()->create();

    $product = Product::factory()->create([
        'vendor_id' => $otherVendor->id,
        'status' => ProductStatus::Active,
    ]);

    $response = $this->actingAs($vendor)->post(route('vendor.products.bulk-status'), [
        'product_ids' => [$product->id],
        'action' => 'archive',
    ]);

    $response->assertRedirect();
    expect($product->fresh()->status)->toBe(ProductStatus::Active);
});
