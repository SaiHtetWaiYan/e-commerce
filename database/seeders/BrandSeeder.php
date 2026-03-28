<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Samsung', 'Apple', 'Nike', 'Adidas', 'Sony',
            'Philips', 'LG', 'Xiaomi', 'Dyson', 'IKEA',
            'Zara', 'H&M', 'Uniqlo', 'Levi\'s', 'Puma',
        ];

        foreach ($brands as $brandName) {
            Brand::query()->updateOrCreate(
                ['slug' => Str::slug($brandName)],
                [
                    'name' => $brandName,
                    'is_active' => true,
                ],
            );
        }
    }
}
