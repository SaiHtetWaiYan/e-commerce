<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isVendor() || $user->isDeliveryAgent();
    }

    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($order->user_id === $user->id) {
            return true;
        }

        if ($user->isVendor()) {
            return $order->items()->where('vendor_id', $user->id)->exists();
        }

        if ($user->isDeliveryAgent()) {
            return $order->shipment?->delivery_agent_id === $user->id;
        }

        return false;
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isVendor() && $order->items()->where('vendor_id', $user->id)->exists();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }
}
