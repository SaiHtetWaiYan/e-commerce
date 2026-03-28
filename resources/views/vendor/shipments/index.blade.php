<x-layouts.vendor>
    <x-ui.card>
        <div class="flex items-center justify-between mb-6 border-b border-gray-100 dark:border-gray-800 pb-4">
            <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Shipments</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <th class="px-3 py-3">Order #</th>
                        <th class="px-3 py-3">Customer</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Agent</th>
                        <th class="px-3 py-3">Est. Delivery</th>
                        <th class="px-3 py-3">Updated</th>
                        <th class="px-3 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($shipments as $shipment)
                        @php
                            $statusColors = [
                                'pending' => 'border-gray-200 bg-gray-50 text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                'assigned' => 'border-blue-200 bg-blue-50 text-blue-700 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
                                'picked_up' => 'border-purple-200 bg-purple-50 text-purple-700 dark:border-purple-800 dark:bg-purple-900/20 dark:text-purple-300',
                                'in_transit' => 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300',
                                'delivered' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-300',
                                'failed' => 'border-red-200 bg-red-50 text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300',
                            ];
                            $colorClass = $statusColors[$shipment->status->value] ?? $statusColors['pending'];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-3 py-3 font-bold text-gray-900 dark:text-white">{{ $shipment->order->order_number }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-300">{{ $shipment->order->user?->name ?? 'Guest' }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                                    {{ str_replace('_', ' ', $shipment->status->value) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-300">{{ $shipment->deliveryAgent?->name ?? 'Unassigned' }}</td>
                            <td class="px-3 py-3 text-gray-600 dark:text-gray-300">{{ $shipment->estimated_delivery_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-3 py-3 text-gray-500 dark:text-gray-400">{{ $shipment->updated_at->diffForHumans() }}</td>
                            <td class="px-3 py-3">
                                <a href="{{ route('vendor.shipments.show', $shipment) }}" class="text-primary-600 dark:text-primary-400 font-bold hover:text-primary-800 dark:hover:text-primary-300 transition-colors">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-12 text-center text-gray-500 dark:text-gray-400">No shipments found for your orders.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $shipments->links() }}</div>
    </x-ui.card>
</x-layouts.vendor>
