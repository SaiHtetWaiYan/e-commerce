<x-layouts.app>
    <div class="max-w-4xl mx-auto py-8 px-4">
        <x-ui.card title="My Disputes">
            <div class="space-y-3 text-sm">
                @forelse ($disputes as $dispute)
                    @php
                        $statusColors = [
                            'pending' => 'border-yellow-200 bg-yellow-50 text-yellow-700 dark:border-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
                            'under_review' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
                            'resolved' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300',
                            'closed' => 'border-gray-200 bg-gray-100 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
                        ];
                        $colorClass = $statusColors[$dispute->status->value] ?? $statusColors['pending'];
                    @endphp
                    <div class="flex items-center justify-between rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3">
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $dispute->subject }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Order: {{ $dispute->order->order_number }} · {{ $dispute->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                            {{ str_replace('_', ' ', $dispute->status->value) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <p>You have no disputes.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $disputes->links() }}</div>
        </x-ui.card>
    </div>
</x-layouts.app>
