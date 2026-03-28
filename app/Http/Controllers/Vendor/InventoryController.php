<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->forVendor((int) auth()->id())
            ->orderByRaw('stock_quantity <= low_stock_threshold DESC')
            ->orderBy('stock_quantity')
            ->paginate(25);

        $lowStockCount = Product::query()
            ->forVendor((int) auth()->id())
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();

        $totalProducts = Product::query()
            ->forVendor((int) auth()->id())
            ->count();

        return view('vendor.inventory.index', compact('products', 'lowStockCount', 'totalProducts'));
    }

    public function updateStock(\App\Http\Requests\Vendor\UpdateStockRequest $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        abort_unless((int) $product->vendor_id === (int) auth()->id(), 403);

        $product->update($request->validated());

        return back()->with('status', "Stock updated for \"{$product->name}\".");
    }
}
