<?php

namespace Tests\Feature\Delivery;

use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\Delivery\ShipmentAssignedNotification;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\Notification;

test('tracking number is auto-generated with correct format', function () {
    $service = app(ShipmentService::class);
    $trackingNumber = $service->generateTrackingNumber();

    expect($trackingNumber)->toStartWith('TRK-')
        ->and($trackingNumber)->toMatch('/^TRK-\d{8}-\d{6}$/');
});

test('retry delivery resets failed shipment to assigned', function () {
    Notification::fake();

    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create();
    $shipment = Shipment::factory()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $agent->id,
        'status' => ShipmentStatus::Failed,
    ]);

    $service = app(ShipmentService::class);
    $result = $service->retryDelivery($shipment);

    expect($result->status)->toBe(ShipmentStatus::Assigned);
    Notification::assertSentTo($agent, ShipmentAssignedNotification::class);
});

test('admin can retry failed delivery', function () {
    $admin = User::factory()->admin()->create();
    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create();
    $shipment = Shipment::factory()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $agent->id,
        'status' => ShipmentStatus::Failed,
    ]);

    Notification::fake();
    $response = $this->actingAs($admin)->patch(route('admin.shipments.retry', $shipment));

    $response->assertRedirect(route('admin.shipments.show', $shipment));
    expect($shipment->fresh()->status)->toBe(ShipmentStatus::Assigned);
});

test('admin can update shipment ETA with time window', function () {
    $admin = User::factory()->admin()->create();
    $order = Order::factory()->create();
    $shipment = Shipment::factory()->create(['order_id' => $order->id]);

    $response = $this->actingAs($admin)->patch(route('admin.shipments.eta', $shipment), [
        'estimated_delivery_date' => now()->addDays(3)->format('Y-m-d'),
        'estimated_delivery_time_from' => '14:00',
        'estimated_delivery_time_to' => '17:00',
    ]);

    $response->assertRedirect(route('admin.shipments.show', $shipment));
    expect($shipment->fresh()->estimated_delivery_time_from)->toBe('14:00:00')
        ->and($shipment->fresh()->estimated_delivery_time_to)->toBe('17:00:00');
});

test('reassignment creates tracking event and notifies new agent', function () {
    Notification::fake();

    $agent1 = User::factory()->deliveryAgent()->create();
    $agent2 = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create();
    $shipment = Shipment::factory()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => $agent1->id,
        'status' => ShipmentStatus::Assigned,
    ]);

    $service = app(ShipmentService::class);
    $result = $service->assignDeliveryAgent($shipment, $agent2);

    expect($result->delivery_agent_id)->toBe($agent2->id);
    Notification::assertSentTo($agent2, ShipmentAssignedNotification::class);

    $latestEvent = $result->trackingEvents->first();
    expect($latestEvent->description)->toBe('Delivery agent reassigned.');
});

test('initial assignment creates tracking event with assigned description', function () {
    Notification::fake();

    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create();
    $shipment = Shipment::factory()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => null,
        'status' => ShipmentStatus::Pending,
    ]);

    $service = app(ShipmentService::class);
    $result = $service->assignDeliveryAgent($shipment, $agent);

    $latestEvent = $result->trackingEvents->first();
    expect($latestEvent->description)->toBe('Delivery agent assigned.');
});

test('assignment reloads preloaded tracking relations before notifying', function () {
    Notification::fake();

    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create();
    $shipment = Shipment::factory()->create([
        'order_id' => $order->id,
        'delivery_agent_id' => null,
        'status' => ShipmentStatus::Pending,
    ]);

    $shipment->load(['order', 'deliveryAgent', 'trackingEvents']);

    $result = app(ShipmentService::class)->assignDeliveryAgent($shipment, $agent);

    expect($result->trackingEvents)->toHaveCount(1)
        ->and($result->trackingEvents->first()->description)->toBe('Delivery agent assigned.');
});
