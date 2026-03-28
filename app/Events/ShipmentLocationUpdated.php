<?php

namespace App\Events;

use App\Models\Shipment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ShipmentLocationUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Shipment $shipment) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('orders.'.$this->shipment->order_id.'.shipment'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'shipment.location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'shipment_id' => $this->shipment->id,
            'status' => $this->shipment->status->value,
            'latitude' => $this->shipment->current_latitude,
            'longitude' => $this->shipment->current_longitude,
            'tracking_number' => $this->shipment->tracking_number,
            'updated_at' => $this->shipment->updated_at?->toISOString(),
        ];
    }
}
