<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $startDate = now()->subDays(30);

        // Daily revenue and order counts for the last 30 days
        $trendData = Order::query()
            ->where('created_at', '>=', $startDate)
            ->whereNotIn('status', [OrderStatus::Cancelled->value, OrderStatus::Refunded->value])
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing dates with zero values
        $trends = [];
        for ($i = 0; $i <= 30; $i++) {
            $date = now()->subDays(30 - $i)->format('Y-m-d');
            $trends[] = [
                'date' => $date,
                'revenue' => $trendData->has($date) ? (float) $trendData[$date]->revenue : 0,
                'orders' => $trendData->has($date) ? (int) $trendData[$date]->orders : 0,
            ];
        }

        $grossRevenue = (float) Order::query()
            ->whereNotIn('status', [OrderStatus::Cancelled->value, OrderStatus::Refunded->value])
            ->sum('total');

        $partiallyRefundedRevenue = (float) ReturnRequest::query()
            ->where('status', ReturnStatus::Refunded)
            ->whereHas('order', fn ($query) => $query->where('status', '!=', OrderStatus::Refunded->value))
            ->sum('refund_amount');

        $totalRevenue = max($grossRevenue - $partiallyRefundedRevenue, 0.0);

        return view('admin.dashboard', [
            'usersCount' => User::query()->count(),
            'vendorsCount' => User::query()->vendors()->count(),
            'productsCount' => Product::query()->count(),
            'ordersCount' => Order::query()->count(),
            'totalRevenue' => $totalRevenue,
            'trends' => collect($trends),
            'recentOrders' => Order::query()
                ->with(['user', 'items' => fn ($q) => $q->with('product.brand')])
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
