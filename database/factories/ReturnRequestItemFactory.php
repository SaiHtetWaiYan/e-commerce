<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\ReturnRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReturnRequestItem>
 */
class ReturnRequestItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'return_request_id' => ReturnRequest::factory(),
            'order_item_id' => OrderItem::factory(),
            'quantity' => $this->faker->numberBetween(1, 3),
            'subtotal' => $this->faker->randomFloat(2, 5, 200),
        ];
    }
}
