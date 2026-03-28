<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Order Management</h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">View and manage all marketplace orders.</p>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.orders.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by order number, customer..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white">
                </div>
                <select name="status" class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-300">
                    <option value="">All Statuses</option>
                    @foreach (['pending', 'hold', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <select name="payment_status" class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-300">
                    <option value="">All Payments</option>
                    @foreach (['pending', 'paid', 'failed', 'refunded'] as $ps)
                        <option value="{{ $ps }}" @selected(request('payment_status') === $ps)>{{ ucfirst($ps) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-sm">Filter</button>
            </div>
        </form>

        {{-- Orders Table --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-800/50">
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Items</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Payment</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($orders as $order)
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                    'hold' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                    'confirmed' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                    'processing' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                                    'shipped' => 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 border-primary-200 dark:border-primary-800',
                                    'delivered' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                    'cancelled' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
                                    'refunded' => 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700',
                                ];
                                $paymentColors = [
                                    'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400',
                                    'paid' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400',
                                    'failed' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400',
                                    'refunded' => 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400',
                                ];
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $order->user->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->user->email ?? '' }}</p>
                                </td>
                                <td class="px-5 py-4 text-gray-600 dark:text-gray-400">{{ $order->items->count() }}</td>
                                <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wide border {{ $statusColors[$order->status->value] ?? '' }}">{{ $order->status->value }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase {{ $paymentColors[$order->payment_status->value] ?? '' }}">{{ $order->payment_status->value }}</span>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400 dark:text-gray-500 font-medium">No orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.admin>
