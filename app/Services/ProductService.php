<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function paginateForStorefront(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $with = [
            'images',
            'vendor.vendorProfile',
            'categories',
            'brand',
        ];

        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $with['campaigns'] = fn ($builder) => $builder->active()->orderByDesc('starts_at');
        }

        $query = Product::query()
            ->active()
            ->with($with);

        if (! empty($filters['q'])) {
            $term = trim((string) $filters['q']);
            $query->where(function (Builder $builder) use ($term): Builder {
                return $builder
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        if (! empty($filters['category'])) {
            $query->whereHas('categories', fn (Builder $builder): Builder => $builder->where('slug', $filters['category']));
        }

        if (! empty($filters['brand'])) {
            $query->whereHas('brand', fn (Builder $builder): Builder => $builder->where('slug', $filters['brand']));
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $query->where('base_price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $query->where('base_price', '<=', (float) $filters['max_price']);
        }

        if (! empty($filters['rating'])) {
            $query->where('avg_rating', '>=', (int) $filters['rating']);
        }

        $sort = (string) ($filters['sort'] ?? 'latest');
        match ($sort) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'best_selling' => $query->orderByDesc('total_sold'),
            'rating' => $query->orderByDesc('avg_rating'),
            default => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function createForVendor(User $vendor, array $validated): Product
    {
        return DB::transaction(function () use ($vendor, $validated): Product {
            $payload = Arr::except($validated, ['category_ids', 'images', 'is_active']);
            $payload['vendor_id'] = $vendor->id;
            $payload['slug'] = $this->generateUniqueSlug((string) $validated['name']);
            $payload['status'] = ! empty($validated['is_active']) ? ProductStatus::PendingReview : ProductStatus::Draft;

            $product = Product::query()->create($payload);

            $this->syncCategoriesAndImages($product, $validated);

            return $product->load(['categories', 'images', 'brand']);
        });
    }

    public function updateForVendor(Product $product, array $validated): Product
    {
        return DB::transaction(function () use ($product, $validated): Product {
            $payload = Arr::except($validated, ['category_ids', 'images', 'is_active']);

            if (isset($validated['name']) && $validated['name'] !== $product->name) {
                $payload['slug'] = $this->generateUniqueSlug((string) $validated['name'], $product->id);
            }

            if (array_key_exists('is_active', $validated)) {
                $payload['status'] = ! empty($validated['is_active']) ? ProductStatus::PendingReview : ProductStatus::Draft;
            }

            $product->update($payload);

            $this->syncCategoriesAndImages($product, $validated);

            return $product->load(['categories', 'images', 'brand']);
        });
    }

    public function deleteForVendor(Product $product): void
    {
        DB::transaction(function () use ($product): void {
            $product->loadMissing('images');

            $this->deleteStoredProductImages($product->images->pluck('image_path')->all());

            $product->images()->delete();
            $product->delete();
        });
    }

    protected function syncCategoriesAndImages(Product $product, array $payload): void
    {
        if (array_key_exists('category_ids', $payload)) {
            $product->categories()->sync((array) $payload['category_ids']);
        }

        if (array_key_exists('images', $payload)) {
            if (! empty($payload['images'])) {
                $existingImagePaths = $product->images()->pluck('image_path')->all();

                $product->images()->delete();
                $this->deleteStoredProductImages($existingImagePaths);

                foreach ((array) $payload['images'] as $index => $file) {
                    if ($file instanceof UploadedFile) {
                        $path = $file->store('products', 'public');
                    } else {
                        $path = (string) $file;
                    }

                    $product->images()->create([
                        'image_path' => Storage::url($path),
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                    ]);
                }
            }
        }
    }

    /**
     * @param  array<int, mixed>  $imagePaths
     */
    protected function deleteStoredProductImages(array $imagePaths): void
    {
        $pathsToDelete = collect($imagePaths)
            ->map(fn (mixed $imagePath): ?string => $this->normalizeStoredImagePath((string) $imagePath))
            ->filter()
            ->values()
            ->all();

        if ($pathsToDelete !== []) {
            Storage::disk('public')->delete($pathsToDelete);
        }
    }

    protected function normalizeStoredImagePath(string $imagePath): ?string
    {
        if ($imagePath === '') {
            return null;
        }

        $parsedPath = parse_url($imagePath, PHP_URL_PATH);
        $host = parse_url($imagePath, PHP_URL_HOST);
        $normalizedPath = is_string($parsedPath) && $parsedPath !== '' ? $parsedPath : $imagePath;

        if ($host !== null && ! str_contains($normalizedPath, '/storage/')) {
            return null;
        }

        $normalizedPath = ltrim($normalizedPath, '/');

        if (Str::startsWith($normalizedPath, 'storage/')) {
            return Str::after($normalizedPath, 'storage/');
        }

        return $normalizedPath;
    }

    protected function generateUniqueSlug(string $name, ?int $ignoreProductId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while ($this->slugExists($slug, $ignoreProductId)) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, ?int $ignoreProductId = null): bool
    {
        return Product::query()
            ->where('slug', $slug)
            ->when($ignoreProductId !== null, fn (Builder $query): Builder => $query->where('id', '!=', $ignoreProductId))
            ->exists();
    }
}
