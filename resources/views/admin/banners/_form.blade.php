@php
    $banner = $banner ?? new \App\Models\Banner();
    $positions = $positions ?? [];
    $action = $action ?? '#';
    $method = $method ?? 'POST';
    $submitLabel = $submitLabel ?? 'Save Banner';

    $startsAtValue = old('starts_at', $banner->starts_at?->format('Y-m-d\TH:i'));
    $expiresAtValue = old('expires_at', $banner->expires_at?->format('Y-m-d\TH:i'));
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <div class="space-y-4">
            <div>
                <label for="banner-title" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Banner Title</label>
                <input id="banner-title" name="title" value="{{ old('title', $banner->title) }}" required class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Mega weekend flash sale">
            </div>

            <div>
                <label for="banner-link" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Link URL</label>
                <input id="banner-link" name="link" value="{{ old('link', $banner->link) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="https://example.test/campaigns/mega-sale">
            </div>

            <div>
                <label for="banner-image" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Banner Image</label>
                <input id="banner-image" name="image" type="file" accept="image/*" class="block w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-300">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use a wide promotional image for hero placement.</p>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label for="banner-position" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Placement</label>
                <select id="banner-position" name="position" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @foreach ($positions as $value => $label)
                        <option value="{{ $value }}" @selected(old('position', $banner->position ?? 'hero') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="banner-sort-order" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Sort Order</label>
                <input id="banner-sort-order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $banner->sort_order ?? 0) }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="banner-starts-at" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Starts At</label>
                    <input id="banner-starts-at" name="starts_at" type="datetime-local" value="{{ $startsAtValue }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label for="banner-expires-at" class="mb-1 block text-sm font-bold text-gray-700 dark:text-gray-300">Expires At</label>
                    <input id="banner-expires-at" name="expires_at" type="datetime-local" value="{{ $expiresAtValue }}" class="w-full rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 dark:text-white focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2.5">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true)) class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Banner is active</span>
            </label>
        </div>
    </div>

    <div class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40 p-5">
        <p class="text-xs font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">Current Preview</p>
        <div class="mt-3 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
            @if ($banner->image)
                <img src="{{ str_starts_with($banner->image, 'http') ? $banner->image : Storage::url($banner->image) }}" alt="{{ $banner->title }}" class="h-56 w-full object-cover">
            @else
                <div class="flex h-56 items-center justify-center bg-gradient-to-r from-primary-100 via-white to-accent-100 text-sm font-bold text-gray-500 dark:from-primary-900/30 dark:via-gray-900 dark:to-accent-900/20 dark:text-gray-300">
                    Banner preview will appear after upload
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-200 dark:border-gray-700 pt-5">
        <a href="{{ route('admin.banners.index') }}" class="rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-2.5 text-sm font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
            Cancel
        </a>
        <button type="submit" class="rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary-700">
            {{ $submitLabel }}
        </button>
    </div>
</form>
