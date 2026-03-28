<x-layouts.vendor>
    <x-ui.card title="Edit Coupon">
        <form action="{{ route('vendor.coupons.update', $coupon) }}" method="POST" class="grid gap-3 sm:grid-cols-2">
            @csrf
            @method('PUT')
            <input name="code" placeholder="Code" value="{{ old('code', $coupon->code) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <select name="type" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                <option value="percentage" @selected(old('type', $coupon->type->value) === 'percentage')>Percentage</option>
                <option value="fixed" @selected(old('type', $coupon->type->value) === 'fixed')>Fixed</option>
                <option value="free_shipping" @selected(old('type', $coupon->type->value) === 'free_shipping')>Free Shipping</option>
            </select>
            <input name="value" type="number" step="0.01" placeholder="Value" value="{{ old('value', $coupon->value) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <input name="min_order_amount" type="number" step="0.01" placeholder="Min order" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="max_discount_amount" type="number" step="0.01" placeholder="Max discount" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="usage_limit" type="number" placeholder="Usage limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="per_user_limit" type="number" placeholder="Per user limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="starts_at" type="datetime-local" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="expires_at" type="datetime-local" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500" @checked(old('is_active', $coupon->is_active))>
                Active
            </label>
            <textarea name="description" placeholder="Description" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">{{ old('description', $coupon->description) }}</textarea>
            <x-ui.button type="submit" class="sm:col-span-2">Update Coupon</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.vendor>
