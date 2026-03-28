<x-layouts.app>
    <div class="max-w-[1200px] mx-auto px-4 py-8 space-y-10">
        <section class="relative overflow-hidden rounded-3xl border border-orange-200/70 dark:border-orange-900/40 bg-gradient-to-br from-orange-50 via-white to-amber-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 px-6 py-10 sm:px-10">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full bg-orange-300/30 blur-3xl dark:bg-orange-500/20"></div>
            <div class="relative max-w-2xl">
                <p class="text-xs font-black uppercase tracking-[0.2em] text-orange-600 dark:text-orange-300">Marketplace Events</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-gray-900 dark:text-white sm:text-4xl">Seasonal Campaigns & Mega Sales</h1>
                <p class="mt-3 text-sm font-medium text-gray-600 dark:text-gray-300">Shop campaign-exclusive pricing, limited-time drops, and themed collections.</p>
            </div>
        </section>

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Active Campaigns</h2>
                <span class="rounded-full bg-primary-50 dark:bg-primary-900/30 px-3 py-1 text-xs font-bold uppercase tracking-wide text-primary-700 dark:text-primary-300">{{ $activeCampaigns->count() }} Live</span>
            </div>

            @if ($activeCampaigns->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($activeCampaigns as $campaign)
                        <a href="{{ route('storefront.campaigns.show', $campaign->slug) }}" class="group overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-lg">
                            @if ($campaign->thumbnail_image)
                                <img src="{{ Storage::url($campaign->thumbnail_image) }}" alt="{{ $campaign->name }}" class="h-40 w-full object-cover">
                            @else
                                <div class="h-40 w-full bg-gradient-to-br from-orange-500 to-amber-500"></div>
                            @endif
                            <div class="p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white" style="background-color: {{ $campaign->badge_color ?? '#f97316' }};">
                                        {{ $campaign->badge_text ?: 'Campaign' }}
                                    </span>
                                    <span class="text-xs font-bold text-gray-500 dark:text-gray-400">{{ $campaign->products_count }} items</span>
                                </div>
                                <h3 class="mt-3 text-lg font-black text-gray-900 dark:text-white">{{ $campaign->name }}</h3>
                                <p class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">Ends {{ $campaign->ends_at?->diffForHumans() }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-10 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                    No active campaigns right now.
                </div>
            @endif
        </section>

        <section class="space-y-4">
            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Upcoming Campaigns</h2>

            @if ($upcomingCampaigns->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($upcomingCampaigns as $campaign)
                        <a href="{{ route('storefront.campaigns.show', $campaign->slug) }}" class="group overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="h-2 w-full" style="background-color: {{ $campaign->badge_color ?? '#f97316' }};"></div>
                            <div class="p-4">
                                <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                    Coming Soon
                                </span>
                                <h3 class="mt-3 text-lg font-black text-gray-900 dark:text-white">{{ $campaign->name }}</h3>
                                <p class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">Starts {{ $campaign->starts_at?->diffForHumans() }}</p>
                                <p class="mt-3 text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $campaign->products_count }} items planned</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-10 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                    No upcoming campaigns scheduled.
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
