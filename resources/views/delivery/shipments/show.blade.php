<x-layouts.delivery>
    <x-ui.card title="Shipment {{ $shipment->tracking_number ?? '#'.$shipment->id }}">
        @php
            $shippingAddress = is_array($shipment->order->shipping_address) ? $shipment->order->shipping_address : [];
            $recipientName = (string) ($shippingAddress['full_name'] ?? $shippingAddress['name'] ?? $shipment->order->user->name);
            $streetAddress = (string) ($shippingAddress['street_address'] ?? $shippingAddress['address_line_1'] ?? $shippingAddress['street'] ?? $shippingAddress['address'] ?? '');
            $addressLineTwo = (string) ($shippingAddress['address_line_2'] ?? '');
            $city = (string) ($shippingAddress['city'] ?? '');
            $state = (string) ($shippingAddress['state'] ?? '');
            $postalCode = (string) ($shippingAddress['postal_code'] ?? $shippingAddress['zip'] ?? '');
            $country = (string) ($shippingAddress['country'] ?? '');
            $phone = (string) ($shippingAddress['phone'] ?? '');
            $cityState = collect([$city, $state])->filter(fn (string $part): bool => $part !== '')->implode(', ');
            $normalizedPaymentMethod = (string) ($shipment->order->payment_method ?? '');
            $isCodOrder = in_array($normalizedPaymentMethod, ['cod', 'cash_on_delivery'], true);
            $isCodPaid = $shipment->order->payment_status->value === 'paid';
        @endphp

        <p class="text-sm text-gray-500 dark:text-gray-400">Order: {{ $shipment->order->order_number }}</p>
        <p class="text-sm text-gray-500 dark:text-gray-400">Customer: {{ $shipment->order->user->name }}</p>

        <div class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 p-4 text-sm">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Delivery Address</p>
            <p class="mt-2 font-bold text-gray-900 dark:text-white">{{ $recipientName }}</p>
            @if ($phone !== '')
                <p class="text-gray-600 dark:text-gray-300">{{ $phone }}</p>
            @endif
            @if ($streetAddress !== '')
                <p class="mt-2 text-gray-700 dark:text-gray-200">{{ $streetAddress }}</p>
            @endif
            @if ($addressLineTwo !== '')
                <p class="text-gray-700 dark:text-gray-200">{{ $addressLineTwo }}</p>
            @endif
            @if ($cityState !== '' || $postalCode !== '')
                <p class="text-gray-700 dark:text-gray-200">{{ trim($cityState.' '.$postalCode) }}</p>
            @endif
            @if ($country !== '')
                <p class="text-gray-700 dark:text-gray-200">{{ $country }}</p>
            @endif
        </div>

        <form action="{{ route('delivery.shipments.update', $shipment) }}" method="POST" class="mt-4 grid gap-3 sm:grid-cols-2">
            @csrf
            @method('PATCH')
            <select name="status" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                @foreach (['assigned', 'picked_up', 'in_transit', 'delivered', 'failed'] as $status)
                    <option value="{{ $status }}" @selected($shipment->status->value === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <input name="location" placeholder="Location" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="latitude" placeholder="Latitude" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="longitude" placeholder="Longitude" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <textarea name="description" placeholder="Description" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
            @if ($isCodOrder)
                <div class="sm:col-span-2 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50/70 dark:bg-amber-900/20 p-3">
                    <p class="text-xs font-bold uppercase tracking-wide text-amber-700 dark:text-amber-300">Cash On Delivery</p>
                    <p class="mt-1 text-xs text-amber-800 dark:text-amber-200">Current payment status: {{ $shipment->order->payment_status->value }}</p>
                    <label class="mt-2 inline-flex items-start gap-2 text-sm font-medium text-amber-900 dark:text-amber-100">
                        <input
                            type="checkbox"
                            name="cash_collected"
                            value="1"
                            class="mt-0.5 h-4 w-4 rounded border-amber-300 text-primary-600 focus:ring-primary-500 dark:border-amber-600 dark:bg-gray-900"
                            @checked((string) old('cash_collected', $isCodPaid ? '1' : '0') === '1')
                        >
                        Confirm cash has been collected from the customer.
                    </label>
                    @error('cash_collected')
                        <p class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif
            <x-ui.button type="submit" class="sm:col-span-2">Update Shipment</x-ui.button>
        </form>

        <div class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 p-4">
            <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Delivery Proof</p>

            @if ($shipment->delivery_proof_image)
                <a href="{{ asset('storage/'.$shipment->delivery_proof_image) }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex text-sm font-semibold text-primary-600 dark:text-primary-400 underline">
                    View uploaded proof
                </a>
            @else
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">No proof uploaded yet.</p>
            @endif

            <form action="{{ route('delivery.shipments.proof', $shipment) }}" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3 sm:grid-cols-2">
                @csrf
                <input type="file" name="proof_image" accept="image/*" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                <input type="text" name="recipient_name" placeholder="Recipient name" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                <input type="text" name="recipient_phone" placeholder="Recipient phone" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                <textarea name="notes" placeholder="Notes" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
                <x-ui.button type="submit" class="sm:col-span-2">Upload Delivery Proof</x-ui.button>
            </form>
        </div>

        <div class="mt-6 space-y-2 text-sm">
            @foreach ($shipment->trackingEvents as $event)
                <div class="rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                    <p class="font-medium capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $event->status) }}</p>
                    <p class="text-gray-500 dark:text-gray-400">{{ $event->description }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $event->event_at->format('M d, Y H:i') }}</p>
                </div>
            @endforeach
        </div>
    </x-ui.card>
</x-layouts.delivery>
