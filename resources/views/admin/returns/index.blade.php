<x-layouts.admin>
    <x-ui.card title="Return Requests">
        <div x-data="{ selected: [] }" class="space-y-4">
            {{-- Bulk Action Bar --}}
            <div x-show="selected.length > 0" x-cloak class="flex items-center gap-3 rounded-xl border border-primary-200 dark:border-primary-800 bg-primary-50 dark:bg-primary-900/20 px-4 py-3" x-transition>
                <span class="text-sm font-bold text-primary-700 dark:text-primary-300" x-text="selected.length + ' selected'"></span>
                <form method="POST" action="{{ route('admin.returns.bulk-approve') }}" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="return_ids[]" :value="id">
                    </template>
                    <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-emerald-700 transition-colors">Approve Selected</button>
                </form>
                <form method="POST" action="{{ route('admin.returns.bulk-reject') }}" class="inline">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="return_ids[]" :value="id">
                    </template>
                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-red-700 transition-colors">Reject Selected</button>
                </form>
                <button @click="selected = []" type="button" class="ml-auto text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Clear</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-2 w-8"></th>
                            <th class="px-3 py-2">ID</th>
                            <th class="px-3 py-2">Order</th>
                            <th class="px-3 py-2">Customer</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Submitted</th>
                            <th class="px-3 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($returns as $return)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-3 py-2">
                                    @if ($return->status->value === 'pending')
                                        <input type="checkbox" value="{{ $return->id }}" x-model.number="selected" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                    @endif
                                </td>
                                <td class="px-3 py-2 font-bold text-gray-900 dark:text-white">#{{ $return->id }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $return->order->order_number }}</td>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $return->user->name }}</td>
                                <td class="px-3 py-2">
                                    @php
                                        $returnColors = [
                                            'pending' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                                            'approved' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                                            'refunded' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                        ];
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold capitalize {{ $returnColors[$return->status->value] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                                        {{ $return->status->value }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $return->created_at->format('M d, Y') }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ route('admin.returns.show', $return) }}" class="text-primary-600 dark:text-primary-400 font-bold hover:text-primary-800 transition-colors text-xs">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-12 text-center text-gray-500 dark:text-gray-400">No return requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $returns->links() }}</div>
    </x-ui.card>
</x-layouts.admin>
