<x-layouts.admin>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Create Campaign</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Set up a campaign and enroll products later from the detail page.</p>
        </div>

        <x-ui.card>
            @include('admin.campaigns._form', [
                'campaign' => $campaign,
                'action' => route('admin.campaigns.store'),
                'method' => 'POST',
                'submitLabel' => 'Create Campaign',
            ])
        </x-ui.card>
    </div>
</x-layouts.admin>
