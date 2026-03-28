<x-layouts.app>
    @php
        $activeCampaign = $product->getActiveCampaign();
        $campaignPrice = $product->getActiveCampaignPrice();
        $displayPrice = $campaignPrice ?? (float) $product->base_price;

        $legacyReferencePrice = $product->compare_price && $product->compare_price > $product->base_price
            ? (float) $product->compare_price
            : null;
        $originalPrice = $campaignPrice !== null ? (float) $product->base_price : $legacyReferencePrice;
        $hasDiscount = $originalPrice !== null && $originalPrice > $displayPrice;
        $discountPercent = $hasDiscount ? round((($originalPrice - $displayPrice) / $originalPrice) * 100) : 0;

        $rating = $product->avg_rating ?? 0;
        $reviewCount = $product->review_count ?? 0;
        $soldCount = $product->total_sold ?? 0;
        $isVerified = $product->vendor->vendorProfile?->is_verified ?? false;
        $isNew = $product->created_at && $product->created_at->diffInDays(now()) <= 7;
    @endphp

    <div class="max-w-[1200px] mx-auto px-4 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-6 bg-white dark:bg-gray-900 px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm w-fit">
            <a href="{{ route('storefront.home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>
            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('storefront.products.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Products</a>
            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white line-clamp-1 max-w-[200px] sm:max-w-xs font-bold">{{ $product->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left: Image Gallery -->
            <div class="lg:w-[480px] flex-shrink-0" x-data="{ activeImage: 0 }">
                <!-- Main Image -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm aspect-square overflow-hidden mb-4 relative group">
                    @forelse ($product->images as $index => $image)
                        @php
                            $imgSrc = str_starts_with($image->image_path, 'http') || str_starts_with($image->image_path, '/storage') ? $image->image_path : asset('storage/'.$image->image_path);
                        @endphp
                        <img x-show="activeImage === {{ $index }}" src="{{ $imgSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105" x-transition.opacity.duration.300ms onerror="this.onerror=null;this.src='https://placehold.co/600x600/f1f5f9/64748b?text={{ urlencode($product->name) }}';">
                    @empty
                        <img src="https://placehold.co/600x600/f1f5f9/64748b?text={{ urlencode($product->name) }}" alt="{{ $product->name }}" class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105">
                    @endforelse
                </div>

                <!-- Thumbnails -->
                @if ($product->images->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                        @foreach ($product->images as $index => $image)
                            @php
                                $thumbSrc = str_starts_with($image->image_path, 'http') ? $image->image_path : asset('storage/'.$image->image_path);
                            @endphp
                            <button @click="activeImage = {{ $index }}"
                                    :class="activeImage === {{ $index }} ? 'border-primary-600 dark:border-primary-500 ring-2 ring-primary-600/20 dark:ring-primary-500/20' : 'border-gray-200 dark:border-gray-700 opacity-70 hover:opacity-100'"
                                    class="w-20 h-20 flex-shrink-0 border-2 rounded-xl overflow-hidden bg-gray-50 dark:bg-gray-800 transition-all duration-200">
                                <img src="{{ $thumbSrc }}" alt="" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Center: Product Info -->
            <div class="flex-1 bg-white dark:bg-gray-900 p-6 md:p-8 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm" x-data="{ qty: 1 }">
                <!-- Title -->
                <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white mb-4 leading-tight tracking-tight">
                    @if ($isVerified)
                        <span class="inline-flex items-center gap-1 bg-gradient-to-r from-primary-600 to-accent-500 text-white text-xs font-black px-2 py-1 rounded-md mr-2 align-middle shadow-sm uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Mall
                        </span>
                    @endif
                    @if ($isNew)
                        <span class="inline-flex items-center gap-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-xs font-black px-2 py-1 rounded-md mr-2 align-middle shadow-sm uppercase tracking-wider">
                            New
                        </span>
                    @endif
                    {{ $product->name }}
                </h1>

                <!-- Rating & Sold -->
                <div class="flex flex-wrap items-center gap-y-2 gap-x-4 text-sm mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-1.5 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-500 px-2 py-1 rounded-lg">
                        <span class="font-black">{{ number_format($rating, 1) }}</span>
                        <div class="flex">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($rating) ? 'text-yellow-400 fill-current' : 'text-yellow-200 dark:text-yellow-700/50' }}" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                    <a href="#reviews" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors">{{ $reviewCount }} Ratings</a>
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                    <span class="text-gray-600 dark:text-gray-400 font-medium whitespace-nowrap"><span class="text-gray-900 dark:text-white font-bold">{{ $soldCount >= 1000 ? number_format($soldCount / 1000, 1).'k' : $soldCount }}</span> Sold</span>
                </div>

                <!-- Price -->
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 mb-6 border border-gray-100 dark:border-gray-800">
                    <div class="flex items-end gap-3 flex-wrap">
                        <span class="text-4xl font-black text-primary-600 dark:text-primary-400 tracking-tight">${{ number_format($displayPrice, 2) }}</span>
                        @if ($hasDiscount)
                            <span class="text-lg font-bold text-gray-400 dark:text-gray-500 line-through mb-1">${{ number_format($originalPrice, 2) }}</span>
                            <span class="bg-accent-100 text-accent-700 dark:bg-accent-900/30 dark:text-accent-400 text-sm font-black px-2 py-1 rounded-lg mb-1.5 uppercase tracking-wide">Save {{ $discountPercent }}%</span>
                        @endif
                        @if ($activeCampaign && $activeCampaign->badge_text)
                            <span class="text-sm font-black px-2 py-1 rounded-lg mb-1.5 uppercase tracking-wide text-white" style="background-color: {{ $activeCampaign->badge_color ?? '#f97316' }};">
                                {{ $activeCampaign->badge_text }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Shipping -->
                <div class="flex items-start sm:items-center gap-4 mb-6 text-sm">
                    <span class="text-gray-500 dark:text-gray-400 font-medium w-16 flex-shrink-0">Shipping</span>
                    <div class="flex items-center gap-2 text-gray-900 dark:text-white bg-green-50 dark:bg-green-900/10 px-3 py-2 rounded-lg border border-green-100 dark:border-green-900/30 flex-1 sm:flex-none">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        @if ($displayPrice >= 50)
                            <span class="text-green-700 dark:text-green-400 font-bold">Free Shipping</span>
                        @else
                            <span class="font-medium">Standard Shipping</span>
                        @endif
                    </div>
                </div>

                <!-- Stock Indicator -->
                <div class="flex items-center gap-4 mb-6 text-sm">
                    <span class="text-gray-500 dark:text-gray-400 font-medium w-16 flex-shrink-0">Stock</span>
                    @if ($product->stock_quantity <= 0)
                        <div class="flex items-center gap-2 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 px-3 py-2 rounded-lg border border-red-200 dark:border-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <span class="font-bold">Out of Stock</span>
                        </div>
                    @elseif ($product->stock_quantity <= 5)
                        <div class="flex items-center gap-2 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 px-3 py-2 rounded-lg border border-orange-200 dark:border-orange-800 animate-pulse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="font-bold">Only {{ $product->stock_quantity }} left in stock!</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 px-3 py-2 rounded-lg border border-emerald-200 dark:border-emerald-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-bold">In Stock</span>
                        </div>
                    @endif
                </div>

                <!-- Description Snippet -->
                <div class="mb-8 pb-8 border-b border-gray-100 dark:border-gray-800">
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed font-medium">{{ $product->short_description ?? Str::limit($product->description, 200) }}</p>
                </div>

                {{-- Variant Selection --}}
                @if ($product->variants->isNotEmpty())
                    @php
                        $attributeGroups = $product->variants
                            ->flatMap(fn ($v) => $v->attributeValues->map(fn ($av) => [
                                'attribute' => $av->attribute->name,
                                'value' => $av->value,
                                'variant_id' => $v->id,
                            ]))
                            ->groupBy('attribute');
                    @endphp
                    <div class="mb-8 pb-8 border-b border-gray-100 dark:border-gray-800 space-y-5" x-data="{ selectedVariant: null }">
                        @foreach ($attributeGroups as $attrName => $values)
                            <div>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 block">{{ $attrName }}</span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($values->unique('value') as $item)
                                        <button type="button"
                                            @click="selectedVariant = {{ $item['variant_id'] }}"
                                            :class="selectedVariant === {{ $item['variant_id'] }} ? 'border-primary-600 dark:border-primary-500 bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 ring-2 ring-primary-600/20' : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-gray-500'"
                                            class="px-4 py-2 rounded-xl text-sm font-bold border-2 transition-all">
                                            {{ $item['value'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <input type="hidden" name="variant_id" :value="selectedVariant" form="add-to-cart-form">
                    </div>
                @endif

                @if ($product->stock_quantity > 0)
                <!-- Quantity -->
                <div class="mb-8 grid gap-3 sm:grid-cols-[auto_1fr] sm:items-center">
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Quantity</span>
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex items-center rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shadow-sm">
                            <button type="button" @click="if(qty > 1) qty--" class="h-12 w-12 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:text-primary-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                            </button>
                            <input type="number" x-model="qty" min="1" max="{{ $product->stock_quantity }}" class="h-12 w-16 text-center text-xl font-black text-gray-900 dark:text-white border-none focus:ring-0 p-0 bg-transparent" form="add-to-cart-form">
                            <button type="button" @click="if(qty < {{ $product->stock_quantity }}) qty++" class="h-12 w-12 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-white dark:hover:bg-gray-700 hover:text-primary-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <span class="inline-flex items-center rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm font-bold text-gray-500 dark:text-gray-400">
                            {{ $product->stock_quantity }} pieces available
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <form id="add-to-cart-form" action="{{ route('storefront.cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" :value="qty">

                    <div class="flex flex-wrap items-center gap-3 sm:gap-4"
                        x-data="{
                            isWishlisted: {{ auth()->check() && \App\Models\Wishlist::where('user_id', auth()->id())->where('product_id', $product->id)->exists() ? 'true' : 'false' }},
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
                    >
                        <button type="submit" class="flex min-h-14 flex-[1_1_220px] items-center justify-center gap-2 rounded-xl border-2 border-primary-600 dark:border-primary-500 bg-primary-50 dark:bg-primary-900/20 px-4 text-sm font-bold uppercase tracking-[0.02em] text-primary-700 dark:text-primary-400 shadow-sm transition-all hover:-translate-y-0.5 hover:bg-primary-100 dark:hover:bg-primary-900/40">
                            <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span class="whitespace-nowrap">Add to Cart</span>
                        </button>
                        <button type="submit" class="flex min-h-14 flex-[1_1_220px] items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-bold uppercase tracking-[0.02em] text-white transition-all hover:-translate-y-0.5 hover:bg-primary-700 hover:shadow-lg hover:shadow-primary-500/30">
                            <span class="whitespace-nowrap">Buy Now</span>
                        </button>

                        <button @click="toggleWishlist($event)" type="button" class="h-14 w-14 shrink-0 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 shadow-sm transition-all hover:-translate-y-0.5 hover:border-red-500 hover:bg-red-50 hover:text-red-500 dark:hover:border-red-500 dark:hover:bg-red-900/20 dark:hover:text-red-400 flex items-center justify-center" title="Add to Wishlist">
                            <svg x-show="!isWishlisted" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            <svg x-cloak x-show="isWishlisted" class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" /></svg>
                        </button>
                    </div>
                </form>
                @else
                <!-- Out of Stock -->
                <div class="mb-8 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-2xl p-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-red-400 dark:text-red-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    <p class="text-red-700 dark:text-red-400 font-bold text-lg">This item is currently out of stock</p>
                    <p class="text-red-500 dark:text-red-500 text-sm font-medium mt-1">Check back later or browse similar products</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button disabled class="flex-1 h-14 bg-gray-100 dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-700 text-gray-400 dark:text-gray-500 font-bold rounded-xl cursor-not-allowed flex items-center justify-center gap-2 uppercase tracking-wide text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Add to Cart
                    </button>
                    <button disabled class="flex-1 h-14 bg-gray-300 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-bold rounded-xl cursor-not-allowed text-sm uppercase tracking-wide">
                        Sold Out
                    </button>
                </div>
                @endif

                {{-- Social Sharing --}}
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Share this product</p>
                    <div class="flex items-center gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-[#1877F2] text-white flex items-center justify-center hover:opacity-80 hover:scale-110 transition-all" title="Share on Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center hover:opacity-80 hover:scale-110 transition-all" title="Share on X">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' - ' . request()->url()) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-[#25D366] text-white flex items-center justify-center hover:opacity-80 hover:scale-110 transition-all" title="Share on WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>
                        <button onclick="navigator.clipboard.writeText(window.location.href).then(() => { this.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'/></svg>'; setTimeout(() => { this.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3\'/></svg>'; }, 2000) })" class="w-9 h-9 rounded-xl bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 hover:scale-110 transition-all" title="Copy link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Seller Card (Desktop) -->
            <!-- Right: Seller Card (Desktop) -->
            <div class="hidden lg:block w-[320px] flex-shrink-0 space-y-4">
                <!-- Seller Info -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6">
                    <div class="flex items-center gap-4 mb-5 pb-5 border-b border-gray-100 dark:border-gray-800">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-100 to-primary-50 dark:from-primary-900/40 dark:to-primary-800/20 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-black text-xl shadow-sm border border-primary-200 dark:border-primary-800/50">
                            {{ substr($product->vendor->vendorProfile?->store_name ?? $product->vendor->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-base text-gray-900 dark:text-white truncate" title="{{ $product->vendor->vendorProfile?->store_name ?? $product->vendor->name }}">{{ $product->vendor->vendorProfile?->store_name ?? $product->vendor->name }}</p>
                            @if ($isVerified)
                                <div class="flex items-center gap-1 mt-1 text-accent-600 dark:text-accent-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    <span class="text-xs font-bold uppercase tracking-wider">Verified Seller</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-center mb-5">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 border border-gray-100 dark:border-gray-700">
                            <p class="font-black text-lg text-gray-900 dark:text-white">95%</p>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Positive</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 border border-gray-100 dark:border-gray-700">
                            <p class="font-black text-lg text-gray-900 dark:text-white">97%</p>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">Ship on Time</p>
                        </div>
                    </div>

                    <a href="{{ $product->vendor->vendorProfile ? route('storefront.vendor.show', $product->vendor->vendorProfile->store_slug) : '#' }}" class="block w-full text-center border-2 border-primary-600 dark:border-primary-500 text-primary-600 dark:text-primary-400 text-sm font-bold uppercase tracking-wide py-2.5 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:-translate-y-0.5 transition-all">
                        Visit Store
                    </a>
                </div>

                <!-- Delivery Info -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6 text-sm">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider text-xs">Delivery Details</h3>
                    <div class="space-y-4 text-gray-600 dark:text-gray-400 font-medium">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="pt-1">
                                <p class="text-sm text-gray-900 dark:text-white font-bold">Ship from Warehouse</p>
                                <p class="text-xs mt-0.5">Dispatched within 24 hours</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="pt-1">
                                <p class="text-sm text-gray-900 dark:text-white font-bold">Standard Delivery</p>
                                <p class="text-xs mt-0.5">Estimated 3-7 business days</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Info -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6 text-sm">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-4 uppercase tracking-wider text-xs">Return & Warranty</h3>
                    <div class="space-y-4 text-gray-600 dark:text-gray-400 font-medium">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-accent-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                            </div>
                            <div class="pt-1.5">
                                <p class="text-sm text-gray-900 dark:text-white font-bold">7-Day Free Returns</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div class="pt-1.5">
                                <p class="text-sm text-gray-600 dark:text-gray-400 font-medium whitespace-nowrap">Warranty not available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Description & Reviews -->
        <!-- Product Description & Reviews -->
        <div class="mt-8 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6 md:p-8">
            <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                Product Description
            </h2>
            <div class="text-base text-gray-700 dark:text-gray-300 leading-loose prose dark:prose-invert max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        <!-- Reviews Section -->
        @if ($product->reviews->isNotEmpty())
            <div class="mt-8 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6 md:p-8" id="reviews">
                <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    Ratings & Reviews <span class="text-sm font-medium text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full ml-2">{{ $product->reviews->count() }}</span>
                </h2>
                <div class="space-y-6">
                    @foreach ($product->reviews->take(5) as $review)
                        <div class="pb-6 border-b border-gray-50 dark:border-gray-800/50 last:border-0 last:pb-0">
                            <div class="flex items-center gap-1.5 mb-2">
                                <div class="flex">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400 fill-current' : 'text-gray-300 dark:text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($review->rating, 1) }}</span>
                            </div>
                            @if ($review->comment)
                                <p class="text-base text-gray-700 dark:text-gray-300 mb-3">{{ $review->comment }}</p>
                            @endif
                            @if ($review->reviewImages->isNotEmpty())
                                <div class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                                    @foreach ($review->reviewImages as $reviewImage)
                                        @if ($reviewImage->media_type === 'video')
                                            <video controls class="h-36 w-full rounded-2xl border border-gray-200 object-cover dark:border-gray-700">
                                                <source src="{{ Storage::url($reviewImage->file_path) }}">
                                            </video>
                                        @else
                                            <img src="{{ Storage::url($reviewImage->file_path) }}" alt="Review media for {{ $product->name }}" class="h-36 w-full rounded-2xl border border-gray-200 object-cover dark:border-gray-700">
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            <div class="flex items-center gap-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                                <span>by <strong class="text-gray-700 dark:text-gray-300">{{ $review->user->name ?? 'Anonymous' }}</strong></span>
                                <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                                <span>{{ $review->created_at->diffForHumans() }}</span>
                                @if ($review->is_verified_purchase)
                                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                                    <span class="text-accent-600 dark:text-accent-500 flex items-center gap-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Verified Purchase
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($recentlyViewedProducts->isNotEmpty())
            <section class="mt-8 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-wider">Recently Viewed</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($recentlyViewedProducts as $recentlyViewedProduct)
                            <x-storefront.product-card :product="$recentlyViewedProduct" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Related Products -->
        @if ($relatedProducts->isNotEmpty())
            <section class="mt-8 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden mb-8">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                    <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-wider">You May Also Like</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($relatedProducts->take(6) as $related)
                            <x-storefront.product-card :product="$related" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>

    {{-- SEO Structured Data --}}
    @php
        $seoImage = $product->images->isNotEmpty()
            ? (str_starts_with($product->images->first()->image_path, 'http') ? $product->images->first()->image_path : asset('storage/'.$product->images->first()->image_path))
            : '';
        $seoPrice = number_format($product->base_price, 2, '.', '');
        $seoAvailability = $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';
        $seoSeller = $product->vendor->vendorProfile?->store_name ?? $product->vendor->name;
        $seoBrand = $product->brand?->name ?? 'Unbranded';
        $seoRating = number_format($product->avg_rating ?? 0, 1);
        $seoReviewCount = $product->review_count ?? 0;
    @endphp
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Product",
        "name": @json($product->name),
        "description": @json(Str::limit(strip_tags($product->description ?? ''), 300)),
        "image": @json($seoImage),
        "sku": @json($product->sku ?? ''),
        "brand": {
            "@@type": "Brand",
            "name": @json($seoBrand)
        },
        "offers": {
            "@@type": "Offer",
            "url": @json(request()->url()),
            "priceCurrency": "USD",
            "price": @json($seoPrice),
            "availability": @json($seoAvailability),
            "seller": {
                "@@type": "Organization",
                "name": @json($seoSeller)
            }
        }
        @if (($product->avg_rating ?? 0) > 0)
        ,"aggregateRating": {
            "@@type": "AggregateRating",
            "ratingValue": @json($seoRating),
            "reviewCount": @json($seoReviewCount)
        }
        @endif
    }
    </script>
</x-layouts.app>
