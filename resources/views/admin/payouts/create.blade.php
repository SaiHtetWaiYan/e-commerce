<x-layouts.admin>
    <x-ui.card title="Create Vendor Payout">
        <form action="{{ route('admin.payouts.store') }}" method="POST" class="space-y-4 max-w-lg">
            @csrf
            <div>
                <label for="vendor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vendor</label>
                <select name="vendor_id" id="vendor_id" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                    <option value="">Select Vendor</option>
                    @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->vendorProfile->store_name ?? $vendor->name }} ({{ $vendor->email }})</option>
                    @endforeach
                </select>
                @error('vendor_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="period_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period Start</label>
                    <input type="date" name="period_start" id="period_start" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                    @error('period_start') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="period_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Period End</label>
                    <input type="date" name="period_end" id="period_end" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                    @error('period_end') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Payment Method</label>
                <select name="payment_method" id="payment_method" class="mt-1 block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="paypal">PayPal</option>
                </select>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Calculate & Create Payout</button>
                <a href="{{ route('admin.payouts.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:text-white">Cancel</a>
            </div>
        </form>
    </x-ui.card>
</x-layouts.admin>
