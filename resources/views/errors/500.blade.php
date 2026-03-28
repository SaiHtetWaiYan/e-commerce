<x-layouts.app title="Server Error">
    <div class="max-w-[1200px] mx-auto px-4 py-20">
        <div class="text-center">
            <div class="w-24 h-24 bg-red-50 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h1 class="text-7xl font-black text-gray-200 dark:text-gray-800 mb-2">500</h1>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Something Went Wrong</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">We're experiencing a temporary issue. Please try again in a few moments.</p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('storefront.home') }}" class="inline-flex items-center gap-2 bg-primary-600 text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-primary-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Go Home
                </a>
                <button onclick="window.location.reload()" class="inline-flex items-center gap-2 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-bold px-6 py-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Try Again
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
