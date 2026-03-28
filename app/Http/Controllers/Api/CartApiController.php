<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\AddToCartRequest;
use App\Http\Requests\Storefront\ApplyCouponRequest;
use App\Http\Requests\Storefront\DestroyCartItemRequest;
use App\Http\Requests\Storefront\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class CartApiController extends Controller
{
    public function __construct(public CartService $cartService) {}

    public function index(): JsonResponse
    {
        $cart = $this->cartService->resolveCart(auth()->user(), session()->getId());

        return response()->json([
            'cart' => $cart,
            'totals' => $this->cartService->calculateTotals($cart),
        ]);
    }

    public function add(AddToCartRequest $request): JsonResponse
    {
        try {
            $cart = $this->cartService->addItem(
                $request->user(),
                session()->getId(),
                (int) $request->validated('product_id'),
                $request->validated('variant_id') !== null ? (int) $request->validated('variant_id') : null,
                (int) $request->validated('quantity'),
            );
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Item added to cart.',
            'cart' => $cart,
            'totals' => $this->cartService->calculateTotals($cart),
        ]);
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): JsonResponse
    {
        try {
            $cart = $this->cartService->updateItem($item, (int) $request->validated('quantity'));
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Cart item updated.',
            'cart' => $cart,
            'totals' => $this->cartService->calculateTotals($cart),
        ]);
    }

    public function destroy(DestroyCartItemRequest $request, CartItem $item): JsonResponse
    {
        $cart = $this->cartService->removeItem($item);

        return response()->json([
            'message' => 'Cart item removed.',
            'cart' => $cart,
            'totals' => $this->cartService->calculateTotals($cart),
        ]);
    }

    public function applyCoupon(ApplyCouponRequest $request): JsonResponse
    {
        $cart = $this->cartService->resolveCart($request->user(), session()->getId());

        try {
            $cart = $this->cartService->applyCoupon($cart, (string) $request->validated('code'));
        } catch (InvalidArgumentException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Coupon applied.',
            'cart' => $cart,
            'totals' => $this->cartService->calculateTotals($cart),
        ]);
    }
}
