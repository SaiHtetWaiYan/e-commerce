<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\VendorProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class VendorStoreController extends Controller
{
    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $vendorProfile = VendorProfile::query()
            ->with('user')
            ->where(function ($query) use ($slug): void {
                $decoded = urldecode($slug);
                $normalizedSlug = Str::slug($decoded);

                $query
                    ->where('store_slug', $slug)
                    ->orWhere('store_slug', $normalizedSlug)
                    ->orWhereRaw('LOWER(store_name) = ?', [Str::lower($decoded)]);
            })
            ->first();

        if ($vendorProfile === null) {
            if (Product::query()->where('slug', $slug)->exists()) {
                return redirect()->route('storefront.products.show', ['slug' => $slug], 301);
            }

            abort(404);
        }

        $with = ['images', 'vendor.vendorProfile', 'brand'];
        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $with['campaigns'] = fn ($query) => $query->active()->orderByDesc('starts_at');
        }

        $productsQuery = Product::query()
            ->active()
            ->forVendor($vendorProfile->user_id)
            ->with($with);

        if ($request->filled('q')) {
            $keyword = trim((string) $request->string('q'));
            $productsQuery->where(function ($query) use ($keyword): void {
                $query
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        $sort = (string) $request->string('sort', 'latest');
        match ($sort) {
            'price_asc' => $productsQuery->orderBy('base_price'),
            'price_desc' => $productsQuery->orderByDesc('base_price'),
            'rating' => $productsQuery->orderByDesc('avg_rating'),
            'best_selling' => $productsQuery->orderByDesc('total_sold'),
            default => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(12)->withQueryString();

        $stats = Product::query()
            ->active()
            ->forVendor($vendorProfile->user_id)
            ->selectRaw('COUNT(*) as products_count, COALESCE(SUM(total_sold), 0) as total_sold, ROUND(COALESCE(AVG(avg_rating), 0), 1) as average_rating')
            ->first();

        return view('storefront.vendor.show', [
            'vendorProfile' => $vendorProfile,
            'products' => $products,
            'stats' => [
                'products_count' => (int) $stats->products_count,
                'total_sold' => (int) $stats->total_sold,
                'average_rating' => (float) $stats->average_rating,
            ],
            'filters' => $request->only(['q', 'sort']),
        ]);
    }
}
