<?php

namespace App\Policies;

use App\Models\ReturnRequest;
use App\Models\User;

class ReturnRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isVendor() || $user->isCustomer();
    }

    public function view(User $user, ReturnRequest $returnRequest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ((int) $returnRequest->user_id === $user->id) {
            return true;
        }

        if ($user->isVendor()) {
            return $returnRequest->order->items()->where('vendor_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return ! $user->isAdmin() && ! $user->isVendor();
    }

    public function update(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->isAdmin();
    }
}
