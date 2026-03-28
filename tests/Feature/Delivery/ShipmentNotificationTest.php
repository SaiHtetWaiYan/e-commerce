<?php

namespace Tests\Feature\Delivery;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\Delivery\ShipmentAssignedNotification;
use App\Services\ShipmentService;
use Illuminate\Support\Facades\Notification;

test('delivery agent receives notification when assigned to shipment', function () {
    Notification::fake();

    $agent = User::factory()->deliveryAgent()->create();
    $order = Order::factory()->create();
    $shipment = Shipment::factory()->create(['order_id' => $order->id]);

    $service = app(ShipmentService::class);
    $service->assignDeliveryAgent($shipment, $agent);

    Notification::assertSentTo($agent, ShipmentAssignedNotification::class, function ($notification) use ($shipment) {
        return $notification->shipment->id === $shipment->id;
    });
});
