<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(public ProductService $productService) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'category', 'brand', 'min_price', 'max_price', 'sort', 'rating']);
        $products = $this->productService->paginateForStorefront($filters);

        $categories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $brands = Brand::query()
            ->orderBy('name')
            ->get();

        return view('storefront.search.results', [
            'products' => $products,
            'filters' => $filters,
            'query' => (string) ($filters['q'] ?? ''),
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
