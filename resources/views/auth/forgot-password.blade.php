<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-primary-600 dark:bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - {{ config('app.name', 'Marketplace') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-gray-900 dark:text-gray-100 bg-primary-600 dark:bg-gray-950 relative">
    <!-- Background Decor -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none flex justify-center items-center">
        <div class="absolute w-[600px] h-[600px] bg-white/10 dark:bg-primary-500/10 rounded-full blur-[100px] -top-32 -left-32"></div>
        <div class="absolute w-[500px] h-[500px] bg-accent-500/20 dark:bg-accent-500/10 rounded-full blur-[80px] bottom-0 right-0 transform translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="min-h-full flex flex-col items-center justify-center px-4 py-12 relative z-10">
        <!-- Logo -->
        <a href="{{ route('storefront.home') }}" class="flex items-center gap-3 mb-8 group h-12">
            @php $appLogo = App\Models\AppSetting::resolvedMarketplaceSettings()['marketplace.logo'] ?? null; @endphp
            @if($appLogo)
                <img src="{{ Storage::url($appLogo) }}" alt="{{ config('app.name') }}" class="h-full object-contain filter drop-shadow-lg group-hover:scale-105 transition-transform">
            @else
                <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400 font-black text-2xl shadow-lg group-hover:scale-105 group-hover:rotate-3 transition-transform">
                    {{ substr(config('app.name'), 0, 1) }}
                </div>
                <span class="text-3xl font-black text-white tracking-tight">{{ config('app.name') }}</span>
            @endif
        </a>

        <!-- Forgot Password Card -->
        <div class="w-full max-w-[420px] bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-8 pb-0 text-center">
                <div class="w-16 h-16 bg-primary-50 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Forgot Password?</h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Enter your email and we'll send you a reset link</p>
            </div>

            <div class="p-8">
                @if (session('status'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-4">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 text-green-500 dark:text-green-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm font-medium text-green-700 dark:text-green-400">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-4">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                            </svg>
                            <div class="text-sm font-medium text-red-700 dark:text-red-400 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="you@example.com">
                    </div>

                    <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white rounded-xl font-bold shadow-lg shadow-primary-500/30 dark:shadow-primary-500/20 hover:shadow-xl hover:-translate-y-0.5 transition-all text-base">
                        Send Reset Link
                    </button>
                </form>

                <p class="mt-6 text-center text-sm font-medium text-gray-600 dark:text-gray-400">
                    Remember your password? <a href="{{ route('login') }}" class="font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
