<x-layouts.admin>
    <div class="space-y-6">
        <x-ui.card title="Customers">
            <div class="space-y-2 text-sm">
                @forelse ($customers as $customer)
                    <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->email }}</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            Customer
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No customers found.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $customers->links() }}</div>
        </x-ui.card>

        <x-ui.card title="Vendors">
            <div class="space-y-2 text-sm">
                @forelse ($vendors as $vendor)
                    <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $vendor->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $vendor->email }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Store: {{ $vendor->vendorProfile?->store_name ?? 'No store profile' }}
                            </p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            Vendor
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No vendors found.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $vendors->links() }}</div>
        </x-ui.card>

        <x-ui.card title="Delivery Agents">
            <div class="space-y-2 text-sm">
                @forelse ($deliveryAgents as $deliveryAgent)
                    <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $deliveryAgent->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $deliveryAgent->email }}</p>
                        </div>
                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                            Delivery Agent
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">No delivery agents found.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $deliveryAgents->links() }}</div>
        </x-ui.card>
    </div>
</x-layouts.admin>
