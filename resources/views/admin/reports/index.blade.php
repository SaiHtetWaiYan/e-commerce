<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Reports & Analytics</h2>
            <a href="{{ route('admin.reports.export', array_filter($filters)) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 border-2 border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors bg-white dark:bg-gray-800 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>
        </div>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-[1fr_1fr_auto_auto] md:items-end">
                <div>
                    <label for="start_date" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                    <input id="start_date" name="start_date" type="date" value="{{ $filters['start_date'] ?? '' }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label for="end_date" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                    <input id="end_date" name="end_date" type="date" value="{{ $filters['end_date'] ?? '' }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700 transition-colors">Apply</button>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Reset</a>
            </div>
        </form>
        
        {{-- Summary Stats --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @php
                $stats = [
                    ['label' => 'Total Revenue', 'value' => '$' . number_format($totalRevenue, 2), 'value_classes' => 'text-emerald-600 dark:text-emerald-400'],
                    ['label' => 'Total Orders', 'value' => number_format($totalOrders), 'value_classes' => 'text-blue-600 dark:text-blue-400'],
                    ['label' => 'Total Customers', 'value' => number_format($totalUsers), 'value_classes' => 'text-violet-600 dark:text-violet-400'],
                    ['label' => 'Total Vendors', 'value' => number_format($totalVendors), 'value_classes' => 'text-orange-600 dark:text-orange-400'],
                ];
            @endphp
            @foreach ($stats as $stat)
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                    <p class="mt-1 text-2xl font-bold {{ $stat['value_classes'] }}">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Orders by Status --}}
        <x-ui.card title="Orders by Status">
            <div class="flex flex-wrap gap-3">
                @foreach ($ordersByStatus as $status => $count)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 px-4 py-2 text-center">
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $count }}</p>
                        <p class="text-xs capitalize text-gray-500 dark:text-gray-400">{{ str_replace('_', ' ', $status) }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Top Products --}}
            <x-ui.card title="Top Selling Products">
                <div class="space-y-2 text-sm">
                    @foreach ($topProducts as $product)
                        <div class="flex items-center justify-between rounded-md border border-gray-100 dark:border-gray-800 px-3 py-2">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $product->product_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->total_qty }} sold</p>
                            </div>
                            <p class="font-semibold text-gray-900 dark:text-white">${{ number_format($product->total_revenue, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            {{-- Top Vendors --}}
            <x-ui.card title="Top Vendors">
                <div class="space-y-2 text-sm">
                    @foreach ($topVendors as $vendor)
                        <div class="flex items-center justify-between rounded-md border border-gray-100 dark:border-gray-800 px-3 py-2">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $vendor->store_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $vendor->order_count }} orders</p>
                            </div>
                            <p class="font-semibold text-gray-900 dark:text-white">${{ number_format($vendor->total_revenue, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        {{-- Recent Orders --}}
        <x-ui.card title="Recent Orders">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-2">Order</th>
                            <th class="px-3 py-2">Customer</th>
                            <th class="px-3 py-2">Total</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($recentOrders as $order)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $order->order_number }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $order->user->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2">${{ number_format($order->total, 2) }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold capitalize bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                        {{ $order->status->value }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</x-layouts.admin>
