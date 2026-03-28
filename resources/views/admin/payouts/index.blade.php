<x-layouts.admin>
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Vendor Payouts</h3>
            <a href="{{ route('admin.payouts.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create Payout</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <th class="px-3 py-2">Vendor</th>
                        <th class="px-3 py-2">Period</th>
                        <th class="px-3 py-2">Amount</th>
                        <th class="px-3 py-2">Commission</th>
                        <th class="px-3 py-2">Net</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($payouts as $payout)
                        <tr>
                            <td class="px-3 py-2 font-medium">{{ $payout->vendor->vendorProfile->store_name ?? $payout->vendor->name }}</td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $payout->period_start->format('M d') }} - {{ $payout->period_end->format('M d, Y') }}</td>
                            <td class="px-3 py-2">${{ number_format($payout->amount, 2) }}</td>
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400">${{ number_format($payout->commission_amount, 2) }}</td>
                            <td class="px-3 py-2 font-medium">${{ number_format($payout->net_amount, 2) }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold capitalize {{ $payout->status === 'paid' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' }}">
                                    {{ $payout->status }}
                                </span>
                            </td>
                            <td class="px-3 py-2">
                                @if ($payout->status === 'pending')
                                    <form action="{{ route('admin.payouts.pay', $payout) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="payment_reference" placeholder="Reference" class="w-28 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-2 py-1 text-xs" required>
                                        <button type="submit" class="rounded bg-emerald-600 px-2 py-1 text-xs font-medium text-white hover:bg-emerald-700">Mark Paid</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $payout->payment_reference }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-6 text-center text-gray-400 dark:text-gray-500">No payouts found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payouts->links() }}</div>
    </x-ui.card>
</x-layouts.admin>
