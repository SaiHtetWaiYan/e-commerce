<x-layouts.customer>
    <x-ui.card title="Request Return for Order {{ $order->order_number }}">
        <form action="{{ route('customer.returns.store', $order) }}" method="POST" class="space-y-6 max-w-lg">
            @csrf
            <div>
                <label for="reason" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Reason for Return</label>
                <textarea name="reason" id="reason" rows="4" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm shadow-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:text-white transition-all placeholder-gray-400" placeholder="Please describe why you want to return this order..." required>{{ old('reason') }}</textarea>
                @error('reason') <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-800 p-4">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Select Items to Return</h4>
                <div class="space-y-2">
                    @foreach ($order->items as $item)
                        <label class="flex items-center justify-between gap-3 rounded-lg border border-gray-200/50 dark:border-gray-700/50 bg-white dark:bg-gray-900 px-4 py-3 text-sm shadow-sm">
                            <span class="flex items-center gap-3">
                                <input type="checkbox" name="order_item_ids[]" value="{{ $item->id }}" @checked(in_array($item->id, old('order_item_ids', []))) class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800">
                                <span class="font-bold text-gray-900 dark:text-white">{{ $item->product_name }}</span>
                            </span>
                            <span class="font-medium text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }} <span class="mx-1.5 text-gray-300 dark:text-gray-600">&bull;</span> <span class="text-primary-600 dark:text-primary-400 font-bold">${{ number_format($item->subtotal, 2) }}</span></span>
                        </label>
                    @endforeach
                </div>
                @error('order_item_ids')
                    <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('order_item_ids.*')
                    <p class="mt-2 text-sm font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('customer.orders.show', $order) }}" class="inline-flex justify-center rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-transparent px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all sm:w-auto w-full">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-primary-700 transition-all shadow-sm sm:w-auto w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Submit Return Request
                </button>
            </div>
        </form>
    </x-ui.card>
</x-layouts.customer>
