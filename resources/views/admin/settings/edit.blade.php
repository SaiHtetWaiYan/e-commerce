<x-layouts.admin>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Marketplace Settings</h1>
            <p class="mt-1 text-sm font-medium text-gray-500 dark:text-gray-400">Update pricing and operational defaults without editing environment variables.</p>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-ui.card title="Brand Settings">
                <div class="grid gap-4">
                    <div>
                        <label for="logo" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">App Logo</label>
                        @if($settings['logo'])
                            <img src="{{ Storage::url($settings['logo']) }}" alt="App Logo" class="h-16 object-contain mb-4 rounded-lg bg-white dark:bg-gray-800 p-2 border border-gray-200 dark:border-gray-700">
                        @endif
                        <input id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/20 dark:file:text-primary-400 focus:outline-none">
                        @error('logo') <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to keep the current logo. Recommended format: SVG or PNG.</p>
                    </div>
                </div>
            </x-ui.card>

            <div class="grid gap-6 lg:grid-cols-2">
                <x-ui.card title="Pricing Rules">
                    <div class="grid gap-4">
                        <div>
                            <label for="default_currency" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Currency Code</label>
                            <input id="default_currency" name="default_currency" value="{{ old('default_currency', $settings['default_currency']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm uppercase text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="USD">
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="default_shipping_fee" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Default Shipping Fee</label>
                                <input id="default_shipping_fee" name="default_shipping_fee" type="number" min="0" step="0.01" value="{{ old('default_shipping_fee', $settings['default_shipping_fee']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label for="free_shipping_threshold" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Free Shipping Threshold</label>
                                <input id="free_shipping_threshold" name="free_shipping_threshold" type="number" min="0" step="0.01" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="default_tax_rate" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Default Tax Rate (%)</label>
                                <input id="default_tax_rate" name="default_tax_rate" type="number" min="0" max="100" step="0.01" value="{{ old('default_tax_rate', $settings['default_tax_rate']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Example: enter <code>7</code> for 7% tax.</p>
                            </div>
                            <div>
                                <label for="vendor_default_commission_rate" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Vendor Commission Rate (%)</label>
                                <input id="vendor_default_commission_rate" name="vendor_default_commission_rate" type="number" min="0" max="100" step="0.01" value="{{ old('vendor_default_commission_rate', $settings['vendor_default_commission_rate']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card title="Operational Defaults">
                    <div class="grid gap-4">
                        <div>
                            <label for="default_carrier" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Default Carrier</label>
                            <input id="default_carrier" name="default_carrier" value="{{ old('default_carrier', $settings['default_carrier']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="order_number_prefix" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Order Prefix</label>
                                <input id="order_number_prefix" name="order_number_prefix" value="{{ old('order_number_prefix', $settings['order_number_prefix']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm uppercase text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label for="tracking_prefix" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Tracking Prefix</label>
                                <input id="tracking_prefix" name="tracking_prefix" value="{{ old('tracking_prefix', $settings['tracking_prefix']) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm uppercase text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>

                        <label class="flex items-start gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3">
                            <input type="checkbox" name="vendor_require_approval" value="1" @checked(old('vendor_require_approval', $settings['vendor_require_approval'])) class="mt-1 h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span>
                                <span class="block text-sm font-bold text-gray-700 dark:text-gray-300">Require vendor approval</span>
                                <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">When enabled, newly registered vendors must be approved by an admin before they can access seller workflows.</span>
                            </span>
                        </label>
                    </div>
                </x-ui.card>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
