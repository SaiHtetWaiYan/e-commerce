<x-layouts.admin>
    <div class="space-y-6">
        <x-ui.card title="Return Request #{{ $returnRequest->id }}">
            <div class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Order</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $returnRequest->order->order_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $returnRequest->user->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-medium capitalize text-gray-900 dark:text-white">{{ $returnRequest->status->value }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Submitted</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $returnRequest->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-gray-500 dark:text-gray-400">Reason</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $returnRequest->reason }}</p>
                </div>
                @if ($returnRequest->refund_amount)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Refund Amount</p>
                        <p class="font-medium text-gray-900 dark:text-white">${{ number_format($returnRequest->refund_amount, 2) }}</p>
                    </div>
                @endif
                @if ($returnRequest->admin_notes)
                    <div class="sm:col-span-2">
                        <p class="text-gray-500 dark:text-gray-400">Admin Notes</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $returnRequest->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </x-ui.card>

        @if ($returnRequest->items->isNotEmpty())
            <x-ui.card title="Returned Items">
                <div class="space-y-2 text-sm">
                    @foreach ($returnRequest->items as $returnItem)
                        <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                            <span class="font-medium text-gray-900 dark:text-white">{{ $returnItem->orderItem?->product_name ?? 'Item #'.$returnItem->order_item_id }}</span>
                            <span class="text-gray-500 dark:text-gray-400">Qty: {{ $returnItem->quantity }} | ${{ number_format((float) $returnItem->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        @if ($returnRequest->status->value === 'pending')
            <div class="grid gap-6 md:grid-cols-2">
                <x-ui.card title="Approve Return">
                    <form action="{{ route('admin.returns.approve', $returnRequest) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Refund Amount</label>
                            <input type="number" step="0.01" name="refund_amount" value="{{ $returnRequest->refund_amount }}" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Notes</label>
                            <textarea name="admin_notes" rows="2" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
                        </div>
                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Approve & Refund</button>
                    </form>
                </x-ui.card>

                <x-ui.card title="Reject Return">
                    <form action="{{ route('admin.returns.reject', $returnRequest) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Rejection</label>
                            <textarea name="admin_notes" rows="3" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required></textarea>
                        </div>
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Reject Return</button>
                    </form>
                </x-ui.card>
            </div>
        @endif

        <a href="{{ route('admin.returns.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:text-white">&larr; Back to Returns</a>
    </div>
</x-layouts.admin>
