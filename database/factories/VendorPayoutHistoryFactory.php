<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VendorPayout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendorPayoutHistory>
 */
class VendorPayoutHistoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'vendor_payout_id' => VendorPayout::factory(),
            'performed_by' => User::factory()->admin(),
            'action' => $this->faker->randomElement(['created', 'approved', 'processing', 'completed', 'failed', 'rejected']),
            'note' => $this->faker->optional(0.5)->sentence(),
            'meta' => $this->faker->optional(0.3)->passthrough([
                'previous_status' => 'pending',
                'new_status' => 'completed',
            ]),
        ];
    }
}
