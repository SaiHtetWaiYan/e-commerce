<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_id' => Product::factory(),
            'order_item_id' => null,
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional(0.8)->paragraph(),
            'images' => null,
            'is_verified_purchase' => false,
            'is_approved' => true,
            'vendor_reply' => null,
            'vendor_replied_at' => null,
        ];
    }

    public function verifiedPurchase(): static
    {
        return $this->state(fn (): array => [
            'is_verified_purchase' => true,
        ]);
    }

    public function unapproved(): static
    {
        return $this->state(fn (): array => [
            'is_approved' => false,
        ]);
    }

    public function withVendorReply(): static
    {
        return $this->state(fn (): array => [
            'vendor_reply' => $this->faker->paragraph(),
            'vendor_replied_at' => now(),
        ]);
    }

    public function withImages(): static
    {
        return $this->afterCreating(function (Review $review): void {
            $review->reviewImages()->createMany([
                [
                    'file_path' => 'reviews/'.$this->faker->uuid().'.jpg',
                    'media_type' => 'image',
                    'sort_order' => 0,
                ],
                [
                    'file_path' => 'reviews/'.$this->faker->uuid().'.jpg',
                    'media_type' => 'image',
                    'sort_order' => 1,
                ],
            ]);
        });
    }
}
