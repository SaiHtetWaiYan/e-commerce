<x-layouts.vendor>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $campaign->name }}</h1>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $campaign->starts_at->format('M d, Y') }} - {{ $campaign->ends_at->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('vendor.campaigns.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-bold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Back to Campaigns
        </a>
    </div>

    @if ($campaign->banner_image)
        <div class="w-full h-48 md:h-64 lg:h-80 bg-gray-100 dark:bg-gray-800 rounded-3xl overflow-hidden mb-8 shadow-sm">
            <img src="{{ Storage::url($campaign->banner_image) }}" alt="{{ $campaign->name }}" class="w-full h-full object-cover">
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Campaign Details -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-black text-gray-900 dark:text-white mb-4">About Campaign</h3>
                
                @if ($campaign->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">{{ $campaign->description }}</p>
                @endif
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700/50">
                        <span class="text-sm font-bold text-gray-500 dark:text-gray-400">Discount Type</span>
                        <span class="text-sm font-black text-gray-900 dark:text-white">{{ $campaign->discount_type->label() }}</span>
                    </div>

                    @if ($campaign->discount_type->value !== 'custom')
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400">Discount Value</span>
                            <span class="text-sm font-black text-primary-600 dark:text-primary-400">
                                @if ($campaign->discount_type->value === 'percentage')
                                    {{ (int) $campaign->discount_value }}% OFF
                                @else
                                    ${{ number_format($campaign->discount_value, 2) }} OFF
                                @endif
                            </span>
                        </div>
                    @endif

                    @if ($campaign->max_discount_amount)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400">Max Discount</span>
                            <span class="text-sm font-black text-gray-900 dark:text-white">${{ number_format($campaign->max_discount_amount, 2) }}</span>
                        </div>
                    @endif

                    @if ($campaign->badge_text)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700/50">
                            <span class="text-sm font-bold text-gray-500 dark:text-gray-400">Campaign Badge</span>
                            <span class="text-xs font-bold text-white px-2 py-0.5 rounded" style="background-color: {{ $campaign->badge_color ?? '#f97316' }}">
                                {{ $campaign->badge_text }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Enrollment Area -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Currently Enrolled Products -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Enrolled Products (<span class="text-primary-600">{{ $enrolledProducts->count() }}</span>)</h3>
                </div>
                
                <div class="p-6">
                    @if ($enrolledProducts->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">You haven't enrolled any products in this campaign yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($enrolledProducts as $product)
                                <div class="flex items-center gap-4 p-4 border border-gray-100 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                    <div class="w-16 h-16 rounded-lg bg-gray-200 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                        @if ($product->primary_image)
                                            <img src="{{ str_starts_with($product->primary_image, 'http') ? $product->primary_image : Storage::url($product->primary_image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $product->name }}</h4>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->base_price, 2) }}</span>
                                            <span class="text-sm font-black text-primary-600 dark:text-primary-400">
                                                ${{ number_format($campaign->getCampaignPriceForEnrolledProduct($product), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <form action="{{ route('vendor.campaigns.withdraw', [$campaign, $product]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-bold text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" onclick="return confirm('Are you sure you want to withdraw this product?');">
                                            Withdraw
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Enroll Products -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden mt-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Available to Enroll</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select products to join this campaign.</p>
                </div>

                <div class="p-6">
                    @if ($availableProducts->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">You don't have any eligible products to enroll.</p>
                        </div>
                    @else
                        <form action="{{ route('vendor.campaigns.enroll', $campaign) }}" method="POST" id="enroll-form">
                            @csrf
                            <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 mb-6">
                                @foreach ($availableProducts as $product)
                                    <label class="flex items-start gap-4 p-4 border border-gray-100 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer transition-colors relative">
                                        <div class="flex items-center h-6">
                                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700">
                                        </div>
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                            @if ($product->primary_image)
                                                <img src="{{ str_starts_with($product->primary_image, 'http') ? $product->primary_image : Storage::url($product->primary_image) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $product->name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Base Price: ${{ number_format($product->base_price, 2) }}</p>
                                        </div>
                                        
                                        @if ($campaign->discount_type->value === 'custom')
                                            <div class="w-32">
                                                <label class="sr-only">Custom Price</label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-gray-500 sm:text-sm">$</span>
                                                    </div>
                                                    <input type="number" step="0.01" name="custom_prices[{{ $product->id }}]" placeholder="Sale Price" class="pl-7 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:ring-primary-500 focus:border-primary-500 text-gray-900 dark:text-white">
                                                </div>
                                            </div>
                                        @endif
                                    </label>
                                @endforeach
                            </div>

                            <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-colors shadow-sm text-sm">
                                Enroll Selected Products
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</x-layouts.vendor>
