<x-layouts.app>
    @php
        $shopSlug = $vendorProfile->store_slug ?: $vendorProfile->store_name;
    @endphp

    <!-- Store Header -->
    <div class="bg-gradient-to-r from-primary-700 to-primary-900 border-b border-primary-950 overflow-hidden relative">
        @if ($vendorProfile->store_banner)
            <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-40" style="background-image: url('{{ asset('storage/' . $vendorProfile->store_banner) }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-primary-900/80 to-transparent"></div>
        @else
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        @endif
        <div class="max-w-[1200px] mx-auto px-4 py-8 relative z-10">
            <div class="flex flex-col sm:flex-row items-center sm:items-stretch gap-6">
                <!-- Store Avatar -->
                <div class="w-24 h-24 flex-shrink-0 overflow-hidden border-4 border-white/20 rounded-2xl bg-white shadow-xl group">
                    @if ($vendorProfile->store_logo)
                        <img src="{{ asset('storage/'.$vendorProfile->store_logo) }}" alt="{{ $vendorProfile->store_name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($vendorProfile->store_name) }}&background=6366f1&color=fff&size=128&font-size=0.33" alt="{{ $vendorProfile->store_name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    @endif
                </div>

                <!-- Store Info -->
                <div class="flex-1 text-white text-center sm:text-left flex flex-col justify-center">
                    <div class="flex items-center justify-center sm:justify-start gap-2 mb-2">
                        <h1 class="text-3xl font-black tracking-tight">{{ $vendorProfile->store_name }}</h1>
                        @if ($vendorProfile->is_verified)
                            <span class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-md text-white border border-white/30 text-[10px] font-black px-2 py-1 rounded-md uppercase tracking-widest shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Verified
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-primary-100 font-medium max-w-2xl leading-relaxed">{{ $vendorProfile->store_description ?: 'Discover a curated collection of premium products from '.$vendorProfile->user->name.'.' }}</p>
                </div>

                <!-- Store Stats -->
                <div class="flex items-center justify-center gap-8 text-white mt-6 sm:mt-0 bg-black/20 rounded-2xl px-8 py-4 backdrop-blur-sm border border-white/10 shadow-inner">
                    <div class="text-center">
                        <p class="text-2xl font-black tracking-tight shadow-sm">{{ $stats['products_count'] }}</p>
                        <p class="text-[10px] text-primary-200 uppercase tracking-widest font-bold mt-0.5">Products</p>
                    </div>
                    <div class="w-px h-10 bg-white/20"></div>
                    <div class="text-center">
                        <p class="text-2xl flex items-center justify-center gap-1 font-black shadow-sm tracking-tight text-white"><span class="text-yellow-400">★</span> {{ number_format($stats['average_rating'], 1) }}</p>
                        <p class="text-[10px] text-primary-200 uppercase tracking-widest font-bold mt-0.5">Rating</p>
                    </div>
                </div>
            </div>
            
            <!-- Store Actions -->
            <div class="mt-6 flex justify-center sm:justify-start">
                @auth
                    @if(auth()->id() !== $vendorProfile->user_id)
                        <div x-data="{ open: false }">
                            <button @click="open = true" class="inline-flex items-center gap-2 bg-white text-primary-700 font-bold px-5 py-2.5 rounded-xl shadow-md hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Chat with Vendor
                            </button>

                            <!-- Chat Modal -->
                            <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false" aria-hidden="true"></div>
                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                    <div x-show="open" x-transition class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                        <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-gray-100 dark:border-gray-800">
                                            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                                                Message {{ $vendorProfile->store_name }}
                                            </h3>
                                        </div>
                                        <form action="{{ route('customer.conversations.start') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="vendor_id" value="{{ $vendorProfile->user_id }}">
                                            <div class="px-4 py-5 sm:p-6">
                                                <div class="mb-4">
                                                    <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Message</label>
                                                    <textarea id="body" name="body" rows="4" class="mt-2 block w-full rounded-xl border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:text-white p-3" placeholder="Hello, I have a question about..." required></textarea>
                                                </div>
                                            </div>
                                            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 flex justify-end gap-3 sm:px-6 border-t border-gray-100 dark:border-gray-800">
                                                <button type="button" @click="open = false" class="inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-800 text-sm font-bold text-gray-700 dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="inline-flex justify-center rounded-xl border border-transparent px-4 py-2 bg-primary-600 text-sm font-bold text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                                    Send Message
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-white text-primary-700 font-bold px-5 py-2.5 rounded-xl shadow-md hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Log in to Chat
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="max-w-[1200px] mx-auto px-4 py-8">
        <!-- Search & Sort Bar -->
        <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-3 mb-8">
            <form method="GET" action="{{ route('storefront.vendor.show', ['slug' => $shopSlug]) }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search across all products in this store..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm font-medium text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all placeholder-gray-400 dark:placeholder-gray-500">
                </div>
                
                <div class="flex gap-3">
                    <select name="sort" class="flex-1 sm:flex-none border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all cursor-pointer" onchange="this.form.submit()">
                        <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Sort by: Newest</option>
                        <option value="best_selling" @selected(($filters['sort'] ?? '') === 'best_selling')>Sort by: Best Selling</option>
                        <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Sort by: Top Rated</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: High to Low</option>
                    </select>
                    
                    <button type="submit" class="hidden sm:inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold shadow-md shadow-primary-500/20 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        Search Store
                    </button>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            @forelse ($products as $product)
                <x-storefront.product-card :product="$product" />
            @empty
                <div class="col-span-full bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl py-20 text-center shadow-sm">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2 tracking-tight">No products found</h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">We couldn't find any items matching your specific search within this store's catalog.</p>
                    <a href="{{ route('storefront.vendor.show', ['slug' => $shopSlug]) }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-xl text-sm font-bold transition-all shadow-sm">
                        Clear All Filters
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($products->hasPages())
            <div class="mt-8 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl p-4 shadow-sm">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
