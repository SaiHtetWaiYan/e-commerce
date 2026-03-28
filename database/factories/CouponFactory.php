<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(CouponType::cases());

        return [
            'vendor_id' => null,
            'code' => strtoupper(Str::random(8)),
            'description' => $this->faker->sentence(),
            'type' => $type,
            'value' => $type === CouponType::Percentage
                ? $this->faker->numberBetween(5, 50)
                : $this->faker->randomFloat(2, 5, 100),
            'min_order_amount' => $this->faker->optional(0.5)->randomFloat(2, 20, 200),
            'max_discount_amount' => $this->faker->optional(0.3)->randomFloat(2, 10, 100),
            'usage_limit' => $this->faker->optional(0.5)->numberBetween(10, 1000),
            'used_count' => 0,
            'per_user_limit' => $this->faker->optional(0.4)->numberBetween(1, 5),
            'starts_at' => now(),
            'expires_at' => now()->addDays($this->faker->numberBetween(7, 90)),
            'is_active' => true,
        ];
    }

    public function forVendor(): static
    {
        return $this->state(fn (): array => [
            'vendor_id' => User::factory()->vendor(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subDays(30),
            'expires_at' => now()->subDay(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
