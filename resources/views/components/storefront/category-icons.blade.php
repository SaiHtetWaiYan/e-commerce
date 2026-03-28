@props(['categories'])

@if ($categories->isNotEmpty())
<section class="p-4">
    <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-8 lg:grid-cols-10 gap-3">
        @foreach ($categories as $category)
            <a href="{{ route('storefront.categories.show', $category->slug) }}" class="flex flex-col items-center gap-1.5 group text-center">
                <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center group-hover:bg-primary-100 dark:group-hover:bg-primary-800/40 transition-colors overflow-hidden ring-1 ring-primary-100 dark:ring-primary-800/50 group-hover:ring-primary-200 dark:group-hover:ring-primary-700">
                    @if ($category->icon)
                        <span class="text-2xl">{{ $category->icon }}</span>
                    @elseif ($category->image && str_starts_with($category->image, 'http'))
                        <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
                    @endif
                </div>
                <span class="text-[11px] text-gray-600 dark:text-gray-400 leading-tight line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors font-medium">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>
@endif
