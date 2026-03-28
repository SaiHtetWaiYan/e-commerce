<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductModerationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->with(['vendor.vendorProfile', 'brand', 'categories'])
            ->whereIn('status', [ProductStatus::PendingReview, ProductStatus::Rejected])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('vendor', fn ($vendorQuery) => $vendorQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $products = $query->paginate(20)->withQueryString();

        return view('admin.products.review.index', [
            'products' => $products,
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless(in_array($product->status, [ProductStatus::PendingReview, ProductStatus::Rejected], true), 404);

        return view('admin.products.review.show', [
            'product' => $product->load(['vendor.vendorProfile', 'brand', 'categories', 'images']),
        ]);
    }

    public function approve(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $product->update([
            'status' => ProductStatus::Active,
            'moderation_notes' => $validated['comment'] ?? null,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        return redirect()->route('admin.products.review.index')
            ->with('status', "Product \"{$product->name}\" approved and published.");
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        $product->update([
            'status' => ProductStatus::Rejected,
            'moderation_notes' => $validated['comment'],
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        return redirect()->route('admin.products.review.index')
            ->with('status', "Product \"{$product->name}\" was rejected.");
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $count = Product::query()
            ->whereIn('id', $validated['product_ids'])
            ->whereIn('status', [ProductStatus::PendingReview, ProductStatus::Rejected])
            ->update([
                'status' => ProductStatus::Active,
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
            ]);

        return redirect()->route('admin.products.review.index')
            ->with('status', "{$count} product(s) approved.");
    }

    public function bulkReject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $count = Product::query()
            ->whereIn('id', $validated['product_ids'])
            ->whereIn('status', [ProductStatus::PendingReview])
            ->update([
                'status' => ProductStatus::Rejected,
                'moderated_by' => auth()->id(),
                'moderated_at' => now(),
            ]);

        return redirect()->route('admin.products.review.index')
            ->with('status', "{$count} product(s) rejected.");
    }
}
