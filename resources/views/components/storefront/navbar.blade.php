<header class="sticky top-0 z-50 glass-strong shadow-sm" x-data="{ mobileMenuOpen: false, userMenuOpen: false, megaMenuOpen: false, activeCategory: null }">
    <!-- Top Utility Bar -->
    <div class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-200/50 dark:border-gray-800/50 hidden md:block">
        <div class="max-w-[1200px] mx-auto px-4 flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 h-8">
            <div class="flex items-center gap-4">
                @if (auth()->check() && auth()->user()->isVendor())
                    <a href="{{ route('vendor.dashboard') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Seller Centre</a>
                @else
                    <a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Sell on {{ config('app.name') }}</a>
                @endif
                <span class="text-gray-300 dark:text-gray-700">|</span>
                <span class="text-gray-500 dark:text-gray-400">Download App</span>
                <span class="text-gray-300 dark:text-gray-700">|</span>
                <span class="text-gray-500 dark:text-gray-400">Follow us on</span>
                <div class="flex items-center gap-2 text-gray-400">
                    <svg class="w-3.5 h-3.5 hover:text-primary-600 dark:hover:text-primary-400 cursor-pointer transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    <svg class="w-3.5 h-3.5 hover:text-primary-600 dark:hover:text-primary-400 cursor-pointer transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63z"/></svg>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <!-- Dark Mode Toggle -->
                <button @click="$store.darkMode.toggle()" class="relative p-1 rounded-lg hover:bg-gray-200/50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer group" title="Toggle dark mode">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg x-show="$store.darkMode.on" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 rotate-90 scale-0" x-transition:enter-end="opacity-100 rotate-0 scale-100" class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/></svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg x-show="!$store.darkMode.on" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -rotate-90 scale-0" x-transition:enter-end="opacity-100 rotate-0 scale-100" class="w-4 h-4 text-gray-500 group-hover:text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>
                </button>
                <span class="text-gray-300 dark:text-gray-700">|</span>
                <a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Help
                </a>
                @auth
                    <a href="{{ route('customer.dashboard') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">My Account</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Login</a>
                    @if (Route::has('register'))
                        <span class="text-gray-300 dark:text-gray-700">|</span>
                        <a href="{{ route('register') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Sign Up</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Header Bar -->
    <div class="bg-transparent">
        <div class="max-w-[1200px] mx-auto px-4 py-3">
            <div class="flex items-center gap-6">
                <!-- Logo -->
                <a href="{{ route('storefront.home') }}" class="flex-shrink-0 hover-lift flex items-center h-10">
                    @php $appLogo = App\Models\AppSetting::resolvedMarketplaceSettings()['marketplace.logo'] ?? null; @endphp
                    @if($appLogo)
                        <img src="{{ Storage::url($appLogo) }}" alt="{{ config('app.name') }}" class="h-full object-contain">
                    @else
                        <span class="text-2xl font-black tracking-tight text-gradient">{{ strtoupper(config('app.name', 'LAZADA')) }}</span>
                    @endif
                </a>

                <!-- Search Bar -->
                <form action="{{ route('storefront.search.index') }}" method="GET" class="flex-1 max-w-2xl mx-4 hidden md:flex" x-data="searchAutocomplete()" @click.away="showResults = false">
                    <div class="flex w-full relative">
                        <input
                            type="text"
                            name="q"
                            x-model="query"
                            @input.debounce.300ms="fetchResults"
                            @focus="if(results.length) showResults = true"
                            @keydown.escape="showResults = false"
                            value="{{ request('q') }}"
                            placeholder="Search in {{ config('app.name') }}..."
                            class="flex-1 px-5 py-2.5 text-sm rounded-full bg-gray-100 dark:bg-gray-800 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent placeholder-gray-400 transition-all shadow-inner"
                            autocomplete="off"
                        >
                        <button type="submit" class="absolute right-1 top-1 bottom-1 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white px-5 rounded-full transition-colors flex items-center shadow-md">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>

                        <!-- Autocomplete Dropdown -->
                        <div x-show="showResults && results.length > 0" x-transition.opacity.duration.200ms class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl z-50 overflow-hidden max-h-[400px] overflow-y-auto" style="display: none;">
                            <template x-for="item in results" :key="item.id">
                                <a :href="'/products/' + item.slug" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border-b border-gray-50 dark:border-gray-800 last:border-0">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 overflow-hidden flex-shrink-0">
                                        <img :src="item.image_url" :alt="item.name" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="item.name"></p>
                                        <p x-show="item.subtitle" class="text-[11px] text-gray-500 dark:text-gray-400 truncate" x-text="item.subtitle"></p>
                                        <p class="text-xs font-bold text-primary-600 dark:text-primary-400" x-text="'$' + parseFloat(item.base_price).toFixed(2)"></p>
                                    </div>
                                </a>
                            </template>
                            <a :href="'/search?q=' + encodeURIComponent(query)" class="block px-4 py-3 text-center text-sm font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                                View all results for "<span x-text="query"></span>"
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Right Icons -->
                <div class="flex items-center gap-4 ml-auto">
                    @auth
                        {{-- Notifications --}}
                        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                        <a href="{{ route('customer.notifications.index') }}" class="relative text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full" title="Notifications">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            @if ($unreadCount > 0)
                                <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-gray-900">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                            @endif
                        </a>
                    @endauth
                    <!-- Cart -->
                    @php
                        $cartCount = app(\App\Services\CartService::class)->getCartItemCount(auth()->user(), session()->getId());
                    @endphp
                    <a href="{{ route('storefront.cart.index') }}" class="relative text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @if ($cartCount > 0)
                            <span class="absolute top-0 right-0 flex h-4 w-4 items-center justify-center rounded-full bg-accent-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white dark:ring-gray-900">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                        @endif
                    </a>

                    <!-- User Menu (Desktop) -->
                    @auth
                        <div class="relative hidden md:block">
                            <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" class="flex items-center gap-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors p-1.5 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-500 text-white flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-white dark:ring-gray-800">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>
                            <div x-show="userMenuOpen" x-transition.opacity class="absolute right-0 mt-3 w-56 glass-strong rounded-xl shadow-xl py-2 z-50 transform origin-top-right transition-all" style="display: none;">
                                <div class="px-4 py-3 border-b border-gray-100/50 dark:border-gray-700/50">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <div class="py-1">
                                    @if (auth()->user()->isVendor())
                                        <a href="{{ route('vendor.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Vendor Dashboard</a>
                                    @endif
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Admin Dashboard</a>
                                    @endif
                                    @if (auth()->user()->isDeliveryAgent())
                                        <a href="{{ route('delivery.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Delivery Dashboard</a>
                                    @endif
                                    @if (auth()->user()->role->value === 'customer')
                                        <a href="{{ route('customer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">My Account</a>
                                        <a href="{{ route('customer.orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">My Orders</a>
                                        <a href="{{ route('customer.wishlist.index') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Wishlist</a>
                                    @endif
                                </div>
                                <div class="border-t border-gray-100/50 dark:border-gray-700/50 mt-1 pt-1">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">Sign Out</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700 dark:text-gray-300 p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="!mobileMenuOpen"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-show="mobileMenuOpen" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="md:hidden mt-3">
                <form action="{{ route('storefront.search.index') }}" method="GET" class="flex relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search..." class="flex-1 px-4 py-2.5 text-sm rounded-full bg-gray-100 dark:bg-gray-800 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-primary-500">
                    <button type="submit" class="absolute right-1 top-1 bottom-1 bg-primary-600 text-white px-4 rounded-full">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Category Navigation Bar (Desktop) -->
    <nav class="border-b border-gray-200/50 dark:border-gray-800/50 hidden md:block bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm">
        <div class="max-w-[1200px] mx-auto px-4">
            <ul class="flex items-center h-12 text-sm font-medium text-gray-600 dark:text-gray-300 gap-8 overflow-x-auto scrollbar-hide">
                <!-- All Categories Trigger -->
                <li class="relative h-full flex items-center" @mouseenter="megaMenuOpen = true" @mouseleave="megaMenuOpen = false">
                    <button class="flex items-center gap-2 hover:text-primary-600 dark:hover:text-primary-400 transition-colors whitespace-nowrap h-full border-b-2 border-transparent hover:border-primary-600 dark:hover:border-primary-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        All Categories
                    </button>

                    <!-- Mega Menu Dropdown -->
                    <div x-show="megaMenuOpen" x-transition.opacity class="absolute top-full left-0 mt-0 glass-strong border-t-0 shadow-xl z-50 flex rounded-b-xl overflow-hidden" style="display: none; min-width: 720px;">
                        <!-- Category List -->
                        <div class="w-64 border-r border-gray-200/50 dark:border-gray-700/50 py-3 max-h-[400px] overflow-y-auto bg-white/50 dark:bg-gray-900/50">
                            @foreach ($navCategories as $index => $cat)
                                <a href="{{ route('storefront.categories.show', $cat->slug) }}"
                                   @mouseenter="activeCategory = {{ $index }}"
                                   :class="activeCategory === {{ $index }} ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-700 dark:text-gray-300'"
                                   class="flex items-center justify-between px-5 py-2.5 text-sm hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                                    <span class="flex items-center gap-3">
                                        @if ($cat->icon)
                                            <span class="opacity-70">{{ $cat->icon }}</span>
                                        @endif
                                        {{ $cat->name }}
                                    </span>
                                    @if ($cat->children->isNotEmpty())
                                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>

                        <!-- Subcategories Panel -->
                        <div class="flex-1 p-6 min-h-[300px] bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900">
                            @foreach ($navCategories as $index => $cat)
                                <div x-show="activeCategory === {{ $index }}" class="grid grid-cols-3 gap-x-6 gap-y-4" style="display: none;">
                                    @foreach ($cat->children as $child)
                                        <a href="{{ route('storefront.categories.show', $child->slug) }}" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors py-1 hover:translate-x-1 duration-200 inline-block">
                                            {{ $child->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>

                <!-- Quick Category Links -->
                @foreach ($navCategories->take(6) as $cat)
                    <li class="h-full flex items-center">
                        <a href="{{ route('storefront.categories.show', $cat->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors whitespace-nowrap h-full flex items-center border-b-2 border-transparent hover:border-primary-600 dark:hover:border-primary-400">{{ $cat->name }}</a>
                    </li>
                @endforeach
                <li class="h-full flex items-center">
                    <a href="{{ route('storefront.campaigns.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors whitespace-nowrap h-full flex items-center border-b-2 border-transparent hover:border-primary-600 dark:hover:border-primary-400">Campaigns</a>
                </li>
                <li class="h-full flex items-center ml-auto">
                    <a href="{{ route('storefront.products.index') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors whitespace-nowrap flex items-center gap-1">
                        All Products
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-transition class="md:hidden glass-strong border-b border-gray-200/50 shadow-xl overflow-hidden" style="display: none;">
        <div class="py-3 px-2">
            <a href="{{ route('storefront.products.index') }}" class="block px-4 py-3 text-sm font-bold text-gradient rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">All Products</a>
            <a href="{{ route('storefront.campaigns.index') }}" class="block px-4 py-3 text-sm font-bold text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20">Campaigns</a>
            @foreach ($navCategories->take(8) as $cat)
                <a href="{{ route('storefront.categories.show', $cat->slug) }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600">{{ $cat->name }}</a>
            @endforeach
            <div class="border-t border-gray-100/50 dark:border-gray-700/50 mt-3 pt-3 px-2">
                @guest
                    <a href="{{ route('login') }}" class="block w-full text-center bg-primary-600 text-white font-medium px-4 py-2.5 rounded-lg shadow-sm">Sign In / Register</a>
                @else
                    <a href="{{ route('customer.dashboard') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">My Account</a>
                    <a href="{{ route('customer.orders.index') }}" class="block px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">My Orders</a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-3 text-sm font-medium text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">Sign Out</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>

<script>
    function searchAutocomplete() {
        return {
            query: '{{ request("q") }}',
            results: [],
            showResults: false,
            async fetchResults() {
                if (this.query.length < 2) { this.results = []; this.showResults = false; return; }
                try {
                    const res = await fetch('/api/search/suggest?q=' + encodeURIComponent(this.query));
                    const data = await res.json();
                    this.results = data.results || [];
                    this.showResults = this.results.length > 0;
                } catch (e) { this.results = []; this.showResults = false; }
            }
        }
    }
</script>

</header>
