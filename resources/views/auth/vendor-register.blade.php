<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-primary-600 dark:bg-gray-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sell on {{ config('app.name', 'Marketplace') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased text-gray-900 dark:text-gray-100 relative">
    <!-- Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none flex justify-center items-center">
        <div class="absolute w-[600px] h-[600px] bg-white/10 dark:bg-primary-500/10 rounded-full blur-[100px] -top-32 -left-32"></div>
        <div class="absolute w-[500px] h-[500px] bg-accent-500/20 dark:bg-accent-500/10 rounded-full blur-[80px] bottom-0 right-0 transform translate-x-1/2 translate-y-1/2"></div>
    </div>

    <div class="min-h-full flex flex-col items-center py-12 px-4 relative z-10">
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

        <!-- Vendor Register Card -->
        <div class="w-full max-w-[480px] bg-white dark:bg-gray-800 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-12">
            <div class="p-8 pb-0 text-center">
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Start selling on {{ config('app.name') }}</h2>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Create a vendor account to open your online store</p>
            </div>

            <div class="p-8">
                <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                    <div class="flex gap-3">
                        <svg class="h-5 w-5 text-blue-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Your vendor account will be reviewed by our team. You can start listing products once approved.</p>
                    </div>
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

                <form action="{{ route('vendor.register.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-2">Account Information</p>

                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="Your full name">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="you@example.com">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Phone number <span class="text-gray-400">(optional)</span></label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="+1 (555) 000-0000">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="Minimum 8 characters">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="Re-enter your password">
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-6 mt-6">
                        <p class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Store Information</p>
                    </div>

                    <div>
                        <label for="store_name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Store name</label>
                        <input id="store_name" name="store_name" type="text" value="{{ old('store_name') }}" required class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all" placeholder="Your store name">
                    </div>

                    <div>
                        <label for="store_description" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Store description <span class="text-gray-400">(optional)</span></label>
                        <textarea id="store_description" name="store_description" rows="4" class="block w-full border border-gray-300 dark:border-gray-600 rounded-xl py-2.5 px-4 text-gray-900 dark:text-white bg-white dark:bg-gray-900 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all resize-none" placeholder="Tell customers about your store and what you sell...">{{ old('store_description') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 dark:bg-primary-500 dark:hover:bg-primary-600 text-white rounded-xl font-bold shadow-lg shadow-primary-500/30 dark:shadow-primary-500/20 hover:shadow-xl hover:-translate-y-0.5 transition-all text-base mt-4">
                        Create Vendor Account
                    </button>
                </form>

                <p class="mt-8 text-center text-sm font-medium text-gray-600 dark:text-gray-400">
                    Already have an account? <a href="{{ route('login') }}" class="font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 transition-colors">Sign in</a>
                </p>

                <div class="mt-4 text-center">
                    <a href="{{ route('register') }}" class="text-xs font-bold text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Just want to shop? Create a customer account</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
