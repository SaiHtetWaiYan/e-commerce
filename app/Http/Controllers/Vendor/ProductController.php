<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Vendor\StoreProductRequest;
use App\Http\Requests\Vendor\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(public ProductService $productService) {}

    public function index(): View
    {
        $products = Product::query()
            ->forVendor((int) auth()->id())
            ->with(['images', 'brand', 'categories'])
            ->latest()
            ->paginate(20);

        return view('vendor.products.index', ['products' => $products]);
    }

    public function create(): View
    {
        return view('vendor.products.create', [
            'brands' => Brand::query()->active()->orderBy('name')->get(),
            'categories' => Category::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = $this->productService->createForVendor($request->user(), $request->validated());

        return redirect()->route('vendor.products.edit', $product)->with('status', 'Product created.');
    }

    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        return view('vendor.products.edit', [
            'product' => $product->load(['categories', 'images', 'brand']),
            'brands' => Brand::query()->active()->orderBy('name')->get(),
            'categories' => Category::query()->active()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $this->productService->updateForVendor($product, $request->validated());

        return back()->with('status', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        $this->productService->deleteForVendor($product);

        return redirect()->route('vendor.products.index')->with('status', 'Product removed.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
            'action' => ['required', 'string', 'in:activate,archive'],
        ]);

        $vendorId = (int) auth()->id();
        $newStatus = $validated['action'] === 'activate'
            ? \App\Enums\ProductStatus::PendingReview
            : \App\Enums\ProductStatus::Archived;

        $count = Product::query()
            ->whereIn('id', $validated['product_ids'])
            ->where('vendor_id', $vendorId)
            ->update(['status' => $newStatus]);

        $label = $validated['action'] === 'activate' ? 'submitted for review' : 'archived';

        return redirect()->route('vendor.products.index')
            ->with('status', "{$count} product(s) {$label}.");
    }
}
