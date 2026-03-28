<x-layouts.vendor>
    <x-ui.card title="Order {{ $order->order_number }}">
        @php
            $statusColors = [
                'pending' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-300 dark:border-yellow-800',
                'confirmed' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800',
                'processing' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-300 dark:border-indigo-800',
                'shipped' => 'bg-primary-50 text-primary-700 border-primary-200 dark:bg-primary-900/20 dark:text-primary-300 dark:border-primary-800',
                'delivered' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800',
                'cancelled' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-800',
                'refunded' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
            ];
            $currentStatus = $order->status->value;
            $statusColorClass = $statusColors[$currentStatus] ?? 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
            $isLockedForVendor = in_array($currentStatus, ['cancelled', 'refunded'], true);
        @endphp

        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Customer: {{ $order->user->name }} ({{ $order->user->email }})</p>
        <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
            Current Status:
            <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $statusColorClass }}">
                {{ str_replace('_', ' ', $currentStatus) }}
            </span>
        </p>

        <div class="space-y-3 text-sm">
            @foreach ($order->items as $item)
                <div class="rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</p>
                    <p class="text-gray-500 dark:text-gray-400">Qty {{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</p>
                </div>
            @endforeach
        </div>

        @if ($isLockedForVendor)
            <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 px-4 py-3 text-sm text-gray-600 dark:text-gray-300">
                This order is {{ $currentStatus }} and can no longer be updated by vendor.
            </div>
        @else
            <form action="{{ route('vendor.orders.status', $order) }}" method="POST" class="mt-6 flex flex-wrap items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    @foreach (['confirmed', 'processing', 'shipped', 'delivered'] as $status)
                        <option value="{{ $status }}" @selected($order->status->value === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <input type="text" name="comment" placeholder="Optional note" class="w-full sm:w-auto rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <x-ui.button size="sm" type="submit">Update Status</x-ui.button>
            </form>
        @endif

        <div class="mt-8 border-t border-gray-100 dark:border-gray-800 pt-6">
            <a href="{{ route('vendor.orders.invoice', $order) }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Download Invoice
            </a>
        </div>
    </x-ui.card>
</x-layouts.vendor>
