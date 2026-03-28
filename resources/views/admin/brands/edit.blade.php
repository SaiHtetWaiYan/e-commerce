<x-layouts.admin>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Brand: {{ $brand->name }}</h1>
            <a href="{{ route('admin.brands.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                &larr; Back to Brands
            </a>
        </div>

        <x-ui.card>
            <form action="{{ route('admin.brands.update', $brand) }}" method="POST" class="space-y-4 text-sm">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input id="name" name="name" value="{{ old('name', $brand->name) }}" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Slug (Optional)</label>
                    <input id="slug" name="slug" value="{{ old('slug', $brand->slug) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to auto-generate based on name.</p>
                    @error('slug')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="logo" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Logo URL (Optional)</label>
                    <input id="logo" name="logo" value="{{ old('logo', $brand->logo) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                    @error('logo')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', $brand->is_active ? '1' : '0') === '1') class="rounded border-gray-300 dark:border-gray-600">
                    <span class="text-gray-700 dark:text-gray-300">Active brand</span>
                </label>
                @error('is_active')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                    <x-ui.button type="submit">Update Brand</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.admin>
