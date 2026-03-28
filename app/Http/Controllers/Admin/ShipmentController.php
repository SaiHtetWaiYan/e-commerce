<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function __construct(public ShipmentService $shipmentService) {}

    public function index(): View
    {
        $shipments = Shipment::query()
            ->with(['order.user', 'deliveryAgent'])
            ->latest()
            ->paginate(20);

        $deliveryAgents = User::query()->deliveryAgents()->get();

        return view('admin.shipments.index', compact('shipments', 'deliveryAgents'));
    }

    public function show(Shipment $shipment): View
    {
        $shipment->load(['order.user', 'order.items.product', 'deliveryAgent', 'trackingEvents']);
        $deliveryAgents = User::query()->deliveryAgents()->get();

        return view('admin.shipments.show', compact('shipment', 'deliveryAgents'));
    }

    public function create(): View
    {
        $orders = Order::query()
            ->doesntHave('shipment')
            ->with('user')
            ->latest()
            ->get();

        $deliveryAgents = User::query()->deliveryAgents()->get();

        return view('admin.shipments.create', compact('orders', 'deliveryAgents'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'delivery_agent_id' => ['nullable', 'exists:users,id'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'carrier_name' => ['nullable', 'string', 'max:100'],
            'estimated_delivery_date' => ['nullable', 'date', 'after:today'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Shipment::query()->create([
            'order_id' => $validated['order_id'],
            'delivery_agent_id' => $validated['delivery_agent_id'] ?? null,
            'tracking_number' => $validated['tracking_number'] ?? $this->shipmentService->generateTrackingNumber(),
            'carrier_name' => $validated['carrier_name'] ?? config('marketplace.default_carrier', 'Marketplace Express'),
            'estimated_delivery_date' => $validated['estimated_delivery_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => isset($validated['delivery_agent_id']) ? ShipmentStatus::Assigned : ShipmentStatus::Pending,
        ]);

        return redirect()->route('admin.shipments.index')
            ->with('status', 'Shipment created successfully.');
    }

    public function assign(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'delivery_agent_id' => ['required', 'exists:users,id'],
        ]);

        $agent = User::query()->findOrFail($validated['delivery_agent_id']);
        $this->shipmentService->assignDeliveryAgent($shipment, $agent);

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('status', 'Delivery agent assigned successfully.');
    }

    public function retry(Shipment $shipment): RedirectResponse
    {
        $this->shipmentService->retryDelivery($shipment);

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('status', 'Delivery retry scheduled.');
    }

    public function updateEta(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'estimated_delivery_date' => ['required', 'date', 'after_or_equal:today'],
            'estimated_delivery_time_from' => ['nullable', 'date_format:H:i'],
            'estimated_delivery_time_to' => ['nullable', 'date_format:H:i', 'after:estimated_delivery_time_from'],
        ]);

        $shipment->update([
            'estimated_delivery_date' => $validated['estimated_delivery_date'],
            'estimated_delivery_time_from' => $validated['estimated_delivery_time_from'] ?? null,
            'estimated_delivery_time_to' => $validated['estimated_delivery_time_to'] ?? null,
        ]);

        return redirect()->route('admin.shipments.show', $shipment)
            ->with('status', 'Estimated delivery updated.');
    }
}
