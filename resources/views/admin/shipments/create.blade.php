<x-layouts.admin>
    <x-ui.card title="Create Shipment">
        <form action="{{ route('admin.shipments.store') }}" method="POST" class="space-y-4 max-w-lg">
            @csrf
            <div>
                <label for="order_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Order</label>
                <select name="order_id" id="order_id" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                    <option value="">Select Order</option>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}" @selected(old('order_id') == $order->id)>{{ $order->order_number }} — {{ $order->user->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
                @error('order_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="delivery_agent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Agent</label>
                <select name="delivery_agent_id" id="delivery_agent_id" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="">None (assign later)</option>
                    @foreach ($deliveryAgents as $agent)
                        <option value="{{ $agent->id }}" @selected(old('delivery_agent_id') == $agent->id)>{{ $agent->name }}</option>
                    @endforeach
                </select>
                @error('delivery_agent_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tracking Number</label>
                    <input type="text" name="tracking_number" id="tracking_number" value="{{ old('tracking_number') }}" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" placeholder="e.g. 1Z999AA10123456784">
                    @error('tracking_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="carrier_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrier Name</label>
                    <input type="text" name="carrier_name" id="carrier_name" value="{{ old('carrier_name') }}" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" placeholder="e.g. UPS, FedEx">
                    @error('carrier_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="estimated_delivery_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estimated Delivery Date</label>
                <input type="date" name="estimated_delivery_date" id="estimated_delivery_date" value="{{ old('estimated_delivery_date') }}" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                @error('estimated_delivery_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" placeholder="Optional delivery notes...">{{ old('notes') }}</textarea>
                @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Create Shipment</button>
                <a href="{{ route('admin.shipments.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:text-white">Cancel</a>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
