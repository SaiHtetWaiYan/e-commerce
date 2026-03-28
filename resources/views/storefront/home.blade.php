<x-layouts.app>
    <div class="max-w-[1200px] mx-auto px-4 mt-6 space-y-6">

        <!-- Banner Carousel + Side Panels -->
        <div class="flex gap-4">
            <!-- Main Carousel -->
            <div class="flex-1 min-w-0 rounded-2xl overflow-hidden shadow-sm">
                <x-storefront.banner-carousel :banners="$banners" />
            </div>

            <!-- Side Promo Panels (Desktop Only) -->
            <div class="hidden lg:flex flex-col gap-4 w-[320px] flex-shrink-0">
                <div class="flex-1 bg-gradient-to-br from-primary-600 to-primary-500 dark:from-primary-700 dark:to-primary-600 rounded-2xl p-6 flex flex-col justify-center text-white shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-bold tracking-wider opacity-90 uppercase">New Member</p>
                        <p class="text-2xl font-black mt-1 leading-tight">Get 20% Off</p>
                        <p class="text-sm mt-1 opacity-90 font-medium">On your first order</p>
                        <a href="{{ route('storefront.products.index') }}" class="mt-4 bg-white text-primary-600 text-sm font-bold px-5 py-2.5 rounded-xl inline-block text-center hover:bg-gray-50 hover:shadow-lg hover:-translate-y-0.5 transition-all w-fit">SHOP NOW</a>
                    </div>
                </div>
                <div class="flex-1 bg-gradient-to-br from-accent-600 to-accent-500 dark:from-accent-700 dark:to-accent-600 rounded-2xl p-6 flex flex-col justify-center text-white shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative z-10">
                        <p class="text-xs font-bold tracking-wider opacity-90 uppercase">Free Delivery</p>
                        <p class="text-2xl font-black mt-1 leading-tight">Orders $50+</p>
                        <p class="text-sm mt-1 opacity-90 font-medium">Nationwide shipping</p>
                        <a href="{{ route('storefront.products.index') }}" class="mt-4 bg-white text-accent-600 text-sm font-bold px-5 py-2.5 rounded-xl inline-block text-center hover:bg-gray-50 hover:shadow-lg hover:-translate-y-0.5 transition-all w-fit">LEARN MORE</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Icons -->
        <div class="bg-white dark:bg-gray-900/80 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-800">
            <x-storefront.category-icons :categories="$categories" />
        </div>

        <!-- Flash Sale -->
        <div class="bg-white dark:bg-gray-900/80 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
            <x-storefront.flash-sale :products="$flashSaleProducts" :campaign="$activeFlashSaleCampaign" />
        </div>

        @if ($activeCampaigns->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-4 px-1">
                    <h2 class="font-bold text-gray-900 dark:text-white text-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.082V19m0 0l-5.5-3M11 19l5.5-3M4 7l7-4 7 4M4 7v10l7 4 7-4V7"/></svg>
                        Campaign Picks
                    </h2>
                    <a href="{{ route('storefront.campaigns.index') }}" class="text-primary-600 dark:text-primary-400 text-sm font-semibold hover:text-primary-700 dark:hover:text-primary-300 transition-colors flex items-center gap-1">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($activeCampaigns as $campaign)
                        <a href="{{ route('storefront.campaigns.show', $campaign->slug) }}" class="group relative overflow-hidden rounded-2xl border border-orange-200/70 dark:border-orange-900/40 bg-gradient-to-br from-orange-50 via-white to-amber-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 p-5 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="absolute -right-8 -top-8 h-24 w-24 rounded-full bg-orange-200/40 blur-2xl dark:bg-orange-500/20"></div>
                            <div class="relative flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-orange-500">{{ $campaign->badge_text ?: 'Campaign' }}</p>
                                    <h3 class="mt-1 text-lg font-black text-gray-900 dark:text-white">{{ $campaign->name }}</h3>
                                    <p class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                                        Ends {{ $campaign->ends_at?->diffForHumans() }}
                                    </p>
                                </div>
                                <span class="inline-flex rounded-full border border-orange-200 dark:border-orange-900/40 bg-white/80 dark:bg-gray-800 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-orange-600 dark:text-orange-300">
                                    {{ $campaign->products_count }} items
                                </span>
                            </div>
                            <span class="mt-4 inline-flex items-center gap-1 text-sm font-bold text-orange-600 dark:text-orange-300">
                                Shop Now
                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Active Campaigns -->
        @if ($activeCampaigns->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-4 px-1">
                    <h2 class="font-black text-gray-900 dark:text-white text-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.082V19m0 0l-5.5-3M11 19l5.5-3M4 7l7-4 7 4M4 7v10l7 4 7-4V7"/></svg>
                        Special Campaigns
                    </h2>
                    <a href="{{ route('storefront.campaigns.index') }}" class="text-accent-600 dark:text-accent-400 text-sm font-semibold hover:text-accent-700 dark:hover:text-accent-300 transition-colors flex items-center gap-1">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($activeCampaigns as $campaign)
                        <a href="{{ route('storefront.campaigns.show', $campaign->slug) }}" class="group relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 block h-[180px]">
                            @if ($campaign->banner_image)
                                <img src="{{ Storage::url($campaign->banner_image) }}" alt="{{ $campaign->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 dark:opacity-60">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-accent-500/80 to-primary-600/80"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent"></div>
                            <div class="absolute inset-0 p-5 flex flex-col justify-end">
                                <div class="flex justify-between items-end gap-2">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-wider mb-1" style="color: {{ $campaign->badge_color ?? '#f97316' }}">{{ $campaign->badge_text ?: 'Campaign' }}</p>
                                        <h3 class="text-lg font-black text-white line-clamp-1">{{ $campaign->name }}</h3>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="flex flex-col gap-0.5 text-xs font-medium text-gray-300">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Ends {{ $campaign->ends_at?->diffForHumans() }}
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center justify-center rounded-lg bg-white/20 backdrop-blur-md px-2.5 py-1 text-xs font-bold text-white shadow-sm border border-white/10 group-hover:bg-white group-hover:text-primary-600 transition-colors">
                                        Shop Now
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Featured Products -->
        @if ($featuredProducts->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-4 px-1">
                    <h2 class="font-bold text-gray-900 dark:text-white text-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        Featured Products
                    </h2>
                    <a href="{{ route('storefront.products.index') }}" class="text-primary-600 dark:text-primary-400 text-sm font-semibold hover:text-primary-700 dark:hover:text-primary-300 transition-colors flex items-center gap-1">
                        See All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($featuredProducts as $product)
                        <x-storefront.product-card :product="$product" />
                    @endforeach
                </div>
            </section>
        @endif

        @if ($recentlyViewedProducts->isNotEmpty())
            <section>
                <div class="flex items-center justify-between mb-4 px-1">
                    <h2 class="font-bold text-gray-900 dark:text-white text-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"/></svg>
                        Recently Viewed
                    </h2>
                    <a href="{{ route('storefront.products.index') }}" class="text-primary-600 dark:text-primary-400 text-sm font-semibold hover:text-primary-700 dark:hover:text-primary-300 transition-colors flex items-center gap-1">
                        Browse More
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($recentlyViewedProducts as $recentlyViewedProduct)
                        <x-storefront.product-card :product="$recentlyViewedProduct" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Just For You -->
        <section>
            <div class="flex items-center justify-center py-4 mb-4">
                <h2 class="font-black text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-accent-600 dark:from-primary-400 dark:to-accent-400 text-2xl relative inline-block">
                    Just For You
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-12 h-1 bg-gradient-to-r from-primary-500 to-accent-500 rounded-full"></div>
                </h2>
            </div>

            @if ($justForYouProducts->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($justForYouProducts as $product)
                        <x-storefront.product-card :product="$product" />
                    @endforeach
                </div>

                <div class="p-8 text-center">
                    <a href="{{ route('storefront.products.index') }}" class="inline-flex items-center justify-center gap-2 border-2 border-primary-600 text-primary-600 dark:border-primary-400 dark:text-primary-400 hover:bg-primary-600 hover:text-white dark:hover:bg-primary-500 dark:hover:text-white font-bold text-sm px-10 py-3 rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5">
                        Load More Products
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>
            @else
                <div class="text-center py-16 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="text-base font-medium">No products available yet.</p>
                    <p class="text-sm mt-2">Run <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-primary-600 dark:text-primary-400 font-mono text-xs">php artisan db:seed</code> to populate data.</p>
                </div>
            @endif
        </section>

    </div>
</x-layouts.app>
