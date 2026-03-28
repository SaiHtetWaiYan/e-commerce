<?php

use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;

test('delivery agent can see shipping address on shipment details page', function () {
    $deliveryAgent = User::factory()->deliveryAgent()->create();
    $customer = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'shipping_address' => [
            'full_name' => 'Sherlock Holmes',
            'phone' => '+44-20-1111-2222',
            'street_address' => '221B Baker Street',
            'city' => 'London',
            'state' => 'Greater London',
            'postal_code' => 'NW1',
            'country' => 'United Kingdom',
        ],
    ]);

    $shipment = Shipment::query()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $deliveryAgent->id,
        'status' => 'assigned',
    ]);

    $this->actingAs($deliveryAgent)
        ->get(route('delivery.shipments.show', $shipment))
        ->assertSuccessful()
        ->assertSee('Delivery Address')
        ->assertSee('221B Baker Street')
        ->assertSee('Sherlock Holmes');
});

test('delivery page supports legacy shipping address keys', function () {
    $deliveryAgent = User::factory()->deliveryAgent()->create();
    $customer = User::factory()->create();

    $order = Order::factory()->create([
        'user_id' => $customer->id,
        'shipping_address' => [
            'name' => 'Legacy Customer',
            'address_line_1' => '42 Old Road',
            'city' => 'Mandalay',
            'state' => 'Mandalay',
            'zip' => '05011',
            'country' => 'Myanmar',
        ],
    ]);

    $shipment = Shipment::query()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $deliveryAgent->id,
        'status' => 'assigned',
    ]);

    $this->actingAs($deliveryAgent)
        ->get(route('delivery.shipments.show', $shipment))
        ->assertSuccessful()
        ->assertSee('42 Old Road')
        ->assertSee('Legacy Customer');
});
