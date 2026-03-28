@props([
    'products',
    'campaign' => null,
])

@if ($products->isNotEmpty())
<section x-data="flashSaleTimer(@js($campaign?->ends_at?->toIso8601String()))">
    <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-primary-700 via-primary-600 to-primary-700 dark:from-primary-900 dark:via-primary-800 dark:to-primary-900">
        <div class="flex items-center gap-3">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                {{ $campaign?->name ?? 'Flash Sale' }}
            </h2>
            <div class="flex items-center gap-1">
                <div class="bg-white dark:bg-gray-900 text-primary-600 dark:text-primary-400 font-bold text-xs px-2 py-1 rounded-lg shadow-sm" x-text="hours.toString().padStart(2, '0')">00</div>
                <span class="text-white font-bold">:</span>
                <div class="bg-white dark:bg-gray-900 text-primary-600 dark:text-primary-400 font-bold text-xs px-2 py-1 rounded-lg shadow-sm" x-text="minutes.toString().padStart(2, '0')">00</div>
                <span class="text-white font-bold">:</span>
                <div class="bg-white dark:bg-gray-900 text-primary-600 dark:text-primary-400 font-bold text-xs px-2 py-1 rounded-lg shadow-sm" x-text="seconds.toString().padStart(2, '0')">00</div>
            </div>
        </div>
        <a
            href="{{ $campaign ? route('storefront.campaigns.show', $campaign->slug) : route('storefront.products.index') }}"
            class="text-white text-sm font-bold hover:text-primary-200 transition-colors flex items-center gap-1"
        >
            Shop All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <div class="overflow-x-auto scrollbar-hide">
        <div class="flex gap-3 p-4" style="min-width: max-content;">
            @foreach ($products as $product)
                @php
                    $imageSrc = $product->primary_image
                        ? (str_starts_with($product->primary_image, 'http') || str_starts_with($product->primary_image, '/storage') ? $product->primary_image : asset('storage/'.$product->primary_image))
                        : 'https://placehold.co/200x200/f1f5f9/64748b?text='.urlencode($product->name);

                    $salePrice = $campaign !== null
                        ? $campaign->getCampaignPriceForEnrolledProduct($product)
                        : $product->getEffectivePrice();

                    $fallbackOriginalPrice = $product->compare_price && $product->compare_price > $product->base_price
                        ? (float) $product->compare_price
                        : (float) $product->base_price;

                    $originalPrice = $campaign !== null
                        ? (float) $product->base_price
                        : $fallbackOriginalPrice;

                    $hasDiscount = $originalPrice > $salePrice;
                    $discountPercent = $hasDiscount
                        ? round((($originalPrice - $salePrice) / $originalPrice) * 100)
                        : 0;
                @endphp
                <a href="{{ route('storefront.products.show', $product->slug) }}" class="flex-shrink-0 w-[140px] group">
                    <div class="relative aspect-square bg-gray-50 dark:bg-gray-800 overflow-hidden rounded-xl mb-2">
                        <img src="{{ $imageSrc }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                        @if ($hasDiscount)
                            <div class="absolute top-1.5 right-1.5 bg-accent-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                                -{{ $discountPercent }}%
                            </div>
                        @endif
                    </div>
                    @if ($hasDiscount)
                        <p class="text-gray-400 dark:text-gray-500 text-xs line-through">${{ number_format($originalPrice, 2) }}</p>
                    @endif
                    <p class="text-primary-600 dark:text-primary-400 font-black text-sm">${{ number_format($salePrice, 2) }}</p>
                    <div class="w-full bg-primary-100 dark:bg-primary-900/40 rounded-full h-1.5 mt-1.5">
                        <div class="bg-primary-600 dark:bg-primary-500 h-1.5 rounded-full transition-all" style="width: {{ min(($product->total_sold ?? 0) / max(($product->stock_quantity + ($product->total_sold ?? 0)), 1) * 100, 100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-500 dark:text-gray-400 mt-0.5 font-medium">{{ $product->total_sold ?? 0 }} sold</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<script>
    function flashSaleTimer(campaignEndsAt = null) {
        return {
            hours: 0,
            minutes: 0,
            seconds: 0,
            endTime: null,
            init() {
                this.endTime = this.resolveEndTime(campaignEndsAt);
                this.updateTimer();
                setInterval(() => this.updateTimer(), 1000);
            },
            resolveEndTime(campaignEnd) {
                if (campaignEnd) {
                    const parsed = new Date(campaignEnd);
                    if (! Number.isNaN(parsed.getTime())) {
                        return parsed;
                    }
                }

                const endOfDay = new Date();
                endOfDay.setHours(23, 59, 59, 999);
                return endOfDay;
            },
            updateTimer() {
                if (! campaignEndsAt && this.endTime <= new Date()) {
                    this.endTime = this.resolveEndTime(null);
                }

                const now = new Date();
                const diff = Math.max(0, Math.floor((this.endTime - now) / 1000));

                this.hours = Math.floor(diff / 3600);
                this.minutes = Math.floor((diff % 3600) / 60);
                this.seconds = diff % 60;
            }
        };
    }
</script>
@endif
