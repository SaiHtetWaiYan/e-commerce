<x-layouts.app>
    <div class="max-w-[1200px] mx-auto px-4 py-6">
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-6 font-medium">
            <a href="{{ route('storefront.home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white">Search</span>
        </nav>

        <div class="flex flex-col md:flex-row gap-6">
            <div class="hidden md:block w-[240px] flex-shrink-0">
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm sticky top-24 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Filters
                        </h2>
                    </div>

                    <form method="GET" action="{{ route('storefront.search.index') }}" class="p-5 space-y-6">
                        @if ($query !== '')
                            <input type="hidden" name="q" value="{{ $query }}">
                        @endif

                        <div>
                            <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">Sort By</h3>
                            <div class="relative">
                                <select name="sort" class="w-full appearance-none border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-2.5 pl-3 pr-8 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all dark:text-gray-300" onchange="this.form.submit()">
                                    <option value="latest" @selected(($filters['sort'] ?? '') === 'latest')>Newest Arrivals</option>
                                    <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
                                    <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
                                    <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Top Rated</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        @if ($categories->isNotEmpty())
                            <div class="border-t border-gray-100 dark:border-gray-800 pt-5">
                                <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">Category</h3>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="category" value="{{ $category->slug }}" @checked(($filters['category'] ?? '') === $category->slug) class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors flex-1">{{ $category->name }}</span>
                                            <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-800 rounded-full px-1.5">{{ $category->products_count }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($brands->isNotEmpty())
                            <div class="border-t border-gray-100 dark:border-gray-800 pt-5">
                                <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">Brand</h3>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach ($brands as $brand)
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="brand" value="{{ $brand->slug }}" @checked(($filters['brand'] ?? '') === $brand->slug) class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $brand->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="border-t border-gray-100 dark:border-gray-800 pt-5">
                            <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">Price Range</h3>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">$</span>
                                    <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm pl-7 pr-2 py-2.5 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300">
                                </div>
                                <span class="text-gray-400 font-medium">-</span>
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-medium">$</span>
                                    <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm pl-7 pr-2 py-2.5 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-800 pt-5">
                            <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3 uppercase tracking-wider">Rating</h3>
                            <div class="space-y-2">
                                @for ($rating = 4; $rating >= 1; $rating--)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" name="rating" value="{{ $rating }}" @checked((string) ($filters['rating'] ?? '') === (string) $rating) class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                        <div class="flex items-center gap-0.5">
                                            @for ($star = 1; $star <= 5; $star++)
                                                <svg class="w-3.5 h-3.5 {{ $star <= $rating ? 'text-amber-400 fill-current' : 'text-gray-200 dark:text-gray-600' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">& Up</span>
                                    </label>
                                @endfor
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-primary-600 text-white text-sm font-bold py-3 rounded-xl shadow-sm hover:bg-primary-700 hover:shadow-md hover:-translate-y-0.5 transition-all">
                                Apply Filters
                            </button>
                            <a href="{{ $query !== '' ? route('storefront.search.index', ['q' => $query]) : route('storefront.search.index') }}" class="px-4 py-3 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-bold text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors" title="Clear filters">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm mb-6 px-5 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Search Results</span>
                        @if ($query !== '')
                            <div class="h-4 w-px bg-gray-300 dark:bg-gray-700"></div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Results for <span class="font-semibold text-primary-600 dark:text-primary-400">"{{ $query }}"</span></span>
                        @endif
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-3 py-1 rounded-full"><span class="text-gray-900 dark:text-white">{{ $products->total() }}</span> Items</span>
                </div>

                <div class="md:hidden mb-6">
                    <form method="GET" action="{{ route('storefront.search.index') }}" class="flex gap-3">
                        @if ($query !== '')
                            <input type="hidden" name="q" value="{{ $query }}">
                        @endif
                        @foreach (['category', 'brand', 'min_price', 'max_price', 'rating'] as $hiddenFilter)
                            @if (($filters[$hiddenFilter] ?? '') !== '')
                                <input type="hidden" name="{{ $hiddenFilter }}" value="{{ $filters[$hiddenFilter] }}">
                            @endif
                        @endforeach
                        <div class="relative flex-1">
                            <select name="sort" class="w-full appearance-none border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-sm py-3 pl-4 pr-10 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent shadow-sm dark:text-gray-300 font-medium" onchange="this.form.submit()">
                                <option value="latest" @selected(($filters['sort'] ?? '') === 'latest')>Newest Arrivals</option>
                                <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
                                <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
                                <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Top Rated</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse ($products as $product)
                        <div class="h-full">
                            <x-storefront.product-card :product="$product" />
                        </div>
                    @empty
                        <div class="col-span-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl py-20 px-4 text-center shadow-sm">
                            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No results found</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">Try broadening your keywords or clearing some filters.</p>
                            <a href="{{ $query !== '' ? route('storefront.search.index', ['q' => $query]) : route('storefront.search.index') }}" class="inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 font-bold hover:text-primary-700 dark:hover:text-primary-300 hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Clear filters
                            </a>
                        </div>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <div class="mt-8 bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
