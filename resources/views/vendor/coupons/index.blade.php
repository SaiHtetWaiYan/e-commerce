<x-layouts.vendor>
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Coupons</h1>
        <a href="{{ route('vendor.coupons.create') }}"><x-ui.button size="sm">Create Coupon</x-ui.button></a>
    </div>

    <x-ui.card>
        <div class="space-y-3 text-sm">
            @foreach ($coupons as $coupon)
                <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $coupon->code }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $coupon->type->value }} | {{ $coupon->value }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('vendor.coupons.edit', $coupon) }}" class="text-sm font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400">Edit</a>
                        <form action="{{ route('vendor.coupons.destroy', $coupon) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-ui.button size="sm" variant="danger" type="submit">Delete</x-ui.button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $coupons->links() }}</div>
    </x-ui.card>
</x-layouts.vendor>
