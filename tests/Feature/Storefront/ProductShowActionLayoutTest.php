<?php

use App\Models\Product;
use App\Models\User;

test('product details page shows quantity and action controls', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile(['is_verified' => true])->create();
    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'stock_quantity' => 268,
    ]);

    $this->get(route('storefront.products.show', $product->slug))
        ->assertSuccessful()
        ->assertSee('Quantity')
        ->assertSee('Add to Cart')
        ->assertSee('Buy Now')
        ->assertSee('pieces available');
});
