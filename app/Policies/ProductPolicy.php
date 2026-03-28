<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::Vendor, UserRole::Admin], true);
    }

    public function view(?User $user, Product $product): bool
    {
        if ($product->status->value === 'active') {
            return true;
        }

        if ($user === null) {
            return false;
        }

        return $user->isAdmin() || $product->vendor_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isVendor() || $user->isAdmin();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || $product->vendor_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin() || $product->vendor_id === $user->id;
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->isAdmin() || $product->vendor_id === $user->id;
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }
}
