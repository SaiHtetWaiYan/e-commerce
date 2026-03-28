<?php

namespace App\Notifications\Customer;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order_status',
            'order_id' => $this->order->id,
            'message' => "Order #{$this->order->id} is now {$this->order->status->value}.",
            'url' => route('customer.orders.show', $this->order),
        ];
    }
}
