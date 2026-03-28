@php
    $campaign = $campaign ?? new \App\Models\Campaign();
    $action = $action ?? '#';
    $method = $method ?? 'POST';
    $submitLabel = $submitLabel ?? 'Save Campaign';

    $startsAtValue = old('starts_at', $campaign->starts_at?->format('Y-m-d\TH:i'));
    $endsAtValue = old('ends_at', $campaign->ends_at?->format('Y-m-d\TH:i'));
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{
    discountType: @js(old('discount_type', $campaign->discount_type?->value ?? \App\Enums\CampaignDiscountType::Percentage->value)),
    badgeText: @js(old('badge_text', $campaign->badge_text ?? 'SALE')),
    badgeColor: @js(old('badge_color', $campaign->badge_color ?? '#f97316')),
}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-4">
            <div>
                <label for="campaign-name" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Campaign Name</label>
                <input
                    id="campaign-name"
                    name="name"
                    value="{{ old('name', $campaign->name) }}"
                    required
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="3.3 Mega Sale"
                >
            </div>

            <div>
                <label for="campaign-slug" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Slug</label>
                <input
                    id="campaign-slug"
                    name="slug"
                    value="{{ old('slug', $campaign->slug) }}"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="3-3-mega-sale"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Auto-generated if left blank.</p>
            </div>

            <div>
                <label for="campaign-description" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Description</label>
                <textarea
                    id="campaign-description"
                    name="description"
                    rows="4"
                    class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="Write a short campaign description"
                >{{ old('description', $campaign->description) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="banner_image" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Banner Image</label>
                    <input id="banner_image" name="banner_image" type="file" accept="image/*" class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300">
                    @if ($campaign->banner_image)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Current: {{ $campaign->banner_image }}</p>
                    @endif
                </div>
                <div>
                    <label for="thumbnail_image" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Thumbnail Image</label>
                    <input id="thumbnail_image" name="thumbnail_image" type="file" accept="image/*" class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300">
                    @if ($campaign->thumbnail_image)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Current: {{ $campaign->thumbnail_image }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="badge_text" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Badge Text</label>
                    <input id="badge_text" name="badge_text" x-model="badgeText" value="{{ old('badge_text', $campaign->badge_text) }}" maxlength="50" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="3.3">
                </div>
                <div>
                    <label for="badge_color" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Badge Color</label>
                    <div class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-2 py-2">
                        <input id="badge_color" name="badge_color" type="color" x-model="badgeColor" value="{{ old('badge_color', $campaign->badge_color ?? '#f97316') }}" class="h-8 w-10 cursor-pointer rounded border border-gray-200 dark:border-gray-700 bg-transparent p-0">
                        <input x-model="badgeColor" class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 px-2 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300" placeholder="#f97316">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-4">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Badge Preview</p>
                <div class="mt-3 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-black uppercase tracking-wide text-white" :style="`background-color: ${badgeColor || '#f97316'}`" x-text="badgeText || 'SALE'"></span>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="discount_type" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Discount Type</label>
                    <select id="discount_type" name="discount_type" x-model="discountType" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @foreach (\App\Enums\CampaignDiscountType::cases() as $discountType)
                            <option value="{{ $discountType->value }}" @selected(old('discount_type', $campaign->discount_type?->value ?? \App\Enums\CampaignDiscountType::Percentage->value) === $discountType->value)>
                                {{ Str::title($discountType->value) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div x-show="discountType !== 'custom'" x-cloak>
                    <label for="discount_value" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Discount Value</label>
                    <input id="discount_value" name="discount_value" type="number" step="0.01" min="0" value="{{ old('discount_value', $campaign->discount_value) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="30">
                </div>
            </div>

            <div>
                <label for="max_discount_amount" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Max Discount Amount (Optional)</label>
                <input id="max_discount_amount" name="max_discount_amount" type="number" step="0.01" min="0" value="{{ old('max_discount_amount', $campaign->max_discount_amount) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="100">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="starts_at" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Starts At</label>
                    <input id="starts_at" name="starts_at" type="datetime-local" value="{{ $startsAtValue }}" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label for="ends_at" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Ends At</label>
                    <input id="ends_at" name="ends_at" type="datetime-local" value="{{ $endsAtValue }}" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2.5">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $campaign->is_active) ? true : false) class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Activate campaign immediately</span>
            </label>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-5">
        <a href="{{ route('admin.campaigns.index') }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
            Cancel
        </a>
        <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700">
            {{ $submitLabel }}
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.getElementById('campaign-name');
        const slugInput = document.getElementById('campaign-slug');

        if (! nameInput || ! slugInput) {
            return;
        }

        let isSlugTouched = slugInput.value.trim() !== '';

        slugInput.addEventListener('input', () => {
            isSlugTouched = slugInput.value.trim() !== '';
        });

        nameInput.addEventListener('input', () => {
            if (isSlugTouched) {
                return;
            }

            slugInput.value = nameInput.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
    });
</script>
