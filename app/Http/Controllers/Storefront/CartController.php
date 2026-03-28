<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\AddToCartRequest;
use App\Http\Requests\Storefront\ApplyCouponRequest;
use App\Http\Requests\Storefront\DestroyCartItemRequest;
use App\Http\Requests\Storefront\UpdateCartItemRequest;
use App\Models\Category;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use InvalidArgumentException;

class CartController extends Controller
{
    public function __construct(public CartService $cartService) {}

    public function index(): View
    {
        $cart = $this->cartService->resolveCart(auth()->user(), $this->sessionId());
        $totals = $this->cartService->calculateTotals($cart);
        $popularCategories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(6)
            ->get();

        return view('storefront.cart.index', [
            'cart' => $cart,
            'totals' => $totals,
            'popularCategories' => $popularCategories,
        ]);
    }

    public function add(AddToCartRequest $request): RedirectResponse
    {
        try {
            $this->cartService->addItem(
                $request->user(),
                $this->sessionId(),
                (int) $request->validated('product_id'),
                $request->validated('variant_id') !== null ? (int) $request->validated('variant_id') : null,
                (int) $request->validated('quantity'),
            );
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['cart' => $exception->getMessage()]);
        }

        return back()->with('status', 'Item added to cart.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): RedirectResponse
    {
        try {
            $quantity = (int) $request->validated('quantity');
            
            if ($request->has('action')) {
                if ($request->input('action') === 'increase') {
                    $quantity++;
                } elseif ($request->input('action') === 'decrease') {
                    $quantity--;
                }
            }
            
            // Re-enforce stock limit to be safe, CartService might already do this but good for UX logic
            if ($item->product->stock_quantity !== null && $quantity > $item->product->stock_quantity) {
                $quantity = $item->product->stock_quantity;
            }

            if ($quantity < 1) {
                $quantity = 1;
            }

            $this->cartService->updateItem($item, $quantity);
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['cart' => $exception->getMessage()]);
        }

        return back()->with('status', 'Cart updated.');
    }

    public function destroy(DestroyCartItemRequest $request, CartItem $item): RedirectResponse
    {
        $this->cartService->removeItem($item);

        return back()->with('status', 'Item removed from cart.');
    }

    public function applyCoupon(ApplyCouponRequest $request): RedirectResponse
    {
        $cart = $this->cartService->resolveCart($request->user(), $this->sessionId());

        try {
            $this->cartService->applyCoupon($cart, (string) $request->validated('code'));
        } catch (InvalidArgumentException $exception) {
            return back()->withErrors(['coupon' => $exception->getMessage()]);
        }

        return back()->with('status', 'Coupon applied.');
    }

    protected function sessionId(): string
    {
        return session()->getId();
    }
}
