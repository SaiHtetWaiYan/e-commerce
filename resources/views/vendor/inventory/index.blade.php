<x-layouts.vendor>
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Products</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $totalProducts }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock Items</p>
                <p class="mt-1 text-2xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $lowStockCount }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Stock</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $totalProducts - $lowStockCount }}</p>
            </div>
        </div>

        <x-ui.card title="Inventory">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-2">Product</th>
                            <th class="px-3 py-2">SKU</th>
                            <th class="px-3 py-2">Stock</th>
                            <th class="px-3 py-2">Threshold</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($products as $product)
                            @php $isLow = $product->stock_quantity <= $product->low_stock_threshold; @endphp
                            <tr class="{{ $isLow ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                <td class="px-3 py-2 font-medium text-gray-900 dark:text-white max-w-[200px] truncate">{{ $product->name }}</td>
                                <td class="px-3 py-2 text-gray-500 dark:text-gray-400 text-xs">{{ $product->sku ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    <form action="{{ route('vendor.inventory.update-stock', $product) }}" method="POST" class="flex items-center gap-2" id="stock-form-{{ $product->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" min="0" class="w-20 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg px-2 py-1.5 text-sm text-center font-bold {{ $isLow ? 'text-red-600 border-red-300 dark:border-red-700' : 'text-gray-900 dark:text-white' }} outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        <input type="hidden" name="low_stock_threshold" value="{{ $product->low_stock_threshold }}">
                                    </form>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="low_stock_threshold" value="{{ $product->low_stock_threshold }}" min="0" form="stock-form-{{ $product->id }}" class="w-16 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg px-2 py-1.5 text-sm text-center text-gray-500 dark:text-gray-400 outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        onchange="document.querySelector('#stock-form-{{ $product->id }} input[type=hidden][name=low_stock_threshold]').value = this.value">
                                </td>
                                <td class="px-3 py-2">
                                    @if ($product->stock_quantity <= 0)
                                        <span class="inline-flex rounded-full bg-gray-100 dark:bg-gray-800 px-2 py-0.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Out of Stock</span>
                                    @elseif ($isLow)
                                        <span class="inline-flex rounded-full bg-red-100 dark:bg-red-900/30 px-2 py-0.5 text-xs font-semibold text-red-700 dark:text-red-400">Low Stock</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400">In Stock</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <button type="submit" form="stock-form-{{ $product->id }}" class="text-xs font-bold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 px-3 py-1.5 rounded-lg transition-colors">
                                        Save
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400 dark:text-gray-500">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </x-ui.card>
    </div>
</x-layouts.vendor>
