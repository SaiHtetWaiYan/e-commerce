<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing(['user', 'items']);

        if ($order->user?->email) {
            Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
        }
    }
}
