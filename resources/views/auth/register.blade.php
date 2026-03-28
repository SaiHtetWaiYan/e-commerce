<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-primary-600 dark:bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - {{ config('app.name', 'Marketplace') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-gray-900 dark:text-gray-100 relative">
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

        <!-- Register Card -->
        <div class="w-full max-w-[420px] bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-8 pb-0 text-center">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Create an account</h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Join {{ config('app.name') }} to start shopping</p>
            </div>

            <div class="p-8">
                <!-- Social Logins -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <a href="{{ route('auth.social.redirect', 'google') }}" class="flex items-center justify-center gap-2 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <svg class="h-4 w-4" viewBox="0 0 24 24">
                            <path d="M12.0003 4.75C13.7703 4.75 15.3553 5.36002 16.6053 6.54998L20.0303 3.125C17.9502 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.28027 6.60998L5.27028 9.70498C6.21525 6.86002 8.87028 4.75 12.0003 4.75Z" fill="#EA4335"/>
                            <path d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L19.945 21.1C22.2 19.01 23.49 15.92 23.49 12.275Z" fill="#4285F4"/>
                            <path d="M5.26498 14.2949C5.02498 13.5699 4.88501 12.7999 4.88501 11.9999C4.88501 11.1999 5.01998 10.4299 5.26498 9.7049L1.275 6.60986C0.46 8.22986 0 10.0599 0 11.9999C0 13.9399 0.46 15.7699 1.28 17.3899L5.26498 14.2949Z" fill="#FBBC05"/>
                            <path d="M12.0004 24.0001C15.2404 24.0001 17.9654 22.935 19.9454 21.095L16.0804 18.095C15.0054 18.82 13.6204 19.245 12.0004 19.245C8.8704 19.245 6.21537 17.135 5.2654 14.29L1.27539 17.385C3.25539 21.31 7.3104 24.0001 12.0004 24.0001Z" fill="#34A853"/>
                        </svg>
                        Google
                    </a>
                    <a href="{{ route('auth.social.redirect', 'github') }}" class="flex items-center justify-center gap-2 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <svg class="h-4 w-4 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                        </svg>
                        GitHub
                    </a>
                </div>

                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200 dark:border-gray-700"></div></div>
                    <div class="relative flex justify-center text-sm"><span class="bg-white dark:bg-gray-800 px-3 text-gray-500 dark:text-gray-400 font-medium">Or create account with email</span></div>
                </div>
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

                <form action="{{ route('register.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="Your full name">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="you@example.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="Minimum 8 characters">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="Re-enter your password">
                    </div>

                    <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white rounded-xl font-bold shadow-lg shadow-primary-500/30 dark:shadow-primary-500/20 hover:shadow-xl hover:-translate-y-0.5 transition-all text-base mt-2">
                        Create Account
                    </button>
                </form>

                <p class="mt-6 text-center text-sm font-medium text-gray-600 dark:text-gray-400">
                    Already have an account? <a href="{{ route('login') }}" class="font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">Sign in</a>
                </p>

                <div class="mt-4 text-center">
                    <a href="{{ route('vendor.register') }}" class="text-xs font-bold text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Want to sell on {{ config('app.name') }}? Register as a vendor</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
