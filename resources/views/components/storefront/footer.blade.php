<footer class="bg-white dark:bg-gray-900 border-t border-gray-200/50 dark:border-gray-800/50 mt-auto">
    <div class="max-w-[1200px] mx-auto px-4 py-12">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
            <!-- Customer Service -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Customer Service</h3>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Help Centre</a></li>
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">How to Buy</a></li>
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Returns & Refunds</a></li>
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Contact Us</a></li>
                </ul>
            </div>

            <!-- About -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">{{ config('app.name') }}</h3>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Careers</a></li>
                    <li><a href="{{ route('storefront.pages.privacy') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Privacy Policy</a></li>
                    <li><a href="{{ route('storefront.pages.terms') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Terms of Service</a></li>
                </ul>
            </div>

            <!-- Sell -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Sell</h3>
                <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <li><a href="{{ route('vendor.register') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Sell on {{ config('app.name') }}</a></li>
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Seller Centre</a></li>
                    <li><a href="#" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Seller Guidelines</a></li>
                </ul>
            </div>

            <!-- Payment -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Payment Methods</h3>
                <div class="flex flex-wrap gap-2">
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">Visa</div>
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">MasterCard</div>
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">PayPal</div>
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">COD</div>
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">Bank Transfer</div>
                </div>

                <h3 class="text-sm font-bold text-gray-900 dark:text-white mt-6 mb-4">Delivery Partners</h3>
                <div class="flex flex-wrap gap-2">
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">Express</div>
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">Standard</div>
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md px-2.5 py-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium">Economy</div>
                </div>
            </div>

            <!-- Follow & Download -->
            <div>
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Follow Us</h3>
                <div class="flex items-center gap-3 mb-6">
                    <a href="#" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63z"/></svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                </div>

                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">{{ config('app.name') }} App</h3>
                <div class="flex flex-col gap-3">
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 text-white rounded-xl px-4 py-2 flex items-center gap-3 w-fit cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
                        <div class="text-[10px] leading-tight"><span class="opacity-70">Download on the</span><br><span class="font-bold text-sm">App Store</span></div>
                    </div>
                    <div class="bg-gradient-to-br from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 text-white rounded-xl px-4 py-2 flex items-center gap-3 w-fit cursor-pointer hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3.609 1.814L13.792 12 3.61 22.186a.996.996 0 01-.61-.92V2.734a1 1 0 01.609-.92zm10.89 10.893l2.302 2.302-10.937 6.333 8.635-8.635zm3.199-3.199l2.302 2.302a1 1 0 010 1.38l-2.302 2.302L15.1 12l2.598-2.492zM5.864 2.658L16.8 8.99l-2.302 2.302-8.635-8.635z"/></svg>
                        <div class="text-[10px] leading-tight"><span class="opacity-70">GET IT ON</span><br><span class="font-bold text-sm">Google Play</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Bar -->
    <div class="glass-strong border-t border-gray-200/50 dark:border-gray-800/50">
        <div class="max-w-[1200px] mx-auto px-4 py-5 flex flex-col md:flex-row items-center justify-between text-xs text-gray-500 dark:text-gray-400">
            <p>&copy; {{ now()->year }} <span class="font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</span>. All rights reserved.</p>
            <div class="flex items-center justify-center gap-6 mt-4 md:mt-0 bg-white/50 dark:bg-gray-800/50 px-6 py-2 rounded-full">
                <a href="{{ route('storefront.pages.privacy') }}" class="hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors hover-lift">Privacy Policy</a>
                <a href="{{ route('storefront.pages.terms') }}" class="hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors hover-lift">Terms of Service</a>
                <a href="{{ route('storefront.pages.privacy') }}" class="hover:text-primary-600 dark:hover:text-primary-400 font-medium transition-colors hover-lift">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>
