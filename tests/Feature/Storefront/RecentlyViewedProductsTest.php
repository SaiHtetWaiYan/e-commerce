<?php

use App\Models\Product;
use App\Models\User;
use App\Services\RecentlyViewedProductsService;

test('viewed products are remembered and shown on the homepage', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile([
        'store_name' => 'Viewed Store',
        'is_verified' => true,
    ])->create();

    $firstProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Viewed Product One',
    ]);

    $secondProduct = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'name' => 'Viewed Product Two',
    ]);

    $this->get(route('storefront.products.show', $firstProduct->slug))->assertSuccessful();

    $secondResponse = $this->get(route('storefront.products.show', $secondProduct->slug));

    $secondResponse->assertSuccessful()
        ->assertSessionHas(RecentlyViewedProductsService::SESSION_KEY, [
            $secondProduct->id,
            $firstProduct->id,
        ]);

    $this->get(route('storefront.home'))
        ->assertSuccessful()
        ->assertSee('Recently Viewed')
        ->assertSee('Viewed Product One')
        ->assertSee('Viewed Product Two');
});
