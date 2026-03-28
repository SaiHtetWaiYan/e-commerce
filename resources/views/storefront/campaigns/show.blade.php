<x-layouts.app>
    <div class="max-w-[1200px] mx-auto px-4 py-6 space-y-6">
        <section class="relative overflow-hidden rounded-3xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            @if ($campaign->banner_image)
                <img src="{{ Storage::url($campaign->banner_image) }}" alt="{{ $campaign->name }}" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-black/50"></div>
            @else
                <div class="absolute inset-0 bg-gradient-to-r from-orange-500 via-amber-500 to-orange-600"></div>
            @endif

            <div class="relative p-6 sm:p-8" x-data="campaignTimer(@js($campaign->ends_at?->toIso8601String()))">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wide text-white" style="background-color: {{ $campaign->badge_color ?? '#f97316' }};">
                        {{ $campaign->badge_text ?: 'Campaign' }}
                    </span>
                    <span class="inline-flex rounded-full border border-white/40 bg-white/15 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-white">
                        {{ $campaign->isRunning() ? 'Live Now' : ($campaign->starts_at?->isFuture() ? 'Coming Soon' : 'Ended') }}
                    </span>
                </div>

                <h1 class="mt-4 text-3xl font-black tracking-tight text-white">{{ $campaign->name }}</h1>
                <p class="mt-2 max-w-2xl text-sm font-medium text-white/90">{{ $campaign->description ?: 'Campaign pricing is available on selected products for a limited time.' }}</p>

                <div class="mt-5 flex flex-wrap items-center gap-2 text-xs font-bold text-white/95">
                    <span class="rounded-lg bg-white/15 px-2.5 py-1">{{ $campaign->starts_at?->format('M d, Y H:i') }}</span>
                    <span>to</span>
                    <span class="rounded-lg bg-white/15 px-2.5 py-1">{{ $campaign->ends_at?->format('M d, Y H:i') }}</span>
                </div>

                @if ($campaign->isRunning())
                    <div class="mt-5 inline-flex items-center gap-1.5 rounded-xl bg-black/25 px-3 py-2 text-xs font-black text-white">
                        <span class="rounded bg-white/90 px-2 py-1 text-orange-600" x-text="hours.toString().padStart(2, '0')">00</span>
                        <span>:</span>
                        <span class="rounded bg-white/90 px-2 py-1 text-orange-600" x-text="minutes.toString().padStart(2, '0')">00</span>
                        <span>:</span>
                        <span class="rounded bg-white/90 px-2 py-1 text-orange-600" x-text="seconds.toString().padStart(2, '0')">00</span>
                        <span class="ml-1 uppercase tracking-wide">left</span>
                    </div>
                @endif
            </div>
        </section>

        <div class="flex flex-col gap-6 md:flex-row">
            <aside class="w-full md:w-64 flex-shrink-0">
                <form method="GET" action="{{ route('storefront.campaigns.show', $campaign->slug) }}" class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 shadow-sm space-y-5">
                    <div>
                        <h2 class="text-sm font-black uppercase tracking-wide text-gray-900 dark:text-white">Filters</h2>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</label>
                        <select name="category" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" @selected(($filters['category'] ?? null) === $category->slug)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Brand</label>
                        <select name="brand" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">All Brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->slug }}" @selected(($filters['brand'] ?? null) === $brand->slug)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Price Range</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input name="min_price" type="number" min="0" placeholder="Min" value="{{ $filters['min_price'] ?? '' }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <input name="max_price" type="number" min="0" placeholder="Max" value="{{ $filters['max_price'] ?? '' }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Sort</label>
                        <select name="sort" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="featured" @selected(($filters['sort'] ?? 'featured') === 'featured')>Featured</option>
                            <option value="price_asc" @selected(($filters['sort'] ?? null) === 'price_asc')>Price: Low to High</option>
                            <option value="price_desc" @selected(($filters['sort'] ?? null) === 'price_desc')>Price: High to Low</option>
                            <option value="best_selling" @selected(($filters['sort'] ?? null) === 'best_selling')>Best Selling</option>
                            <option value="rating" @selected(($filters['sort'] ?? null) === 'rating')>Top Rated</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" class="rounded-xl bg-primary-600 px-3 py-2.5 text-xs font-bold uppercase tracking-wide text-white hover:bg-primary-700">Apply</button>
                        <a href="{{ route('storefront.campaigns.show', $campaign->slug) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-center text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Reset</a>
                    </div>
                </form>
            </aside>

            <div class="flex-1 min-w-0 space-y-4">
                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-5 py-3 shadow-sm flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $products->total() }} Campaign Products</p>
                    <a href="{{ route('storefront.campaigns.index') }}" class="text-xs font-bold uppercase tracking-wide text-primary-600 dark:text-primary-400 hover:underline">All Campaigns</a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($products as $product)
                        @php
                            $campaignPrice = $campaign->getCampaignPriceForEnrolledProduct($product);
                            $basePrice = (float) $product->base_price;
                            $hasDiscount = $campaignPrice < $basePrice;
                            $discountPercent = $hasDiscount ? round((($basePrice - $campaignPrice) / $basePrice) * 100) : 0;

                            $primaryImage = $product->primary_image;
                            $imageSrc = $primaryImage
                                ? (str_starts_with($primaryImage, 'http') || str_starts_with($primaryImage, '/storage') ? $primaryImage : asset('storage/'.$primaryImage))
                                : 'https://placehold.co/400x400/f1f5f9/64748b?text='.urlencode($product->name);
                        @endphp
                        <a href="{{ route('storefront.products.show', $product->slug) }}" class="group overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-800">
                                <img src="{{ $imageSrc }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                <div class="absolute left-2.5 top-2.5 inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white" style="background-color: {{ $campaign->badge_color ?? '#f97316' }};">
                                    {{ $campaign->badge_text ?: 'Sale' }}
                                </div>
                                @if ($hasDiscount)
                                    <div class="absolute right-2.5 top-2.5 inline-flex rounded-full bg-rose-500 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white">
                                        -{{ $discountPercent }}%
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="line-clamp-2 text-sm font-bold text-gray-900 dark:text-white">{{ $product->name }}</h3>
                                <div class="mt-2 flex items-end gap-2">
                                    <span class="text-lg font-black text-primary-600 dark:text-primary-400">${{ number_format($campaignPrice, 2) }}</span>
                                    @if ($hasDiscount)
                                        <span class="text-xs font-medium text-gray-400 line-through">${{ number_format($basePrice, 2) }}</span>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $product->vendor->vendorProfile?->store_name ?? $product->vendor->name }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 py-12 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                            No products match the selected filters.
                        </div>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function campaignTimer(campaignEndsAt) {
            return {
                hours: 0,
                minutes: 0,
                seconds: 0,
                init() {
                    if (! campaignEndsAt) {
                        return;
                    }

                    this.update(campaignEndsAt);
                    setInterval(() => this.update(campaignEndsAt), 1000);
                },
                update(endsAt) {
                    const end = new Date(endsAt);
                    const now = new Date();
                    const diff = Math.max(0, Math.floor((end - now) / 1000));

                    this.hours = Math.floor(diff / 3600);
                    this.minutes = Math.floor((diff % 3600) / 60);
                    this.seconds = diff % 60;
                }
            };
        }
    </script>
</x-layouts.app>
