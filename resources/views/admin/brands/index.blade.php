<x-layouts.admin>
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-ui.card title="Brands">
                @if (session('status'))
                    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-400">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-3 text-sm">
                    @forelse ($brands as $brand)
                        <div class="rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $brand->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Slug: {{ $brand->slug }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Products: {{ $brand->products_count }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded px-2 py-1 text-xs {{ $brand->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-md border border-dashed border-gray-300 dark:border-gray-600 px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                            No brands yet. Create your first brand from the form.
                        </p>
                    @endforelse
                </div>

                <div class="mt-4">{{ $brands->links() }}</div>
            </x-ui.card>
        </div>

        <div class="xl:col-span-1">
            <x-ui.card title="Create Brand">
                <form action="{{ route('admin.brands.store') }}" method="POST" class="space-y-4 text-sm">
                    @csrf

                    <div>
                        <label for="name" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input id="name" name="name" value="{{ old('name') }}" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Slug (Optional)</label>
                        <input id="slug" name="slug" value="{{ old('slug') }}" placeholder="auto-generated-if-empty" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        @error('slug')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="logo" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Logo URL (Optional)</label>
                        <input id="logo" name="logo" value="{{ old('logo') }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        @error('logo')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', '1') === '1') class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-gray-700 dark:text-gray-300">Active brand</span>
                    </label>
                    @error('is_active')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <x-ui.button type="submit" class="w-full">Create Brand</x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>
