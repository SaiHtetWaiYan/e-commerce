<x-layouts.app>
    <div class="max-w-[1200px] mx-auto px-4 py-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-6 font-medium">
            <a href="{{ route('storefront.home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white font-bold">{{ $category->name }}</span>
        </nav>

        <!-- Category Header -->
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-6 mb-4 shadow-sm">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight mb-1">{{ $category->name }}</h1>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $products->total() }} products found</p>
        </div>

        <!-- Subcategories -->
        @if($category->children->isNotEmpty())
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 mb-4 shadow-sm">
                <div class="flex gap-2 overflow-x-auto pb-1">
                    @foreach($category->children as $child)
                        <a href="{{ route('storefront.categories.show', $child->slug) }}" class="flex-shrink-0 inline-flex items-center px-4 py-2 border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300 rounded-xl hover:border-primary-500 dark:hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all whitespace-nowrap shadow-sm">
                            {{ $child->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Filters -->
            <aside class="w-full md:w-64 flex-shrink-0">
                <form id="filter-form" action="{{ route('storefront.categories.show', $category->slug) }}" method="GET" class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-5 shadow-sm sticky top-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">Filters</h2>
                        @if(request()->hasAny(['brand', 'min_price', 'max_price', 'rating']))
                            <a href="{{ route('storefront.categories.show', $category->slug) }}" class="text-xs font-bold text-primary-600 hover:text-primary-800 dark:text-primary-400">Clear All</a>
                        @endif
                    </div>

                    <!-- Hidden Sort Input to preserve sorting -->
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <!-- Price Range -->
                    <div class="mb-6">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3">Price Range</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:text-white">
                            <span class="text-gray-500">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full text-sm border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:text-white">
                        </div>
                    </div>

                    <!-- Minimum Rating -->
                    <div class="mb-6">
                        <h3 class="text-xs font-bold text-gray-900 dark:text-white mb-3">Minimum Rating</h3>
                        <div class="space-y-2">
                            @foreach([4, 3, 2, 1] as $rating)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $rating }}" {{ request('rating') == $rating ? 'checked' : '' }} class="text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    <div class="flex items-center text-yellow-400">
                                        @for($i=0; $i<$rating; $i++)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-xs text-gray-600 dark:text-gray-400 ml-1">& Up</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-xl transition-colors">
                        Apply Filters
                    </button>
                </form>
            </aside>

            <!-- Product Grid Area -->
            <div class="flex-1">
                <!-- Sort Bar -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl px-5 py-3 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between shadow-sm gap-3">
                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }}</span>
                    
                    <div class="flex items-center gap-2 text-sm w-full sm:w-auto">
                        <span class="text-gray-500 dark:text-gray-400 font-bold text-xs uppercase tracking-wider hidden sm:inline">Sort By:</span>
                        <select form="filter-form" name="sort" class="w-full sm:w-auto border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-2 px-3 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all text-gray-700 dark:text-gray-300 font-medium" onchange="document.getElementById('filter-form').submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest Arrivals</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse ($products as $product)
                        <x-storefront.product-card :product="$product" />
                    @empty
                        <div class="col-span-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm py-16 text-center">
                            <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-inner rounded-full flex items-center justify-center mb-4 text-gray-400 dark:text-gray-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">No products found</h3>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">Try adjusting your filters.</p>
                            @if(request()->hasAny(['brand', 'min_price', 'max_price', 'rating']))
                                <a href="{{ route('storefront.categories.show', $category->slug) }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                    Clear all filters
                                </a>
                            @endif
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-layouts.app>
