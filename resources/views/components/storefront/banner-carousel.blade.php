@props(['banners'])

<div x-data="bannerCarousel()" x-init="startAutoPlay()" class="relative w-full overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800">
    <div class="relative aspect-[2.5/1] md:aspect-[3/1]">
        @forelse ($banners as $index => $banner)
            <div x-show="current === {{ $index }}"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0">
                <a href="{{ $banner->link ?? '#' }}" class="block w-full h-full">
                    @php
                        $bannerSrc = $banner->image
                            ? (str_starts_with($banner->image, 'http') ? $banner->image : asset('storage/'.$banner->image))
                            : 'https://placehold.co/1200x400/7c3aed/ffffff?text='.urlencode($banner->title);
                    @endphp
                    <img src="{{ $bannerSrc }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                </a>
            </div>
        @empty
            <div class="absolute inset-0 bg-gradient-to-br from-primary-700 via-primary-600 to-accent-600 dark:from-primary-900 dark:via-primary-800 dark:to-accent-800 flex items-center justify-center">
                <div class="text-center text-white">
                    <h2 class="text-2xl md:text-4xl font-black mb-2 tracking-tight">Welcome to {{ config('app.name') }}</h2>
                    <p class="text-sm md:text-lg opacity-90 font-medium">Discover amazing deals every day</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($banners->count() > 1)
        <!-- Navigation Arrows -->
        <button @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/20 hover:bg-black/40 backdrop-blur-sm text-white rounded-full flex items-center justify-center transition-all cursor-pointer shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/20 hover:bg-black/40 backdrop-blur-sm text-white rounded-full flex items-center justify-center transition-all cursor-pointer shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>

        <!-- Dot Indicators -->
        <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex items-center gap-1.5">
            @foreach ($banners as $index => $banner)
                <button @click="goTo({{ $index }})" :class="current === {{ $index }} ? 'bg-white w-6' : 'bg-white/50 w-2'" class="h-2 rounded-full transition-all duration-300 cursor-pointer"></button>
            @endforeach
        </div>
    @endif
</div>

<script>
    function bannerCarousel() {
        return {
            current: 0,
            total: {{ $banners->count() ?: 1 }},
            interval: null,
            startAutoPlay() {
                this.interval = setInterval(() => this.next(), 5000);
            },
            next() {
                this.current = (this.current + 1) % this.total;
            },
            prev() {
                this.current = (this.current - 1 + this.total) % this.total;
            },
            goTo(index) {
                this.current = index;
                clearInterval(this.interval);
                this.startAutoPlay();
            }
        }
    }
</script>
