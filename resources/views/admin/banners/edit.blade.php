<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Edit Banner</h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Update timing, destination, or creative for this placement.</p>
            </div>
            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-bold text-red-700 shadow-sm hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12m-9 4v6m3-6v6m3-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h10m-11 0l1 12a2 2 0 002 2h6a2 2 0 002-2l1-12"/></svg>
                    Delete Banner
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
            @include('admin.banners._form', [
                'banner' => $banner,
                'positions' => $positions,
                'action' => route('admin.banners.update', $banner),
                'method' => 'PUT',
                'submitLabel' => 'Update Banner',
            ])
        </div>
    </div>
</x-layouts.admin>
