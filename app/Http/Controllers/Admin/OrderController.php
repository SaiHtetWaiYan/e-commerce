<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Notifications\Customer\ReviewRequestNotification;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService) {}

    public function index(Request $request): View
    {
        $query = Order::query()
            ->with(['user', 'items'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search): void {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['user', 'items.product.images', 'shipment.trackingEvents', 'statusHistories']),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $status = OrderStatus::from((string) $request->validated('status'));
        $comment = $request->validated('comment');

        $order->update(['status' => $status]);

        if ($status === OrderStatus::Delivered) {
            $order->update(['delivered_at' => now()]);
            $order->user?->notify(new ReviewRequestNotification($order));
        }

        $order->statusHistories()->create([
            'status' => $status->value,
            'comment' => $comment ?? 'Status updated by admin.',
            'changed_by' => auth()->id(),
            'created_at' => now(),
        ]);

        return back()->with('status', 'Order status updated successfully.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_ids' => ['required', 'array', 'min:1'],
            'order_ids.*' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required', 'string'],
        ]);

        $status = OrderStatus::from($validated['status']);
        $orders = Order::query()->whereIn('id', $validated['order_ids'])->get();
        $updatedCount = 0;

        foreach ($orders as $order) {
            $order->update(['status' => $status]);

            if ($status === OrderStatus::Delivered) {
                $order->update(['delivered_at' => now()]);
                $order->user?->notify(new ReviewRequestNotification($order));
            }

            $order->statusHistories()->create([
                'status' => $status->value,
                'comment' => 'Bulk status update by admin.',
                'changed_by' => auth()->id(),
                'created_at' => now(),
            ]);

            $updatedCount++;
        }

        return back()->with('status', "{$updatedCount} order(s) updated to {$status->value}.");
    }

    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = Order::query()
            ->with(['user', 'items.product'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $orders = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-export-'.now()->format('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($orders): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Date', 'Customer', 'Email', 'Status', 'Payment', 'Items', 'Subtotal', 'Shipping', 'Total']);

            foreach ($orders as $order) {
                $itemCount = $order->items->sum('quantity');

                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user?->name ?? 'Guest',
                    $order->user?->email ?? '',
                    $order->status->value,
                    $order->payment_status->value,
                    $itemCount,
                    number_format((float) $order->subtotal, 2, '.', ''),
                    number_format((float) $order->shipping_fee, 2, '.', ''),
                    number_format((float) $order->total, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function downloadInvoice(Order $order): \Illuminate\Http\Response
    {
        $order->load(['items.product', 'user']);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
