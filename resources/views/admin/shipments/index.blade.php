<x-layouts.admin>
    <x-ui.card title="Shipments">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700 text-left text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">
                        <th class="px-3 py-2">Order</th>
                        <th class="px-3 py-2">Customer</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Agent</th>
                        <th class="px-3 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($shipments as $shipment)
                        <tr>
                            <td class="px-3 py-2 font-medium">
                                <a href="{{ route('admin.shipments.show', $shipment) }}" class="text-blue-600 hover:underline">{{ $shipment->order->order_number }}</a>
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $shipment->order->user->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold capitalize
                                    {{ $shipment->status->value === 'delivered' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : ($shipment->status->value === 'in_transit' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400') }}">
                                    {{ str_replace('_', ' ', $shipment->status->value) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $shipment->deliveryAgent->name ?? 'Unassigned' }}</td>
                            <td class="px-3 py-2">
                                @if (!$shipment->delivery_agent_id)
                                    <form action="{{ route('admin.shipments.assign', $shipment) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="delivery_agent_id" class="rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-2 py-1 text-xs">
                                            <option value="">Select Agent</option>
                                            @foreach ($deliveryAgents as $agent)
                                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded bg-blue-600 px-2 py-1 text-xs font-medium text-white hover:bg-blue-700">Assign</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.shipments.show', $shipment) }}" class="text-xs text-blue-600 hover:underline">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400 dark:text-gray-500">No shipments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $shipments->links() }}</div>
    </x-ui.card>
</x-layouts.admin>
