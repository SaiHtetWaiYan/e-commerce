<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchApiController extends Controller
{
    public function suggest(Request $request): JsonResponse
    {
        $query = trim((string) $request->query('q', ''));

        if ($query === '') {
            return response()->json(['results' => []]);
        }

        $normalizedQuery = Str::lower($query);
        $likeQuery = '%'.$normalizedQuery.'%';
        $prefixQuery = $normalizedQuery.'%';

        $results = Product::query()
            ->active()
            ->where(function ($builder) use ($likeQuery): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', [$likeQuery])
                    ->orWhereRaw('LOWER(COALESCE(sku, \'\')) LIKE ?', [$likeQuery])
                    ->orWhereRaw('LOWER(COALESCE(description, \'\')) LIKE ?', [$likeQuery])
                    ->orWhereHas('brand', fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', [$likeQuery]))
                    ->orWhereHas('vendor.vendorProfile', fn ($query) => $query->whereRaw('LOWER(store_name) LIKE ?', [$likeQuery]));
            })
            ->with(['images', 'brand', 'vendor.vendorProfile'])
            ->orderByRaw(
                "CASE
                    WHEN LOWER(name) = ? THEN 0
                    WHEN LOWER(name) LIKE ? THEN 1
                    WHEN LOWER(COALESCE(sku, '')) LIKE ? THEN 2
                    ELSE 3
                END",
                [$normalizedQuery, $prefixQuery, $prefixQuery],
            )
            ->orderByDesc('total_sold')
            ->limit(8)
            ->get(['id', 'name', 'slug', 'base_price', 'sku', 'vendor_id', 'brand_id']);

        return response()->json([
            'results' => $results->map(function (Product $product): array {
                $imagePath = $product->primary_image;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'base_price' => (float) $product->base_price,
                    'image_url' => $imagePath
                        ? (str_starts_with($imagePath, 'http') || str_starts_with($imagePath, '/storage')
                            ? $imagePath
                            : asset('storage/'.$imagePath))
                        : 'https://placehold.co/80x80/f1f5f9/64748b?text='.urlencode($product->name),
                    'subtitle' => collect([
                        $product->brand?->name,
                        $product->vendor->vendorProfile?->store_name,
                        $product->sku,
                    ])->filter()->join(' • '),
                ];
            })->values(),
        ]);
    }
}
