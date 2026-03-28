<x-layouts.admin>
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-ui.card title="Categories">
                @if (session('status'))
                    <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700 dark:text-emerald-400">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="space-y-3 text-sm">
                    @forelse ($categories as $category)
                        <div class="rounded-md border border-gray-200 dark:border-gray-700 px-3 py-2">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Slug: {{ $category->slug }}
                                        @if ($category->parent)
                                            | Parent: {{ $category->parent->name }}
                                        @endif
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Products: {{ $category->products_count }} | Sort: {{ $category->sort_order }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded px-2 py-1 text-xs {{ $category->is_active ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="rounded-md border border-dashed border-gray-300 dark:border-gray-600 px-3 py-8 text-center text-gray-500 dark:text-gray-400">
                            No categories yet. Create your first category from the form.
                        </p>
                    @endforelse
                </div>

                <div class="mt-4">{{ $categories->links() }}</div>
            </x-ui.card>
        </div>

        <div class="xl:col-span-1">
            <x-ui.card title="Create Category">
                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4 text-sm">
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
                        <label for="parent_id" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Parent Category</label>
                        <select id="parent_id" name="parent_id" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                            <option value="">None</option>
                            @foreach ($parentCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" @selected((string) old('parent_id') === (string) $parentCategory->id)>
                                    {{ $parentCategory->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="icon" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Icon (Optional)</label>
                        <input id="icon" name="icon" value="{{ old('icon') }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        @error('icon')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                        <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        @error('sort_order')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                        <textarea id="description" name="description" rows="3" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', '1') === '1') class="rounded border-gray-300 dark:border-gray-600">
                        <span class="text-gray-700 dark:text-gray-300">Active category</span>
                    </label>
                    @error('is_active')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror

                    <x-ui.button type="submit" class="w-full">Create Category</x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-layouts.admin>
