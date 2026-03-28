<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Events\ShipmentLocationUpdated;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\Delivery\ShipmentAssignedNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    public function generateTrackingNumber(): string
    {
        $prefix = (string) config('marketplace.tracking_prefix', 'TRK');

        return $prefix . '-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function assignDeliveryAgent(Shipment $shipment, User $deliveryAgent): Shipment
    {
        $previousAgentId = $shipment->delivery_agent_id;

        $shipment->update([
            'delivery_agent_id' => $deliveryAgent->id,
            'status' => ShipmentStatus::Assigned,
        ]);

        $shipment->trackingEvents()->create([
            'status' => ShipmentStatus::Assigned->value,
            'description' => $previousAgentId ? 'Delivery agent reassigned.' : 'Delivery agent assigned.',
            'event_at' => now(),
            'created_at' => now(),
        ]);

        $hydratedShipment = $this->loadShipmentRelations($shipment);

        $deliveryAgent->notify(new ShipmentAssignedNotification($hydratedShipment));

        return $hydratedShipment;
    }

    public function updateStatus(Shipment $shipment, ShipmentStatus $status, ?string $description = null): Shipment
    {
        return DB::transaction(function () use ($shipment, $status, $description): Shipment {
            $shipment->update([
                'status' => $status,
                'shipped_at' => $status === ShipmentStatus::InTransit ? now() : $shipment->shipped_at,
                'delivered_at' => $status === ShipmentStatus::Delivered ? now() : $shipment->delivered_at,
            ]);

            $shipment->trackingEvents()->create([
                'status' => $status->value,
                'description' => $description,
                'event_at' => now(),
                'created_at' => now(),
            ]);

            $hydratedShipment = $this->loadShipmentRelations($shipment);

            ShipmentLocationUpdated::dispatch($hydratedShipment);

            return $hydratedShipment;
        });
    }

    public function retryDelivery(Shipment $shipment): Shipment
    {
        return DB::transaction(function () use ($shipment): Shipment {
            $shipment->update(['status' => ShipmentStatus::Assigned]);

            $shipment->trackingEvents()->create([
                'status' => ShipmentStatus::Assigned->value,
                'description' => 'Delivery reattempt scheduled.',
                'event_at' => now(),
                'created_at' => now(),
            ]);

            $hydratedShipment = $this->loadShipmentRelations($shipment);

            if ($hydratedShipment->deliveryAgent) {
                $hydratedShipment->deliveryAgent->notify(new ShipmentAssignedNotification($hydratedShipment));
            }

            return $hydratedShipment;
        });
    }

    public function updateLocation(Shipment $shipment, array $payload): Shipment
    {
        return DB::transaction(function () use ($shipment, $payload): Shipment {
            $shipment->update([
                'current_latitude' => Arr::get($payload, 'latitude'),
                'current_longitude' => Arr::get($payload, 'longitude'),
            ]);

            $shipment->trackingEvents()->create([
                'status' => $shipment->status->value,
                'description' => Arr::get($payload, 'description'),
                'location' => Arr::get($payload, 'location'),
                'latitude' => Arr::get($payload, 'latitude'),
                'longitude' => Arr::get($payload, 'longitude'),
                'event_at' => now(),
                'created_at' => now(),
            ]);

            $hydratedShipment = $this->loadShipmentRelations($shipment);

            ShipmentLocationUpdated::dispatch($hydratedShipment);

            return $hydratedShipment;
        });
    }

    protected function loadShipmentRelations(Shipment $shipment): Shipment
    {
        foreach (['order', 'deliveryAgent', 'trackingEvents'] as $relation) {
            $shipment->unsetRelation($relation);
        }

        return $shipment->loadMissing(['order', 'deliveryAgent', 'trackingEvents']);
    }
}
