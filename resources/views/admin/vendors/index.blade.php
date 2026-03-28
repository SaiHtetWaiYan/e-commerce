<x-layouts.admin>
    <x-ui.card title="Vendors">
        <div class="space-y-2 text-sm">
            @foreach ($vendors as $vendor)
                <a href="{{ route('admin.vendors.show', $vendor) }}" class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 bg-white dark:bg-gray-900/40">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $vendor->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $vendor->vendorProfile->store_name ?? 'No store profile' }}</p>
                    </div>
                    <span class="rounded px-2 py-1 text-xs {{ $vendor->vendorProfile?->is_verified ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                        {{ $vendor->vendorProfile?->is_verified ? 'Verified' : 'Pending' }}
                    </span>
                </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $vendors->links() }}</div>
    </x-ui.card>
</x-layouts.admin>
