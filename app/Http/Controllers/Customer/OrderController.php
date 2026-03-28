<?php

namespace App\Http\Controllers\Customer;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService) {}

    public function index(Request $request): View
    {
        $query = Order::query()
            ->forUser((int) auth()->id())
            ->with(['items', 'shipment'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search): void {
                $q->where('order_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('customer.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        return view('customer.orders.show', [
            'order' => $order->load(['items.product.images', 'shipment.trackingEvents']),
        ]);
    }

    public function track(Order $order): View
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        return view('customer.orders.tracking', [
            'order' => $order->load(['shipment.trackingEvents', 'shipment.deliveryAgent']),
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);
        abort_unless($order->canBeCancelled(), 403, 'This order cannot be cancelled.');

        $this->orderService->cancelOrder($order, $request->input('reason'));

        return redirect()->route('customer.orders.show', $order)
            ->with('status', 'Order has been cancelled successfully.');
    }

    public function downloadInvoice(Order $order): \Illuminate\Http\Response
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        $order->load(['items.product', 'user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function reorder(Order $order, CartService $cartService): RedirectResponse
    {
        abort_unless((int) $order->user_id === (int) auth()->id(), 403);

        $order->load(['items.product', 'items.variant']);
        $addedCount = 0;

        foreach ($order->items as $item) {
            if ($item->product === null || $item->product->status !== ProductStatus::Active) {
                continue;
            }

            $availableStock = $item->variant?->stock_quantity ?? $item->product->stock_quantity;

            if ($availableStock <= 0) {
                continue;
            }

            $quantity = min($item->quantity, $availableStock);

            try {
                $cartService->addItem(
                    auth()->user(),
                    session()->getId(),
                    (int) $item->product_id,
                    $item->variant_id !== null ? (int) $item->variant_id : null,
                    (int) $quantity,
                );
                $addedCount++;
            } catch (InvalidArgumentException) {
                continue;
            }
        }

        $statusMessage = $addedCount > 0
            ? 'Available items from this order have been added to your cart.'
            : 'No available items could be added from this order.';

        return redirect()->route('storefront.cart.index')
            ->with('status', $statusMessage);
    }
}
