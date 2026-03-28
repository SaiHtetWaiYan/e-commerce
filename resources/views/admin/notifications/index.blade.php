<x-layouts.admin>
    <x-ui.card title="Notifications">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $notifications->total() }} notification{{ $notifications->total() !== 1 ? 's' : '' }}</p>
            @if (auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('admin.notifications.mark-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">Mark all as read</button>
                </form>
            @endif
        </div>

        <div class="space-y-2">
            @forelse ($notifications as $notification)
                <div class="flex items-start gap-4 p-4 rounded-xl border {{ $notification->read_at ? 'border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900' : 'border-primary-200 dark:border-primary-900/50 bg-primary-50/50 dark:bg-primary-900/10' }} transition-colors">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $notification->read_at ? 'bg-gray-100 dark:bg-gray-800 text-gray-400' : 'bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->data['title'] ?? 'Notification' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                        @if (isset($notification->data['url']))
                            <a href="{{ $notification->data['url'] }}" class="text-xs font-bold text-primary-600 hover:underline mt-1 inline-block">View Details &rarr;</a>
                        @endif
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if (! $notification->read_at)
                        <span class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0 mt-2"></span>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">No notifications yet</h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">We'll notify you about orders, promotions, and more.</p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="mt-4">{{ $notifications->links() }}</div>
        @endif
    </x-ui.card>
</x-layouts.admin>
