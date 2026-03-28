<x-layouts.vendor>
    <div class="space-y-4">
        <x-ui.card>
            <div class="flex items-center gap-3">
                <a href="{{ route('vendor.conversations.index') }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:text-gray-300">&larr;</a>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-orange-100 text-orange-600 font-bold">
                    {{ substr($conversation->buyer->name ?? '?', 0, 1) }}
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $conversation->buyer->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $conversation->buyer->email ?? '' }}</p>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card>
            <div class="space-y-4 max-h-96 overflow-y-auto" id="messages">
                @foreach ($conversation->messages as $message)
                    @php $isMe = (int) $message->sender_id === (int) auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs rounded-xl px-4 py-2 {{ $isMe ? 'bg-orange-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white' }}">
                            <p class="text-sm">{{ $message->body }}</p>
                            @if ($message->attachment_path)
                                <a
                                    href="{{ asset('storage/'.$message->attachment_path) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-2 inline-flex items-center text-xs font-semibold underline {{ $isMe ? 'text-orange-100' : 'text-primary-600 dark:text-primary-400' }}"
                                >
                                    View attachment
                                </a>
                            @endif
                            <p class="mt-1 text-xs {{ $isMe ? 'text-orange-200' : 'text-gray-400 dark:text-gray-500' }}">{{ $message->created_at->format('h:i A') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card>
            <form action="{{ route('vendor.conversations.reply', $conversation) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                @csrf
                <input type="text" name="body" placeholder="Type a message..." class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm focus:border-orange-500 focus:ring-orange-500" required>
                <input type="file" name="attachment" class="rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm">
                <button type="submit" class="rounded-lg bg-orange-600 px-4 py-2 text-sm font-medium text-white hover:bg-orange-700">Send</button>
            </form>
        </x-ui.card>
    </div>
</x-layouts.vendor>
