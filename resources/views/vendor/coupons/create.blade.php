<x-layouts.vendor>
    <x-ui.card title="Create Coupon">
        <form action="{{ route('vendor.coupons.store') }}" method="POST" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <input name="code" placeholder="Code" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <select name="type" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
                <option value="percentage">Percentage</option>
                <option value="fixed">Fixed</option>
                <option value="free_shipping">Free Shipping</option>
            </select>
            <input name="value" type="number" step="0.01" placeholder="Value" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none" required>
            <input name="min_order_amount" type="number" step="0.01" placeholder="Min order" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="max_discount_amount" type="number" step="0.01" placeholder="Max discount" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="usage_limit" type="number" placeholder="Usage limit" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="per_user_limit" type="number" placeholder="Per user limit" value="1" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="starts_at" type="datetime-local" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <input name="expires_at" type="datetime-local" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            <textarea name="description" placeholder="Description" class="sm:col-span-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none"></textarea>
            <x-ui.button type="submit" class="sm:col-span-2">Save Coupon</x-ui.button>
        </form>
    </x-ui.card>
</x-layouts.vendor>
