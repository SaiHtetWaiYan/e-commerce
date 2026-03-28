<x-layouts.vendor>
    <x-ui.card>
        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-gray-800 pb-4">
            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Orders</h2>
            <a href="{{ route('vendor.orders.export', request()->query()) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 border-2 border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors bg-white dark:bg-gray-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export CSV
            </a>
        </div>
        <div class="mb-6 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gradient-to-br from-gray-50 to-white dark:from-gray-900 dark:to-gray-900/60 p-4 sm:p-5">
            <form method="GET" action="{{ route('vendor.orders.index') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-4">
                    <label for="q" class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-400">Search</label>
                    <div class="relative mt-1">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                        </span>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Order #, customer name, email" class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-9 pr-3 py-2.5 text-sm text-gray-900 dark:text-white placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500 outline-none">
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label for="status" class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-400">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500 outline-none">
                        <option value="">All Statuses</option>
                        @foreach (\App\Enums\OrderStatus::cases() as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', Str::title($status->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label for="from" class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-400">Date From</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>

                <div class="lg:col-span-2">
                    <label for="to" class="block text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-gray-400">Date To</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500 outline-none">
                </div>

                <div class="lg:col-span-2 flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors">
                        Apply
                    </button>
                    <a href="{{ route('vendor.orders.index') }}" class="inline-flex flex-1 items-center justify-center rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="space-y-3 text-sm">
            @forelse ($orders as $order)
                <a href="{{ route('vendor.orders.show', $order) }}" class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 hover:bg-gray-50 dark:bg-gray-800 transition">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->user?->name ?? $order->shipping_name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border
                            @if($order->status === \App\Enums\OrderStatus::Delivered || $order->status === \App\Enums\OrderStatus::Shipped) border-green-200 bg-green-100 text-green-800 dark:bg-green-900/30 dark:border-green-800/50 dark:text-green-400
                            @elseif($order->status === \App\Enums\OrderStatus::Cancelled || $order->status === \App\Enums\OrderStatus::Refunded) border-red-200 bg-red-100 text-red-800 dark:bg-red-900/30 dark:border-red-800/50 dark:text-red-400
                            @else border-yellow-200 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:border-yellow-800/50 dark:text-yellow-400 @endif
                        ">
                            {{ str_replace('_', ' ', Str::title($order->status->name)) }}
                        </span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $order->created_at->format('M j, Y') }}</p>
                    </div>
                </a>
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No orders found matching your criteria.
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </x-ui.card>
</x-layouts.vendor>
