<?php

namespace Database\Factories;

use App\Enums\CampaignDiscountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Campaign>
 */
class CampaignFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement([
            '3.3 Mega Sale',
            '9.9 Super Deals',
            'New Year Sale',
            'Weekend Specials',
            'Brand Festival',
        ]);

        $startsAt = now()->subDay();
        $endsAt = now()->addDays(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'description' => fake()->sentence(12),
            'banner_image' => null,
            'thumbnail_image' => null,
            'badge_text' => fake()->randomElement(['3.3', '9.9', 'HOT', 'SALE']),
            'badge_color' => fake()->hexColor(),
            'discount_type' => CampaignDiscountType::Percentage,
            'discount_value' => fake()->randomFloat(2, 10, 60),
            'max_discount_amount' => fake()->randomFloat(2, 15, 300),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_active' => true,
            'created_by' => User::factory()->admin(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(2),
            'is_active' => true,
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(5),
            'is_active' => true,
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
            'is_active' => true,
        ]);
    }

    public function percentage(float $discount = 30): static
    {
        return $this->state(fn (): array => [
            'discount_type' => CampaignDiscountType::Percentage,
            'discount_value' => $discount,
            'max_discount_amount' => null,
        ]);
    }

    public function fixed(float $discount = 10): static
    {
        return $this->state(fn (): array => [
            'discount_type' => CampaignDiscountType::Fixed,
            'discount_value' => $discount,
            'max_discount_amount' => null,
        ]);
    }

    public function custom(): static
    {
        return $this->state(fn (): array => [
            'discount_type' => CampaignDiscountType::Custom,
            'discount_value' => null,
            'max_discount_amount' => null,
        ]);
    }
}
