<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

test('search results page applies the selected filters', function () {
    $brand = Brand::factory()->create(['name' => 'Apex']);
    $category = Category::factory()->create(['name' => 'Smartphones']);
    $vendor = User::factory()->vendor()->hasVendorProfile([
        'store_name' => 'Apex Mobile',
        'is_verified' => true,
    ])->create();

    $matchingProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'brand_id' => $brand->id,
        'name' => 'Filtered Phone',
        'base_price' => 79.99,
        'avg_rating' => 4.6,
    ]);
    $matchingProduct->categories()->attach($category);

    $excludedProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'brand_id' => $brand->id,
        'name' => 'Budget Phone',
        'base_price' => 39.99,
        'avg_rating' => 2.8,
    ]);
    $excludedProduct->categories()->attach($category);

    $response = $this->get(route('storefront.search.index', [
        'q' => 'Phone',
        'category' => $category->slug,
        'brand' => $brand->slug,
        'min_price' => 70,
        'max_price' => 90,
        'rating' => 4,
        'sort' => 'price_desc',
    ]));

    $response->assertSuccessful()
        ->assertSee('Filters')
        ->assertSee('Filtered Phone')
        ->assertDontSee('Budget Phone');
});

test('search suggestions return ranked metadata for matching products', function () {
    $brand = Brand::factory()->create(['name' => 'Nimbus']);
    $vendor = User::factory()->vendor()->hasVendorProfile([
        'store_name' => 'Exact Match Store',
        'is_verified' => true,
    ])->create();

    $exactMatch = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'brand_id' => $brand->id,
        'name' => 'Alpha Phone',
        'sku' => 'ALPHA-001',
    ]);

    Product::factory()->create([
        'vendor_id' => $vendor->id,
        'brand_id' => $brand->id,
        'name' => 'Alpha Case',
        'sku' => 'ALPHA-CASE',
    ]);

    $response = $this->getJson('/api/search/suggest?q=Alpha%20Phone');

    $response->assertSuccessful()
        ->assertJsonPath('results.0.id', $exactMatch->id)
        ->assertJsonPath('results.0.name', 'Alpha Phone');

    $firstResult = $response->json('results.0');

    expect($firstResult['subtitle'])->toContain('Nimbus')
        ->toContain('Exact Match Store')
        ->toContain('ALPHA-001');
    expect($firstResult['image_url'])->not->toBeEmpty();
});
