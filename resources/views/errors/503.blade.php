<x-layouts.app title="Maintenance Mode">
    <div class="max-w-[1200px] mx-auto px-4 py-20">
        <div class="text-center">
            <div class="w-24 h-24 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-8">
                <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-7xl font-black text-gray-200 dark:text-gray-800 mb-2">503</h1>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Under Maintenance</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">We're performing scheduled maintenance to improve your experience. We'll be back shortly.</p>
            <button onclick="window.location.reload()" class="inline-flex items-center gap-2 bg-primary-600 text-white text-sm font-bold px-6 py-3 rounded-xl hover:bg-primary-700 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Refresh Page
            </button>
        </div>
    </div>
</x-layouts.app>
