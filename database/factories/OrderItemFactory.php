<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'product_id' => \App\Models\Product::factory(),
            'vendor_id' => \App\Models\User::factory()->vendor(),
            'product_name' => $this->faker->words(3, true),
            'quantity' => 1,
            'unit_price' => 10,
            'subtotal' => 10,
        ];
    }
}
