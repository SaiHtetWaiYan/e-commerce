<x-layouts.app>
    <div class="min-h-[60vh] flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-lg p-8 text-center">
            <div class="w-16 h-16 bg-primary-50 dark:bg-primary-900/20 rounded-full mx-auto flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/></svg>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white mb-2">Verify Your Email</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mb-6">
                We've sent a verification link to <span class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->email }}</span>.
                Please check your inbox and click the link to verify your account.
            </p>

            @if (session('status'))
                <div class="bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 px-4 py-3 rounded-xl text-sm font-bold mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full bg-primary-600 text-white font-bold py-3 rounded-xl hover:bg-primary-700 transition-colors shadow-sm text-sm uppercase tracking-wide">
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
