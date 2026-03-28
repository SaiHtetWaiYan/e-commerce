<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'image' => 'banners/'.$this->faker->uuid().'.jpg',
            'link' => $this->faker->optional(0.7)->url(),
            'position' => $this->faker->randomElement(['hero', 'sidebar', 'footer', 'category']),
            'sort_order' => $this->faker->numberBetween(0, 20),
            'is_active' => true,
            'starts_at' => now(),
            'expires_at' => now()->addDays($this->faker->numberBetween(7, 60)),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subDays(30),
            'expires_at' => now()->subDay(),
        ]);
    }
}
