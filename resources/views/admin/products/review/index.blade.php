<x-layouts.admin>
    <x-ui.card title="Product Review Queue">
        <form method="GET" class="mb-4 grid gap-3 md:grid-cols-3">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Search by product, SKU, vendor"
                class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
            >
            <select
                name="status"
                class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"
            >
                <option value="">All statuses</option>
                <option value="pending_review" @selected(request('status') === 'pending_review')>Pending review</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
            </select>
            <x-ui.button type="submit">Filter</x-ui.button>
        </form>

        <div x-data="{ selected: [] }" class="space-y-4">
            {{-- Bulk Action Bar --}}
            <div x-show="selected.length > 0" x-cloak class="flex items-center gap-3 rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20 px-4 py-3" x-transition>
                <span class="text-sm font-bold text-primary-700 dark:text-primary-300" x-text="selected.length + ' selected'"></span>
                <form method="POST" action="{{ route('admin.products.review.bulk-approve') }}" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition-colors">Approve Selected</button>
                </form>
                <form method="POST" action="{{ route('admin.products.review.bulk-reject') }}" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="product_ids[]" :value="id">
                    </template>
                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-700 transition-colors">Reject Selected</button>
                </form>
                <button @click="selected = []" type="button" class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Clear</button>
            </div>

            <div class="space-y-3 text-sm">
                @forelse ($products as $product)
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-800 transition cursor-pointer" :class="selected.includes({{ $product->id }}) && 'ring-2 ring-primary-500 border-primary-400 dark:border-primary-600'">
                        <input type="checkbox" value="{{ $product->id }}" x-model.number="selected" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        <a href="{{ route('admin.products.review.show', $product) }}" class="flex-1 min-w-0" @click.stop>
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Vendor: {{ $product->vendor->vendorProfile->store_name ?? $product->vendor->name ?? 'Unknown' }}
                                | SKU: {{ $product->sku ?? 'N/A' }}
                            </p>
                        </a>
                        <span class="rounded px-2 py-1 text-xs font-semibold {{ $product->status->value === 'pending_review' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                            {{ str_replace('_', ' ', $product->status->value) }}
                        </span>
                    </label>
                @empty
                    <p class="rounded-md border border-dashed border-gray-300 dark:border-gray-600 px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                        No products waiting for moderation.
                    </p>
                @endforelse
            </div>
        </div>

        <div class="mt-4">{{ $products->links() }}</div>
    </x-ui.card>
</x-layouts.admin>
