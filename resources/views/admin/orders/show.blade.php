<x-layouts.admin>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Order {{ $order->order_number }}</h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Placed {{ $order->created_at->format('M d, Y \a\t h:i A') }} by {{ $order->user->name ?? 'Guest' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.orders.invoice', $order) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 border-2 border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors bg-white dark:bg-gray-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Invoice
                </a>
                <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 dark:text-gray-400 border-2 border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Main --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Items --}}
                <x-ui.card title="Order Items">
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-4 py-3 first:pt-0 last:pb-0">
                                @if ($item->product?->images?->first())
                                    <img src="{{ $item->product->images->first()->image_url }}" class="w-14 h-14 rounded-xl object-cover border border-gray-100 dark:border-gray-800">
                                @elseif ($item->product_image)
                                    <img src="{{ str_starts_with($item->product_image, 'http') || str_starts_with($item->product_image, '/storage') ? $item->product_image : asset('storage/' . $item->product_image) }}" class="w-14 h-14 rounded-xl object-cover border border-gray-100 dark:border-gray-800">
                                @else
                                    <div class="w-14 h-14 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                @endif
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->product_name }}</p>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Variant: {{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</p>
                                </div>
                                <p class="font-bold text-gray-900 dark:text-white">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>

                {{-- Status History --}}
                @if ($order->statusHistories && $order->statusHistories->isNotEmpty())
                    <x-ui.card title="Status History">
                        <div class="space-y-3 border-l-2 border-primary-200 dark:border-primary-900/50 pl-5 ml-1">
                            @foreach ($order->statusHistories->sortByDesc('created_at') as $history)
                                <div class="relative">
                                    <span class="absolute -left-[27px] top-1 w-2.5 h-2.5 rounded-full bg-primary-500 ring-4 ring-white dark:ring-gray-900"></span>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white uppercase">{{ $history->status }}</p>
                                    @if ($history->comment)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $history->comment }}</p>
                                    @endif
                                    <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $history->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </x-ui.card>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Update Status --}}
                <x-ui.card title="Update Status">
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase">Order Status</label>
                            <select name="status" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-300">
                                @foreach (['hold', 'cancelled', 'refunded'] as $s)
                                    <option value="{{ $s }}" @selected($order->status->value === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase">Comment</label>
                            <textarea name="comment" rows="2" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white resize-none" placeholder="Optional note..."></textarea>
                        </div>
                        <button type="submit" class="w-full bg-primary-600 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-primary-700 transition-colors shadow-sm">Update Status</button>
                    </form>
                </x-ui.card>

                {{-- Summary --}}
                <x-ui.card title="Order Summary">
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Subtotal</span><span class="font-bold text-gray-900 dark:text-white">${{ number_format($order->subtotal, 2) }}</span></div>
                        @if ((float) $order->discount_amount > 0)
                            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Discount</span><span class="font-bold text-emerald-600 dark:text-emerald-400">-${{ number_format($order->discount_amount, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Shipping</span><span class="font-bold text-gray-900 dark:text-white">${{ number_format($order->shipping_fee, 2) }}</span></div>
                        @if ((float) $order->tax_amount > 0)
                            <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Tax</span><span class="font-bold text-gray-900 dark:text-white">${{ number_format($order->tax_amount, 2) }}</span></div>
                        @endif
                        <div class="flex justify-between pt-3 border-t border-gray-100 dark:border-gray-800"><span class="font-bold text-gray-900 dark:text-white">Total</span><span class="font-black text-lg text-primary-600 dark:text-primary-400">${{ number_format($order->total, 2) }}</span></div>
                    </div>
                </x-ui.card>

                {{-- Customer --}}
                <x-ui.card title="Customer">
                    <div class="text-sm space-y-2">
                        <p class="font-bold text-gray-900 dark:text-white">{{ $order->user->name ?? 'Guest' }}</p>
                        <p class="text-gray-500 dark:text-gray-400">{{ $order->user->email ?? '-' }}</p>
                        <p class="text-xs text-gray-400 uppercase font-bold mt-2">Payment: {{ $order->payment_method ?? 'N/A' }}</p>
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.admin>
