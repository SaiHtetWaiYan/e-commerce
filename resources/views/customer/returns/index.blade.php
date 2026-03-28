<x-layouts.customer>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">My Returns</h1>
        </div>

        <x-ui.card>
            <div class="space-y-4">
                @forelse ($returns as $return)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 p-5 hover:shadow-sm hover:border-gray-200 dark:hover:border-gray-700 transition-all">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-bold text-gray-900 dark:text-white text-base">Order {{ $return->order->order_number }}</p>
                                @php
                                    $returnColors = [
                                        'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                                        'approved' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'rejected' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 dark:text-red-400 border-red-200 dark:border-red-800',
                                        'refunded' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide border shadow-sm {{ $returnColors[$return->status->value] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">
                                    {{ $return->status->value }}
                                </span>
                            </div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $return->reason }}</p>
                            <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mt-2">Submitted {{ $return->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <div class="sm:text-right flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t border-gray-100 dark:border-gray-800 sm:border-t-0 pt-3 sm:pt-0 mt-2 sm:mt-0">
                            @if ($return->refund_amount)
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Refund Amount</div>
                                <p class="text-lg font-black text-gray-900 dark:text-white">${{ number_format($return->refund_amount, 2) }}</p>
                            @else
                                <div class="text-sm font-medium text-gray-400 dark:text-gray-500">Refund Amount</div>
                                <p class="text-base font-bold text-gray-400 dark:text-gray-500">Pending</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-4 shadow-inner text-gray-400">
                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <p class="font-medium text-gray-900 dark:text-gray-100 mb-2">No return requests yet.</p>
                        <p class="text-sm">When you return items, they will appear here.</p>
                    </div>
                @endforelse
            </div>
            
            @if($returns->hasPages())
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $returns->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.customer>
