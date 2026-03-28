<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\User;
use InvalidArgumentException;

class CouponService
{
    public function validateOrFail(Coupon $coupon, float $subtotal, ?User $user = null): void
    {
        if (! $coupon->is_active) {
            throw new InvalidArgumentException('Coupon is inactive.');
        }

        if ($coupon->starts_at !== null && $coupon->starts_at->isFuture()) {
            throw new InvalidArgumentException('Coupon is not active yet.');
        }

        if ($coupon->expires_at !== null && $coupon->expires_at->isPast()) {
            throw new InvalidArgumentException('Coupon has expired.');
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw new InvalidArgumentException('Coupon usage limit reached.');
        }

        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            throw new InvalidArgumentException('Order does not meet coupon minimum amount.');
        }

        if ($user !== null && $coupon->per_user_limit > 0) {
            $usedByUser = $user->orders()->where('coupon_id', $coupon->id)->count();
            if ($usedByUser >= $coupon->per_user_limit) {
                throw new InvalidArgumentException('You have reached the coupon usage limit.');
            }
        }
    }

    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        $discount = match ($coupon->type) {
            CouponType::Percentage => $subtotal * ((float) $coupon->value / 100),
            CouponType::Fixed => min((float) $coupon->value, $subtotal),
            CouponType::FreeShipping => 0,
        };

        if ($coupon->max_discount_amount !== null) {
            $discount = min($discount, (float) $coupon->max_discount_amount);
        }

        return round(max(0, $discount), 2);
    }
}
