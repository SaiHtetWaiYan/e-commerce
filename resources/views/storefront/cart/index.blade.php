<x-layouts.app>
    <div class="max-w-[1200px] mx-auto px-4 py-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-6 bg-white dark:bg-gray-900 px-4 py-3 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm w-fit">
            <a href="{{ route('storefront.home') }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Home
            </a>
            <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 dark:text-white font-bold">Shopping Cart</span>
        </nav>

        @if ($cart->items->isNotEmpty())
            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Cart Items -->
                <div class="flex-1 min-w-0">
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                        <!-- Header Row -->
                        <div class="hidden sm:grid grid-cols-12 gap-4 px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            <div class="col-span-5">Product</div>
                            <div class="col-span-2 text-center">Price</div>
                            <div class="col-span-3 text-center">Quantity</div>
                            <div class="col-span-2 text-right">Total</div>
                        </div>

                        @php
                            $grouped = $cart->items->groupBy(fn ($item) => $item->product->vendor_id);
                        @endphp

                        @foreach ($grouped as $vendorId => $vendorItems)
                            @php $store = $vendorItems->first()->product->vendor->vendorProfile; @endphp
                            <!-- Vendor Group Header -->
                            <div class="flex items-center gap-2 px-6 py-3 bg-gray-50/80 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-800">
                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $store?->store_name ?? 'Unknown Store' }}</span>
                            </div>

                            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($vendorItems as $item)
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 items-center px-6 py-6 group hover:bg-gray-50/50 dark:hover:bg-gray-800/50 transition-colors">
                                    <!-- Product Info -->
                                    <div class="sm:col-span-5 flex items-start gap-4">
                                        <a href="{{ route('storefront.products.show', $item->product->slug) }}" class="flex-shrink-0 relative rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700 w-24 h-24">
                                            @php
                                                $imageSrc = $item->product->primary_image
                                                    ? (str_starts_with($item->product->primary_image, 'http') ? $item->product->primary_image : asset('storage/'.$item->product->primary_image))
                                                    : 'https://placehold.co/400x400/f1f5f9/64748b?text='.urlencode($item->product->name);
                                            @endphp
                                            <img src="{{ $imageSrc }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500" onerror="this.onerror=null;this.src='https://placehold.co/400x400/f1f5f9/64748b?text={{ urlencode($item->product->name) }}';">
                                        </a>
                                        <div class="flex flex-col justify-between h-full min-h-[96px] py-1">
                                            <a href="{{ route('storefront.products.show', $item->product->slug) }}" class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 hover:text-primary-600 dark:hover:text-primary-400 transition-colors leading-relaxed">
                                                {{ $item->product->name }}
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Unit Price -->
                                    <div class="sm:col-span-2 sm:text-center mt-2 sm:mt-0 flex justify-between sm:block">
                                        <span class="sm:hidden text-xs font-medium text-gray-500 uppercase tracking-wider">Price:</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">${{ number_format($item->unit_price, 2) }}</span>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="sm:col-span-3 flex items-center sm:justify-center gap-3 mt-3 sm:mt-0">
                                        <form action="{{ route('storefront.cart.update', $item) }}" method="POST" class="flex w-max shrink-0 items-center border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800 shadow-sm">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" name="action" value="decrease" class="w-8 h-8 shrink-0 flex items-center justify-center text-gray-500 hover:text-primary-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                            </button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" class="w-10 h-8 shrink-0 text-center text-sm font-semibold text-gray-900 dark:text-white bg-transparent border-x border-gray-200 dark:border-gray-700 outline-none p-0 focus:ring-0 appearance-none [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none [&::-moz-appearance]:textfield" onchange="this.form.submit()">
                                            <button type="submit" name="action" value="increase" class="w-8 h-8 shrink-0 flex items-center justify-center text-gray-500 hover:text-primary-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" {{ $item->quantity >= $item->product->stock_quantity ? 'disabled' : '' }}>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"/></svg>
                                            </button>
                                        </form>

                                        <form id="remove-form-{{ $item->id }}" action="{{ route('storefront.cart.destroy', $item) }}" method="POST" class="shrink-0" x-data="{ confirmingRemoval: false }">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" @click="confirmingRemoval = true" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors group/btn" title="Remove from cart">
                                                <svg class="w-4 h-4 group-hover/btn:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>

                                            <!-- Confirmation Modal -->
                                            <template x-teleport="body">
                                                <div x-show="confirmingRemoval" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/50 dark:bg-black/50 backdrop-blur-sm p-4" x-transition.opacity style="display: none;">
                                                    <div @click.away="confirmingRemoval = false" class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" x-transition.scale.origin.center x-show="confirmingRemoval">
                                                        <div class="p-6 text-center">
                                                            <div class="w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                                                                <svg class="w-6 h-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </div>
                                                            <h3 class="text-lg font-black text-gray-900 dark:text-white mb-2">Remove Item?</h3>
                                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-6">Are you sure you want to remove this item from your cart?</p>
                                                            <div class="flex gap-3">
                                                                <button type="button" @click="confirmingRemoval = false" class="flex-1 px-4 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700/80 text-gray-700 dark:text-gray-300 text-sm font-bold rounded-xl transition-colors">Cancel</button>
                                                                <button type="button" @click="document.getElementById('remove-form-{{ $item->id }}').submit()" class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors shadow-sm shadow-red-500/30">Remove</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </form>
                                    </div>

                                    <!-- Total -->
                                    <div class="sm:col-span-2 text-right mt-2 sm:mt-0 flex justify-between sm:block pt-3 sm:pt-0 border-t border-gray-100 dark:border-gray-800 sm:border-0 relative">
                                        <div class="sm:hidden absolute -top-3 left-0 right-0 h-px bg-gray-100 dark:bg-gray-800"></div>
                                        <span class="sm:hidden text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider items-center flex">Total:</span>
                                        <span class="text-base font-black text-primary-600 dark:text-primary-400">${{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:w-[340px] flex-shrink-0">
                    <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm sticky top-24 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
                            <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17L15 17M9 13L15 13M9 9L11 9M13 3H8.2C7.0799 3 6.51984 3 6.09202 3.21799C5.71569 3.40973 5.40973 3.71569 5.21799 4.09202C5 4.51984 5 5.0799 5 6.2V17.8C5 18.9201 5 19.4802 5.21799 19.908C5.40973 20.2843 5.71569 20.5903 6.09202 20.782C6.51984 21 7.0799 21 8.2 21H15.8C16.9201 21 17.4802 21 17.908 20.782C18.2843 20.5903 18.5903 20.2843 18.782 19.908C19 19.4802 19 18.9201 19 17.8V8L14 3H13Z"/></svg>
                                Order Summary
                            </h2>
                        </div>

                        <div class="p-6">
                            <!-- Coupon Code -->
                            <form action="{{ route('storefront.cart.coupon') }}" method="POST" class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                                @csrf
                                <label for="coupon_code" class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">Discount Code</label>
                                <div class="flex gap-2">
                                    <input type="text" id="coupon_code" name="coupon_code" class="flex-1 w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-white" placeholder="Enter code">
                                    <button type="submit" class="bg-gray-900 dark:bg-gray-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors shadow-sm">
                                        Apply
                                    </button>
                                </div>
                            </form>

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
                                        </dt>
                                        <dd class="font-bold">-${{ number_format($totals['discount_amount'], 2) }}</dd>
                                    </div>
                                @endif
                                <div class="pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
                                    <div class="flex justify-between items-end">
                                        <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                                        <dd class="text-2xl font-black text-primary-600 dark:text-primary-400 leading-none">${{ number_format($totals['total'], 2) }}</dd>
                                    </div>
                                    <p class="text-[10px] text-gray-500 text-right mt-1 font-medium">VAT included, where applicable</p>
                                </div>
                            </dl>

                            <a href="{{ route('storefront.checkout.index') }}" class="mt-8 block w-full bg-primary-600 text-white text-center font-bold py-3.5 rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-xl hover:-translate-y-0.5 hover:bg-primary-700 transition-all text-sm uppercase tracking-wider">
                                Proceed to Checkout
                            </a>
                            
                            <!-- Payment Methods Icons -->
                            <div class="mt-6 flex items-center justify-center gap-2">
                                <div class="w-8 h-5 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                    <span class="text-[8px] font-black text-[#1a1f71]">VISA</span>
                                </div>
                                <div class="w-8 h-5 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                                    <div class="flex"><div class="w-3 h-3 bg-[#eb001b] rounded-full mix-blend-multiply opacity-80"></div><div class="w-3 h-3 bg-[#f79e1b] rounded-full -ml-1 mix-blend-multiply opacity-80"></div></div>
                                </div>
                                <div class="w-8 h-5 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 flex items-center justify-center text-[8px] font-black italic text-[#003087]">
                                    Pay<span class="text-[#0079C1]">Pal</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm py-20 px-6 text-center">
                <div class="max-w-sm mx-auto">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-primary-100 to-accent-100 dark:from-primary-900/30 dark:to-accent-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-primary-500 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white mb-2">Your cart is empty</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-8 leading-relaxed">Looks like you haven't added anything yet. Discover amazing products and great deals!</p>
                    <a href="{{ route('storefront.products.index') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-all hover:-translate-y-0.5 shadow-lg shadow-primary-500/30 hover:shadow-xl uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        Start Shopping
                    </a>
                </div>

                <!-- Quick Links -->
                <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Popular Categories</p>
                    <div class="flex flex-wrap items-center justify-center gap-2">
                        @foreach ($popularCategories as $cat)
                            <a href="{{ route('storefront.products.index', ['category' => $cat->slug]) }}" class="px-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-xs font-bold text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-200 dark:hover:border-primary-800 transition-all">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
