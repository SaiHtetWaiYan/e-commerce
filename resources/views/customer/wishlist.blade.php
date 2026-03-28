<x-layouts.customer>
    <x-ui.card title="Wishlist">
        <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
            @forelse ($wishlistItems as $item)
                <div class="h-full">
                    <x-storefront.product-card :product="$item->product" />
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500 dark:text-gray-400">
                    <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-4 shadow-inner">
                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <p class="font-medium text-lg text-gray-900 dark:text-gray-100 mb-2">Your wishlist is empty.</p>
                    <a href="{{ route('storefront.products.index') }}" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">Start browsing</a>
                </div>
            @endforelse
        </div>

        @if ($wishlistItems->hasPages())
            <div class="mt-6 border-t border-gray-100 dark:border-gray-800 pt-6">{{ $wishlistItems->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.customer>
