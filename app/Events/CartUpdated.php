<?php

namespace App\Events;

use App\Models\Cart;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CartUpdated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Cart $cart) {}

    public function broadcastOn(): array
    {
        if ($this->cart->user_id !== null) {
            return [new PrivateChannel('users.'.$this->cart->user_id.'.cart')];
        }

        return [new Channel('carts.session.'.$this->cart->session_id)];
    }

    public function broadcastAs(): string
    {
        return 'cart.updated';
    }

    public function broadcastWith(): array
    {
        $cart = $this->cart->loadMissing(['items.product', 'items.variant']);

        return [
            'cart_id' => $cart->id,
            'items_count' => $cart->items->sum('quantity'),
            'subtotal' => (float) $cart->items->sum(fn ($item): float => (float) $item->unit_price * $item->quantity),
        ];
    }
}
