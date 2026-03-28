<?php

namespace App\Http\Controllers\Vendor;

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequestItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $vendorId = (int) $user->id;

        $productsCount = Product::query()->forVendor($vendorId)->count();
        $activeProductsCount = Product::query()->forVendor($vendorId)->active()->count();
        $ordersCount = Order::query()->forVendor($vendorId)->count();
        $pendingOrdersCount = Order::query()->forVendor($vendorId)->where('status', 'pending')->count();

        // Revenue metrics
        $grossTotalRevenue = (float) Order::query()
            ->forVendor($vendorId)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->sum('total');

        $partiallyRefundedRevenue = (float) ReturnRequestItem::query()
            ->whereHas('orderItem', fn ($query) => $query->forVendor($vendorId))
            ->whereHas('returnRequest', function ($query): void {
                $query->where('status', ReturnStatus::Refunded)
                    ->whereHas('order', fn ($orderQuery) => $orderQuery->where('status', '!=', OrderStatus::Refunded->value));
            })
            ->sum('subtotal');

        $totalRevenue = max($grossTotalRevenue - $partiallyRefundedRevenue, 0.0);

        $grossMonthlyRevenue = (float) Order::query()
            ->forVendor($vendorId)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $monthlyPartiallyRefundedRevenue = (float) ReturnRequestItem::query()
            ->whereHas('orderItem', fn ($query) => $query->forVendor($vendorId))
            ->whereHas('returnRequest', function ($query): void {
                $query->where('status', ReturnStatus::Refunded)
                    ->whereHas('order', function ($orderQuery): void {
                        $orderQuery
                            ->where('status', '!=', OrderStatus::Refunded->value)
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                    });
            })
            ->sum('subtotal');

        $monthlyRevenue = max($grossMonthlyRevenue - $monthlyPartiallyRefundedRevenue, 0.0);

        // Monthly sales trend (last 6 months)
        $monthlySales = Order::query()
            ->forVendor($vendorId)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->selectRaw("to_char(created_at, 'YYYY-MM') as month, COUNT(*) as count, SUM(total) as revenue")
            ->groupByRaw("to_char(created_at, 'YYYY-MM')")
            ->orderBy('month')
            ->get();

        // Top selling products
        $topProducts = Product::query()
            ->forVendor($vendorId)
            ->withCount(['orderItems' => fn ($query) => $query->whereHas('order', fn ($q) => $q->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']))])
            ->orderByDesc('order_items_count')
            ->limit(5)
            ->get();

        // Low stock products
        $lowStockProducts = Product::query()
            ->forVendor($vendorId)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get();

        $recentOrders = Order::query()
            ->forVendor($vendorId)
            ->with(['items' => fn ($query) => $query->forVendor($vendorId), 'user'])
            ->latest()
            ->limit(8)
            ->get();

        return view('vendor.dashboard', [
            'productsCount' => $productsCount,
            'activeProductsCount' => $activeProductsCount,
            'ordersCount' => $ordersCount,
            'pendingOrdersCount' => $pendingOrdersCount,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlySales' => $monthlySales,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
        ]);
    }
}
