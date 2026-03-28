<?php

namespace Tests\Feature\Customer;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

test('customer can view their disputes list', function () {
    $customer = User::factory()->create();

    $response = $this->actingAs($customer)->get(route('customer.disputes.index'));

    $response->assertSuccessful();
});

test('customer can view the dispute creation form', function () {
    $customer = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $customer->id]);

    $response = $this->actingAs($customer)->get(route('customer.disputes.create', $order));

    $response->assertSuccessful();
    $response->assertSee($order->order_number);
});

test('customer can submit a dispute', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $customer = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $customer->id]);
    OrderItem::factory()->create(['order_id' => $order->id, 'vendor_id' => $vendor->id]);

    $response = $this->actingAs($customer)->post(route('customer.disputes.store'), [
        'order_id' => $order->id,
        'subject' => 'Item not delivered',
        'description' => 'I have not received my order after 2 weeks.',
    ]);

    $response->assertRedirect(route('customer.disputes.index'));
    $this->assertDatabaseHas('disputes', [
        'order_id' => $order->id,
        'complainant_id' => $customer->id,
        'subject' => 'Item not delivered',
    ]);
});

test('customer cannot file dispute on someone elses order', function () {
    $customer = User::factory()->create();
    $otherCustomer = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $otherCustomer->id]);

    $response = $this->actingAs($customer)->get(route('customer.disputes.create', $order));

    $response->assertForbidden();
});
