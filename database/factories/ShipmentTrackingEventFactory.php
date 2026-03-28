<?php

namespace Database\Factories;

use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentTrackingEvent>
 */
class ShipmentTrackingEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'status' => $this->faker->randomElement(['picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed']),
            'description' => $this->faker->sentence(),
            'location' => $this->faker->city().', '.$this->faker->state(),
            'latitude' => $this->faker->optional(0.5)->latitude(),
            'longitude' => $this->faker->optional(0.5)->longitude(),
            'event_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'created_at' => now(),
        ];
    }
}
