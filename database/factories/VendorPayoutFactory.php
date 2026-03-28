<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendorPayout>
 */
class VendorPayoutFactory extends Factory
{
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 100, 5000);
        $commissionAmount = round($amount * 0.1, 2);

        return [
            'vendor_id' => User::factory()->vendor(),
            'amount' => $amount,
            'commission_amount' => $commissionAmount,
            'net_amount' => $amount - $commissionAmount,
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']),
            'payment_method' => $this->faker->randomElement(['bank_transfer', 'paypal', 'stripe']),
            'payment_reference' => $this->faker->optional(0.5)->uuid(),
            'period_start' => $periodStart = $this->faker->dateTimeBetween('-60 days', '-30 days'),
            'period_end' => $this->faker->dateTimeBetween($periodStart, '-1 day'),
            'paid_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (): array => [
            'status' => 'completed',
            'payment_reference' => $this->faker->uuid(),
            'paid_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }
}
