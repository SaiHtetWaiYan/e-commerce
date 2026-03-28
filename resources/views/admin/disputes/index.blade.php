<x-layouts.admin>
    <x-ui.card>
        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-gray-800 pb-4">
            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Disputes</h2>
            <form method="GET" class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500 dark:text-gray-300">
                    <option value="">All Statuses</option>
                    @foreach (\App\Enums\DisputeStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ str_replace('_', ' ', ucfirst($status->name)) }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <th class="px-3 py-3">ID</th>
                        <th class="px-3 py-3">Order</th>
                        <th class="px-3 py-3">Complainant</th>
                        <th class="px-3 py-3">Subject</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Filed</th>
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
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
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-3 py-3 font-bold text-gray-900 dark:text-white">#{{ $dispute->id }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-300">{{ $dispute->order->order_number }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-300">{{ $dispute->complainant->name }}</td>
                            <td class="px-3 py-3 text-gray-900 dark:text-white font-medium">{{ Str::limit($dispute->subject, 40) }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                                    {{ str_replace('_', ' ', $dispute->status->value) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-gray-500 dark:text-gray-400">{{ $dispute->created_at->diffForHumans() }}</td>
                            <td class="px-3 py-3">
                                <a href="{{ route('admin.disputes.show', $dispute) }}" class="text-primary-600 dark:text-primary-400 font-bold hover:text-primary-800 transition-colors">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-12 text-center text-gray-500 dark:text-gray-400">No disputes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $disputes->links() }}</div>
    </x-ui.card>
</x-layouts.admin>
