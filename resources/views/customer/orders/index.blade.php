<x-layouts.customer>
    <x-ui.card title="Your Orders">
        {{-- Filters --}}
        <form method="GET" action="{{ route('customer.orders.index') }}" class="mb-6 space-y-4">
            {{-- Status Tabs --}}
            <div class="flex flex-wrap gap-2">
                @php
                    $statuses = ['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled'];
                    $currentStatus = request('status', 'all');
                @endphp
                @foreach ($statuses as $value => $label)
                    <button type="submit" name="status" value="{{ $value === 'all' ? '' : $value }}" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wide border transition-all {{ ($value === 'all' && !request('status')) || request('status') === $value ? 'bg-primary-600 text-white border-primary-600 shadow-sm' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            {{-- Search + Date --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by order number..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white">
                </div>
                <input type="date" name="from" value="{{ request('from') }}" class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-300">
                <input type="date" name="to" value="{{ request('to') }}" class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-gray-300">
                <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-sm">Filter</button>
            </div>
        </form>

        <div class="space-y-4 text-sm mt-2">
            @forelse ($orders as $order)
                <a href="{{ route('customer.orders.show', $order) }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 p-5 hover:shadow-md hover:border-primary-200 dark:hover:border-primary-900/50 hover:-translate-y-0.5 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white dark:bg-gray-900 shadow-sm flex items-center justify-center text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-gray-800 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors flex-shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div>
                            <p class="font-black text-lg text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Order #{{ $order->order_number }}</p>
                            <div class="flex items-center gap-2 mt-0.5 text-gray-500 dark:text-gray-400 font-medium">
                                <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="sm:text-right flex items-center justify-between sm:block gap-4">
                        <p class="font-black text-gray-900 dark:text-white text-xl mb-1">${{ number_format($order->total, 2) }}</p>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                'processing' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                'shipped' => 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 border-primary-200 dark:border-primary-800',
                                'delivered' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                'cancelled' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 dark:text-red-400 border-red-200 dark:border-red-800',
                            ];
                            $colorClass = $statusColors[$order->status->value] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700';
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wide border shadow-sm {{ $colorClass }}">
                            {{ $order->status->value }}
                        </span>
                    </div>
                    <!-- View Icon -->
                    <div class="hidden sm:flex text-gray-300 dark:text-gray-600 group-hover:text-primary-500 dark:group-hover:text-primary-400 transition-colors ml-4">
                        <svg class="w-6 h-6 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            @empty
                <div class="py-16 text-center text-gray-500 dark:text-gray-400">
                    <div class="mx-auto w-20 h-20 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-5 text-gray-400 shadow-inner">
                        <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    </div>
                    <p class="font-black text-xl text-gray-900 dark:text-gray-100 mb-2">No orders yet.</p>
                    <p class="font-medium text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">When you place an order, it will appear here.</p>
                    <a href="{{ route('storefront.products.index') }}" class="inline-flex items-center text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 px-6 py-2.5 rounded-xl transition-all shadow-sm uppercase tracking-wide">Start shopping</a>
                </div>
            @endforelse
        </div>

        @if ($orders->hasPages())
            <div class="mt-8 border-t border-gray-100 dark:border-gray-800 pt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </x-ui.card>
</x-layouts.customer>
