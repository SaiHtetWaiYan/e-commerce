<x-layouts.admin>
    <x-ui.card title="Vendor Profile">
        <div class="space-y-1 mb-6">
            <p class="text-sm text-gray-900 dark:text-white"><span class="font-semibold text-gray-500 dark:text-gray-400">Name:</span> {{ $vendor->name }}</p>
            <p class="text-sm text-gray-900 dark:text-white"><span class="font-semibold text-gray-500 dark:text-gray-400">Email:</span> {{ $vendor->email }}</p>
            <p class="text-sm text-gray-900 dark:text-white"><span class="font-semibold text-gray-500 dark:text-gray-400">Store:</span> {{ $vendor->vendorProfile?->store_name ?? '-' }}</p>
        </div>

        <div class="mt-4 flex gap-2">
            <form action="{{ route('admin.vendors.approve', $vendor) }}" method="POST">
                @csrf
                @method('PATCH')
                <x-ui.button size="sm" type="submit">Approve</x-ui.button>
            </form>
            <form action="{{ route('admin.vendors.reject', $vendor) }}" method="POST">
                @csrf
                @method('PATCH')
                <x-ui.button size="sm" variant="danger" type="submit">Reject</x-ui.button>
            </form>
        </div>
    </x-ui.card>

    <x-ui.card title="Vendor Products" class="mt-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($vendor->products as $product)
                <x-storefront.product-card :product="$product" />
            @endforeach
        </div>
    </x-ui.card>
</x-layouts.admin>
