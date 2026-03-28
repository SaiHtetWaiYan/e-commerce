<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        $channels = [new PrivateChannel('users.'.$this->order->user_id.'.orders')];

        $vendorIds = $this->order->items()->pluck('vendor_id')->unique();
        foreach ($vendorIds as $vendorId) {
            $channels[] = new PrivateChannel('vendors.'.$vendorId.'.orders');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status->value,
            'total' => (float) $this->order->total,
        ];
    }
}
