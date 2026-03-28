<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Campaign Management</h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Create themed sales like 3.3, 9.9, and New Year campaigns.</p>
            </div>
            <a href="{{ route('admin.campaigns.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Campaign
            </a>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $tabs = [
                        'all' => 'All',
                        'active' => 'Active',
                        'upcoming' => 'Upcoming',
                        'ended' => 'Ended',
                    ];
                @endphp
                @foreach ($tabs as $key => $label)
                    <a
                        href="{{ route('admin.campaigns.index', array_filter(['tab' => $key, 'q' => request('q')])) }}"
                        class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-bold uppercase tracking-wide transition-colors {{ $tab === $key ? 'border-primary-600 bg-primary-50 text-primary-700 dark:border-primary-500 dark:bg-primary-900/30 dark:text-primary-300' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                    >
                        {{ $label }}
                        <span class="rounded-full bg-white/80 dark:bg-gray-800 px-2 py-0.5 text-[10px]">{{ $statusCounts[$key] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.campaigns.index') }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                    <input name="q" value="{{ request('q') }}" placeholder="Search campaign name or slug" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">Search</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="px-5 py-3">Campaign</th>
                            <th class="px-5 py-3">Badge</th>
                            <th class="px-5 py-3">Discount</th>
                            <th class="px-5 py-3">Products</th>
                            <th class="px-5 py-3">Start</th>
                            <th class="px-5 py-3">End</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($campaigns as $campaign)
                            @php
                                $status = $campaign->isRunning() ? 'running' : ($campaign->starts_at?->isFuture() ? 'upcoming' : ($campaign->ends_at?->isPast() ? 'ended' : 'inactive'));
                                $statusClass = [
                                    'running' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800',
                                    'upcoming' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800',
                                    'ended' => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
                                    'inactive' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800',
                                ][$status];
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $campaign->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">/{{ $campaign->slug }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($campaign->badge_text)
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-black uppercase tracking-wide text-white" style="background-color: {{ $campaign->badge_color ?? '#f97316' }};">
                                            {{ $campaign->badge_text }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs font-medium text-gray-700 dark:text-gray-300">
                                    @if ($campaign->discount_type)
                                        {{ Str::title($campaign->discount_type->value) }}
                                        @if ($campaign->discount_value !== null)
                                            : {{ $campaign->discount_type->value === 'percentage' ? number_format((float) $campaign->discount_value, 0).'%' : '$'.number_format((float) $campaign->discount_value, 2) }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $campaign->products_count }}</td>
                                <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $campaign->starts_at?->format('M d, Y H:i') }}</td>
                                <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $campaign->ends_at?->format('M d, Y H:i') }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-wide {{ $statusClass }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.campaigns.show', $campaign) }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-2.5 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">View</a>
                                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-2.5 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Edit</a>
                                        <form action="{{ route('admin.campaigns.toggle', $campaign) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg border border-primary-200 bg-primary-50 px-2.5 py-1.5 text-xs font-bold text-primary-700 hover:bg-primary-100 dark:border-primary-800 dark:bg-primary-900/30 dark:text-primary-300">
                                                {{ $campaign->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-14 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                    No campaigns found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($campaigns->hasPages())
                <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-4">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
