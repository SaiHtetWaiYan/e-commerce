<x-layouts.vendor>
    <x-ui.card title="Store Settings">
        <form action="{{ route('vendor.settings.update') }}" method="POST" enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-2">
            @csrf
            @method('PUT')
            <input name="store_name" value="{{ old('store_name', $vendorProfile?->store_name) }}" placeholder="Store name" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Store Logo</label>
                @if($vendorProfile?->store_logo)
                    <img src="{{ Storage::url($vendorProfile->store_logo) }}" alt="Store Logo" class="h-16 w-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700 mb-2">
                @endif
                <input type="file" name="store_logo" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/20 dark:file:text-primary-400">
                @error('store_logo')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Store Banner</label>
                @if($vendorProfile?->store_banner)
                    <img src="{{ Storage::url($vendorProfile->store_banner) }}" alt="Store Banner" class="w-full h-16 object-cover rounded-lg border border-gray-200 dark:border-gray-700 mb-2">
                @endif
                <input type="file" name="store_banner" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/20 dark:file:text-primary-400">
                @error('store_banner')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <input name="bank_name" value="{{ old('bank_name', $vendorProfile?->bank_name) }}" placeholder="Bank name" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="bank_account_number" value="{{ old('bank_account_number', $vendorProfile?->bank_account_number) }}" placeholder="Bank account number" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="bank_account_name" value="{{ old('bank_account_name', $vendorProfile?->bank_account_name) }}" placeholder="Bank account name" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <textarea name="store_description" placeholder="Store description" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">{{ old('store_description', $vendorProfile?->store_description) }}</textarea>
            <x-ui.button type="submit" class="sm:col-span-2">Update Store</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.vendor>
