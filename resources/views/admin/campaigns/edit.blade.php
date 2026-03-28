<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Edit Campaign</h1>
                <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Update campaign schedule, pricing, and branding.</p>
            </div>
            <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" onsubmit="return confirm('Delete this campaign?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                    Delete Campaign
                </button>
            </form>
        </div>

        <x-ui.card>
            @include('admin.campaigns._form', [
                'campaign' => $campaign,
                'action' => route('admin.campaigns.update', $campaign),
                'method' => 'PUT',
                'submitLabel' => 'Update Campaign',
            ])
        </x-ui.card>
    </div>
</x-layouts.admin>
