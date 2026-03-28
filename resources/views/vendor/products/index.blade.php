<x-layouts.vendor>
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Products</h1>
        <a href="{{ route('vendor.products.create') }}"><x-ui.button size="sm">Add Product</x-ui.button></a>
    </div>

    <x-ui.card>
        <div x-data="{ selected: [] }" class="space-y-4">
            {{-- Bulk Action Bar --}}
            <div x-show="selected.length > 0" x-cloak class="flex items-center gap-3 rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20 px-4 py-3" x-transition>
                <span class="text-sm font-bold text-primary-700 dark:text-primary-300" x-text="selected.length + ' selected'"></span>
                <form method="POST" action="{{ route('vendor.products.bulk-status') }}" class="inline">
                    @csrf
                    <input type="hidden" name="action" value="activate">
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition-colors">Activate Selected</button>
                </form>
                <form method="POST" action="{{ route('vendor.products.bulk-status') }}" class="inline">
                    @csrf
                    <input type="hidden" name="action" value="archive">
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>
                    <button type="submit" class="rounded-lg bg-gray-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-gray-700 transition-colors">Archive Selected</button>
                </form>
                <button @click="selected = []" type="button" class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Clear</button>
            </div>

            <div class="space-y-3 text-sm">
                @forelse ($products as $product)
                    @php
                        $statusStyles = [
                            'active' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                            'draft' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300',
                            'pending_review' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                            'archived' => 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-500',
                        ];
                    @endphp
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer" :class="selected.includes({{ $product->id }}) && 'ring-2 ring-primary-500 border-primary-400 dark:border-primary-600'">
                        <input type="checkbox" value="{{ $product->id }}" x-model.number="selected" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${{ number_format($product->base_price, 2) }}</p>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusStyles[$product->status->value] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ str_replace('_', ' ', $product->status->value) }}
                        </span>
                        <a href="{{ route('vendor.products.edit', $product) }}" @click.stop><x-ui.button size="sm" variant="secondary">Edit</x-ui.button></a>
                    </label>
                @empty
                    <p class="rounded-md border border-dashed border-gray-300 dark:border-gray-600 px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                        No products yet. <a href="{{ route('vendor.products.create') }}" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">Add your first product</a>.
                    </p>
                @endforelse
            </div>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </x-ui.card>
</x-layouts.vendor>
