@props(['title' => null])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 shadow-sm']) }}>
    @if ($title)
        <h3 class="mb-5 text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $title }}</h3>
    @endif

    {{ $slot }}
</div>
