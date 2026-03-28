<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Marketplace') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Prevent dark mode flash -->
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body x-data class="min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 font-sans antialiased flex flex-col transition-colors duration-300">
    <!-- Announcement Bar -->
    <div class="bg-gradient-to-r from-primary-700 via-primary-600 to-primary-700 dark:from-primary-900 dark:via-primary-800 dark:to-primary-900 text-white text-xs font-medium py-1.5 px-4 text-center">
        Free shipping on all orders over $50! <a href="{{ route('storefront.products.index') }}" class="underline hover:text-primary-200 ml-1 font-bold">Shop now</a>
    </div>

    <!-- Main Navigation -->
    <x-storefront.navbar />

    <!-- Flash Messages -->
    <div class="max-w-[1200px] mx-auto w-full px-4 mt-4">
        @if (session('status'))
            <div class="mb-3 rounded-xl border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/30 p-3 text-sm text-green-700 dark:text-green-300 flex items-center shadow-sm">
                <svg class="h-4 w-4 mr-2 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-3 rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/30 p-3 text-sm text-red-700 dark:text-red-300 shadow-sm">
                <div class="flex items-center mb-1.5 font-medium">
                    <svg class="h-4 w-4 mr-2 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    There were some problems with your input.
                </div>
                <ul class="list-disc pl-7 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow w-full pb-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-storefront.footer />
</body>
</html>
