<x-layouts.customer>
    <x-ui.card title="My Messages">
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse ($conversations as $conversation)
                <a href="{{ route('customer.conversations.show', $conversation) }}" class="flex items-center gap-4 px-2 py-3 hover:bg-gray-50 dark:bg-gray-800 rounded-lg transition">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-lazada-100 text-lazada-600 font-bold">
                        {{ substr($conversation->vendor->vendorProfile->store_name ?? $conversation->vendor->name ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="font-medium text-gray-900 dark:text-white truncate">{{ $conversation->vendor->vendorProfile->store_name ?? $conversation->vendor->name ?? 'Unknown' }}</p>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $conversation->last_message_at?->diffForHumans() }}</span>
                        </div>
                        @if ($conversation->messages->first())
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ $conversation->messages->first()->body !== '' ? $conversation->messages->first()->body : ($conversation->messages->first()->attachment_path ? 'Attachment' : '') }}
                            </p>
                        @endif
                    </div>
                </a>
            @empty
                <p class="py-6 text-center text-gray-400 dark:text-gray-500">No conversations yet.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $conversations->links() }}</div>
    </x-ui.card>
</x-layouts.customer>
