<x-layouts.vendor>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Campaigns</h1>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">Enroll your products in active platform campaigns to boost sales</p>
        </div>
    </div>

    @if ($campaigns->isEmpty())
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-600">
                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.082V19m0 0l-5.5-3M11 19l5.5-3M4 7l7-4 7 4M4 7v10l7 4 7-4V7"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No Active Campaigns</h3>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 max-w-sm mx-auto">There are currently no active or upcoming campaigns available for enrollment. Check back later.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach ($campaigns as $campaign)
                @php
                    $isUpcoming = $campaign->starts_at->isFuture();
                    $isActive = !$isUpcoming && $campaign->ends_at->isFuture();
                @endphp
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col relative group">
                    <!-- Banner -->
                    <div class="h-32 bg-gray-100 dark:bg-gray-700 relative overflow-hidden">
                        @if ($campaign->banner_image)
                            <img src="{{ Storage::url($campaign->banner_image) }}" alt="{{ $campaign->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-r from-primary-500 to-accent-500"></div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3">
                            @if ($isUpcoming)
                                <span class="bg-blue-100 text-blue-800 text-xs font-black px-2.5 py-1 rounded-lg uppercase tracking-wider backdrop-blur-md bg-opacity-90 dark:bg-blue-900/40 dark:text-blue-300">Coming Soon</span>
                            @else
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-black px-2.5 py-1 rounded-lg uppercase tracking-wider backdrop-blur-md bg-opacity-90 dark:bg-emerald-900/40 dark:text-emerald-300">Active</span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h3 class="text-lg font-black text-gray-900 dark:text-white line-clamp-1" title="{{ $campaign->name }}">{{ $campaign->name }}</h3>
                            @if ($campaign->badge_text)
                                <span class="text-white text-[10px] font-bold px-2 py-0.5 rounded shadow-sm whitespace-nowrap" style="background-color: {{ $campaign->badge_color ?? '#f97316' }}">
                                    {{ $campaign->badge_text }}
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4 line-clamp-2">{{ $campaign->description }}</p>

                        <div class="space-y-2 mb-6">
                            <div class="flex items-center text-sm font-medium text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $campaign->starts_at->format('M d') }} - {{ $campaign->ends_at->format('M d, Y') }}
                            </div>
                            <div class="flex items-center text-sm font-medium text-primary-600 dark:text-primary-400 pb-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $campaign->discount_type->label() }}
                                @if ($campaign->discount_type->value === 'percentage')
                                    ({{ (int) $campaign->discount_value }}% off)
                                @elseif ($campaign->discount_type->value === 'fixed')
                                    (${{ number_format($campaign->discount_value, 2) }} off)
                                @endif
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                <span class="bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-lg">{{ $campaign->products_count }}</span>
                                <span class="text-gray-500 dark:text-gray-400 font-medium ml-1">enrolled</span>
                            </div>
                            <a href="{{ route('vendor.campaigns.show', $campaign) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-xl shadow-sm text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{ $campaigns->links() }}
        </div>
    @endif
</x-layouts.vendor>
