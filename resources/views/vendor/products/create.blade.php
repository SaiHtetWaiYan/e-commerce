<x-layouts.vendor>
    <x-ui.card title="Create Product">
        <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-2">
            @csrf
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Product Images</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <input name="name" placeholder="Product name" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <input name="sku" placeholder="SKU" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="base_price" type="number" step="0.01" placeholder="Base price" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <input name="compare_price" type="number" step="0.01" placeholder="Compare price" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="stock_quantity" type="number" placeholder="Stock quantity" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <x-ui.select name="brand_id" placeholder="Brand" :options="$brands->map(fn($b) => ['value' => $b->id, 'label' => $b->name])->toArray()" />
            <x-ui.multi-select name="category_ids[]" placeholder="Select categories..." :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" required />
            <textarea name="description" rows="4" placeholder="Description" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
            <label class="flex items-center gap-2 sm:col-span-2 mt-2">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Submit for review (publishes after admin approval)</span>
            </label>
            <x-ui.button type="submit" class="sm:col-span-2 mt-2">Save Product</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.vendor>
