<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 500);

        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->randomElement(['Small', 'Medium', 'Large', 'XL', 'Red', 'Blue', 'Black', 'White']),
            'sku' => strtoupper(Str::random(8)),
            'price' => $price,
            'compare_price' => $this->faker->optional(0.3)->randomFloat(2, $price, $price * 1.5),
            'stock_quantity' => $this->faker->numberBetween(0, 200),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
