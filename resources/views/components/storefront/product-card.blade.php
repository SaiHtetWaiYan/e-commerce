@props(['product'])

@php
    $activeCampaign = $product->getActiveCampaign();
    $campaignPrice = $product->getActiveCampaignPrice();

    $displayPrice = $campaignPrice ?? (float) $product->base_price;

    $legacyReferencePrice = $product->compare_price && $product->compare_price > $product->base_price
        ? (float) $product->compare_price
        : null;

    $originalPrice = $campaignPrice !== null
        ? (float) $product->base_price
        : $legacyReferencePrice;

    $hasDiscount = $originalPrice !== null && $originalPrice > $displayPrice;
    $discountPercent = $hasDiscount
        ? round((($originalPrice - $displayPrice) / $originalPrice) * 100)
        : 0;

    $isNew = $product->created_at && $product->created_at->diffInDays(now()) <= 7;
    $isVerifiedVendor = $product->vendor->vendorProfile?->is_verified ?? false;
    $rating = $product->avg_rating ?? 0;
    $soldCount = $product->total_sold ?? 0;

    $primaryImage = $product->primary_image;
    $imageSrc = $primaryImage
        ? (str_starts_with($primaryImage, 'http') || str_starts_with($primaryImage, '/storage') ? $primaryImage : asset('storage/'.$primaryImage))
        : 'https://placehold.co/400x400/f1f5f9/64748b?text='.urlencode($product->name);

    $isWishlisted = false;
    if (auth()->check()) {
        static $wishlistedIds = null;
        if ($wishlistedIds === null) {
            $wishlistedIds = \App\Models\Wishlist::query()->where('user_id', auth()->id())->pluck('product_id')->toArray();
        }
        $isWishlisted = in_array($product->id, $wishlistedIds, true);
    }
@endphp

<article
    x-data="{
        isWishlisted: {{ $isWishlisted ? 'true' : 'false' }},
        toggleWishlist(e) {
            e.preventDefault();
            @if(!auth()->check())
                window.location.href = '{{ route('login') }}';
                return;
            @endif
            fetch('{{ route('api.wishlist.toggle', $product) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if(data.status === 'added' || data.status === 'removed') {
                    this.isWishlisted = !this.isWishlisted;
                }
            }).catch(() => {});
        }
    }"
    class="group relative flex flex-col bg-white dark:bg-gray-800/60 border border-gray-200/80 dark:border-gray-700/50 hover:shadow-xl dark:hover:shadow-2xl dark:hover:shadow-primary-900/20 transition-all duration-300 hover:-translate-y-1 h-full rounded-2xl overflow-hidden"
>
    <a href="{{ route('storefront.products.show', $product->slug) }}" class="block relative aspect-square bg-gray-100 dark:bg-gray-700/50 overflow-hidden">
        <img
            src="{{ $imageSrc }}"
            alt="{{ $product->name }}"
            class="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-110"
            loading="lazy"
            onerror="this.onerror=null;this.src='https://placehold.co/400x400/f1f5f9/64748b?text={{ urlencode($product->name) }}';"
        >

        <button @click.prevent="toggleWishlist($event)" class="absolute top-2.5 left-2.5 z-30 p-2 rounded-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm text-gray-400 hover:text-red-500 shadow-sm transition-colors border border-gray-200/50 dark:border-gray-700/50">
            <svg x-show="!isWishlisted" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
            <svg x-cloak x-show="isWishlisted" class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" /></svg>
        </button>

        <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

        @if ($product->stock_quantity <= 0)
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-[2px] flex items-center justify-center z-20">
                <span class="bg-red-600 text-white text-xs font-black px-4 py-2 rounded-full shadow-lg uppercase tracking-widest">Sold Out</span>
            </div>
        @endif

        <div class="absolute top-2.5 right-2.5 flex flex-col gap-1.5 z-10 items-end">
            @if ($activeCampaign && $activeCampaign->badge_text)
                <div class="text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md" style="background-color: {{ $activeCampaign->badge_color ?? '#f97316' }};">
                    {{ $activeCampaign->badge_text }}
                </div>
            @endif

            @if ($hasDiscount)
                <div class="bg-gradient-to-r from-rose-500 to-pink-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md">
                    Save {{ $discountPercent }}%
                </div>
            @endif

            @if ($isNew)
                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md">
                    New
                </div>
            @endif
        </div>

        @if ($displayPrice >= 50)
            <div class="absolute bottom-2.5 left-2.5 bg-emerald-500/90 backdrop-blur-sm text-white text-[9px] font-bold px-2.5 py-1 rounded-lg shadow-sm z-10 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                Free Shipping
            </div>
        @endif
    </a>

    <div class="flex flex-col flex-grow p-3.5 lg:p-4">
        <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-2 mb-2 leading-snug min-h-[36px] group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
            <a href="{{ route('storefront.products.show', $product->slug) }}">
                @if ($isVerifiedVendor)
                    <span class="inline-block bg-primary-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md mr-1 align-middle">Mall</span>
                @endif
                {{ $product->name }}
            </a>
        </h3>

        <div class="mt-auto">
            <div class="flex items-baseline gap-2">
                <span class="text-base font-black text-primary-600 dark:text-primary-400">${{ number_format($displayPrice, 2) }}</span>
                @if ($hasDiscount)
                    <span class="text-[11px] font-medium text-gray-400 dark:text-gray-500 line-through">${{ number_format($originalPrice, 2) }}</span>
                @endif
            </div>

            <div class="flex items-center justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-0.5">
                    @if ($rating > 0)
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-3 h-3 {{ $i <= round($rating) ? 'text-amber-400 fill-current' : 'text-gray-200 dark:text-gray-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        @endfor
                    @endif
                </div>
                @if ($soldCount > 0)
                    <span class="font-medium text-gray-400 dark:text-gray-500">{{ $soldCount >= 1000 ? number_format($soldCount / 1000, 1).'k' : $soldCount }} sold</span>
                @endif
            </div>

            @if ($product->vendor->vendorProfile)
                <div class="text-[10px] font-medium text-gray-400 dark:text-gray-500 mt-1.5 truncate flex items-center gap-1">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ $product->vendor->vendorProfile->store_name }}
                </div>
            @endif
        </div>
    </div>
</article>
