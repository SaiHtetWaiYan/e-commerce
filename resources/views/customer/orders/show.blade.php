<x-layouts.customer>
    <div class="space-y-6">
        {{-- Order Header --}}
        <x-ui.card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white tracking-tight">Order {{ $order->order_number }}</h2>
                    <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                            'confirmed' => 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 dark:text-blue-400 border-blue-200 dark:border-blue-800',
                            'processing' => 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800',
                            'shipped' => 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 border-primary-200 dark:border-primary-800',
                            'delivered' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                            'cancelled' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 dark:text-red-400 border-red-200 dark:border-red-800',
                            'refunded' => 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700',
                        ];
                        $paymentColors = [
                            'pending' => 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800',
                            'paid' => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                            'failed' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 dark:text-red-400 border-red-200 dark:border-red-800',
                            'refunded' => 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700',
                        ];
                    @endphp
                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide border shadow-sm {{ $statusColors[$order->status->value] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">
                        {{ $order->status->value }}
                    </span>
                    <span class="inline-flex items-center rounded-lg px-3 py-1 text-xs font-bold uppercase tracking-wide border shadow-sm {{ $paymentColors[$order->payment_status->value] ?? 'bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-400 border-gray-200 dark:border-gray-700' }}">
                        Payment: {{ $order->payment_status->value }}
                    </span>
                </div>
            </div>
        </x-ui.card>

        {{-- Order Items --}}
        <x-ui.card title="Items">
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($order->items as $item)
                    <div class="flex items-start gap-4 py-4 first:pt-0 last:pb-0">
                        @if ($item->product && $item->product->images->isNotEmpty())
                            <img src="{{ $item->product->images->first()->image_url }}" alt="{{ $item->product_name }}" class="h-20 w-20 flex-shrink-0 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800 object-cover shadow-sm">
                        @elseif ($item->product_image)
                            <img src="{{ str_starts_with($item->product_image, 'http') || str_starts_with($item->product_image, '/storage') ? $item->product_image : asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="h-20 w-20 flex-shrink-0 rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800 object-cover shadow-sm">
                        @else
                            <div class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800 shadow-sm text-gray-400 dark:text-gray-500">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 dark:text-white text-base">{{ $item->product_name }}</p>
                            @if ($item->variant_name)
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5">Variant: {{ $item->variant_name }}</p>
                            @endif
                            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }} x ${{ number_format($item->unit_price, 2) }}</p>
                        </div>
                        <p class="text-base font-black text-gray-900 dark:text-white">${{ number_format($item->subtotal, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <div class="grid gap-6 md:grid-cols-2">
            {{-- Shipping Address --}}
            <x-ui.card title="Shipping Address">
                @if ($order->shipping_address)
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400 space-y-1 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                        @if (is_array($order->shipping_address))
                            @php
                                $recipientName = (string) ($order->shipping_address['full_name'] ?? $order->shipping_address['name'] ?? '');
                                $streetAddress = (string) ($order->shipping_address['street_address'] ?? $order->shipping_address['address_line_1'] ?? $order->shipping_address['street'] ?? '');
                                $addressLineTwo = (string) ($order->shipping_address['address_line_2'] ?? '');
                                $city = (string) ($order->shipping_address['city'] ?? '');
                                $state = (string) ($order->shipping_address['state'] ?? '');
                                $postalCode = (string) ($order->shipping_address['postal_code'] ?? $order->shipping_address['zip'] ?? '');
                            @endphp

                            <p class="font-bold text-gray-900 dark:text-white mb-2">{{ $recipientName }}</p>
                            <p>{{ $streetAddress }}</p>
                            @if ($addressLineTwo !== '')
                                <p>{{ $addressLineTwo }}</p>
                            @endif
                            <p>
                                {{ $city }}{{ $state !== '' ? ', '.$state : '' }}
                                {{ $postalCode }}
                            </p>
                            <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                            @if (!empty($order->shipping_address['phone']))
                                <p class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $order->shipping_address['phone'] }}
                                </p>
                            @endif
                        @else
                            <p class="whitespace-pre-line">{{ $order->shipping_address }}</p>
                        @endif
                    </div>
                @else
                    <div class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        No shipping address provided.
                    </div>
                @endif
            </x-ui.card>

            {{-- Order Totals --}}
            <x-ui.card title="Order Summary">
                <div class="space-y-3 text-sm font-medium bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-100 dark:border-gray-800">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                        <span class="text-gray-900 dark:text-white font-bold">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if ((float) $order->discount_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Discount</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-lg border border-emerald-100 dark:border-emerald-800/50">-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 dark:text-gray-400">Shipping</span>
                        <span class="text-gray-900 dark:text-white font-bold">${{ number_format($order->shipping_fee, 2) }}</span>
                    </div>
                    @if ((float) $order->tax_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-gray-400">Tax</span>
                            <span class="text-gray-900 dark:text-white font-bold">${{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                        <span class="text-base font-black text-gray-900 dark:text-white">Total</span>
                        <span class="text-xl font-black text-primary-600 dark:text-primary-400">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Shipment Info --}}
        @if ($order->shipment)
            <x-ui.card title="Shipment Tracking">
                <div class="flex flex-wrap items-center gap-4 text-sm font-medium p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-800">
                    @if ($order->shipment->tracking_number)
                        <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg> Tracking: <span class="font-bold text-gray-900 dark:text-white">{{ $order->shipment->tracking_number }}</span></span>
                    @endif
                    @if ($order->shipment->carrier_name)
                        <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg> Carrier: <span class="font-bold text-gray-900 dark:text-white">{{ $order->shipment->carrier_name }}</span></span>
                    @endif
                    <span class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm px-3 py-1 text-xs font-bold capitalize text-primary-600 dark:text-primary-400">{{ str_replace('_', ' ', $order->shipment->status->value) }}</span>
                </div>

                @if ($order->shipment->trackingEvents && $order->shipment->trackingEvents->isNotEmpty())
                    <div class="mt-6 space-y-4 border-l-2 border-primary-200 dark:border-primary-900/50 pl-6 ml-2">
                        @foreach ($order->shipment->trackingEvents as $event)
                            <div class="relative">
                                <span class="absolute -left-[31px] top-1 mt-0.5 w-3 h-3 rounded-full bg-primary-500 ring-4 ring-white dark:ring-gray-900"></span>
                                <p class="text-sm font-bold text-gray-900 dark:text-white capitalize">{{ str_replace('_', ' ', $event->status) }}</p>
                                @if ($event->description)
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5">{{ $event->description }}</p>
                                @endif
                                <p class="text-xs font-bold text-gray-400 mt-1 uppercase tracking-wider">{{ $event->event_at->format('M d, Y h:i A') }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('customer.orders.track', $order) }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors group">
                        View full tracking details
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </x-ui.card>
        @endif

        {{-- Action Buttons --}}
        <div x-data="{ showMsg: false }">
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('customer.orders.index') }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 border-2 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Orders
                </a>

                <a href="{{ route('customer.orders.invoice', $order) }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Download Invoice
                </a>

                @if ($order->status === \App\Enums\OrderStatus::Delivered)
                    <a href="{{ route('customer.returns.create', $order) }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl bg-orange-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-orange-700 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        Request Return
                    </a>
                @endif

                {{-- Message Seller --}}
                @if ($order->items->first()?->vendor_id)
                    <button type="button" @click="showMsg = !showMsg" class="inline-flex items-center gap-2 rounded-xl border-2 border-primary-600 bg-white dark:bg-transparent px-5 py-2.5 text-sm font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        Message Seller
                    </button>
                @endif

            <form action="{{ route('customer.orders.reorder', $order) }}" method="POST" class="flex flex-shrink-0">
                @csrf
                <button type="submit" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl bg-gray-900 dark:bg-gray-100 px-5 py-2.5 text-sm font-bold text-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-200 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Reorder Again
                </button>
            </form>

            <a href="{{ route('customer.disputes.create', $order) }}" class="inline-flex flex-shrink-0 items-center gap-2 rounded-xl border-2 border-red-600 bg-white dark:bg-transparent px-5 py-2.5 text-sm font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                File Dispute
            </a>

            @if ($order->canBeCancelled())
                <div x-data="{ showCancelModal: false }" class="flex-shrink-0">
                    <button @click="showCancelModal = true" type="button" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Cancel Order
                    </button>

                    {{-- Cancel Confirmation Modal --}}
                    <div x-show="showCancelModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0" x-transition.opacity>
                        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCancelModal = false"></div>
                        <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-900 p-6 sm:p-8 shadow-2xl ring-1 ring-gray-200 dark:ring-gray-800" @click.stop x-transition.scale.origin.bottom>
                            <div class="mb-5 flex items-center gap-4">
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 dark:text-white">Cancel Order</h3>
                                    <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Are you sure you want to cancel order <span class="font-bold text-gray-900 dark:text-white">{{ $order->order_number }}</span>?</p>
                                </div>
                            </div>

                            <form action="{{ route('customer.orders.cancel', $order) }}" method="POST">
                                @csrf
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-800">
                                    <label for="cancel-reason" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Reason for cancellation <span class="text-gray-400 font-medium">(optional)</span></label>
                                    <textarea id="cancel-reason" name="reason" rows="3" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm shadow-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:text-white transition-all placeholder-gray-400" placeholder="Please tell us why you want to cancel..."></textarea>
                                </div>

                                <div class="mt-6 flex flex-col-reverse sm:flex-row justify-end gap-3">
                                    <button type="button" @click="showCancelModal = false" class="inline-flex justify-center rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-transparent px-5 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all sm:w-auto w-full">
                                        Keep Order
                                    </button>
                                    <button type="submit" class="inline-flex justify-center rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700 transition-all shadow-sm sm:w-auto w-full">
                                        Yes, Cancel Order
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if ($order->items->first()?->vendor_id)
            <form action="{{ route('customer.conversations.start') }}" method="POST" enctype="multipart/form-data" x-show="showMsg" x-cloak class="mt-4" x-transition>
                @csrf
                <input type="hidden" name="vendor_id" value="{{ $order->items->first()->vendor_id }}">
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input type="text" name="body" placeholder="Type your message..." class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2.5 text-sm shadow-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:text-white transition-all" required>
                    <input type="file" name="attachment" class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300">
                    <button type="submit" class="rounded-xl flex-shrink-0 bg-primary-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-primary-700 shadow-sm transition-colors">Send</button>
                </div>
            </form>
        @endif
    </div>

        {{-- Write Reviews (only for delivered orders) --}}
        @if ($order->status === \App\Enums\OrderStatus::Delivered)
            <x-ui.card title="Write a Review">
                <div class="space-y-6">
                    @foreach ($order->items as $item)
                        @php
                            $existingReview = $item->product?->reviews()
                                ->with('reviewImages')
                                ->where('user_id', auth()->id())
                                ->where('order_item_id', $item->id)
                                ->first();
                        @endphp
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-800 p-5">
                            <div class="flex items-center gap-3 mb-4">
                                @if ($item->product?->primary_image)
                                    <img src="{{ str_starts_with($item->product->primary_image, 'http') ? $item->product->primary_image : asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->product_name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 dark:border-gray-700">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $item->product_name }}</p>
                                    @if ($item->variant_name)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->variant_name }}</p>
                                    @endif
                                </div>
                            </div>

                            @if ($existingReview)
                                {{-- Show existing review --}}
                                <div class="bg-white dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-1 mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $existingReview->rating ? 'text-amber-400 fill-current' : 'text-gray-200 dark:text-gray-600' }}" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">{{ $existingReview->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if ($existingReview->comment)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $existingReview->comment }}</p>
                                    @endif
                                    @if ($existingReview->reviewImages->isNotEmpty())
                                        <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                            @foreach ($existingReview->reviewImages as $reviewImage)
                                                @if ($reviewImage->media_type === 'video')
                                                    <video controls class="h-28 w-full rounded-xl border border-gray-200 object-cover dark:border-gray-700">
                                                        <source src="{{ Storage::url($reviewImage->file_path) }}">
                                                    </video>
                                                @else
                                                    <img src="{{ Storage::url($reviewImage->file_path) }}" alt="Review media" class="h-28 w-full rounded-xl border border-gray-200 object-cover dark:border-gray-700">
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="mt-2 flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 font-bold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Review submitted
                                    </div>
                                </div>
                            @elseif ($item->product)
                                {{-- Review form --}}
                                <form action="{{ route('customer.reviews.store') }}" method="POST" enctype="multipart/form-data" x-data="{ rating: 0, hoverRating: 0 }">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    <input type="hidden" name="order_item_id" value="{{ $item->id }}">
                                    <input type="hidden" name="rating" :value="rating">

                                    {{-- Star Rating --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Your Rating</label>
                                        <div class="flex items-center gap-1">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <button type="button" @click="rating = {{ $i }}" @mouseenter="hoverRating = {{ $i }}" @mouseleave="hoverRating = 0" class="focus:outline-none transition-transform hover:scale-125">
                                                    <svg :class="(hoverRating || rating) >= {{ $i }} ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600'" class="w-7 h-7 fill-current transition-colors" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                </button>
                                            @endfor
                                            <span x-show="rating > 0" x-transition class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-2" x-text="['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'][rating]"></span>
                                        </div>
                                    </div>

                                    {{-- Comment --}}
                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Your Review <span class="text-gray-400 font-medium">(optional)</span></label>
                                        <textarea name="comment" rows="3" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-none" placeholder="Share your experience with this product..."></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Photos or Videos <span class="text-gray-400 font-medium">(optional)</span></label>
                                        <input type="file" name="media[]" accept="image/*,video/mp4,video/quicktime,video/webm" multiple class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-3 text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300">
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload up to 5 files. Images and short videos are supported.</p>
                                    </div>

                                    <button type="submit" :disabled="rating === 0" :class="rating === 0 ? 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-700 hover:-translate-y-0.5 shadow-sm'" class="px-5 py-2.5 text-white font-bold text-sm rounded-xl transition-all">
                                        Submit Review
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @endif
    </div>

</x-layouts.customer>
