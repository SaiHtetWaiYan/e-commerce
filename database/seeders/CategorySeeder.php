<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            'Electronics' => [
                'icon' => "\u{1F4F1}",
                'children' => ['Smartphones', 'Laptops & Computers', 'Tablets', 'Headphones & Earbuds', 'Cameras', 'Smartwatches'],
            ],
            'Fashion' => [
                'icon' => "\u{1F455}",
                'children' => ["Women's Clothing", "Men's Clothing", 'Shoes', 'Bags & Wallets', 'Accessories', 'Watches'],
            ],
            'Home & Living' => [
                'icon' => "\u{1F3E0}",
                'children' => ['Furniture', 'Kitchen & Dining', 'Bedding', 'Bathroom', 'Storage', 'Lighting'],
            ],
            'Beauty & Health' => [
                'icon' => "\u{2728}",
                'children' => ['Skincare', 'Makeup', 'Haircare', 'Fragrances', 'Health Supplements'],
            ],
            'Sports & Outdoors' => [
                'icon' => "\u{26BD}",
                'children' => ['Exercise Equipment', 'Sportswear', 'Camping & Hiking', 'Cycling', 'Running'],
            ],
            'Toys & Kids' => [
                'icon' => "\u{1F9F8}",
                'children' => ['Educational Toys', 'Outdoor Play', 'Baby Gear', 'Kids Clothing'],
            ],
        ];

        $sortOrder = 0;

        foreach ($definitions as $parentName => $data) {
            $parent = Category::query()->updateOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'icon' => $data['icon'],
                    'description' => "Browse our {$parentName} collection",
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                    '_lft' => $sortOrder * 20,
                    '_rgt' => $sortOrder * 20 + 19,
                ],
            );

            foreach ($data['children'] as $index => $childName) {
                Category::query()->updateOrCreate(
                    ['slug' => Str::slug($parentName.'-'.$childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'description' => "{$childName} in {$parentName}",
                        'is_active' => true,
                        'sort_order' => $index,
                        '_lft' => $parent->_lft + ($index * 2) + 1,
                        '_rgt' => $parent->_lft + ($index * 2) + 2,
                    ],
                );
            }
        }
    }
}
