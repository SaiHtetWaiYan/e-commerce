<?php

namespace Database\Factories;

use App\Enums\DisputeStatus;
use App\Models\Conversation;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dispute>
 */
class DisputeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'conversation_id' => Conversation::factory(),
            'complainant_id' => User::factory(),
            'respondent_id' => User::factory()->vendor(),
            'subject' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => DisputeStatus::Pending,
        ];
    }

    public function underReview(): static
    {
        return $this->state(fn (): array => [
            'status' => DisputeStatus::UnderReview,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn (): array => [
            'status' => DisputeStatus::Resolved,
            'resolution' => fake()->paragraph(),
            'resolved_by' => User::factory()->admin(),
            'resolved_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (): array => [
            'status' => DisputeStatus::Closed,
            'resolution' => fake()->paragraph(),
            'resolved_by' => User::factory()->admin(),
            'resolved_at' => now()->subDay(),
        ]);
    }
}
