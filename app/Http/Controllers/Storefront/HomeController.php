<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Product;
use App\Services\RecentlyViewedProductsService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function __construct(public RecentlyViewedProductsService $recentlyViewedProductsService) {}

    public function index(Request $request): View
    {
        $featuredProducts = collect();
        $flashSaleProducts = collect();
        $justForYouProducts = collect();
        $recentlyViewedProducts = collect();
        $activeCampaigns = collect();
        $activeFlashSaleCampaign = null;
        $hasCampaignTables = Schema::hasTable('campaigns') && Schema::hasTable('campaign_product');
        $productWith = ['images', 'vendor.vendorProfile'];

        if ($hasCampaignTables) {
            $productWith['campaigns'] = fn ($query) => $query->active()->orderByDesc('starts_at');
        }

        if (Schema::hasTable('products')) {
            $featuredProducts = Product::query()
                ->active()
                ->featured()
                ->with($productWith)
                ->latest()
                ->limit(12)
                ->get();

            $justForYouProducts = Product::query()
                ->active()
                ->with($productWith)
                ->inRandomOrder()
                ->limit(30)
                ->get();
        }

        if ($hasCampaignTables) {
            $activeCampaigns = Campaign::query()
                ->active()
                ->withCount('products')
                ->orderBy('ends_at')
                ->limit(6)
                ->get();

            $activeFlashSaleCampaign = Campaign::query()
                ->active()
                ->where(function (Builder $query): void {
                    $query
                        ->where('name', 'like', '%flash%')
                        ->orWhere('slug', 'like', '%flash%')
                        ->orWhere('badge_text', 'like', '%flash%');
                })
                ->orderBy('ends_at')
                ->first();
        }

        if ($activeFlashSaleCampaign instanceof Campaign) {
            $flashSaleProducts = $activeFlashSaleCampaign->products()
                ->active()
                ->with($productWith)
                ->inRandomOrder()
                ->limit(12)
                ->get();
        } elseif (Schema::hasTable('products')) {
            $flashSaleProducts = Product::query()
                ->active()
                ->whereNotNull('compare_price')
                ->whereColumn('compare_price', '>', 'base_price')
                ->with($productWith)
                ->inRandomOrder()
                ->limit(12)
                ->get();
        }

        $categories = collect();
        if (Schema::hasTable('categories')) {
            $categories = Category::query()
                ->active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->active()->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->limit(10)
                ->get();
        }

        $banners = collect();
        if (Schema::hasTable('banners')) {
            $banners = Banner::query()->active()->orderBy('sort_order')->get();
        }

        $recentlyViewedProducts = $this->recentlyViewedProductsService->products($request);

        return view('storefront.home', [
            'featuredProducts' => $featuredProducts,
            'flashSaleProducts' => $flashSaleProducts,
            'justForYouProducts' => $justForYouProducts,
            'recentlyViewedProducts' => $recentlyViewedProducts,
            'categories' => $categories,
            'banners' => $banners,
            'activeCampaigns' => $activeCampaigns,
            'activeFlashSaleCampaign' => $activeFlashSaleCampaign,
        ]);
    }
}
