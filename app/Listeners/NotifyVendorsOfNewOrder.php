<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\VendorNewOrderMail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NotifyVendorsOfNewOrder implements ShouldQueue
{
    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing('items');

        $vendorIds = $order->items->pluck('vendor_id')->unique()->filter();

        foreach ($vendorIds as $vendorId) {
            $vendor = User::query()->find($vendorId);

            if ($vendor?->email) {
                Mail::to($vendor->email)->send(new VendorNewOrderMail($order, $vendor));
            }

            if ($vendor) {
                $vendor->notify(new \App\Notifications\Vendor\NewOrderNotification($order));
            }
        }
    }
}
