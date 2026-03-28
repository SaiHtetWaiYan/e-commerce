<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Campaign;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    public function index(): View
    {
        return view('storefront.campaigns.index', [
            'activeCampaigns' => Campaign::query()
                ->active()
                ->withCount('products')
                ->orderBy('ends_at')
                ->get(),
            'upcomingCampaigns' => Campaign::query()
                ->upcoming()
                ->withCount('products')
                ->get(),
        ]);
    }

    public function show(Request $request, Campaign $campaign): View
    {
        $filters = $request->only(['category', 'brand', 'min_price', 'max_price', 'sort']);

        $productsQuery = $campaign->products()
            ->active()
            ->with(['images', 'vendor.vendorProfile', 'brand', 'categories']);

        if (! empty($filters['category'])) {
            $categorySlug = (string) $filters['category'];
            $productsQuery->whereHas('categories', fn (Builder $builder): Builder => $builder->where('slug', $categorySlug));
        }

        if (! empty($filters['brand'])) {
            $brandSlug = (string) $filters['brand'];
            $productsQuery->whereHas('brand', fn (Builder $builder): Builder => $builder->where('slug', $brandSlug));
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $productsQuery->where('products.base_price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $productsQuery->where('products.base_price', '<=', (float) $filters['max_price']);
        }

        $sort = (string) ($filters['sort'] ?? 'featured');
        match ($sort) {
            'price_asc' => $productsQuery->orderBy('products.base_price'),
            'price_desc' => $productsQuery->orderByDesc('products.base_price'),
            'best_selling' => $productsQuery->orderByDesc('products.total_sold'),
            'rating' => $productsQuery->orderByDesc('products.avg_rating'),
            default => $productsQuery
                ->orderBy('campaign_product.sort_order')
                ->latest('products.created_at'),
        };

        return view('storefront.campaigns.show', [
            'campaign' => $campaign,
            'products' => $productsQuery->paginate(24)->withQueryString(),
            'categories' => Category::query()
                ->active()
                ->whereHas('products.campaigns', fn (Builder $builder): Builder => $builder->where('campaigns.id', $campaign->id))
                ->orderBy('name')
                ->get(),
            'brands' => Brand::query()
                ->whereHas('products.campaigns', fn (Builder $builder): Builder => $builder->where('campaigns.id', $campaign->id))
                ->orderBy('name')
                ->get(),
            'filters' => $filters,
        ]);
    }
}
