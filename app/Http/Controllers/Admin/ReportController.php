<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ReturnStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterReportRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnRequest;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(FilterReportRequest $request): View
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $grossRevenue = (float) $this->applyOrderDateRange(
            Order::query()->where('status', OrderStatus::Delivered),
            $startDate,
            $endDate,
        )
            ->where('status', OrderStatus::Delivered)
            ->sum('total');

        $partiallyRefundedRevenue = (float) $this->applyReturnDateRange(
            ReturnRequest::query()
            ->where('status', ReturnStatus::Refunded)
            ->whereHas('order', function (Builder $query) use ($startDate, $endDate): void {
                $query->where('status', OrderStatus::Delivered);
                $this->applyOrderDateRange($query, $startDate, $endDate);
            }),
            $startDate,
            $endDate,
        )->sum('refund_amount');

        $totalRevenue = max($grossRevenue - $partiallyRefundedRevenue, 0.0);

        $totalOrders = $this->applyOrderDateRange(Order::query(), $startDate, $endDate)->count();

        $totalUsers = $this->applyUserDateRange(
            User::query()->customers(),
            $startDate,
            $endDate,
        )->count();

        $totalVendors = $this->applyUserDateRange(
            User::query()->vendors(),
            $startDate,
            $endDate,
        )->count();

        $ordersByStatus = $this->applyOrderDateRange(
            Order::query()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status'),
            $startDate,
            $endDate,
        )
            ->pluck('count', 'status')
            ->toArray();

        $topProducts = $this->applyOrderItemDateRange(
            OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
            )
            ->groupBy('order_items.product_id', 'order_items.product_name'),
            $startDate,
            $endDate,
        )
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $topVendors = $this->applyOrderItemDateRange(
            OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('vendor_profiles', 'order_items.vendor_id', '=', 'vendor_profiles.user_id')
            ->select('order_items.vendor_id', 'vendor_profiles.store_name', DB::raw('COUNT(DISTINCT order_items.order_id) as order_count'), DB::raw('SUM(order_items.subtotal) as total_revenue')),
            $startDate,
            $endDate,
        )
            ->groupBy('order_items.vendor_id', 'vendor_profiles.store_name')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        $recentOrders = $this->applyOrderDateRange(
            Order::query()
            ->with('user')
            ->latest(),
            $startDate,
            $endDate,
        )
            ->limit(10)
            ->get();

        return view('admin.reports.index', array_merge(compact(
            'totalRevenue',
            'totalOrders',
            'totalUsers',
            'totalVendors',
            'ordersByStatus',
            'topProducts',
            'topVendors',
            'recentOrders',
        ), [
            'filters' => [
                'start_date' => $request->validated('start_date'),
                'end_date' => $request->validated('end_date'),
            ],
        ]));
    }

    public function exportCsv(FilterReportRequest $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);

        $topProducts = $this->applyOrderItemDateRange(
            OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
            )
            ->groupBy('order_items.product_name'),
            $startDate,
            $endDate,
        )
            ->orderByDesc('total_qty')
            ->limit(50)
            ->get();

        return response()->streamDownload(function () use ($topProducts): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fputcsv($handle, ['Product', 'Quantity Sold', 'Revenue']);

            foreach ($topProducts as $product) {
                fputcsv($handle, [
                    $product->product_name,
                    $product->total_qty,
                    number_format((float) $product->total_revenue, 2),
                ]);
            }

            fclose($handle);
        }, $this->reportFilename($request->validated('start_date'), $request->validated('end_date')), [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * @return array{0: CarbonImmutable|null, 1: CarbonImmutable|null}
     */
    protected function resolveDateRange(FilterReportRequest $request): array
    {
        $startDate = $request->filled('start_date')
            ? CarbonImmutable::parse((string) $request->validated('start_date'))->startOfDay()
            : null;

        $endDate = $request->filled('end_date')
            ? CarbonImmutable::parse((string) $request->validated('end_date'))->endOfDay()
            : null;

        return [$startDate, $endDate];
    }

    protected function applyOrderDateRange(Builder $query, ?CarbonImmutable $startDate, ?CarbonImmutable $endDate): Builder
    {
        return $query
            ->when($startDate !== null, fn (Builder $builder): Builder => $builder->where('orders.created_at', '>=', $startDate))
            ->when($endDate !== null, fn (Builder $builder): Builder => $builder->where('orders.created_at', '<=', $endDate));
    }

    protected function applyReturnDateRange(Builder $query, ?CarbonImmutable $startDate, ?CarbonImmutable $endDate): Builder
    {
        return $query
            ->when($startDate !== null, fn (Builder $builder): Builder => $builder->where('return_requests.created_at', '>=', $startDate))
            ->when($endDate !== null, fn (Builder $builder): Builder => $builder->where('return_requests.created_at', '<=', $endDate));
    }

    protected function applyOrderItemDateRange(Builder $query, ?CarbonImmutable $startDate, ?CarbonImmutable $endDate): Builder
    {
        return $query
            ->when($startDate !== null, fn (Builder $builder): Builder => $builder->where('orders.created_at', '>=', $startDate))
            ->when($endDate !== null, fn (Builder $builder): Builder => $builder->where('orders.created_at', '<=', $endDate));
    }

    protected function applyUserDateRange(Builder $query, ?CarbonImmutable $startDate, ?CarbonImmutable $endDate): Builder
    {
        return $query
            ->when($startDate !== null, fn (Builder $builder): Builder => $builder->where('users.created_at', '>=', $startDate))
            ->when($endDate !== null, fn (Builder $builder): Builder => $builder->where('users.created_at', '<=', $endDate));
    }

    protected function reportFilename(?string $startDate, ?string $endDate): string
    {
        $range = match (true) {
            $startDate !== null && $endDate !== null => $startDate.'-to-'.$endDate,
            $startDate !== null => 'from-'.$startDate,
            $endDate !== null => 'until-'.$endDate,
            default => now()->format('Y-m-d'),
        };

        return 'report-'.$range.'.csv';
    }
}
