<x-layouts.admin>
    <div class="space-y-6">
        <x-ui.card title="Shipment Details">
            <div class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Order</p>
                    <a href="{{ route('admin.orders.show', $shipment->order) }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">{{ $shipment->order->order_number }}</a>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->order->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status</p>
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
                    <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                        {{ str_replace('_', ' ', $shipment->status->value) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Delivery Agent</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->deliveryAgent->name ?? 'Unassigned' }}</p>
                </div>
                @if ($shipment->tracking_number)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Tracking Number</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->tracking_number }}</p>
                    </div>
                @endif
                @if ($shipment->carrier_name)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Carrier</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->carrier_name }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Est. Delivery</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        {{ $shipment->estimated_delivery_date?->format('M d, Y') ?? 'N/A' }}
                        @if ($shipment->estimated_delivery_time_from && $shipment->estimated_delivery_time_to)
                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ \Carbon\Carbon::parse($shipment->estimated_delivery_time_from)->format('g:i A') }} – {{ \Carbon\Carbon::parse($shipment->estimated_delivery_time_to)->format('g:i A') }})</span>
                        @endif
                    </p>
                </div>
                @if ($shipment->current_latitude && $shipment->current_longitude)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Current Location</p>
                        <a href="https://www.google.com/maps?q={{ $shipment->current_latitude }},{{ $shipment->current_longitude }}" target="_blank" rel="noopener noreferrer" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">📍 View on Map</a>
                    </div>
                @endif
                <div class="sm:col-span-2">
                    <p class="text-gray-500 dark:text-gray-400">Delivery Proof</p>
                    @if ($shipment->delivery_proof_image)
                        <a href="{{ asset('storage/'.$shipment->delivery_proof_image) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-primary-600 dark:text-primary-400 underline">
                            View proof image
                        </a>
                    @else
                        <p class="font-medium text-gray-900 dark:text-white">Not uploaded</p>
                    @endif
                </div>
            </div>
        </x-ui.card>

        @if ($shipment->trackingEvents && $shipment->trackingEvents->isNotEmpty())
            <x-ui.card title="Tracking Events">
                <div class="space-y-3 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                    @foreach ($shipment->trackingEvents as $event)
                        <div class="relative">
                            <div class="absolute -left-[23px] top-1 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900 bg-primary-500"></div>
                            <p class="text-sm font-medium capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $event->status) }}</p>
                            @if ($event->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->description }}</p>
                            @endif
                            @if ($event->latitude && $event->longitude)
                                <a href="https://www.google.com/maps?q={{ $event->latitude }},{{ $event->longitude }}" target="_blank" rel="noopener noreferrer" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">📍 View on Map</a>
                            @endif
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $event->event_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        {{-- Retry Delivery (failed shipments) --}}
        @if ($shipment->status->value === 'failed')
            <x-ui.card title="Delivery Failed">
                <div class="flex items-center gap-4">
                    <p class="text-sm text-red-600 dark:text-red-400 font-medium">This delivery attempt failed.</p>
                    <form action="{{ route('admin.shipments.retry', $shipment) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-xl bg-amber-600 px-5 py-2 text-sm font-bold text-white hover:bg-amber-700 transition-colors">Retry Delivery</button>
                    </form>
                </div>
            </x-ui.card>
        @endif

        {{-- Assign / Reassign Agent --}}
        <x-ui.card title="{{ $shipment->delivery_agent_id ? 'Reassign Delivery Agent' : 'Assign Delivery Agent' }}">
            <form action="{{ route('admin.shipments.assign', $shipment) }}" method="POST" class="flex items-center gap-3">
                @csrf
                @method('PATCH')
                <select name="delivery_agent_id" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="">Select Agent</option>
                    @foreach ($deliveryAgents ?? [] as $agent)
                        <option value="{{ $agent->id }}" @selected($shipment->delivery_agent_id === $agent->id)>{{ $agent->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">{{ $shipment->delivery_agent_id ? 'Reassign' : 'Assign Agent' }}</button>
            </form>
        </x-ui.card>

        {{-- Update ETA --}}
        <x-ui.card title="Update Estimated Delivery">
            <form action="{{ route('admin.shipments.eta', $shipment) }}" method="POST" class="grid gap-3 sm:grid-cols-3 items-end">
                @csrf
                @method('PATCH')
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Date</label>
                    <input type="date" name="estimated_delivery_date" value="{{ $shipment->estimated_delivery_date?->format('Y-m-d') }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">From Time</label>
                    <input type="time" name="estimated_delivery_time_from" value="{{ $shipment->estimated_delivery_time_from }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">To Time</label>
                        <input type="time" name="estimated_delivery_time_to" value="{{ $shipment->estimated_delivery_time_to }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                    <button type="submit" class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 transition-colors whitespace-nowrap">Update ETA</button>
                </div>
            </form>
        </x-ui.card>

        <a href="{{ route('admin.shipments.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">&larr; Back to Shipments</a>
    </div>
</x-layouts.admin>
