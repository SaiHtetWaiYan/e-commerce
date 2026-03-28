<x-layouts.admin>
    <div class="space-y-6">
        <x-ui.card title="Dispute #{{ $dispute->id }} — {{ $dispute->subject }}">
            @php
                $statusColors = [
                    'pending' => 'border-yellow-200 bg-yellow-50 text-yellow-700 dark:border-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
                    'under_review' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
                    'resolved' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300',
                    'closed' => 'border-gray-200 bg-gray-100 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
                ];
                $colorClass = $statusColors[$dispute->status->value] ?? $statusColors['pending'];
            @endphp

            <div class="grid gap-4 sm:grid-cols-2 text-sm mb-6">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Order</p>
                    <a href="{{ route('admin.orders.show', $dispute->order) }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">{{ $dispute->order->order_number }}</a>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status</p>
                    <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                        {{ str_replace('_', ' ', $dispute->status->value) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Complainant</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $dispute->complainant->name }} ({{ $dispute->complainant->email }})</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Respondent</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $dispute->respondent->name }} ({{ $dispute->respondent->email }})</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-gray-500 dark:text-gray-400 mb-1">Description</p>
                    <p class="font-medium text-gray-900 dark:text-white whitespace-pre-wrap bg-gray-50 dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">{{ $dispute->description }}</p>
                </div>
            </div>

            @if ($dispute->resolution)
                <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-4 text-sm mb-6">
                    <p class="font-bold text-emerald-800 dark:text-emerald-300 mb-1">Resolution</p>
                    <p class="text-emerald-700 dark:text-emerald-400 whitespace-pre-wrap">{{ $dispute->resolution }}</p>
                    @if ($dispute->resolvedBy)
                        <p class="text-xs text-emerald-600 dark:text-emerald-500 mt-2">Resolved by {{ $dispute->resolvedBy->name }} on {{ $dispute->resolved_at->format('M d, Y h:i A') }}</p>
                    @endif
                </div>
            @endif
        </x-ui.card>

        @if (!in_array($dispute->status->value, ['resolved', 'closed']))
            <x-ui.card title="Resolve Dispute">
                <form action="{{ route('admin.disputes.resolve', $dispute) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Resolution Status</label>
                        <select name="status" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-300">
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Resolution Details</label>
                        <textarea name="resolution" rows="4" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-white" required placeholder="Describe the resolution..."></textarea>
                    </div>
                    <button type="submit" class="rounded-xl bg-primary-600 px-6 py-2.5 text-sm font-bold text-white hover:bg-primary-700 transition-colors shadow-sm">Submit Resolution</button>
                </form>
            </x-ui.card>
        @endif

        <a href="{{ route('admin.disputes.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">&larr; Back to Disputes</a>
    </div>
</x-layouts.admin>
