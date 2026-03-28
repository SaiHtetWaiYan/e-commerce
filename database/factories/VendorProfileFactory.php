<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VendorProfile>
 */
class VendorProfileFactory extends Factory
{
    public function definition(): array
    {
        $storeName = $this->faker->company();

        return [
            'user_id' => User::factory()->vendor(),
            'store_name' => $storeName,
            'store_slug' => Str::slug($storeName).'-'.Str::random(4),
            'is_verified' => true,
            'commission_rate' => 10,
        ];
    }
}
