<x-layouts.admin>
    <div class="space-y-6">
        <x-ui.card title="Review Product">
            <div class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Product</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-medium capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $product->status->value) }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Vendor</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $product->vendor->vendorProfile->store_name ?? $product->vendor->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">${{ number_format((float) $product->base_price, 2) }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-gray-500 dark:text-gray-400">Description</p>
                    <p class="font-medium text-gray-900 dark:text-white whitespace-pre-line">{{ $product->description ?: 'No description provided.' }}</p>
                </div>
                @if ($product->moderation_notes)
                    <div class="sm:col-span-2">
                        <p class="text-gray-500 dark:text-gray-400">Previous Moderation Notes</p>
                        <p class="font-medium text-gray-900 dark:text-white whitespace-pre-line">{{ $product->moderation_notes }}</p>
                    </div>
                @endif
                @if ($product->images->isNotEmpty())
                    <div class="sm:col-span-2">
                        <p class="text-gray-500 dark:text-gray-400 mb-2">Images</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->images as $image)
                                <img src="{{ $image->image_path }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-md object-cover border border-gray-200 dark:border-gray-700">
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.card>

        <div class="grid gap-6 md:grid-cols-2">
            <x-ui.card title="Approve Product">
                <form action="{{ route('admin.products.review.approve', $product) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <textarea name="comment" rows="3" placeholder="Optional moderation note..." class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Approve & Publish</button>
                </form>
            </x-ui.card>

            <x-ui.card title="Reject Product">
                <form action="{{ route('admin.products.review.reject', $product) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <textarea name="comment" rows="3" placeholder="Reason for rejection..." class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required></textarea>
                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Reject</button>
                </form>
            </x-ui.card>
        </div>

        <a href="{{ route('admin.products.review.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:text-white">&larr; Back to Product Review Queue</a>
    </div>
</x-layouts.admin>
