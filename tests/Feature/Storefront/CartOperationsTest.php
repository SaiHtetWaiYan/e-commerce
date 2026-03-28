<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

test('can retrieve cart via API', function () {
    $user = User::factory()->create();
    $cart = Cart::create(['user_id' => $user->id, 'session_id' => '123']);

    $response = $this->actingAs($user)
        ->get('/api/cart');

    $response->assertStatus(200);
});

test('can add product to cart via API', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 10, 'base_price' => 100]);

    $response = $this->actingAs($user)
        ->post('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

    $response->assertStatus(200);

    $cart = Cart::where('user_id', $user->id)->first();
    expect($cart)->not->toBeNull()
        ->and($cart->items()->count())->toBe(1)
        ->and($cart->items()->first()->product_id)->toBe($product->id)
        ->and($cart->items()->first()->quantity)->toBe(2);
});

test('can update cart item quantity via API', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 50]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => '123']);
    $item = CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
    ]);

    $response = $this->actingAs($user)
        ->patch("/api/cart/items/{$item->id}", [
            'quantity' => 5,
        ]);

    $response->assertStatus(200);
    expect($item->fresh()->quantity)->toBe(5);
});

test('can remove item from cart via API', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 50]);

    $cart = Cart::create(['user_id' => $user->id, 'session_id' => '123']);
    $item = CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
    ]);

    $response = $this->actingAs($user)
        ->delete("/api/cart/items/{$item->id}");

    $response->assertStatus(200);
    expect(CartItem::find($item->id))->toBeNull();
});

test('cannot remove another users cart item via API', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $product = Product::factory()->create(['stock_quantity' => 50]);

    $cart = Cart::create(['user_id' => $owner->id, 'session_id' => 'owner-session']);
    $item = CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'unit_price' => 10,
    ]);

    $response = $this->actingAs($intruder)
        ->delete("/api/cart/items/{$item->id}");

    $response->assertStatus(403);
    expect(CartItem::find($item->id))->not->toBeNull();
});

test('cart page renders vendor store details without lazy loading', function () {
    $customer = User::factory()->create();
    $vendor = User::factory()->vendor()->hasVendorProfile(['store_name' => 'Vertex Store'])->create();
    $product = Product::factory()->create([
        'vendor_id' => $vendor->id,
        'stock_quantity' => 20,
    ]);

    $cart = Cart::create([
        'user_id' => $customer->id,
        'session_id' => 'cart-session',
    ]);

    CartItem::create([
        'cart_id' => $cart->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 15,
    ]);

    Model::preventLazyLoading();

    try {
        $this->actingAs($customer)
            ->get(route('storefront.cart.index'))
            ->assertSuccessful()
            ->assertSee('Vertex Store');
    } finally {
        Model::preventLazyLoading(false);
    }
});

test('empty cart page only shows active top-level categories', function () {
    $customer = User::factory()->create();
    $category = Category::factory()->create([
        'name' => 'Accessories',
        'sort_order' => 1,
    ]);

    Category::factory()->create([
        'name' => 'Hidden Category',
        'is_active' => false,
    ]);

    Category::factory()->create([
        'name' => 'Child Category',
        'parent_id' => $category->id,
    ]);

    $this->actingAs($customer)
        ->get(route('storefront.cart.index'))
        ->assertSuccessful()
        ->assertSee('Popular Categories')
        ->assertSee('Accessories');
});
