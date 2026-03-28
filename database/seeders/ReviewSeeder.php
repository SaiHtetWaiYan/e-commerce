<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::query()->where('role', 'customer')->pluck('id')->all();

        if (empty($customers)) {
            return;
        }

        $comments = [
            5 => [
                'Excellent quality! Exactly as described. Very happy with my purchase.',
                'Love it! Fast shipping and great product. Will buy again.',
                'Amazing quality for the price. Highly recommend to everyone!',
                'Perfect! Exceeded my expectations. Five stars all the way.',
                'Best purchase I have made this year. Outstanding product.',
            ],
            4 => [
                'Good product overall. Minor issues but nothing major.',
                'Pretty good quality. Delivery was fast. Would recommend.',
                'Nice product, well packaged. Slightly smaller than expected.',
                'Solid product for the price. Happy with it overall.',
            ],
            3 => [
                'Decent product. Not bad but not amazing either.',
                'It is okay. Does the job but could be better quality.',
                'Average product. Expected a bit more for the price.',
            ],
        ];

        $products = Product::query()->inRandomOrder()->limit(30)->get();

        foreach ($products as $product) {
            $reviewCount = rand(2, 5);
            $usedCustomers = [];

            for ($i = 0; $i < $reviewCount; $i++) {
                $availableCustomers = array_diff($customers, $usedCustomers);
                if (empty($availableCustomers)) {
                    break;
                }

                $customerId = fake()->randomElement(array_values($availableCustomers));
                $usedCustomers[] = $customerId;

                $rating = fake()->randomElement([5, 5, 5, 4, 4, 4, 3]);
                $comment = fake()->randomElement($comments[$rating]);

                $review = Review::query()->create([
                    'user_id' => $customerId,
                    'product_id' => $product->id,
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_verified_purchase' => fake()->boolean(70),
                    'is_approved' => true,
                ]);

                if (fake()->boolean(35)) {
                    $review->reviewImages()->create([
                        'file_path' => 'reviews/'.fake()->uuid().'.jpg',
                        'media_type' => 'image',
                        'sort_order' => 0,
                    ]);
                }
            }
        }
    }
}
