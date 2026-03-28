<x-layouts.vendor>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Sales Reports</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Track your revenue, orders, and top products.</p>
        </div>
        <a href="{{ route('vendor.reports.export', ['from' => $filters['from'], 'to' => $filters['to']]) }}" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-primary-700 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </div>

    <!-- Date Filter -->
    <x-ui.card title="Date Range" class="mb-6">
        <form method="GET" action="{{ route('vendor.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">From</label>
                <input type="date" name="from" value="{{ $filters['from'] }}" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none dark:text-gray-300">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">To</label>
                <input type="date" name="to" value="{{ $filters['to'] }}" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none dark:text-gray-300">
            </div>
            <button type="submit" class="bg-gray-900 dark:bg-gray-100 text-white dark:text-gray-900 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-800 dark:hover:bg-gray-200 transition-all">Apply</button>
        </form>
    </x-ui.card>

    <!-- Revenue Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm relative overflow-hidden">
            <div class="absolute bottom-0 inset-x-0 h-1.5 bg-green-500"></div>
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-3">Gross Revenue</p>
            <p class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($grossRevenue, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm relative overflow-hidden">
            <div class="absolute bottom-0 inset-x-0 h-1.5 bg-primary-500"></div>
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-3">Net Revenue</p>
            <p class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($netRevenue, 2) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">After {{ $commissionRate }}% commission (${{ number_format($commissionAmount, 2) }})</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-3">Total Orders</p>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $totalOrders }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $deliveredOrders }} delivered, {{ $cancelledOrders }} cancelled</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm">
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-3">Products</p>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $activeProducts }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">of {{ $totalProducts }} total active</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Top Products -->
        <x-ui.card title="Top Products by Revenue">
            @if ($topProducts->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400 py-4">No sales data available for this period.</p>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($topProducts as $i => $item)
                        <div class="flex items-center justify-between py-3 {{ $i === 0 ? '' : '' }}">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="w-6 h-6 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $i + 1 }}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->product_name }}</span>
                            </div>
                            <div class="text-right flex-shrink-0 ml-4">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format((float) $item->total_revenue, 2) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->total_qty }} sold</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        <!-- Daily Revenue -->
        <x-ui.card title="Daily Revenue">
            @if ($dailyRevenue->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400 py-4">No revenue data available for this period.</p>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($dailyRevenue as $day)
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($day->date)->format('M j, Y') }}</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format((float) $day->revenue, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.vendor>
