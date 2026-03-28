<?php

namespace Tests\Feature\Vendor;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\User;

test('vendor can view shipments for their orders', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $order = Order::factory()->create();
    OrderItem::factory()->create(['order_id' => $order->id, 'vendor_id' => $vendor->id]);
    $shipment = Shipment::factory()->create(['order_id' => $order->id]);

    $response = $this->actingAs($vendor)->get(route('vendor.shipments.index'));

    $response->assertSuccessful();
    $response->assertSee($order->order_number);
});

test('vendor can view a single shipment detail', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $order = Order::factory()->create();
    OrderItem::factory()->create(['order_id' => $order->id, 'vendor_id' => $vendor->id]);
    $shipment = Shipment::factory()->create(['order_id' => $order->id]);

    $response = $this->actingAs($vendor)->get(route('vendor.shipments.show', $shipment));

    $response->assertSuccessful();
    $response->assertSee($order->order_number);
});

test('vendor cannot view shipment for another vendors order', function () {
    $vendor = User::factory()->vendor()->hasVendorProfile()->create();
    $otherVendor = User::factory()->vendor()->hasVendorProfile()->create();
    $order = Order::factory()->create();
    OrderItem::factory()->create(['order_id' => $order->id, 'vendor_id' => $otherVendor->id]);
    $shipment = Shipment::factory()->create(['order_id' => $order->id]);

    $response = $this->actingAs($vendor)->get(route('vendor.shipments.show', $shipment));

    $response->assertForbidden();
});
