<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $agentId = (int) auth()->id();

        return view('delivery.dashboard', [
            'assignedCount' => Shipment::query()->forAgent($agentId)->count(),
            'inTransitCount' => Shipment::query()->forAgent($agentId)->where('status', 'in_transit')->count(),
            'deliveredCount' => Shipment::query()->forAgent($agentId)->where('status', 'delivered')->count(),
            'shipments' => Shipment::query()->forAgent($agentId)->with('order')->latest()->limit(10)->get(),
        ]);
    }
}
