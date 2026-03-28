<x-layouts.vendor>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Store Overview</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Monitor your store's performance and orders.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('vendor.products.create') ?? '#' }}" class="bg-primary-600 dark:bg-primary-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-primary-700 dark:hover:bg-primary-600 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Product
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Total Products</p>
                <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-700 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $productsCount }}</p>
        </div>
        
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col relative overflow-hidden">
            <div class="absolute bottom-0 inset-x-0 h-1.5 bg-green-500 dark:bg-green-400"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Active Products</p>
                <div class="w-10 h-10 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400 border border-green-100 dark:border-green-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $activeProductsCount }}</p>
        </div>
        
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Total Orders</p>
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $ordersCount }}</p>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col relative overflow-hidden">
            <div class="absolute bottom-0 inset-x-0 h-1.5 {{ $pendingOrdersCount > 0 ? 'bg-orange-500 dark:bg-orange-400' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400">Action Required</p>
                <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400 border border-orange-100 dark:border-orange-800 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $pendingOrdersCount }}</p>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1">Orders pending fulfillment</p>
        </div>
    </div>

    <!-- Revenue Stats -->
    <div class="grid gap-4 sm:grid-cols-2 mb-8">
        <div class="bg-gradient-to-br from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8"></div>
            <p class="text-sm font-bold text-primary-100 mb-1">Total Revenue</p>
            <p class="text-3xl font-black">${{ number_format($totalRevenue, 2) }}</p>
            <p class="text-xs text-primary-200 font-medium mt-2">All-time earnings from orders</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 dark:from-emerald-700 dark:to-emerald-800 rounded-2xl p-6 shadow-lg text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-8 translate-x-8"></div>
            <p class="text-sm font-bold text-emerald-100 mb-1">This Month</p>
            <p class="text-3xl font-black">${{ number_format($monthlyRevenue, 2) }}</p>
            <p class="text-xs text-emerald-200 font-medium mt-2">{{ now()->format('F Y') }} earnings</p>
        </div>
    </div>

    <!-- Analytics Row -->
    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <!-- Monthly Sales Chart -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Sales Trend (6 months)</h3>
            </div>
            <div class="p-6">
                @if ($monthlySales->isNotEmpty())
                    @php $maxRevenue = $monthlySales->max('revenue') ?: 1; @endphp
                    <div class="flex items-end gap-3 h-40">
                        @foreach ($monthlySales as $data)
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full rounded-t-lg relative" style="height: {{ max(($data->revenue / $maxRevenue) * 100, 5) }}%">
                                    <div class="absolute inset-0 bg-primary-500 rounded-t-lg hover:bg-primary-600 transition-colors cursor-default" title="${{ number_format($data->revenue, 2) }} ({{ $data->count }} orders)"></div>
                                </div>
                                <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase">{{ \Carbon\Carbon::parse($data->month . '-01')->format('M') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-40 flex items-center justify-center text-sm text-gray-400 font-medium">No sales data yet</div>
                @endif
            </div>
        </div>

        <!-- Top Products + Low Stock -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Top Selling Products</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($topProducts->take(5) as $idx => $product)
                        <div class="px-6 py-3 flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 text-xs font-bold flex items-center justify-center">{{ $idx + 1 }}</span>
                            <p class="flex-1 text-sm font-medium text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                            <span class="text-xs font-bold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-2 py-0.5 rounded-full">{{ $product->order_items_count }} sold</span>
                        </div>
                    @empty
                        <div class="px-6 py-6 text-center text-sm text-gray-400 font-medium">No sales yet</div>
                    @endforelse
                </div>
            </div>

            @if ($lowStockProducts->isNotEmpty())
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-orange-200 dark:border-orange-900/50 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-orange-100 dark:border-orange-900/30 bg-orange-50/50 dark:bg-orange-900/10 flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <h3 class="text-sm font-bold text-orange-700 dark:text-orange-400">Low Stock Alert</h3>
                    </div>
                    <div class="divide-y divide-orange-100 dark:divide-orange-900/30">
                        @foreach ($lowStockProducts as $product)
                            <div class="px-6 py-3 flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate flex-1">{{ $product->name }}</p>
                                <span class="text-xs font-bold text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-900/20 px-2 py-0.5 rounded-full">{{ $product->stock_quantity }} left</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
        <div class="border-b border-gray-200 dark:border-gray-800 bg-gradient-to-r from-gray-50 via-white to-gray-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800/70 px-5 py-4 sm:px-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                    </span>
                    <h2 class="text-lg font-black tracking-tight text-gray-900 dark:text-white">Recent Customer Orders</h2>
                </div>
                <a href="{{ route('vendor.orders.index') }}" class="inline-flex items-center gap-1 rounded-xl border-2 border-gray-200 bg-white px-3 py-1.5 text-xs font-bold uppercase tracking-wide text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    View All
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <div class="space-y-3 p-3 sm:p-4">
            @forelse ($recentOrders as $order)
                @php
                    $statusColors = [
                        'pending' => 'border-yellow-200 bg-yellow-50 text-yellow-700 dark:border-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
                        'processing' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
                        'shipped' => 'border-primary-200 bg-primary-50 text-primary-700 dark:border-primary-800 dark:bg-primary-900/20 dark:text-primary-300',
                        'delivered' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300',
                        'cancelled' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300',
                        'refunded' => 'border-gray-300 bg-gray-100 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
                    ];
                    $colorClass = $statusColors[$order->status->value] ?? 'border-gray-300 bg-gray-100 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300';
                @endphp
                <div class="group flex flex-col gap-4 rounded-2xl border border-gray-200 bg-gradient-to-br from-white to-gray-50/70 p-4 transition-all hover:border-primary-200 hover:shadow-sm dark:border-gray-700 dark:from-gray-900 dark:to-gray-900/60 dark:hover:border-primary-800 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <p class="truncate text-base font-black text-gray-900 dark:text-white">Order #{{ $order->order_number }}</p>
                        <p class="mt-1 text-sm font-medium text-gray-600 dark:text-gray-300">Customer: {{ $order->user?->name ?? 'Guest Customer' }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-bold uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                {{ $order->created_at->diffForHumans() }}
                            </span>
                            <span class="inline-flex items-center rounded-lg border border-gray-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                {{ $order->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex w-full items-center justify-between gap-3 lg:w-auto lg:justify-end">
                        <span class="inline-flex items-center rounded-xl border px-3 py-1.5 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                            {{ str_replace('_', ' ', $order->status->value) }}
                        </span>

                        <a href="{{ route('vendor.orders.show', $order) }}" class="inline-flex items-center gap-1 rounded-xl border-2 border-gray-200 bg-white px-4 py-2 text-sm font-bold text-gray-700 transition-all hover:border-primary-300 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-primary-700 dark:hover:text-primary-300">
                            Manage
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 5 7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-gray-100 bg-gray-50 text-gray-400 shadow-inner dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="mb-1 text-base font-bold text-gray-900 dark:text-white">No orders yet</h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">When customers buy your products, orders will appear here.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.vendor>
