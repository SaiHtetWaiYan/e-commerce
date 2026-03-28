<x-layouts.customer>
    <x-ui.card title="Profile">
        <form action="{{ route('customer.profile.update') }}" method="POST" class="grid gap-4 sm:grid-cols-2">
            @csrf
            @method('PUT')
            <input name="name" value="{{ old('name', $user->name) }}" placeholder="Name" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
            <input name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="Email" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
            <input name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Phone" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300">
            <input name="avatar" value="{{ old('avatar', $user->avatar) }}" placeholder="Avatar path" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300">
            <x-ui.button type="submit" class="sm:col-span-2 w-full mt-2">Save Profile</x-ui.button>
        </form>
    </x-ui.card>

    <div class="mt-8">
        <x-ui.card title="Update Password">
            <form action="{{ route('customer.profile.password') }}" method="POST" class="grid gap-4 sm:grid-cols-2">
                @csrf
                @method('PUT')
                <div class="sm:col-span-2">
                    <input name="current_password" type="password" placeholder="Current Password" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
                    @error('current_password', 'updatePassword')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input name="password" type="password" placeholder="New Password" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
                    @error('password', 'updatePassword')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <input name="password_confirmation" type="password" placeholder="Confirm Password" class="w-full border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all dark:text-gray-300" required>
                </div>
                <x-ui.button type="submit" class="sm:col-span-2 w-full mt-2">Update Password</x-ui.button>

                @if (session('status') === 'password-updated')
                    <p class="text-sm font-bold text-green-600 dark:text-green-400 mt-2 sm:col-span-2 text-center">Password updated successfully.</p>
                @endif
            </form>
        </x-ui.card>
    </div>
</x-layouts.customer>
