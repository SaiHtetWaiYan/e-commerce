<x-layouts.app title="Forbidden">
    <div class="max-w-[1200px] mx-auto px-4 py-20">
        <div class="text-center">
            <div class="w-24 h-24 bg-red-50 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
            </div>
            <h1 class="text-7xl font-black text-gray-200 dark:text-gray-800 mb-2">403</h1>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Access Denied</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">You don't have permission to access this page. Please contact the administrator if you believe this is an error.</p>
            <a href="{{ route('storefront.home') }}" class="inline-flex items-center gap-2 bg-primary-600 text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-primary-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Go Home
            </a>
        </div>
    </div>
</x-layouts.app>
