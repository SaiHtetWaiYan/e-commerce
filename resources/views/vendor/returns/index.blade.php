<x-layouts.vendor>
    <x-ui.card title="Return Requests">
        <div class="space-y-3 text-sm">
            @forelse ($returns as $return)
                <a href="{{ route('vendor.returns.show', $return) }}" class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 hover:bg-gray-50 dark:bg-gray-800 transition">
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Order #{{ $return->order->order_number }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $return->user->name }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 truncate max-w-xs">{{ Str::limit($return->reason, 60) }}</p>
                    </div>
                    <div class="text-right flex-shrink-0 ml-4">
                        @php
                            $badgeClasses = match($return->status->value) {
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800/50',
                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800/50',
                                'rejected' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800/50',
                                'refunded' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800/50',
                                default => 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
                            };
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium border {{ $badgeClasses }}">
                            {{ $return->status->label() }}
                        </span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${{ number_format($return->refund_amount, 2) }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $return->created_at->format('M j, Y') }}</p>
                    </div>
                </a>
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    No return requests found.
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $returns->links() }}</div>
    </x-ui.card>
</x-layouts.vendor>
