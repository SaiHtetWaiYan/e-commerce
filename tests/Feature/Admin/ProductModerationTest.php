<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;

test('admin can view product moderation queue', function () {
    $admin = User::factory()->admin()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();

    Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Needs Review',
        'status' => ProductStatus::PendingReview,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.review.index'))
        ->assertSuccessful()
        ->assertSee('Needs Review');
});

test('admin can approve pending product', function () {
    $admin = User::factory()->admin()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::PendingReview,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.products.review.approve', $product), [
            'comment' => 'Looks good for listing.',
        ])
        ->assertRedirect(route('admin.products.review.index'))
        ->assertSessionHas('status');

    expect($product->fresh()->status)->toBe(ProductStatus::Active);
});

test('admin can reject pending product', function () {
    $admin = User::factory()->admin()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'status' => ProductStatus::PendingReview,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.products.review.reject', $product), [
            'comment' => 'Please improve product photos and description.',
        ])
        ->assertRedirect(route('admin.products.review.index'))
        ->assertSessionHas('status');

    expect($product->fresh()->status)->toBe(ProductStatus::Rejected);
});
