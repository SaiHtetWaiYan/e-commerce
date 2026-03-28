<x-layouts.customer>
    <div class="space-y-6">
        {{-- Back Link --}}
        <a href="{{ route('customer.orders.show', $order) }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Order Details
        </a>

        {{-- Order Summary Header --}}
        <x-ui.card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Track Order #{{ $order->order_number }}</h2>
                    <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                            'confirmed' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                            'processing' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                            'shipped' => 'bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-800',
                            'delivered' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                            'cancelled' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 border-red-200 dark:border-red-800',
                            'refunded' => 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide border shadow-sm {{ $statusColors[$order->status->value] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">
                        {{ str_replace('_', ' ', $order->status->value) }}
                    </span>
                    <span class="text-lg font-black text-gray-900 dark:text-white">${{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </x-ui.card>

        @if ($order->shipment)
            @php
                $shipment = $order->shipment;
                $shipmentStatus = $shipment->status->value;

                // Map shipment statuses to progress steps
                // Steps: Order Placed -> Confirmed -> Shipped -> Out for Delivery -> Delivered
                $steps = [
                    ['key' => 'placed', 'label' => 'Order Placed', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['key' => 'confirmed', 'label' => 'Confirmed', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['key' => 'shipped', 'label' => 'Shipped', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['key' => 'out_for_delivery', 'label' => 'Out for Delivery', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
                    ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'M5 13l4 4L19 7'],
                ];

                // Determine which step is the current one based on shipment status
                $statusToStep = [
                    'pending' => 0,
                    'assigned' => 1,
                    'picked_up' => 2,
                    'in_transit' => 3,
                    'delivered' => 4,
                    'failed' => -1,
                ];

                $currentStepIndex = $statusToStep[$shipmentStatus] ?? 0;
                $isFailed = $shipmentStatus === 'failed';
            @endphp

            {{-- Visual Progress Stepper --}}
            <x-ui.card>
                @if ($isFailed)
                    <div class="flex items-center gap-3 p-4 mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-red-700 dark:text-red-400">Delivery Failed</p>
                            <p class="text-xs font-medium text-red-600/80 dark:text-red-400/70 mt-0.5">There was an issue with this delivery. Please contact support for assistance.</p>
                        </div>
                    </div>
                @endif

                {{-- Desktop Stepper (horizontal) --}}
                <div class="hidden sm:block">
                    <div class="relative flex items-center justify-between">
                        {{-- Progress bar background --}}
                        <div class="absolute left-0 right-0 top-5 h-1 bg-gray-200 dark:bg-gray-700 rounded-full mx-8"></div>

                        {{-- Progress bar filled --}}
                        @if ($currentStepIndex > 0 && !$isFailed)
                            <div class="absolute left-0 top-5 h-1 bg-orange-500 rounded-full mx-8 transition-all duration-500" style="width: calc({{ ($currentStepIndex / (count($steps) - 1)) * 100 }}% - 4rem)"></div>
                        @endif

                        @foreach ($steps as $index => $step)
                            @php
                                $isCompleted = !$isFailed && $index < $currentStepIndex;
                                $isCurrent = !$isFailed && $index === $currentStepIndex;
                                $isPending = $isFailed || $index > $currentStepIndex;
                            @endphp
                            <div class="relative z-10 flex flex-col items-center flex-1">
                                {{-- Step Circle --}}
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300 {{ $isCompleted ? 'bg-orange-500 border-orange-500 text-white shadow-lg shadow-orange-500/30' : ($isCurrent ? 'bg-orange-500 border-orange-500 text-white shadow-lg shadow-orange-500/30 ring-4 ring-orange-500/20' : 'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500') }}">
                                    @if ($isCompleted)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"></path></svg>
                                    @endif
                                </div>
                                {{-- Step Label --}}
                                <span class="mt-3 text-xs font-bold text-center {{ $isCompleted || $isCurrent ? 'text-orange-600 dark:text-orange-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $step['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Mobile Stepper (vertical compact) --}}
                <div class="sm:hidden space-y-3">
                    @foreach ($steps as $index => $step)
                        @php
                            $isCompleted = !$isFailed && $index < $currentStepIndex;
                            $isCurrent = !$isFailed && $index === $currentStepIndex;
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full border-2 {{ $isCompleted ? 'bg-orange-500 border-orange-500 text-white' : ($isCurrent ? 'bg-orange-500 border-orange-500 text-white ring-4 ring-orange-500/20' : 'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500') }}">
                                @if ($isCompleted)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                @else
                                    <span class="text-xs font-bold">{{ $index + 1 }}</span>
                                @endif
                            </div>
                            <span class="text-sm font-bold {{ $isCompleted || $isCurrent ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500' }}">{{ $step['label'] }}</span>
                            @if ($isCurrent)
                                <span class="ml-auto inline-flex items-center rounded-full bg-orange-100 dark:bg-orange-900/30 px-2.5 py-0.5 text-xs font-bold text-orange-700 dark:text-orange-400">Current</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-ui.card>

            {{-- Shipment Details Card --}}
            <x-ui.card title="Shipment Details">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Tracking Number --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Tracking Number</span>
                        </div>
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $shipment->tracking_number ?? 'Not assigned' }}</p>
                    </div>

                    {{-- Carrier --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Carrier</span>
                        </div>
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $shipment->carrier_name ?? 'Not specified' }}</p>
                    </div>

                    {{-- Status --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Current Status</span>
                        </div>
                        @php
                            $shipmentStatusColors = [
                                'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400',
                                'assigned' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400',
                                'picked_up' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400',
                                'in_transit' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400',
                                'delivered' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                'failed' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                            ];
                        @endphp
                        <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold capitalize {{ $shipmentStatusColors[$shipmentStatus] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-400' }}">
                            {{ str_replace('_', ' ', $shipmentStatus) }}
                        </span>
                    </div>

                    {{-- Delivery Agent --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Delivery Agent</span>
                        </div>
                        <p class="text-sm font-black text-gray-900 dark:text-white">{{ $shipment->deliveryAgent?->name ?? 'Not assigned yet' }}</p>
                    </div>

                    {{-- Current Location --}}
                    @if ($shipment->current_latitude && $shipment->current_longitude)
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-2 mb-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Current Location</span>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $shipment->current_latitude }},{{ $shipment->current_longitude }}" target="_blank" rel="noopener noreferrer" class="text-sm font-bold text-orange-600 dark:text-orange-400 hover:underline">📍 View on Map</a>
                        </div>
                    @endif

                    {{-- Estimated Delivery --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Estimated Delivery</span>
                        </div>
                        <p class="text-sm font-black text-gray-900 dark:text-white">
                            {{ $shipment->estimated_delivery_date ? $shipment->estimated_delivery_date->format('M d, Y') : 'Pending' }}
                            @if ($shipment->estimated_delivery_time_from && $shipment->estimated_delivery_time_to)
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ \Carbon\Carbon::parse($shipment->estimated_delivery_time_from)->format('g:i A') }} – {{ \Carbon\Carbon::parse($shipment->estimated_delivery_time_to)->format('g:i A') }})</span>
                            @endif
                        </p>
                    </div>

                    {{-- Shipped / Delivered Date --}}
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2 mb-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                {{ $shipment->delivered_at ? 'Delivered At' : 'Shipped At' }}
                            </span>
                        </div>
                        <p class="text-sm font-black text-gray-900 dark:text-white">
                            @if ($shipment->delivered_at)
                                {{ $shipment->delivered_at->format('M d, Y \a\t h:i A') }}
                            @elseif ($shipment->shipped_at)
                                {{ $shipment->shipped_at->format('M d, Y \a\t h:i A') }}
                            @else
                                Not yet shipped
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Notes --}}
                @if ($shipment->notes)
                    <div class="mt-4 p-4 bg-orange-50 dark:bg-orange-900/10 rounded-xl border border-orange-100 dark:border-orange-900/30">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm font-medium text-orange-700 dark:text-orange-400">{{ $shipment->notes }}</p>
                        </div>
                    </div>
                @endif
            </x-ui.card>

            {{-- Tracking Timeline --}}
            <x-ui.card title="Tracking Timeline">
                @if ($shipment->trackingEvents && $shipment->trackingEvents->isNotEmpty())
                    <div class="relative">
                        {{-- Timeline line --}}
                        <div class="absolute left-[17px] top-2 bottom-2 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                        <div class="space-y-6">
                            @foreach ($shipment->trackingEvents as $index => $event)
                                @php
                                    $isFirst = $index === 0;
                                    $eventStatusIcons = [
                                        'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'assigned' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                                        'picked_up' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                                        'in_transit' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
                                        'delivered' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'failed' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                    ];
                                    $eventStatusValue = is_object($event->status) ? $event->status->value : $event->status;
                                    $iconPath = $eventStatusIcons[$eventStatusValue] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
                                @endphp
                                <div class="relative flex gap-4 pl-10">
                                    {{-- Timeline dot --}}
                                    <div class="absolute left-0 top-0.5 flex items-center justify-center w-[35px] h-[35px] rounded-full {{ $isFirst ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/30 ring-4 ring-orange-500/20' : 'bg-white dark:bg-gray-900 border-2 border-gray-300 dark:border-gray-600 text-gray-400 dark:text-gray-500' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"></path></svg>
                                    </div>

                                    {{-- Event Content --}}
                                    <div class="flex-1 pb-2">
                                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800 {{ $isFirst ? 'ring-1 ring-orange-200 dark:ring-orange-900/50 border-orange-100 dark:border-orange-900/30' : '' }}">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white capitalize">
                                                    {{ str_replace('_', ' ', $eventStatusValue) }}
                                                </p>
                                                <time class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                                    {{ $event->event_at->format('M d, Y \a\t h:i A') }}
                                                </time>
                                            </div>

                                            @if ($event->description)
                                                <p class="mt-1.5 text-sm font-medium text-gray-600 dark:text-gray-400">{{ $event->description }}</p>
                                            @endif

                                            @if ($event->location)
                                                <div class="mt-2 flex items-center gap-1.5 text-xs font-medium text-gray-400 dark:text-gray-500">
                                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    <span>{{ $event->location }}</span>
                                                </div>
                                            @endif

                                            @if ($event->latitude && $event->longitude)
                                                <a href="https://www.google.com/maps?q={{ $event->latitude }},{{ $event->longitude }}" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex items-center gap-1 text-xs font-bold text-orange-600 dark:text-orange-400 hover:underline">
                                                    📍 View on Map
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="py-12 text-center">
                        <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-4 text-gray-400 shadow-inner">
                            <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="font-bold text-gray-900 dark:text-gray-100 mb-1">No tracking events yet</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tracking updates will appear here once your package is on its way.</p>
                    </div>
                @endif
            </x-ui.card>

            {{-- Delivery Proof --}}
            @if ($shipment->delivery_proof_image && $shipment->status === \App\Enums\ShipmentStatus::Delivered)
                <x-ui.card title="Delivery Proof">
                    <div class="rounded-xl overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm">
                        <img src="{{ str_starts_with($shipment->delivery_proof_image, 'http') ? $shipment->delivery_proof_image : asset('storage/' . $shipment->delivery_proof_image) }}" alt="Delivery proof" class="w-full max-h-96 object-contain bg-gray-50 dark:bg-gray-800">
                    </div>
                    @if ($shipment->delivered_at)
                        <p class="mt-3 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                            Delivered on {{ $shipment->delivered_at->format('M d, Y \a\t h:i A') }}
                        </p>
                    @endif
                </x-ui.card>
            @endif
        @else
            {{-- No Shipment --}}
            <x-ui.card>
                <div class="py-12 text-center">
                    <div class="mx-auto w-20 h-20 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-5 text-gray-400 shadow-inner">
                        <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <p class="font-black text-xl text-gray-900 dark:text-gray-100 mb-2">Shipment Not Created Yet</p>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Your order is being prepared. Tracking information will be available once the shipment has been created.</p>
                </div>
            </x-ui.card>
        @endif

        {{-- Bottom Actions --}}
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('customer.orders.show', $order) }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Order
            </a>
            <a href="{{ route('customer.orders.index') }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                All Orders
            </a>
        </div>
    </div>
</x-layouts.customer>
