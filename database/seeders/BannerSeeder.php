<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Mega Sale - Up to 70% Off',
                'image' => 'https://images.unsplash.com/photo-1607082349566-187342175e2f?w=1200&h=400&fit=crop',
                'link' => '/shop',
                'position' => 'hero',
                'sort_order' => 0,
            ],
            [
                'title' => 'New Electronics Collection',
                'image' => 'https://images.unsplash.com/photo-1468495244123-6c6c332eeece?w=1200&h=400&fit=crop',
                'link' => '/categories/electronics',
                'position' => 'hero',
                'sort_order' => 1,
            ],
            [
                'title' => 'Summer Fashion Trends',
                'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200&h=400&fit=crop',
                'link' => '/categories/fashion',
                'position' => 'hero',
                'sort_order' => 2,
            ],
            [
                'title' => 'Home Makeover Sale',
                'image' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=1200&h=400&fit=crop',
                'link' => '/categories/home-living',
                'position' => 'hero',
                'sort_order' => 3,
            ],
            [
                'title' => 'Beauty Week Specials',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=1200&h=400&fit=crop',
                'link' => '/categories/beauty-health',
                'position' => 'hero',
                'sort_order' => 4,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::query()->updateOrCreate(
                ['title' => $banner['title']],
                array_merge($banner, [
                    'is_active' => true,
                    'starts_at' => now()->subDay(),
                    'expires_at' => now()->addMonths(3),
                ]),
            );
        }
    }
}
