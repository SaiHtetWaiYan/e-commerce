<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Review $review): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! $user->isAdmin() && ! $user->isVendor() && ! $user->isDeliveryAgent();
    }

    public function update(User $user, Review $review): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $review->user_id === $user->id;
    }

    public function delete(User $user, Review $review): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return (int) $review->user_id === $user->id;
    }
}
