<x-layouts.vendor>
    <div class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Balance</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">${{ number_format($pendingTotal, 2) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Paid</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">${{ number_format($paidTotal, 2) }}</p>
            </div>
        </div>

        <x-ui.card title="Payout History">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                            <th class="px-3 py-2">Period</th>
                            <th class="px-3 py-2">Gross</th>
                            <th class="px-3 py-2">Commission</th>
                            <th class="px-3 py-2">Net Amount</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Paid At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($payouts as $payout)
                            <tr>
                                <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $payout->period_start->format('M d') }} - {{ $payout->period_end->format('M d, Y') }}</td>
                                <td class="px-3 py-2">${{ number_format($payout->amount, 2) }}</td>
                                <td class="px-3 py-2 text-gray-500 dark:text-gray-400">${{ number_format($payout->commission_amount, 2) }}</td>
                                <td class="px-3 py-2 font-medium">${{ number_format($payout->net_amount, 2) }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold capitalize {{ $payout->status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                        {{ $payout->status }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $payout->paid_at?->format('M d, Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400 dark:text-gray-500">No payouts yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $payouts->links() }}</div>
        </x-ui.card>
    </div>
</x-layouts.vendor>
