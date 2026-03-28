<x-layouts.customer>
    <x-ui.card title="Addresses">
        <form action="{{ route('customer.addresses.store') }}" method="POST" class="mb-8 grid gap-4 sm:grid-cols-2">
            @csrf
            <input name="label" placeholder="Label" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300">
            <input name="full_name" placeholder="Full name" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
            <input name="phone" placeholder="Phone" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
            <input name="city" placeholder="City" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
            <input name="state" placeholder="State" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
            <input name="postal_code" placeholder="Postal code" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300">
            <input name="country" placeholder="Country" value="Laos" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
            <textarea name="street_address" placeholder="Street address" class="sm:col-span-2 w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300 min-h-[100px]" required></textarea>
            
            <label class="sm:col-span-2 inline-flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer group">
                <input type="checkbox" name="is_default" value="1" class="w-5 h-5 rounded-md border-gray-300 text-primary-600 focus:ring-primary-500 bg-gray-50 dark:bg-gray-800 dark:border-gray-600 transition-colors cursor-pointer">
                <span class="group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Set as default</span>
            </label>
            <x-ui.button type="submit" class="sm:col-span-2 w-full mt-2">Add Address</x-ui.button>
        </form>

        <div class="space-y-4 text-sm mt-8 border-t border-gray-100 dark:border-gray-800 pt-6">
            @forelse ($addresses as $address)
                <div x-data="{ editing: false }" class="rounded-2xl border border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 px-5 py-4 hover:shadow-sm hover:border-gray-200 dark:hover:border-gray-700 transition-all">
                    <!-- Display mode -->
                    <div x-show="!editing" class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <p class="font-bold text-gray-900 dark:text-white text-base">{{ $address->label }}</p>
                                @if($address->is_default)
                                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-800">
                                        Default
                                    </span>
                                @endif
                            </div>
                            <p class="font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $address->full_name }} <span class="text-gray-400 mx-1">&bull;</span> {{ $address->phone }}</p>
                            <p class="text-gray-500 dark:text-gray-400 leading-relaxed">{{ $address->street_address }}, {{ $address->city }}, {{ $address->state }}, {{ $address->country }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <button @click="editing = true" class="text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 bg-white dark:bg-gray-800 hover:bg-primary-50 dark:hover:bg-primary-900/20 p-2 rounded-xl border border-gray-200 dark:border-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <form action="{{ route('customer.addresses.destroy', $address) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/40 p-2 rounded-xl transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Edit mode -->
                    <form x-show="editing" x-cloak action="{{ route('customer.addresses.update', $address) }}" method="POST" class="grid gap-3 sm:grid-cols-2">
                        @csrf
                        @method('PUT')
                        <input name="label" value="{{ $address->label }}" placeholder="Label" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300">
                        <input name="full_name" value="{{ $address->full_name }}" placeholder="Full name" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300" required>
                        <input name="phone" value="{{ $address->phone }}" placeholder="Phone" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300" required>
                        <input name="city" value="{{ $address->city }}" placeholder="City" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300" required>
                        <input name="state" value="{{ $address->state }}" placeholder="State" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300" required>
                        <input name="postal_code" value="{{ $address->postal_code }}" placeholder="Postal code" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300">
                        <input name="country" value="{{ $address->country }}" placeholder="Country" class="w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300" required>
                        <textarea name="street_address" placeholder="Street address" class="sm:col-span-2 w-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2 px-3 rounded-lg outline-none focus:ring-2 focus:ring-primary-500 transition-all dark:text-gray-300 min-h-[80px]" required>{{ $address->street_address }}</textarea>
                        
                        <label class="sm:col-span-2 inline-flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer mb-2">
                            <input type="checkbox" name="is_default" value="1" @checked($address->is_default) class="w-4 h-4 rounded-md border-gray-300 text-primary-600 focus:ring-primary-500 bg-white dark:bg-gray-800 dark:border-gray-600 transition-colors">
                            <span>Set as default</span>
                        </label>
                        
                        <div class="sm:col-span-2 flex gap-2">
                            <x-ui.button type="submit" class="flex-1">Save Changes</x-ui.button>
                            <button type="button" @click="editing = false" class="flex-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold rounded-xl py-2.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="py-12 text-center text-gray-500 dark:text-gray-400">
                    <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-4 shadow-inner">
                        <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <p class="font-medium text-gray-900 dark:text-gray-100 mb-2">No addresses saved.</p>
                </div>
            @endforelse
        </div>
    </x-ui.card>
</x-layouts.customer>
