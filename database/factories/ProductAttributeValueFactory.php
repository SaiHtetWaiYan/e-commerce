<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductAttributeValue>
 */
class ProductAttributeValueFactory extends Factory
{
    protected $model = ProductAttributeValue::class;

    public function definition(): array
    {
        return [
            'attribute_id' => ProductAttribute::factory(),
            'value' => $this->faker->word(),
            'color_code' => $this->faker->optional(0.3)->hexColor(),
            'sort_order' => $this->faker->numberBetween(0, 20),
        ];
    }
}
