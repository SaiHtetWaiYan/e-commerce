<x-layouts.app title="Page Not Found">
    <div class="max-w-[1200px] mx-auto px-4 py-20">
        <div class="text-center">
            <div class="w-24 h-24 bg-primary-50 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h1 class="text-7xl font-black text-gray-200 dark:text-gray-800 mb-2">404</h1>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Page Not Found</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Sorry, we couldn't find the page you're looking for. It may have been moved or no longer exists.</p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('storefront.home') }}" class="inline-flex items-center gap-2 bg-primary-600 text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-primary-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Go Home
                </a>
                <a href="{{ route('storefront.products.index') }}" class="inline-flex items-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-bold px-6 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                    Browse Products
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
