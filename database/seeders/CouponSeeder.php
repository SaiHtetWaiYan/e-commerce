<?php

namespace Database\Seeders;

use App\Enums\CouponType;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = User::query()->vendors()->get();

        foreach ($vendors as $index => $vendor) {
            Coupon::query()->updateOrCreate(
                ['code' => 'VENDOR'.($index + 1).'SAVE10'],
                [
                    'vendor_id' => $vendor->id,
                    'description' => '10% off selected items',
                    'type' => CouponType::Percentage,
                    'value' => 10,
                    'min_order_amount' => 50,
                    'usage_limit' => 100,
                    'per_user_limit' => 1,
                    'starts_at' => now()->subDay(),
                    'expires_at' => now()->addMonths(2),
                    'is_active' => true,
                ],
            );
        }
    }
}
