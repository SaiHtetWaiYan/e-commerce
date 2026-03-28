<x-layouts.vendor>
    <x-ui.card title="Edit Product">
        <form id="update-product-form" action="{{ route('vendor.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="grid gap-3 sm:grid-cols-2">
            @csrf
            @method('PUT')
            
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload New Images (leaves existing images intact unless new ones are uploaded)</label>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                
                @if($product->images->isNotEmpty())
                <div class="mt-3 flex gap-2 flex-wrap">
                    @foreach($product->images as $image)
                        <div class="relative w-20 h-20 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                            <img src="{{ $image->image_path }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>
            <input name="name" value="{{ old('name', $product->name) }}" placeholder="Product name" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <input name="sku" value="{{ old('sku', $product->sku) }}" placeholder="SKU" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="base_price" type="number" step="0.01" value="{{ old('base_price', $product->base_price) }}" placeholder="Base price" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <input name="compare_price" type="number" step="0.01" value="{{ old('compare_price', $product->compare_price) }}" placeholder="Compare price" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="stock_quantity" type="number" value="{{ old('stock_quantity', $product->stock_quantity) }}" placeholder="Stock quantity" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <x-ui.select name="brand_id" placeholder="Brand" :value="$product->brand_id" :options="$brands->map(fn($b) => ['value' => $b->id, 'label' => $b->name])->toArray()" />
            <x-ui.multi-select name="category_ids[]" placeholder="Select categories..." :values="$product->categories->pluck('id')" :options="$categories->map(fn($c) => ['value' => $c->id, 'label' => $c->name])->toArray()" required />
            <textarea name="description" rows="4" placeholder="Description" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">{{ old('description', $product->description) }}</textarea>
            
            <label class="flex items-center gap-2 sm:col-span-2 mt-2">
                <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', in_array($product->status, [\App\Enums\ProductStatus::Active, \App\Enums\ProductStatus::PendingReview]) ? '1' : '0') === '1') class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Submit for review (publishes after admin approval)</span>
            </label>
        </form>

        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <x-ui.button type="submit" form="update-product-form">Update Product</x-ui.button>
            <form action="{{ route('vendor.products.destroy', $product) }}" method="POST">
                @csrf
                @method('DELETE')
                <x-ui.button variant="danger" type="submit">Delete</x-ui.button>
            </form>
        </div>
    </x-ui.card>
</x-layouts.vendor>
