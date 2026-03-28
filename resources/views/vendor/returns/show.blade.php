<x-layouts.vendor>
    @php
        $badgeClasses = match($returnRequest->status->value) {
            'pending' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50',
            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50',
            'rejected' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/50',
            'refunded' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50',
            default => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
        };
    @endphp

    <x-ui.card title="Return Request Details">
        <div class="space-y-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="font-medium text-gray-600 dark:text-gray-400">Status:</span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border {{ $badgeClasses }}">
                    {{ $returnRequest->status->label() }}
                </span>
            </div>

            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Order:</span>
                <a href="{{ route('vendor.orders.show', $returnRequest->order) }}" class="text-accent-600 dark:text-accent-400 hover:underline font-medium">
                    #{{ $returnRequest->order->order_number }}
                </a>
            </div>

            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Customer:</span>
                <span class="text-gray-900 dark:text-white">{{ $returnRequest->user->name }}</span>
            </div>

            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Reason:</span>
                <p class="mt-1 text-gray-900 dark:text-white">{{ $returnRequest->reason }}</p>
            </div>

            @if ($returnRequest->admin_notes)
                <div>
                    <span class="font-medium text-gray-600 dark:text-gray-400">Processing Notes:</span>
                    <p class="mt-1 text-gray-900 dark:text-white">{{ $returnRequest->admin_notes }}</p>
                </div>
            @endif

            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Refund Amount:</span>
                <span class="text-gray-900 dark:text-white font-semibold">${{ number_format($returnRequest->refund_amount, 2) }}</span>
            </div>

            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">Submitted:</span>
                <span class="text-gray-900 dark:text-white">{{ $returnRequest->created_at->format('M j, Y \a\t g:i A') }}</span>
            </div>
        </div>
    </x-ui.card>

    <x-ui.card title="Returned Items" class="mt-6">
        <div class="space-y-3 text-sm">
            @foreach ($returnRequest->items as $item)
                <div class="rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                    <p class="font-medium text-gray-900 dark:text-white">{{ $item->orderItem->product->name ?? $item->orderItem->product_name ?? 'Unknown Product' }}</p>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                        <p class="text-gray-900 dark:text-white font-medium">${{ number_format($item->subtotal, 2) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </x-ui.card>

    @if ($canProcessReturn)
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <x-ui.card title="Approve Return">
                <form action="{{ route('vendor.returns.approve', $returnRequest) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="refund_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refund Amount</label>
                        <input id="refund_amount" name="refund_amount" type="number" min="0" step="0.01" value="{{ old('refund_amount', $returnRequest->refund_amount) }}" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none">
                    </div>
                    <div>
                        <label for="approve-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea id="approve-notes" name="notes" rows="3" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-accent-500 focus:border-accent-500 outline-none" placeholder="Optional approval notes for the customer.">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700 transition-colors">Approve Return</button>
                </form>
            </x-ui.card>

            <x-ui.card title="Reject Return">
                <form action="{{ route('vendor.returns.reject', $returnRequest) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="reject-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason</label>
                        <textarea id="reject-notes" name="notes" rows="4" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" placeholder="Explain why this return is being rejected." required>{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-colors">Reject Return</button>
                </form>
            </x-ui.card>
        </div>
    @elseif ($returnRequest->status->value === 'pending')
        <x-ui.card title="Processing Status" class="mt-6">
            <p class="text-sm text-gray-600 dark:text-gray-300">This return includes items outside your catalog, so it must be handled by an admin.</p>
        </x-ui.card>
    @endif

    <div class="mt-6">
        <a href="{{ route('vendor.returns.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Returns
        </a>
    </div>
</x-layouts.vendor>
