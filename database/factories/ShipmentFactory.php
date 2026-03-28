<?php

namespace Database\Factories;

use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipment>
 */
class ShipmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'delivery_agent_id' => null,
            'tracking_number' => strtoupper(Str::random(12)),
            'carrier_name' => $this->faker->randomElement(['DHL', 'FedEx', 'UPS', 'USPS', 'J&T Express']),
            'status' => ShipmentStatus::Pending,
            'estimated_delivery_date' => $this->faker->dateTimeBetween('+1 day', '+14 days'),
            'shipped_at' => null,
            'delivered_at' => null,
            'current_latitude' => null,
            'current_longitude' => null,
            'delivery_proof_image' => null,
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    public function assigned(): static
    {
        return $this->state(fn (): array => [
            'delivery_agent_id' => User::factory()->deliveryAgent(),
            'status' => ShipmentStatus::Assigned,
        ]);
    }

    public function inTransit(): static
    {
        return $this->state(fn (): array => [
            'delivery_agent_id' => User::factory()->deliveryAgent(),
            'status' => ShipmentStatus::InTransit,
            'shipped_at' => now()->subDays(2),
            'current_latitude' => $this->faker->latitude(),
            'current_longitude' => $this->faker->longitude(),
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (): array => [
            'delivery_agent_id' => User::factory()->deliveryAgent(),
            'status' => ShipmentStatus::Delivered,
            'shipped_at' => now()->subDays(5),
            'delivered_at' => now(),
        ]);
    }
}
