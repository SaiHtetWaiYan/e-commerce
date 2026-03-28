<?php

namespace App\Http\Controllers\Delivery;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ShipmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\DeliveryProofRequest;
use App\Http\Requests\Delivery\UpdateShipmentStatusRequest;
use App\Models\Shipment;
use App\Services\PaymentService;
use App\Services\ShipmentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShipmentController extends Controller
{
    public function __construct(public ShipmentService $shipmentService, public PaymentService $paymentService) {}

    public function index(): View
    {
        $shipments = Shipment::query()
            ->forAgent((int) auth()->id())
            ->with(['order.user'])
            ->latest()
            ->paginate(20);

        return view('delivery.shipments.index', ['shipments' => $shipments]);
    }

    public function show(Shipment $shipment): View
    {
        abort_unless((int) $shipment->delivery_agent_id === (int) auth()->id(), 403);

        return view('delivery.shipments.show', [
            'shipment' => $shipment->load(['order.user', 'trackingEvents']),
        ]);
    }

    public function update(UpdateShipmentStatusRequest $request, Shipment $shipment): RedirectResponse
    {
        abort_unless((int) $shipment->delivery_agent_id === (int) auth()->id(), 403);

        $status = ShipmentStatus::from((string) $request->validated('status'));
        $order = $shipment->order;
        $paymentMethod = $this->paymentService->normalizePaymentMethod($order?->payment_method);
        $isCodOrder = $paymentMethod === 'cod';
        $requiresCodCollection = $status === ShipmentStatus::Delivered
            && $order !== null
            && $isCodOrder
            && $order->payment_status !== PaymentStatus::Paid;

        if ($status === ShipmentStatus::Delivered && $shipment->delivery_proof_image === null) {
            return back()->withErrors([
                'status' => 'Upload delivery proof before marking this shipment as delivered.',
            ]);
        }

        if ($requiresCodCollection && ! $request->boolean('cash_collected')) {
            return back()->withErrors([
                'cash_collected' => 'Confirm that cash has been collected before completing COD delivery.',
            ])->withInput();
        }

        $this->shipmentService->updateStatus($shipment, $status, $request->validated('description'));

        if ($request->filled('latitude') && $request->filled('longitude')) {
            $this->shipmentService->updateLocation($shipment, [
                'latitude' => (float) $request->validated('latitude'),
                'longitude' => (float) $request->validated('longitude'),
                'location' => $request->validated('location'),
                'description' => $request->validated('description') ?? 'Location updated',
            ]);
        }

        if ($order !== null) {
            $resolvedOrderStatus = $this->resolveOrderStatusFromShipmentStatus($status);
            $isOrderStatusChanged = $resolvedOrderStatus !== null && $order->status !== $resolvedOrderStatus;

            if ($resolvedOrderStatus !== null) {
                $order->forceFill([
                    'status' => $resolvedOrderStatus,
                    'shipped_at' => $resolvedOrderStatus === OrderStatus::Shipped && $order->shipped_at === null ? now() : $order->shipped_at,
                    'delivered_at' => $resolvedOrderStatus === OrderStatus::Delivered && $order->delivered_at === null ? now() : $order->delivered_at,
                ])->save();
            }

            if ($status === ShipmentStatus::Delivered && $isCodOrder) {
                $this->paymentService->confirmCashOnDelivery($order);
            }

            if ($isOrderStatusChanged && $resolvedOrderStatus !== null) {
                $order->statusHistories()->create([
                    'status' => $resolvedOrderStatus->value,
                    'comment' => $request->validated('description') ?: 'Order status updated from delivery tracking.',
                    'changed_by' => auth()->id(),
                    'created_at' => now(),
                ]);
            }
        }

        return back()->with('status', 'Shipment updated.');
    }

    public function uploadProof(DeliveryProofRequest $request, Shipment $shipment): RedirectResponse
    {
        abort_unless((int) $shipment->delivery_agent_id === (int) auth()->id(), 403);

        $validated = $request->validated();
        $proofPath = $request->file('proof_image')->store('shipment-proofs', 'public');

        $proofNotes = collect([
            'Recipient: '.(string) $validated['recipient_name'],
            ! empty($validated['recipient_phone']) ? 'Phone: '.(string) $validated['recipient_phone'] : null,
            ! empty($validated['notes']) ? 'Notes: '.(string) $validated['notes'] : null,
        ])->filter()->implode(' | ');

        $shipment->update([
            'delivery_proof_image' => $proofPath,
            'notes' => trim(collect([$shipment->notes, $proofNotes])->filter()->implode("\n")),
        ]);

        $shipment->trackingEvents()->create([
            'status' => $shipment->status->value,
            'description' => 'Delivery proof uploaded.',
            'event_at' => now(),
            'created_at' => now(),
        ]);

        return back()->with('status', 'Delivery proof uploaded.');
    }

    private function resolveOrderStatusFromShipmentStatus(ShipmentStatus $shipmentStatus): ?OrderStatus
    {
        return match ($shipmentStatus) {
            ShipmentStatus::PickedUp, ShipmentStatus::InTransit => OrderStatus::Shipped,
            ShipmentStatus::Delivered => OrderStatus::Delivered,
            default => null,
        };
    }
}
