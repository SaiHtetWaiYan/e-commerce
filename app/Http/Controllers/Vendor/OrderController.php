<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\UpdateOrderStatusRequest;
use App\Mail\OrderStatusMail;
use App\Models\Order;
use App\Notifications\Customer\ReviewRequestNotification;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct(public OrderService $orderService) {}

    public function index(Request $request): View
    {
        $query = Order::query()
            ->forVendor((int) auth()->id())
            ->with(['user', 'items' => fn ($query) => $query->forVendor((int) auth()->id())]);

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by Order Number or Customer Information
        if ($request->filled('q')) {
            $search = strtolower(trim((string) $request->input('q')));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(order_number) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        // Date Filters
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('vendor.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        return view('vendor.orders.show', [
            'order' => $order->load([
                'user',
                'items' => fn ($query) => $query->forVendor((int) auth()->id())->with('product.images'),
                'shipment',
            ]),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $vendorId = (int) auth()->id();
        $status = OrderStatus::from((string) $request->validated('status'));

        $order->items()
            ->forVendor($vendorId)
            ->update([
                'status' => $status->value,
                'updated_at' => now(),
            ]);

        $order = $this->orderService->synchronizeStatusFromItems($order);

        $order->statusHistories()->create([
            'status' => $status->value,
            'comment' => $request->validated('comment'),
            'changed_by' => $vendorId,
            'created_at' => now(),
        ]);

        if ($order->user?->email) {
            $statusMessages = [
                'shipped' => 'Your order has been shipped and is on its way!',
                'delivered' => 'Your order has been delivered. We hope you enjoy your purchase!',
                'confirmed' => 'Your order has been confirmed by the seller.',
                'processing' => 'Your order is now being prepared for shipment.',
            ];

            $message = $statusMessages[$status->value] ?? 'Your order status has been updated to: '.str_replace('_', ' ', $status->value);

            Mail::to($order->user->email)->send(new OrderStatusMail($order, $message));
            $order->user->notify(new \App\Notifications\Customer\OrderStatusUpdatedNotification($order));

            if ($status === OrderStatus::Delivered) {
                $order->user->notify(new ReviewRequestNotification($order));
            }
        }

        return back()->with('status', 'Order status updated.');
    }

    public function downloadInvoice(Order $order): \Illuminate\Http\Response
    {
        $this->authorize('view', $order);

        // Load items specific to this vendor to only show their products on the invoice
        $order->load([
            'user',
            'items' => fn ($query) => $query->forVendor((int) auth()->id())->with('product'),
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $query = Order::query()
            ->forVendor((int) auth()->id())
            ->with(['user', 'items' => fn ($query) => $query->forVendor((int) auth()->id())->with('product')]);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $orders = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-export-'.now()->format('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($orders): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order Number', 'Date', 'Customer Name', 'Customer Email', 'Status', 'Products', 'Total For You']);

            foreach ($orders as $order) {
                $vendorItems = $order->items->filter(fn ($i) => (int) $i->vendor_id === (int) auth()->id());
                $vendorTotal = $vendorItems->sum('subtotal');

                $productNames = $vendorItems->map(function ($item) {
                    $name = $item->product_name;
                    if ($item->variant_name) {
                        $name .= ' ('.$item->variant_name.')';
                    }

                    return $name.' x'.$item->quantity;
                })->implode(', ');

                $shippingAddress = is_array($order->shipping_address) ? $order->shipping_address : [];

                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user?->name ?? (string) ($shippingAddress['full_name'] ?? 'Guest Customer'),
                    $order->user?->email ?? (string) ($shippingAddress['email'] ?? ''),
                    $order->status->value,
                    $productNames,
                    number_format((float) $vendorTotal, 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
