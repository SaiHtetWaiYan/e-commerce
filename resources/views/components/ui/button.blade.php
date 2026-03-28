@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center rounded-xl font-bold transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 shadow-sm hover:-translate-y-0.5 hover:shadow-md';
    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-600 dark:focus:ring-offset-gray-900',
        'secondary' => 'bg-gray-100 text-gray-900 hover:bg-gray-200 focus:ring-gray-500 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-900',
        'outline' => 'border-2 border-primary-600 bg-white dark:bg-transparent text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 focus:ring-primary-600 dark:focus:ring-offset-gray-900',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-600 dark:focus:ring-offset-gray-900',
    ];
    $sizes = [
        'sm' => 'h-9 px-4 text-xs',
        'md' => 'h-11 px-5 text-sm',
        'lg' => 'h-14 px-8 text-base',
    ];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base.' '.$variants[$variant].' '.$sizes[$size]]) }}>
    {{ $slot }}
</button>
