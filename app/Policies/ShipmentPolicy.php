<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;

class ShipmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isVendor() || $user->isDeliveryAgent();
    }

    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDeliveryAgent()) {
            return (int) $shipment->delivery_agent_id === $user->id;
        }

        if ($user->isVendor()) {
            return $shipment->order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isDeliveryAgent() && (int) $shipment->delivery_agent_id === $user->id;
    }
}
