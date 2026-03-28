<?php

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('vendor can view products index', function () {
    $user = User::factory()->vendor()->hasVendorProfile()->create();

    $response = $this->actingAs($user)
        ->get('/vendor/products');

    $response->assertStatus(200);
});

test('vendor can create a product', function () {
    $user = User::factory()->vendor()->hasVendorProfile()->create();
    $brand = Brand::factory()->create();
    $category = \App\Models\Category::factory()->create();

    $response = $this->actingAs($user)
        ->post('/vendor/products', [
            'name' => 'Amazing Product',
            'description' => 'This is a great product.',
            'base_price' => 99.99,
            'stock_quantity' => 50,
            'brand_id' => $brand->id,
            'category_ids' => [$category->id],
            'status' => 'draft',
        ]);

    $product = Product::where('name', 'Amazing Product')->first();
    $response->assertRedirect(route('vendor.products.edit', $product));

    expect($product)->not->toBeNull()
        ->and($product->vendor_id)->toBe($user->id)
        ->and($product->base_price)->toBe('99.99');
});

test('vendor can update their product', function () {
    $user = User::factory()->vendor()->hasVendorProfile()->create();
    $product = Product::factory()->create([
        'vendor_id' => $user->id,
        'name' => 'Old Name',
        'base_price' => 50,
    ]);

    $category = \App\Models\Category::factory()->create();

    $response = $this->actingAs($user)
        ->from("/vendor/products/{$product->id}/edit")
        ->put("/vendor/products/{$product->id}", [
            'name' => 'New Name',
            'description' => 'Updated desc',
            'base_price' => 100,
            'stock_quantity' => 20,
            'category_ids' => [$category->id],
            'status' => 'active',
        ]);

    $response->assertRedirect("/vendor/products/{$product->id}/edit");
    $response->assertSessionHas('status', 'Product updated.');

    expect($product->fresh()->name)->toBe('New Name')
        ->and($product->fresh()->base_price)->toBe('100.00');
});

test('vendor cannot update another vendors product', function () {
    $user1 = User::factory()->vendor()->hasVendorProfile()->create();
    $user2 = User::factory()->vendor()->hasVendorProfile()->create();

    $product = Product::factory()->create([
        'vendor_id' => $user2->id,
    ]);

    $response = $this->actingAs($user1)
        ->put("/vendor/products/{$product->id}", [
            'name' => 'Hacked Name',
            'base_price' => 1,
            'stock_quantity' => 1,
            'status' => 'active',
        ]);

    $response->assertStatus(403);

    expect($product->fresh()->name)->not->toBe('Hacked Name');
});

test('vendor can delete a product', function () {
    Storage::fake('public');

    $user = User::factory()->vendor()->hasVendorProfile()->create();
    $product = Product::factory()->create([
        'vendor_id' => $user->id,
    ]);
    $storedPath = UploadedFile::fake()->image('listing.jpg')->store('products', 'public');

    $product->images()->create([
        'image_path' => Storage::url($storedPath),
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $response = $this->actingAs($user)
        ->delete("/vendor/products/{$product->id}");

    $response->assertRedirect(route('vendor.products.index'));
    expect(Product::where('id', $product->id)->exists())->toBeFalse()
        ->and($product->images()->count())->toBe(0)
        ->and(Storage::disk('public')->exists($storedPath))->toBeFalse();
});

test('vendor image replacement removes old files from storage', function () {
    Storage::fake('public');

    $user = User::factory()->vendor()->hasVendorProfile()->create();
    $category = \App\Models\Category::factory()->create();
    $product = Product::factory()->create([
        'vendor_id' => $user->id,
    ]);
    $oldPath = UploadedFile::fake()->image('old-image.jpg')->store('products', 'public');

    $product->images()->create([
        'image_path' => Storage::url($oldPath),
        'sort_order' => 0,
        'is_primary' => true,
    ]);

    $response = $this->actingAs($user)
        ->from("/vendor/products/{$product->id}/edit")
        ->put("/vendor/products/{$product->id}", [
            'name' => $product->name,
            'description' => $product->description,
            'base_price' => $product->base_price,
            'stock_quantity' => $product->stock_quantity,
            'category_ids' => [$category->id],
            'images' => [UploadedFile::fake()->image('new-image.jpg')],
        ]);

    $response->assertRedirect("/vendor/products/{$product->id}/edit");
    $response->assertSessionHas('status', 'Product updated.');

    expect(Storage::disk('public')->exists($oldPath))->toBeFalse()
        ->and($product->fresh()->images)->toHaveCount(1);
});

test('vendor activation request moves product to pending review', function () {
    $user = User::factory()->vendor()->hasVendorProfile()->create();
    $category = \App\Models\Category::factory()->create();

    $this->actingAs($user)->post('/vendor/products', [
        'name' => 'Moderation Target',
        'description' => 'Needs approval',
        'base_price' => 42.00,
        'stock_quantity' => 10,
        'category_ids' => [$category->id],
        'is_active' => 1,
    ])->assertRedirect();

    $product = Product::query()->where('name', 'Moderation Target')->first();

    expect($product)->not->toBeNull()
        ->and($product->status)->toBe(ProductStatus::PendingReview);
});
