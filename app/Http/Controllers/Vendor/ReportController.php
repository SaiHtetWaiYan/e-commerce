<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $vendorId = (int) auth()->id();

        $from = $request->date('from') ?? now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?? now()->endOfDay();

        $orderItemsQuery = OrderItem::query()
            ->forVendor($vendorId)
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to]));

        $grossRevenue = (float) (clone $orderItemsQuery)
            ->whereHas('order', fn ($q) => $q->whereIn('status', [OrderStatus::Delivered, OrderStatus::Shipped, OrderStatus::Processing, OrderStatus::Confirmed]))
            ->sum('subtotal');

        $totalOrders = Order::query()
            ->forVendor($vendorId)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $deliveredOrders = Order::query()
            ->forVendor($vendorId)
            ->whereBetween('created_at', [$from, $to])
            ->where('status', OrderStatus::Delivered)
            ->count();

        $cancelledOrders = Order::query()
            ->forVendor($vendorId)
            ->whereBetween('created_at', [$from, $to])
            ->where('status', OrderStatus::Cancelled)
            ->count();

        $topProducts = OrderItem::query()
            ->forVendor($vendorId)
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->selectRaw('product_id, product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $dailyRevenue = OrderItem::query()
            ->forVendor($vendorId)
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to])
                ->whereIn('status', [OrderStatus::Delivered, OrderStatus::Shipped, OrderStatus::Processing, OrderStatus::Confirmed]))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('DATE(orders.created_at) as date, SUM(order_items.subtotal) as revenue')
            ->groupByRaw('DATE(orders.created_at)')
            ->orderBy('date')
            ->get();

        $totalProducts = Product::query()->forVendor($vendorId)->count();
        $activeProducts = Product::query()->forVendor($vendorId)->active()->count();

        $commissionRate = auth()->user()->vendorProfile?->commission_rate ?? 10.0;
        $commissionAmount = round($grossRevenue * ($commissionRate / 100), 2);
        $netRevenue = round($grossRevenue - $commissionAmount, 2);

        return view('vendor.reports.index', [
            'grossRevenue' => $grossRevenue,
            'netRevenue' => $netRevenue,
            'commissionRate' => $commissionRate,
            'commissionAmount' => $commissionAmount,
            'totalOrders' => $totalOrders,
            'deliveredOrders' => $deliveredOrders,
            'cancelledOrders' => $cancelledOrders,
            'topProducts' => $topProducts,
            'dailyRevenue' => $dailyRevenue,
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'filters' => [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ],
        ]);
    }

    public function exportCsv(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $vendorId = (int) auth()->id();
        $from = $request->date('from') ?? now()->subDays(30)->startOfDay();
        $to = $request->date('to') ?? now()->endOfDay();

        $items = OrderItem::query()
            ->forVendor($vendorId)
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to]))
            ->with('order')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report-'.now()->format('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($items): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order #', 'Date', 'Product', 'Variant', 'Qty', 'Unit Price', 'Subtotal', 'Order Status']);

            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->order->order_number,
                    $item->order->created_at->format('Y-m-d'),
                    $item->product_name,
                    $item->variant_name ?? '-',
                    $item->quantity,
                    number_format((float) $item->unit_price, 2, '.', ''),
                    number_format((float) $item->subtotal, 2, '.', ''),
                    $item->order->status->value,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
