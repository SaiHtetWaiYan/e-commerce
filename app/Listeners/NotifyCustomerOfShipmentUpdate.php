<?php

namespace App\Listeners;

use App\Events\ShipmentLocationUpdated;
use App\Mail\OrderStatusMail;
use App\Notifications\Customer\OrderStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyCustomerOfShipmentUpdate implements ShouldQueue
{
    public function handle(ShipmentLocationUpdated $event): void
    {
        $shipment = $event->shipment;
        $order = $shipment->order;

        if (! $order?->user) {
            return;
        }

        $customer = $order->user;

        $statusMessages = [
            'picked_up' => 'Your order has been picked up and is being prepared for delivery!',
            'in_transit' => 'Your order is out for delivery!',
            'delivered' => 'Your order has been delivered! Thank you for shopping with us.',
            'failed' => 'We were unable to deliver your order. We will retry delivery soon.',
        ];

        $message = $statusMessages[$shipment->status->value] ?? null;

        if (! $message) {
            return;
        }

        if ($customer->email) {
            Mail::to($customer->email)->send(new OrderStatusMail($order, $message));
        }

        $customer->notify(new OrderStatusUpdatedNotification($order));
    }
}
