<x-layouts.admin>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Category: {{ $category->name }}</h1>
            <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                &larr; Back to Categories
            </a>
        </div>

        <x-ui.card>
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4 text-sm">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input id="name" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Slug (Optional)</label>
                    <input id="slug" name="slug" value="{{ old('slug', $category->slug) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave blank to auto-generate based on name.</p>
                    @error('slug')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="parent_id" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Parent Category</label>
                    <select id="parent_id" name="parent_id" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        <option value="">None</option>
                        @foreach ($parentCategories as $parentCategory)
                            <option value="{{ $parentCategory->id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $parentCategory->id)>
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
                    <input id="icon" name="icon" value="{{ old('icon', $category->icon) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                    @error('icon')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sort_order" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order) }}" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                    @error('sort_order')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                    <textarea id="description" name="description" rows="4" class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', $category->is_active ? '1' : '0') === '1') class="rounded border-gray-300 dark:border-gray-600">
                    <span class="text-gray-700 dark:text-gray-300">Active category</span>
                </label>
                @error('is_active')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
                    <x-ui.button type="submit">Update Category</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.admin>
