<?php

use App\Enums\OrderStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

test('customer can reorder available items into cart', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create([
        'stock_quantity' => 8,
    ]);

    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'status' => OrderStatus::Delivered,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'vendor_id' => $product->vendor_id,
        'quantity' => 3,
        'product_name' => $product->name,
        'subtotal' => 30,
        'status' => OrderStatus::Delivered->value,
    ]);

    $this->actingAs($customer)
        ->post(route('customer.orders.reorder', $order))
        ->assertRedirect(route('storefront.cart.index'));

    $cart = Cart::query()->where('user_id', $customer->id)->first();

    expect($cart)->not->toBeNull()
        ->and($cart->items()->count())->toBe(1)
        ->and((int) $cart->items()->first()->quantity)->toBe(3);
});
