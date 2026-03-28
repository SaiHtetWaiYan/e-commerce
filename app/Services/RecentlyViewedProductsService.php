<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class RecentlyViewedProductsService
{
    public const SESSION_KEY = 'storefront.recently_viewed_products';

    public function remember(Request $request, Product $product, int $limit = 12): void
    {
        $ids = collect((array) $request->session()->get(self::SESSION_KEY, []))
            ->map(fn (mixed $id): int => (int) $id)
            ->filter()
            ->reject(fn (int $id): bool => $id === (int) $product->id)
            ->prepend((int) $product->id)
            ->take($limit)
            ->values()
            ->all();

        $request->session()->put(self::SESSION_KEY, $ids);
    }

    /**
     * @return array<int, int>
     */
    public function ids(Request $request): array
    {
        return collect((array) $request->session()->get(self::SESSION_KEY, []))
            ->map(fn (mixed $id): int => (int) $id)
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, Product>
     */
    public function products(Request $request, ?int $excludeProductId = null, int $limit = 6): Collection
    {
        if (! Schema::hasTable('products')) {
            return collect();
        }

        $ids = collect($this->ids($request))
            ->when($excludeProductId !== null, fn (Collection $collection): Collection => $collection->reject(fn (int $id): bool => $id === $excludeProductId))
            ->take($limit)
            ->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        $orderBySql = 'CASE '.collect($ids)
            ->values()
            ->map(fn (int $id, int $index): string => "WHEN products.id = {$id} THEN {$index}")
            ->implode(' ')
            .' ELSE '.count($ids).' END';

        return Product::query()
            ->active()
            ->whereIn('id', $ids->all())
            ->with($this->relations())
            ->orderByRaw($orderBySql)
            ->get();
    }

    /**
     * @return array<int|string, mixed>
     */
    protected function relations(): array
    {
        $relations = ['images', 'vendor.vendorProfile'];

        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $relations['campaigns'] = fn ($query) => $query->active()->orderByDesc('starts_at');
        }

        return $relations;
    }
}
