<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(public ProductService $productService) {}

    public function show(Request $request, string $slug): View
    {
        $category = Category::query()
            ->active()
            ->where('slug', $slug)
            ->with(['children' => fn ($query) => $query->active()->orderBy('sort_order')])
            ->firstOrFail();

        $filters = array_merge($request->only(['q', 'brand', 'min_price', 'max_price', 'sort']), [
            'category' => $category->slug,
        ]);

        $products = $this->productService->paginateForStorefront($filters);

        return view('storefront.categories.show', [
            'category' => $category,
            'products' => $products,
            'filters' => $filters,
        ]);
    }
}
