<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReviewImage>
 */
class ReviewImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'file_path' => 'reviews/'.$this->faker->uuid().'.jpg',
            'media_type' => 'image',
            'sort_order' => 0,
        ];
    }

    public function video(): static
    {
        return $this->state(fn (): array => [
            'file_path' => 'reviews/'.$this->faker->uuid().'.mp4',
            'media_type' => 'video',
        ]);
    }
}
