<x-layouts.app>
    <div class="max-w-[1200px] mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100 dark:border-gray-800">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Checkout</h1>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50 px-3 py-1.5 rounded-lg border border-gray-100 dark:border-gray-700 shadow-sm">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                SSL Secure
            </div>
        </div>

        <form action="{{ route('storefront.checkout.store') }}" method="POST" id="checkout-form" class="hidden">
            @csrf
        </form>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left: Forms -->
            <div class="flex-1 space-y-6">
                <!-- Shipping Information -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                        <span class="bg-primary-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-black shadow-sm">1</span>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Shipping Information</h2>
                    </div>
                    <div class="p-6" x-data="{
                        useExisting: {{ $addresses->isNotEmpty() ? 'true' : 'false' }},
                        selectedAddressId: '{{ $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()?->id ?? '' }}',
                        addresses: {{ Js::from($addresses) }},
                        get selectedAddress() {
                            return this.addresses.find(a => a.id == this.selectedAddressId) || null;
                        }
                    }">
                        @if ($addresses->isNotEmpty())
                            <!-- Address Selection Toggle -->
                            <div class="flex gap-3 mb-6">
                                <button type="button" @click="useExisting = true" :class="useExisting ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'" class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 text-sm font-bold transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Saved Address
                                </button>
                                <button type="button" @click="useExisting = false; selectedAddressId = ''" :class="!useExisting ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500 text-primary-700 dark:text-primary-400' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400'" class="flex-1 flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 text-sm font-bold transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    New Address
                                </button>
                            </div>

                            <!-- Saved Addresses -->
                            <div x-show="useExisting" x-transition class="space-y-3 mb-6">
                                <input type="hidden" form="checkout-form" name="address_id" :value="selectedAddressId" x-bind:disabled="!useExisting">
                                @foreach ($addresses as $address)
                                    <label @click="selectedAddressId = '{{ $address->id }}'" :class="selectedAddressId == '{{ $address->id }}' ? 'border-primary-500 bg-primary-50/50 dark:bg-primary-900/10 ring-2 ring-primary-500/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'" class="block p-4 rounded-xl border-2 cursor-pointer transition-all">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white text-sm flex items-center gap-2">
                                                    {{ $address->full_name }}
                                                    @if ($address->is_default)
                                                        <span class="bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Default</span>
                                                    @endif
                                                    @if ($address->label)
                                                        <span class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">{{ $address->label }}</span>
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $address->street_address }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}, {{ $address->country }}</p>
                                                @if ($address->phone)
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $address->phone }}</p>
                                                @endif
                                            </div>
                                            <div :class="selectedAddressId == '{{ $address->id }}' ? 'bg-primary-600 border-primary-600' : 'border-gray-300 dark:border-gray-600'" class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 mt-0.5 transition-colors">
                                                <svg x-show="selectedAddressId == '{{ $address->id }}'" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endif

                        <!-- Manual Address Form -->
                        <div x-show="!useExisting" x-transition>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Full Name</label>
                                    <input form="checkout-form" type="text" name="shipping_name" value="{{ old('shipping_name', auth()->user()?->name) }}" :required="!useExisting"
                                           class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="John Doe">
                                    @error('shipping_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Phone Number</label>
                                    <input form="checkout-form" type="tel" name="shipping_phone" value="{{ old('shipping_phone') }}" :required="!useExisting"
                                           class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="+1 (555) 000-0000">
                                    @error('shipping_phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Street Address</label>
                                    <textarea form="checkout-form" name="shipping_address" rows="2" :required="!useExisting"
                                              class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-none" placeholder="123 Main St, Apt 4B">{{ old('shipping_address') }}</textarea>
                                    @error('shipping_address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">City</label>
                                    <input form="checkout-form" type="text" name="shipping_city" value="{{ old('shipping_city') }}" :required="!useExisting"
                                           class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="New York">
                                    @error('shipping_city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">State / Province</label>
                                    <input form="checkout-form" type="text" name="shipping_state" value="{{ old('shipping_state') }}" :required="!useExisting"
                                           class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="NY">
                                    @error('shipping_state') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Postal / Zip Code</label>
                                    <input form="checkout-form" type="text" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}"
                                           class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="10001">
                                    @error('shipping_postal_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Country</label>
                                    <input form="checkout-form" type="text" name="shipping_country" value="{{ old('shipping_country', 'United States') }}" :required="!useExisting"
                                           class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                                    @error('shipping_country') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                            <input form="checkout-form" type="email" name="customer_email" value="{{ old('customer_email', auth()->user()?->email) }}" required
                                   class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500" placeholder="you@example.com">
                            @error('customer_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 font-medium tracking-wide">In case we need to contact you about your order.</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                        <span class="bg-primary-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-black shadow-sm">2</span>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Payment Method</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 dark:has-[:checked]:bg-primary-900/10 dark:has-[:checked]:border-primary-500 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                            <input form="checkout-form" type="radio" name="payment_method" value="card" class="w-4 h-4 text-primary-600 focus:ring-primary-600 dark:bg-gray-800" checked>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">Credit Card</span>
                                    <div class="flex gap-1">
                                        <div class="w-8 h-5 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                            <span class="text-[8px] font-black text-[#1a1f71]">VISA</span>
                                        </div>
                                        <div class="w-8 h-5 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                            <div class="flex"><div class="w-3 h-3 bg-[#eb001b] rounded-full mix-blend-multiply opacity-80"></div><div class="w-3 h-3 bg-[#f79e1b] rounded-full -ml-1 mix-blend-multiply opacity-80"></div></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pay securely with your Visa or Mastercard</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 dark:has-[:checked]:bg-primary-900/10 dark:has-[:checked]:border-primary-500 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                            <input form="checkout-form" type="radio" name="payment_method" value="cod" class="w-4 h-4 text-primary-600 focus:ring-primary-600 dark:bg-gray-800">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">Cash on Delivery</span>
                                    <div class="w-8 h-5 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center text-[10px] font-bold text-gray-600 dark:text-gray-400">
                                        COD
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pay with cash when your order is delivered</p>
                            </div>
                        </label>
                        
                        <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 dark:has-[:checked]:bg-primary-900/10 dark:has-[:checked]:border-primary-500 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                            <input form="checkout-form" type="radio" name="payment_method" value="transfer" class="w-4 h-4 text-primary-600 focus:ring-primary-600 dark:bg-gray-800">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">Bank Transfer</span>
                                    <div class="w-8 h-5 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Instructions will be provided after checkout</p>
                            </div>
                        </label>

                        @error('payment_method') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                        <span class="bg-primary-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-black shadow-sm">3</span>
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">Shipping Method</h2>
                    </div>
                    <div class="p-6 space-y-3" x-data="{ selectedShipping: 'standard' }">
                        @foreach ($shippingMethods as $key => $method)
                            <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50 dark:has-[:checked]:bg-primary-900/10 dark:has-[:checked]:border-primary-500 hover:border-gray-300 dark:hover:border-gray-600 transition-all shadow-sm">
                                <input form="checkout-form" type="radio" name="shipping_method" value="{{ $key }}" class="w-4 h-4 text-primary-600 focus:ring-primary-600 dark:bg-gray-800" @checked($key === 'standard') @click="selectedShipping = '{{ $key }}'">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $method['label'] }}</span>
                                        <span class="text-sm font-black text-primary-600 dark:text-primary-400">${{ number_format($method['fee'], 2) }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Estimated {{ $method['days'] }} business days</p>
                                </div>
                            </label>
                        @endforeach
                        @error('shipping_method') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm p-6">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Order Notes (Optional)</label>
                    <textarea form="checkout-form" name="notes" rows="3"
                              class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-none" placeholder="Notes about your order, e.g. special notes for delivery.">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="lg:w-[380px] flex-shrink-0">
                <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm sticky top-24 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                             <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Order Summary
                        </h2>
                    </div>

                    <div class="p-6">
                        <!-- Items -->
                        <div class="max-h-60 overflow-y-auto mb-6 pb-6 border-b border-gray-100 dark:border-gray-800 space-y-4 pr-1">
                            @foreach ($cart->items as $item)
                                <div class="flex gap-4">
                                    <div class="w-16 h-16 flex-shrink-0 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 relative">
                                        @php
                                            $imageSrc = $item->product->primary_image
                                                ? (str_starts_with($item->product->primary_image, 'http') ? $item->product->primary_image : asset('storage/'.$item->product->primary_image))
                                                : 'https://placehold.co/100x100/f1f5f9/64748b?text='.urlencode(Str::limit($item->product->name, 8));
                                        @endphp
                                        <img src="{{ $imageSrc }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='https://placehold.co/100x100/f1f5f9/64748b?text={{ urlencode(Str::limit($item->product->name, 8)) }}';">
                                        <!-- Quantity Badge -->
                                        <span class="absolute -top-1.5 -right-1.5 bg-gray-900 dark:bg-gray-600 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white dark:border-gray-800 shadow-sm z-10">{{ $item->quantity }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0 py-0.5">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 leading-tight mb-1">{{ $item->product->name }}</p>
                                        <p class="text-sm font-bold text-gray-900 dark:text-gray-300">${{ number_format($item->unit_price, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <dl class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between items-center">
                                <dt class="font-medium">Subtotal ({{ $cart->items->sum('quantity') }} items)</dt>
                                <dd class="font-bold text-gray-900 dark:text-white">${{ number_format($totals['subtotal'], 2) }}</dd>
                            </div>

                            <div class="flex justify-between items-center">
                                <dt class="font-medium flex items-center gap-1">
                                    Shipping fee
                                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </dt>
                                <dd class="font-bold text-gray-900 dark:text-white">${{ number_format($totals['shipping_fee'], 2) }}</dd>
                            </div>

                            @if ($totals['discount_amount'] > 0)
                                <div class="flex justify-between items-center text-accent-600 dark:text-accent-400">
                                    <dt class="font-medium flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        Discount
                                        @if ($totals['coupon_code'])
                                            <span class="bg-accent-100 dark:bg-accent-900/30 text-accent-700 dark:text-accent-400 text-[10px] font-bold px-1.5 py-0.5 rounded-md uppercase">{{ $totals['coupon_code'] }}</span>
                                        @endif
                                    </dt>
                                    <dd class="font-bold">-${{ number_format($totals['discount_amount'], 2) }}</dd>
                                </div>
                            @endif

                            <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                                <div class="flex justify-between items-end">
                                    <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                    <dd class="text-2xl font-black text-primary-600 dark:text-primary-400 leading-none">${{ number_format($totals['total'], 2) }}</dd>
                                </div>
                            </div>
                        </dl>

                        <!-- Submit Button -->
                        <button type="submit" form="checkout-form" class="mt-8 w-full bg-primary-600 text-white text-center font-bold py-3.5 rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:-translate-y-0.5 hover:bg-primary-700 transition-all text-sm uppercase tracking-wider">
                            Place Order Complete
                        </button>
                        
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-4 leading-relaxed font-medium">
                            By placing your order, you agree to our <a href="#" class="text-primary-600 dark:text-primary-400 hover:underline">Terms of Service</a> and <a href="#" class="text-primary-600 dark:text-primary-400 hover:underline">Privacy Policy</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
</x-layouts.app>
