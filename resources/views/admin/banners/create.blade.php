<x-layouts.admin>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Create Banner</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Add a new promotional banner for the storefront.</p>
        </div>

        <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm">
            @include('admin.banners._form', [
                'banner' => $banner,
                'positions' => $positions,
                'action' => route('admin.banners.store'),
                'method' => 'POST',
                'submitLabel' => 'Create Banner',
            ])
        </div>
    </div>
</x-layouts.admin>
