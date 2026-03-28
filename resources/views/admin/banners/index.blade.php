<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Homepage Banners</h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Manage hero and supporting placements without touching the database directly.</p>
            </div>
            <a href="{{ route('admin.banners.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Banner
            </a>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-2">
                @php
                    $tabs = [
                        'all' => 'All',
                        'active' => 'Active',
                        'scheduled' => 'Scheduled',
                        'expired' => 'Expired',
                    ];
                @endphp
                @foreach ($tabs as $key => $label)
                    <a href="{{ route('admin.banners.index', array_filter(['status' => $key, 'q' => request('q'), 'position' => request('position')])) }}" class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-bold uppercase tracking-wide transition-colors {{ $status === $key ? 'border-primary-600 bg-primary-50 text-primary-700 dark:border-primary-500 dark:bg-primary-900/30 dark:text-primary-300' : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                        {{ $label }}
                        <span class="rounded-full bg-white/80 dark:bg-gray-800 px-2 py-0.5 text-[10px]">{{ $statusCounts[$key] ?? 0 }}</span>
                    </a>
                @endforeach
            </div>

            <form method="GET" action="{{ route('admin.banners.index') }}" class="mt-4 grid gap-3 md:grid-cols-[1fr_auto_auto]">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                    <input name="q" value="{{ request('q') }}" placeholder="Search banner title or destination" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 pl-9 pr-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <select name="position" class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">All placements</option>
                    @foreach ($positions as $value => $label)
                        <option value="{{ $value }}" @selected($position === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-primary-700">Filter</button>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/50 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="px-5 py-3">Banner</th>
                            <th class="px-5 py-3">Placement</th>
                            <th class="px-5 py-3">Schedule</th>
                            <th class="px-5 py-3">Sort</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($banners as $banner)
                            @php
                                $state = $banner->is_active
                                    ? ($banner->starts_at && $banner->starts_at->isFuture()
                                        ? 'Scheduled'
                                        : ($banner->expires_at && $banner->expires_at->isPast() ? 'Expired' : 'Live'))
                                    : 'Inactive';
                                $stateClasses = match ($state) {
                                    'Live' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800',
                                    'Scheduled' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800',
                                    'Expired' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ str_starts_with($banner->image, 'http') ? $banner->image : Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="h-14 w-24 rounded-xl border border-gray-200 object-cover dark:border-gray-700">
                                        <div class="min-w-0">
                                            <p class="truncate font-bold text-gray-900 dark:text-white">{{ $banner->title }}</p>
                                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ $banner->link ?: 'No destination link' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $positions[$banner->position] ?? ucfirst($banner->position) }}</td>
                                <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">
                                    <p>{{ $banner->starts_at?->format('M d, Y H:i') ?? 'Starts immediately' }}</p>
                                    <p class="mt-1">{{ $banner->expires_at?->format('M d, Y H:i') ?? 'No expiry set' }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $banner->sort_order }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-black uppercase tracking-wide {{ $stateClasses }}">
                                        {{ $state }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" class="rounded-lg border border-gray-200 dark:border-gray-700 px-2.5 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">Edit</a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-2.5 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center text-sm font-medium text-gray-500 dark:text-gray-400">
                                    No banners found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($banners->hasPages())
                <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-4">
                    {{ $banners->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
