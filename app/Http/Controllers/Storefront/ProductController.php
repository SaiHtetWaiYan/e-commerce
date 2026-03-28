<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\RecentlyViewedProductsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    public function __construct(
        public ProductService $productService,
        public RecentlyViewedProductsService $recentlyViewedProductsService,
    ) {}

    public function index(Request $request): View
    {
        $products = $this->productService->paginateForStorefront($request->only([
            'q',
            'category',
            'brand',
            'min_price',
            'max_price',
            'sort',
            'rating',
        ]));

        $categories = \App\Models\Category::query()
            ->active()
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $brands = \App\Models\Brand::query()
            ->orderBy('name')
            ->get();

        return view('storefront.products.index', [
            'products' => $products,
            'filters' => $request->all(),
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $with = [
            'images',
            'vendor.vendorProfile',
            'categories',
            'brand',
            'variants.attributeValues.attribute',
            'reviews' => fn ($query) => $query->approved()->with(['user', 'reviewImages'])->latest(),
        ];

        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $with['campaigns'] = fn ($query) => $query->active()->orderByDesc('starts_at');
        }

        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->with($with)
            ->firstOrFail();

        $relatedWith = ['images', 'vendor.vendorProfile'];
        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $relatedWith['campaigns'] = fn ($query) => $query->active()->orderByDesc('starts_at');
        }

        $relatedProducts = Product::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $product->categories->pluck('id')))
            ->with($relatedWith)
            ->latest()
            ->limit(8)
            ->get();

        $this->recentlyViewedProductsService->remember($request, $product);
        $recentlyViewedProducts = $this->recentlyViewedProductsService->products($request, excludeProductId: (int) $product->id);

        return view('storefront.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'recentlyViewedProducts' => $recentlyViewedProducts,
        ]);
    }
}
