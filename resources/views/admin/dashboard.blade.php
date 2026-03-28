<x-layouts.admin>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Admin Dashboard</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">System overview and key metrics.</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                Generate Report
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Total Revenue</p>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-gray-900 dark:text-white">${{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Total Users</p>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($usersCount) }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Total Vendors</p>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($vendorsCount) }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <p class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-1">Active Products</p>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($productsCount) }}</p>
            </div>
        </div>
        <div class="bg-primary-600 rounded-2xl p-5 shadow-sm text-white">
            <p class="text-sm font-bold text-primary-200 mb-1">Total Orders</p>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-white">{{ number_format($ordersCount) }}</p>
            </div>
        </div>
    </div>

    <!-- Revenue Trends Chart -->
    <div class="mb-8 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden p-6" x-data="{
        init() {
            const data = @js($trends->pluck('revenue'));
            const labels = @js($trends->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d')));
            
            new Chart(this.$refs.canvas, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Revenue',
                        data: data,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }">
        <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight mb-4">Revenue 30-Day Trend</h2>
        <div class="relative h-64 w-full">
            <canvas x-ref="canvas"></canvas>
        </div>
        <!-- Load Chart.js if not already in layout -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    </div>

    <!-- Recent Orders List -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
            <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Latest Platform Orders</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/80 text-gray-500 dark:text-gray-400 font-bold border-b border-gray-200 dark:border-gray-800 uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Order Number</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($recentOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">#{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-right font-black text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 font-bold transition-colors">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 font-medium">
                                No orders have been placed on the platform yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
