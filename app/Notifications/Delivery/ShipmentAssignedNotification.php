<?php

namespace App\Notifications\Delivery;

use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShipmentAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Shipment $shipment) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Shipment Assigned — #' . $this->shipment->tracking_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new shipment has been assigned to you.')
            ->line('Order: ' . $this->shipment->order->order_number)
            ->line('Tracking: ' . $this->shipment->tracking_number)
            ->line('Estimated Delivery: ' . ($this->shipment->estimated_delivery_date?->format('M d, Y') ?? 'N/A'))
            ->action('View Shipment Details', route('delivery.shipments.show', $this->shipment))
            ->line('Please pick up the package at the earliest convenience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shipment_assigned',
            'shipment_id' => $this->shipment->id,
            'order_number' => $this->shipment->order->order_number,
            'message' => "New shipment assigned: Order #{$this->shipment->order->order_number}",
            'url' => route('delivery.shipments.show', $this->shipment),
        ];
    }
}
