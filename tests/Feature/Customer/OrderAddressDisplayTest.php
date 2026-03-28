<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

test('customer order details page displays normalized shipping address keys', function () {
    $customer = User::factory()->create();
    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'shipping_address' => [
            'full_name' => 'Jane Buyer',
            'street_address' => '123 New Street',
            'city' => 'Vientiane',
            'state' => 'Vientiane Prefecture',
            'postal_code' => '10000',
            'country' => 'Laos',
            'phone' => '02012345678',
        ],
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
    ]);

    $this->actingAs($customer)
        ->get(route('customer.orders.show', $order))
        ->assertSuccessful()
        ->assertSee('Jane Buyer')
        ->assertSee('123 New Street')
        ->assertSee('10000');
});
