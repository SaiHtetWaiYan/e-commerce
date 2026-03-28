<?php

namespace Database\Factories;

use App\Enums\ReturnStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnRequest>
 */
class ReturnRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'reason' => $this->faker->randomElement([
                'Defective product',
                'Wrong item received',
                'Item not as described',
                'Changed my mind',
                'Product damaged during shipping',
            ]),
            'status' => ReturnStatus::Pending,
            'admin_notes' => null,
            'refund_amount' => $this->faker->randomFloat(2, 5, 500),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => ReturnStatus::Approved,
            'admin_notes' => $this->faker->sentence(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => [
            'status' => ReturnStatus::Rejected,
            'admin_notes' => $this->faker->sentence(),
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (): array => [
            'status' => ReturnStatus::Refunded,
            'admin_notes' => $this->faker->sentence(),
        ]);
    }
}
