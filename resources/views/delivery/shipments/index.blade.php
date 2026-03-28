<x-layouts.delivery>
    <x-ui.card title="Assigned Shipments">
        <div class="space-y-3 text-sm">
            @foreach ($shipments as $shipment)
                <a href="{{ route('delivery.shipments.show', $shipment) }}" class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2 hover:bg-gray-50 dark:bg-gray-800">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->tracking_number ?? 'Shipment #'.$shipment->id }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Order: {{ $shipment->order->order_number }}</p>
                    </div>
                    <span class="capitalize text-gray-700 dark:text-gray-300">{{ $shipment->status->value }}</span>
                </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $shipments->links() }}</div>
    </x-ui.card>
</x-layouts.delivery>
