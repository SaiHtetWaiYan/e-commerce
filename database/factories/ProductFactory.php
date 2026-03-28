<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'vendor_id' => User::factory()->vendor(),
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug((string) $name).'-'.Str::random(5),
            'description' => $this->faker->paragraph(),
            'short_description' => $this->faker->sentence(),
            'base_price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_quantity' => $this->faker->numberBetween(10, 100),
            'status' => ProductStatus::Active,
        ];
    }
}
