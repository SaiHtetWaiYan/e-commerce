<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Contracts\View\View;

class ShipmentController extends Controller
{
    public function index(): View
    {
        $vendorId = (int) auth()->id();

        $shipments = Shipment::query()
            ->whereHas('order.items', fn ($query) => $query->where('vendor_id', $vendorId))
            ->with(['order.user', 'deliveryAgent'])
            ->latest()
            ->paginate(20);

        return view('vendor.shipments.index', ['shipments' => $shipments]);
    }

    public function show(Shipment $shipment): View
    {
        $vendorId = (int) auth()->id();

        abort_unless(
            $shipment->order->items()->where('vendor_id', $vendorId)->exists(),
            403
        );

        $shipment->load([
            'order.user',
            'order.items' => fn ($query) => $query->where('vendor_id', $vendorId)->with('product'),
            'deliveryAgent',
            'trackingEvents',
        ]);

        return view('vendor.shipments.show', ['shipment' => $shipment]);
    }
}
