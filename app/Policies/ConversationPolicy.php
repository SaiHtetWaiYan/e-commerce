<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return (int) $conversation->buyer_id === $user->id
            || (int) $conversation->vendor_id === $user->id;
    }

    public function create(User $user): bool
    {
        return ! $user->isAdmin() && ! $user->isDeliveryAgent();
    }

    public function reply(User $user, Conversation $conversation): bool
    {
        return (int) $conversation->buyer_id === $user->id
            || (int) $conversation->vendor_id === $user->id;
    }
}
