<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Marketplace Admin',
            'email' => 'admin@marketplace.test',
        ]);

        User::factory()->deliveryAgent()->create([
            'name' => 'Express Courier',
            'email' => 'delivery@marketplace.test',
        ]);

        User::factory(10)->create();

        $stores = [
            ['name' => 'TechWorld Official', 'desc' => 'Your one-stop shop for the latest electronics, gadgets, and tech accessories. Authorized dealer for top brands.'],
            ['name' => 'Fashion Hub', 'desc' => 'Trendy and affordable fashion for men, women, and kids. New arrivals every week!'],
            ['name' => 'HomeStyle Living', 'desc' => 'Transform your home with our curated collection of furniture, decor, and kitchen essentials.'],
            ['name' => 'Beauty Palace', 'desc' => 'Premium skincare, makeup, and beauty products from trusted brands. Glow with confidence!'],
            ['name' => 'SportZone Pro', 'desc' => 'Gear up for your active lifestyle. Sports equipment, apparel, and accessories for every athlete.'],
        ];

        $vendors = User::factory(5)->vendor()->create();

        foreach ($vendors as $index => $vendor) {
            $store = $stores[$index];
            VendorProfile::query()->create([
                'user_id' => $vendor->id,
                'store_name' => $store['name'],
                'store_slug' => Str::slug($store['name']),
                'store_description' => $store['desc'],
                'commission_rate' => 10.00,
                'is_verified' => $index < 3,
                'verified_at' => $index < 3 ? now() : null,
                'bank_name' => 'National Bank',
                'bank_account_number' => 'ACC'.str_pad($vendor->id, 8, '0', STR_PAD_LEFT),
                'bank_account_name' => $vendor->name,
            ]);
        }

        if ($admin->role !== UserRole::Admin) {
            $admin->update(['role' => UserRole::Admin]);
        }
    }
}
