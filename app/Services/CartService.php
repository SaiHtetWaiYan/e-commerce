<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Events\CartUpdated;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class CartService
{
    public function __construct(public CouponService $couponService) {}

    public function resolveCart(?User $user, string $sessionId): Cart
    {
        $cart = $this->getOrCreateCart($user, $sessionId);

        return $cart->load($this->cartRelations());
    }

    public function getCartItemCount(?User $user, string $sessionId): int
    {
        $query = Cart::query();

        if ($user instanceof User) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId)
                ->whereNull('user_id');
        }

        $cart = $query->first();

        return $cart ? (int) $cart->items()->sum('quantity') : 0;
    }

    public function addItem(?User $user, string $sessionId, int $productId, ?int $variantId, int $quantity): Cart
    {
        $quantity = max(1, $quantity);
        $cart = $this->getOrCreateCart($user, $sessionId);

        $productQuery = Product::query()->active();

        if (Schema::hasTable('campaigns') && Schema::hasTable('campaign_product')) {
            $productQuery->with([
                'campaigns' => fn ($query) => $query->active()->orderByDesc('starts_at'),
            ]);
        }

        $product = $productQuery->findOrFail($productId);
        $variant = $variantId !== null ? ProductVariant::query()->where('product_id', $product->id)->findOrFail($variantId) : null;

        $availableStock = $variant?->stock_quantity ?? $product->stock_quantity;
        if ($availableStock < $quantity) {
            throw new InvalidArgumentException('Not enough stock available.');
        }

        $item = $cart->items()
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        $requestedQuantity = $quantity + ($item?->quantity ?? 0);
        if ($availableStock < $requestedQuantity) {
            throw new InvalidArgumentException('Not enough stock available.');
        }

        $unitPrice = $variant !== null
            ? (float) $variant->price
            : $product->getEffectivePrice();

        if ($item instanceof CartItem) {
            $item->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        return $this->refreshAndBroadcast($cart);
    }

    public function updateItem(CartItem $item, int $quantity): Cart
    {
        if ($quantity <= 0) {
            return $this->removeItem($item);
        }

        $maxStock = $item->variant?->stock_quantity ?? $item->product?->stock_quantity ?? $quantity;
        if ($quantity > $maxStock) {
            throw new InvalidArgumentException('Requested quantity exceeds stock.');
        }

        $item->update(['quantity' => $quantity]);

        return $this->refreshAndBroadcast($item->cart);
    }

    public function removeItem(CartItem $item): Cart
    {
        $cart = $item->cart;
        $item->delete();

        return $this->refreshAndBroadcast($cart);
    }

    public function clear(Cart $cart): Cart
    {
        $cart->items()->delete();
        $cart->update(['coupon_id' => null]);

        return $this->refreshAndBroadcast($cart);
    }

    public function applyCoupon(Cart $cart, string $couponCode): Cart
    {
        $coupon = Coupon::query()->active()->whereRaw('LOWER(code) = ?', [mb_strtolower(trim($couponCode))])->first();

        if (! $coupon instanceof Coupon) {
            throw new InvalidArgumentException('Invalid coupon code.');
        }

        $subtotal = (float) $cart->items->sum(fn (CartItem $item): float => (float) $item->unit_price * $item->quantity);
        $this->couponService->validateOrFail($coupon, $subtotal, $cart->user);

        $cart->update(['coupon_id' => $coupon->id]);

        return $this->refreshAndBroadcast($cart);
    }

    public function calculateTotals(Cart $cart, ?string $shippingMethod = null): array
    {
        $subtotal = (float) $cart->items->sum(fn (CartItem $item): float => (float) $item->unit_price * $item->quantity);

        $discount = 0.0;
        if ($cart->coupon instanceof Coupon) {
            $discount = $this->couponService->calculateDiscount($cart->coupon, $subtotal);
        }

        // Determine shipping fee from selected method or default
        $methods = (array) config('marketplace.shipping_methods', []);
        $selectedMethod = $methods[$shippingMethod] ?? null;

        if ($subtotal >= (float) config('marketplace.free_shipping_threshold')) {
            $shippingFee = 0.0;
        } elseif ($selectedMethod !== null) {
            $shippingFee = (float) ($selectedMethod['fee'] ?? 0);
        } else {
            $shippingFee = $subtotal > 0
                ? (float) config('marketplace.default_shipping_fee')
                : 0.0;
        }

        if ($cart->coupon?->type === CouponType::FreeShipping) {
            $shippingFee = 0.0;
        }

        $taxAmount = round(($subtotal - $discount) * (float) config('marketplace.default_tax_rate'), 2);
        $total = max(0, round($subtotal - $discount + $shippingFee + $taxAmount, 2));

        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => $discount,
            'shipping_fee' => $shippingFee,
            'tax_amount' => $taxAmount,
            'total' => $total,
            'coupon_code' => $cart->coupon?->code,
            'shipping_method' => $shippingMethod ?? 'standard',
        ];
    }

    protected function getOrCreateCart(?User $user, string $sessionId): Cart
    {
        return DB::transaction(function () use ($user, $sessionId): Cart {
            if ($user instanceof User) {
                return Cart::query()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['session_id' => $sessionId],
                );
            }

            return Cart::query()->firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null],
            );
        });
    }

    protected function refreshAndBroadcast(Cart $cart): Cart
    {
        $freshCart = $cart->fresh();

        if (! $freshCart instanceof Cart) {
            throw new InvalidArgumentException('Cart could not be refreshed.');
        }

        $freshCart->load($this->cartRelations());

        CartUpdated::dispatch($freshCart);

        return $freshCart;
    }

    /**
     * @return array<int, string>
     */
    protected function cartRelations(): array
    {
        return [
            'items.product.images',
            'items.product.vendor.vendorProfile',
            'items.variant',
            'coupon',
        ];
    }
}
