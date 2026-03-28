<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{id}.cart', function ($user, int $id): bool {
    return (int) $user->id === $id;
});

Broadcast::channel('users.{id}.orders', function ($user, int $id): bool {
    return (int) $user->id === $id;
});

Broadcast::channel('vendors.{id}.orders', function ($user, int $id): bool {
    return (int) $user->id === $id;
});

Broadcast::channel('orders.{orderId}.shipment', function ($user, int $orderId): bool {
    $isOwner = $user->orders()->where('id', $orderId)->exists();
    $isVendor = $user->isVendor() && $user->products()->whereHas('orderItems', fn ($query) => $query->where('order_id', $orderId))->exists();

    return $isOwner || $isVendor || $user->isAdmin() || $user->isDeliveryAgent();
});
