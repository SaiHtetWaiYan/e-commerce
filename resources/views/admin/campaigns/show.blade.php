<x-layouts.admin>
    @php
        $isRunning = $campaign->isRunning();
        $statusLabel = $isRunning ? 'Running' : ($campaign->starts_at?->isFuture() ? 'Upcoming' : ($campaign->ends_at?->isPast() ? 'Ended' : 'Draft'));
    @endphp

    <div class="space-y-6">
        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            <div class="relative px-6 py-6 sm:px-8 {{ $campaign->banner_image ? '' : 'bg-gradient-to-r from-orange-500 to-amber-500' }}">
                @if ($campaign->banner_image)
                    <img src="{{ Storage::url($campaign->banner_image) }}" alt="{{ $campaign->name }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-black/45"></div>
                @endif

                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-gray-900">
                                {{ $statusLabel }}
                            </span>
                            @if ($campaign->badge_text)
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white" style="background-color: {{ $campaign->badge_color ?? '#f97316' }};">
                                    {{ $campaign->badge_text }}
                                </span>
                            @endif
                        </div>
                        <h1 class="mt-3 text-2xl font-black text-white tracking-tight">{{ $campaign->name }}</h1>
                        <p class="mt-2 max-w-2xl text-sm font-medium text-white/90">{{ $campaign->description ?: 'No description added.' }}</p>
                        <p class="mt-3 text-xs font-bold uppercase tracking-wide text-white/90">
                            {{ $campaign->starts_at?->format('M d, Y H:i') }} - {{ $campaign->ends_at?->format('M d, Y H:i') }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="rounded-xl border border-white/40 bg-white/10 px-3 py-2 text-xs font-bold uppercase tracking-wide text-white hover:bg-white/20">Edit</a>
                        <form action="{{ route('admin.campaigns.toggle', $campaign) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="rounded-xl border border-white/40 bg-white/10 px-3 py-2 text-xs font-bold uppercase tracking-wide text-white hover:bg-white/20">
                                {{ $campaign->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid gap-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50/70 dark:bg-gray-800/30 p-5 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Products Enrolled</p>
                    <p class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $campaign->products_count }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Potential Savings</p>
                    <p class="mt-1 text-2xl font-black text-emerald-600 dark:text-emerald-400">${{ number_format($potentialSavings, 2) }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Discount Rule</p>
                    <p class="mt-1 text-sm font-black text-gray-900 dark:text-white">
                        {{ Str::title($campaign->discount_type->value) }}
                        @if ($campaign->discount_value !== null)
                            - {{ $campaign->discount_type->value === 'percentage' ? number_format((float) $campaign->discount_value, 0).'%' : '$'.number_format((float) $campaign->discount_value, 2) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="xl:col-span-2 space-y-6">
                <x-ui.card title="Enrolled Products">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                    <th class="px-3 py-2">Product</th>
                                    <th class="px-3 py-2">Base</th>
                                    <th class="px-3 py-2">Campaign</th>
                                    <th class="px-3 py-2">Override</th>
                                    <th class="px-3 py-2">Sort</th>
                                    <th class="px-3 py-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @forelse ($enrolledProducts as $product)
                                    @php
                                        $campaignPrice = $campaign->getCampaignPriceForEnrolledProduct($product);
                                        $productImage = $product->primary_image
                                            ? (str_starts_with($product->primary_image, 'http') || str_starts_with($product->primary_image, '/storage') ? $product->primary_image : asset('storage/'.$product->primary_image))
                                            : 'https://placehold.co/80x80/f1f5f9/64748b?text='.urlencode($product->name);
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-3">
                                                <img src="{{ $productImage }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                                <div>
                                                    <p class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku ?: 'N/A' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 font-medium text-gray-600 dark:text-gray-300">${{ number_format((float) $product->base_price, 2) }}</td>
                                        <td class="px-3 py-3 font-black text-primary-600 dark:text-primary-400">${{ number_format($campaignPrice, 2) }}</td>
                                        <td class="px-3 py-3 text-xs text-gray-500 dark:text-gray-400">
                                            @if ($product->pivot->custom_price !== null)
                                                Price: ${{ number_format((float) $product->pivot->custom_price, 2) }}
                                            @elseif ($product->pivot->custom_discount_percentage !== null)
                                                {{ $product->pivot->custom_discount_percentage }}% off
                                            @else
                                                Rule-based
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-xs font-bold text-gray-700 dark:text-gray-300">{{ $product->pivot->sort_order }}</td>
                                        <td class="px-3 py-3 text-right">
                                            <form action="{{ route('admin.campaigns.remove-product', [$campaign, $product]) }}" method="POST" onsubmit="return confirm('Remove this product from campaign?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No products enrolled yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($enrolledProducts->hasPages())
                        <div class="mt-4 border-t border-gray-100 dark:border-gray-800 pt-4">
                            {{ $enrolledProducts->links() }}
                        </div>
                    @endif
                </x-ui.card>
            </div>

            <div class="space-y-6">
                <x-ui.card title="Add Products">
                    <form method="GET" action="{{ route('admin.campaigns.show', $campaign) }}" class="mb-4">
                        <input
                            name="product_search"
                            value="{{ $productSearch }}"
                            placeholder="Search product name or SKU"
                            class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                        >
                    </form>

                    <form method="POST" action="{{ route('admin.campaigns.add-products', $campaign) }}" class="space-y-3">
                        @csrf
                        <div class="max-h-[500px] space-y-3 overflow-y-auto pr-1">
                            @forelse ($availableProducts as $product)
                                @php
                                    $productImage = $product->primary_image
                                        ? (str_starts_with($product->primary_image, 'http') || str_starts_with($product->primary_image, '/storage') ? $product->primary_image : asset('storage/'.$product->primary_image))
                                        : 'https://placehold.co/80x80/f1f5f9/64748b?text='.urlencode($product->name);
                                @endphp
                                <label class="block rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-3">
                                    <div class="flex items-start gap-3">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="mt-1 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <img src="{{ $productImage }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-lg border border-gray-200 dark:border-gray-700 object-cover">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">${{ number_format((float) $product->base_price, 2) }}</p>
                                            <div class="mt-2 grid gap-2">
                                                <input type="number" step="0.01" min="0" name="custom_price[{{ $product->id }}]" placeholder="Custom price (optional)" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-2 py-1.5 text-xs text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <input type="number" min="0" max="100" name="custom_discount_percentage[{{ $product->id }}]" placeholder="Custom %" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-2 py-1.5 text-xs text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                                    <input type="number" min="0" name="sort_order[{{ $product->id }}]" placeholder="Sort order" class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 px-2 py-1.5 text-xs text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <p class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-4 text-sm text-gray-500 dark:text-gray-400">
                                    No available products{{ $productSearch ? ' for this search' : '' }}.
                                </p>
                            @endforelse
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">
                            Add Selected Products
                        </button>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-layouts.admin>
