<x-layouts.vendor>
    <div class="space-y-6">
        <x-ui.card title="Shipment for Order #{{ $shipment->order->order_number }}">
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
            <div class="grid gap-4 sm:grid-cols-2 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->order->user?->name ?? 'Guest' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status</p>
                    <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide {{ $colorClass }}">
                        {{ str_replace('_', ' ', $shipment->status->value) }}
                    </span>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Delivery Agent</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->deliveryAgent?->name ?? 'Unassigned' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Est. Delivery</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->estimated_delivery_date?->format('M d, Y') ?? '—' }}</p>
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
            </div>
        </x-ui.card>

        {{-- Your Items in this Order --}}
        <x-ui.card title="Your Items">
            <div class="space-y-3 text-sm">
                @foreach ($shipment->order->items as $item)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 dark:border-gray-800 px-3 py-2">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</p>
                            @if ($item->variant_name)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->variant_name }}</p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                        </div>
                        <p class="font-bold text-gray-900 dark:text-white">${{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        {{-- Tracking Events --}}
        @if ($shipment->trackingEvents && $shipment->trackingEvents->isNotEmpty())
            <x-ui.card title="Tracking Timeline">
                <div class="space-y-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                    @foreach ($shipment->trackingEvents as $event)
                        <div class="relative">
                            <div class="absolute -left-[23px] top-1 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900 bg-primary-500"></div>
                            <p class="text-sm font-bold capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $event->status) }}</p>
                            @if ($event->description)
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $event->description }}</p>
                            @endif
                            @if ($event->location)
                                <p class="text-xs text-gray-500 dark:text-gray-500">📍 {{ $event->location }}</p>
                            @endif
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $event->event_at->format('M d, Y h:i A') }}</p>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif

        <a href="{{ route('vendor.shipments.index') }}" class="inline-flex items-center text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">&larr; Back to Shipments</a>
    </div>
</x-layouts.vendor>
