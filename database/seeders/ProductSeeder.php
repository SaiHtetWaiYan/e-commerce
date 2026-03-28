<?php

namespace Database\Seeders;

use App\Enums\ProductStatus;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = User::query()->vendors()->with('vendorProfile')->get();
        $brands = Brand::query()->pluck('id', 'name')->all();
        $categories = Category::query()->pluck('id', 'name')->all();

        if ($vendors->isEmpty() || empty($brands) || empty($categories)) {
            return;
        }

        $productData = $this->getProductDefinitions();

        foreach ($vendors as $vendorIndex => $vendor) {
            $vendorProducts = $productData[$vendorIndex % count($productData)];

            foreach ($vendorProducts as $index => $item) {
                $name = $item['name'];
                $product = Product::query()->create([
                    'vendor_id' => $vendor->id,
                    'brand_id' => $this->resolveBrandId($item['brand'] ?? null, $brands),
                    'name' => $name,
                    'slug' => Str::slug($name.'-'.($vendorIndex * 100 + $index)),
                    'description' => $item['description'],
                    'short_description' => $item['short_desc'],
                    'base_price' => $item['price'],
                    'compare_price' => $item['compare_price'] ?? null,
                    'sku' => strtoupper(Str::random(10)),
                    'stock_quantity' => fake()->numberBetween(20, 500),
                    'low_stock_threshold' => 5,
                    'status' => ProductStatus::Active,
                    'is_featured' => $index < 3,
                    'meta_title' => $name,
                    'meta_description' => Str::limit($item['short_desc'], 160),
                    'total_sold' => fake()->numberBetween(50, 5000),
                    'avg_rating' => fake()->randomFloat(1, 3.5, 5.0),
                    'review_count' => fake()->numberBetween(10, 500),
                ]);

                $catNames = (array) ($item['categories'] ?? []);
                $catIds = array_filter(array_map(fn ($c) => $categories[$c] ?? null, $catNames));
                if (empty($catIds)) {
                    $catIds = [Arr::random(array_values($categories))];
                }
                $product->categories()->sync($catIds);

                $images = $item['images'] ?? [];
                foreach ($images as $imgIndex => $imageUrl) {
                    $product->images()->create([
                        'image_path' => $imageUrl,
                        'alt_text' => $name,
                        'sort_order' => $imgIndex,
                        'is_primary' => $imgIndex === 0,
                    ]);
                }
            }
        }
    }

    /**
     * @return array<int, array<int, array<string, mixed>>>
     */
    private function getProductDefinitions(): array
    {
        return [
            // Electronics store products
            [
                [
                    'name' => 'Wireless Bluetooth Headphones Pro',
                    'short_desc' => 'Active noise cancelling over-ear headphones with 30hr battery.',
                    'description' => 'Experience premium sound quality with our Wireless Bluetooth Headphones Pro. Features active noise cancellation, 30-hour battery life, comfortable memory foam ear cushions, and crystal-clear microphone for calls. Compatible with all Bluetooth devices.',
                    'price' => 89.99,
                    'compare_price' => 149.99,
                    'brand' => 'Sony',
                    'categories' => ['Headphones & Earbuds'],
                    'images' => [
                        'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&h=500&fit=crop',
                        'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=500&h=500&fit=crop',
                        'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Smart Watch Fitness Tracker Ultra',
                    'short_desc' => 'Advanced fitness tracking with heart rate, GPS, and sleep monitoring.',
                    'description' => 'Stay on top of your health with the Smart Watch Fitness Tracker Ultra. Features heart rate monitoring, built-in GPS, sleep tracking, 50m water resistance, and a stunning AMOLED display. Over 100 workout modes included.',
                    'price' => 199.99,
                    'compare_price' => 299.99,
                    'brand' => 'Samsung',
                    'categories' => ['Smartwatches'],
                    'images' => [
                        'https://images.unsplash.com/photo-1546868871-af0de0ae72be?w=500&h=500&fit=crop',
                        'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Portable Bluetooth Speaker Waterproof',
                    'short_desc' => 'Compact waterproof speaker with powerful 360-degree sound.',
                    'description' => 'Take your music anywhere with this portable Bluetooth speaker. IPX7 waterproof rating, 12-hour battery life, 360-degree sound, built-in microphone for speakerphone, and compact design perfect for outdoor adventures.',
                    'price' => 45.99,
                    'compare_price' => 69.99,
                    'brand' => 'Sony',
                    'categories' => ['Headphones & Earbuds'],
                    'images' => [
                        'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Laptop Stand Adjustable Aluminum',
                    'short_desc' => 'Ergonomic aluminum laptop stand with 6 height adjustments.',
                    'description' => 'Improve your posture and productivity with this premium aluminum laptop stand. 6 adjustable height positions, ventilated design for cooling, supports up to 17" laptops, foldable for portability.',
                    'price' => 35.99,
                    'compare_price' => 55.00,
                    'brand' => 'Apple',
                    'categories' => ['Laptops & Computers'],
                    'images' => [
                        'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'USB-C Hub 7-in-1 Multiport Adapter',
                    'short_desc' => '7-in-1 USB-C hub with HDMI, USB 3.0, SD card reader.',
                    'description' => 'Expand your connectivity with this versatile 7-in-1 USB-C hub. Includes HDMI 4K output, 3x USB 3.0 ports, SD/microSD card readers, and USB-C power delivery. Perfect for MacBook and laptops.',
                    'price' => 29.99,
                    'compare_price' => 49.99,
                    'categories' => ['Laptops & Computers'],
                    'images' => [
                        'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Wireless Charging Pad Fast Charge',
                    'short_desc' => '15W fast wireless charging pad compatible with all Qi devices.',
                    'description' => 'Charge your phone wirelessly with this sleek 15W fast charging pad. Compatible with all Qi-enabled devices, LED indicator, anti-slip surface, and over-charge protection.',
                    'price' => 19.99,
                    'compare_price' => 34.99,
                    'brand' => 'Samsung',
                    'categories' => ['Smartphones'],
                    'images' => [
                        'https://images.unsplash.com/photo-1586816879360-004f5b0c51e3?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Mechanical Gaming Keyboard RGB',
                    'short_desc' => 'Full-size mechanical keyboard with per-key RGB backlighting.',
                    'description' => 'Dominate your games with this mechanical keyboard featuring Cherry MX switches, per-key RGB lighting, macro keys, detachable wrist rest, and USB passthrough. Built with aircraft-grade aluminum.',
                    'price' => 79.99,
                    'compare_price' => 129.99,
                    'categories' => ['Laptops & Computers'],
                    'images' => [
                        'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Noise Cancelling Earbuds Wireless',
                    'short_desc' => 'True wireless earbuds with ANC and 24hr total battery life.',
                    'description' => 'Premium true wireless earbuds with active noise cancellation, transparency mode, 8-hour battery (24hr with case), IPX4 water resistance, and premium sound drivers for an immersive audio experience.',
                    'price' => 59.99,
                    'compare_price' => 99.99,
                    'brand' => 'Apple',
                    'categories' => ['Headphones & Earbuds'],
                    'images' => [
                        'https://images.unsplash.com/photo-1590658268037-6bf12f032f55?w=500&h=500&fit=crop',
                    ],
                ],
            ],

            // Fashion store products
            [
                [
                    'name' => 'Classic Slim Fit Cotton T-Shirt',
                    'short_desc' => 'Premium cotton crew neck t-shirt for everyday comfort.',
                    'description' => 'Crafted from 100% premium combed cotton, this classic slim fit t-shirt offers exceptional comfort and durability. Pre-shrunk fabric, reinforced stitching, and available in multiple colors.',
                    'price' => 24.99,
                    'compare_price' => 39.99,
                    'brand' => 'Uniqlo',
                    'categories' => ["Men's Clothing"],
                    'images' => [
                        'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500&h=500&fit=crop',
                        'https://images.unsplash.com/photo-1562157873-818bc0726f68?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Women\'s High Waist Denim Jeans',
                    'short_desc' => 'Stretch denim skinny jeans with comfortable high waist design.',
                    'description' => 'Flattering high-waist design with premium stretch denim for all-day comfort. Classic 5-pocket styling, durable construction, and a perfect skinny fit that pairs with everything.',
                    'price' => 49.99,
                    'compare_price' => 79.99,
                    'brand' => 'Levi\'s',
                    'categories' => ["Women's Clothing"],
                    'images' => [
                        'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Casual Canvas Sneakers Unisex',
                    'short_desc' => 'Classic canvas low-top sneakers for casual everyday wear.',
                    'description' => 'Timeless canvas sneakers with vulcanized rubber sole, cushioned insole, and breathable canvas upper. Perfect for casual outings and everyday style.',
                    'price' => 39.99,
                    'compare_price' => 59.99,
                    'brand' => 'Nike',
                    'categories' => ['Shoes'],
                    'images' => [
                        'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Leather Crossbody Bag Premium',
                    'short_desc' => 'Genuine leather crossbody bag with adjustable strap.',
                    'description' => 'Crafted from genuine full-grain leather, this crossbody bag features multiple compartments, adjustable strap, magnetic closure, and elegant gold-tone hardware. Perfect for work or casual outings.',
                    'price' => 68.99,
                    'compare_price' => 120.00,
                    'categories' => ['Bags & Wallets'],
                    'images' => [
                        'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Running Shoes Lightweight Mesh',
                    'short_desc' => 'Ultra-lightweight running shoes with breathable mesh upper.',
                    'description' => 'Engineered for performance with breathable mesh upper, responsive cushioning, and durable rubber outsole. Reflective elements for visibility and a sock-like fit for maximum comfort.',
                    'price' => 89.99,
                    'compare_price' => 139.99,
                    'brand' => 'Nike',
                    'categories' => ['Shoes', 'Running'],
                    'images' => [
                        'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=500&h=500&fit=crop',
                        'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Minimalist Analog Watch Classic',
                    'short_desc' => 'Elegant minimalist watch with genuine leather strap.',
                    'description' => 'Timeless minimalist design with Japanese quartz movement, scratch-resistant sapphire crystal, genuine leather strap, and 30m water resistance. A perfect accessory for any occasion.',
                    'price' => 129.99,
                    'compare_price' => 199.99,
                    'categories' => ['Watches'],
                    'images' => [
                        'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Polarized Sunglasses UV Protection',
                    'short_desc' => 'Stylish polarized sunglasses with 100% UV protection.',
                    'description' => 'Premium polarized lenses with 100% UV400 protection. Lightweight TR90 frame, spring hinges for comfortable fit, and anti-glare coating. Perfect for driving and outdoor activities.',
                    'price' => 34.99,
                    'compare_price' => 59.99,
                    'categories' => ['Accessories'],
                    'images' => [
                        'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Hooded Zip-Up Sweatshirt Fleece',
                    'short_desc' => 'Cozy fleece-lined hoodie with full zip and kangaroo pockets.',
                    'description' => 'Stay warm with this cozy fleece-lined zip-up hoodie. Features front kangaroo pockets, ribbed cuffs and hem, adjustable drawstring hood, and soft brushed interior.',
                    'price' => 44.99,
                    'compare_price' => 69.99,
                    'brand' => 'Adidas',
                    'categories' => ["Men's Clothing"],
                    'images' => [
                        'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=500&h=500&fit=crop',
                    ],
                ],
            ],

            // Home & Living store products
            [
                [
                    'name' => 'Memory Foam Pillow Set of 2',
                    'short_desc' => 'Contour memory foam pillows for neck and back support.',
                    'description' => 'Premium memory foam pillows with contour design for optimal neck support. Hypoallergenic, removable bamboo cover, breathable design, and medium firmness for all sleeping positions.',
                    'price' => 39.99,
                    'compare_price' => 69.99,
                    'categories' => ['Bedding'],
                    'images' => [
                        'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Stainless Steel Kitchen Knife Set',
                    'short_desc' => 'Professional 6-piece knife set with wooden block.',
                    'description' => 'Professional-grade kitchen knife set crafted from high-carbon stainless steel. Includes chef\'s knife, bread knife, utility knife, paring knife, kitchen shears, and elegant wooden block. Razor-sharp blades with ergonomic handles.',
                    'price' => 79.99,
                    'compare_price' => 149.99,
                    'categories' => ['Kitchen & Dining'],
                    'images' => [
                        'https://images.unsplash.com/photo-1593618998160-e34014e67546?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Scented Candle Set Aromatherapy',
                    'short_desc' => 'Natural soy wax candles in 3 calming scents.',
                    'description' => 'Handcrafted natural soy wax candles with cotton wicks. Set includes Lavender, Vanilla, and Ocean Breeze scents. 40-hour burn time each. Perfect for relaxation and home decor.',
                    'price' => 28.99,
                    'compare_price' => 45.00,
                    'categories' => ['Bedding'],
                    'images' => [
                        'https://images.unsplash.com/photo-1602028915047-37269d1a73f7?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'LED Desk Lamp Touch Control',
                    'short_desc' => 'Adjustable LED desk lamp with 5 brightness levels.',
                    'description' => 'Modern LED desk lamp with touch-sensitive controls, 5 brightness levels, 3 color temperatures, flexible gooseneck, USB charging port, and eye-care technology for reduced eye strain.',
                    'price' => 32.99,
                    'compare_price' => 54.99,
                    'categories' => ['Lighting'],
                    'images' => [
                        'https://images.unsplash.com/photo-1507473885765-e6ed057ab6fe?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Bamboo Storage Organizer Box Set',
                    'short_desc' => 'Eco-friendly bamboo storage boxes in 3 sizes.',
                    'description' => 'Beautiful natural bamboo storage boxes in small, medium, and large sizes. Features woven lids, reinforced bottoms, and stackable design. Perfect for organizing closets, shelves, and bathrooms.',
                    'price' => 24.99,
                    'compare_price' => 42.00,
                    'categories' => ['Storage'],
                    'images' => [
                        'https://images.unsplash.com/photo-1595428774223-ef52624120d2?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Ceramic Plant Pot Set Modern',
                    'short_desc' => 'Set of 3 minimalist ceramic pots with drainage holes.',
                    'description' => 'Elevate your plant game with these modern ceramic pots. Set of 3 in varying sizes, each with drainage hole and bamboo saucer. Matte finish in neutral colors that complement any decor.',
                    'price' => 34.99,
                    'compare_price' => 55.00,
                    'categories' => ['Furniture'],
                    'images' => [
                        'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Turkish Cotton Bath Towel Set',
                    'short_desc' => 'Luxury Turkish cotton towels, set of 4 with bath mat.',
                    'description' => 'Indulge in luxury with these 100% Turkish cotton towels. Extra thick, highly absorbent, and incredibly soft. Set includes 2 bath towels, 2 hand towels, and 1 bath mat.',
                    'price' => 54.99,
                    'compare_price' => 89.99,
                    'categories' => ['Bathroom'],
                    'images' => [
                        'https://images.unsplash.com/photo-1616627561839-074385245ff6?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Non-Stick Cookware Set 10-Piece',
                    'short_desc' => 'Complete non-stick cookware set with tempered glass lids.',
                    'description' => 'Everything you need in the kitchen! 10-piece non-stick cookware set includes frying pans, saucepans, stockpot, and tempered glass lids. PFOA-free coating, oven safe to 450F, and dishwasher safe.',
                    'price' => 99.99,
                    'compare_price' => 179.99,
                    'categories' => ['Kitchen & Dining'],
                    'images' => [
                        'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=500&h=500&fit=crop',
                    ],
                ],
            ],

            // Beauty store products
            [
                [
                    'name' => 'Vitamin C Brightening Serum',
                    'short_desc' => '20% Vitamin C face serum for glowing, even-toned skin.',
                    'description' => 'Transform your skin with this potent 20% Vitamin C serum enriched with Hyaluronic Acid and Vitamin E. Brightens dark spots, reduces fine lines, and evens skin tone. Suitable for all skin types.',
                    'price' => 22.99,
                    'compare_price' => 45.00,
                    'categories' => ['Skincare'],
                    'images' => [
                        'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Hydrating Face Moisturizer SPF 30',
                    'short_desc' => 'Daily moisturizer with SPF 30 broad spectrum protection.',
                    'description' => 'All-in-one daily moisturizer with SPF 30 broad spectrum protection. Lightweight, non-greasy formula that hydrates deeply while protecting against UV damage. Contains Niacinamide and Ceramides.',
                    'price' => 18.99,
                    'compare_price' => 32.00,
                    'categories' => ['Skincare'],
                    'images' => [
                        'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Matte Lipstick Long-Lasting Set',
                    'short_desc' => '6-piece matte lipstick set in trending shades.',
                    'description' => 'Express yourself with this collection of 6 stunning matte lipstick shades. Ultra-long-lasting formula that stays put for up to 12 hours. Moisturizing ingredients prevent dryness. Cruelty-free and vegan.',
                    'price' => 29.99,
                    'compare_price' => 54.99,
                    'categories' => ['Makeup'],
                    'images' => [
                        'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Argan Oil Hair Treatment',
                    'short_desc' => 'Pure Moroccan argan oil for smooth, shiny hair.',
                    'description' => 'Restore shine and vitality with 100% pure Moroccan argan oil. Tames frizz, repairs split ends, and adds brilliant shine. Lightweight, non-greasy formula suitable for all hair types.',
                    'price' => 16.99,
                    'compare_price' => 28.00,
                    'categories' => ['Haircare'],
                    'images' => [
                        'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Professional Makeup Brush Set',
                    'short_desc' => '12-piece makeup brush set with vegan synthetic bristles.',
                    'description' => 'Complete your beauty toolkit with this professional 12-piece makeup brush set. Vegan synthetic bristles, ergonomic handles, and a stylish travel case. Includes brushes for foundation, powder, eye shadow, and more.',
                    'price' => 34.99,
                    'compare_price' => 64.99,
                    'categories' => ['Makeup'],
                    'images' => [
                        'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Natural Body Lotion Aloe & Shea',
                    'short_desc' => 'Nourishing body lotion with aloe vera and shea butter.',
                    'description' => 'Deep nourishment for your skin with natural aloe vera and shea butter. Absorbs quickly, non-greasy formula, delicate natural fragrance. Paraben-free and dermatologically tested.',
                    'price' => 14.99,
                    'compare_price' => 24.99,
                    'categories' => ['Skincare'],
                    'images' => [
                        'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Eau de Parfum Floral Elegance',
                    'short_desc' => 'Elegant floral perfume with notes of rose, jasmine, and musk.',
                    'description' => 'A sophisticated blend of Bulgarian rose, jasmine, and white musk. This elegant eau de parfum lasts up to 8 hours with moderate sillage. 50ml glass bottle with atomizer spray.',
                    'price' => 55.99,
                    'compare_price' => 89.99,
                    'categories' => ['Fragrances'],
                    'images' => [
                        'https://images.unsplash.com/photo-1541643600914-78b084683601?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Sheet Mask Variety Pack 10 Masks',
                    'short_desc' => 'Assorted face masks for hydration, brightening, and firming.',
                    'description' => 'Pamper your skin with this variety pack of 10 sheet masks. Includes Hyaluronic Acid, Vitamin C, Collagen, Green Tea, and Snail Mucin masks. Premium bio-cellulose material for maximum absorption.',
                    'price' => 19.99,
                    'compare_price' => 35.00,
                    'categories' => ['Skincare'],
                    'images' => [
                        'https://images.unsplash.com/photo-1596755389378-c31d21fd1273?w=500&h=500&fit=crop',
                    ],
                ],
            ],

            // Sports store products
            [
                [
                    'name' => 'Yoga Mat Premium Non-Slip',
                    'short_desc' => 'Extra thick 6mm yoga mat with alignment lines.',
                    'description' => 'Premium yoga mat with 6mm thickness for joint protection. Non-slip texture on both sides, laser-etched alignment lines, eco-friendly TPE material, and includes carrying strap.',
                    'price' => 29.99,
                    'compare_price' => 49.99,
                    'brand' => 'Nike',
                    'categories' => ['Exercise Equipment'],
                    'images' => [
                        'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Resistance Bands Set 5 Levels',
                    'short_desc' => 'Color-coded resistance bands from light to extra heavy.',
                    'description' => 'Versatile set of 5 resistance bands with different resistance levels. Made from natural latex, includes door anchor, handles, ankle straps, and carrying bag. Perfect for home workouts.',
                    'price' => 19.99,
                    'compare_price' => 34.99,
                    'categories' => ['Exercise Equipment'],
                    'images' => [
                        'https://images.unsplash.com/photo-1598289431512-b97b0917affc?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Sports Water Bottle Insulated 1L',
                    'short_desc' => 'Double-wall insulated water bottle keeps cold 24hrs.',
                    'description' => 'Stay hydrated with this premium insulated water bottle. Double-wall vacuum insulation keeps drinks cold for 24 hours or hot for 12. BPA-free, leak-proof lid, wide mouth opening.',
                    'price' => 24.99,
                    'compare_price' => 39.99,
                    'categories' => ['Exercise Equipment'],
                    'images' => [
                        'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Compression Sports Leggings',
                    'short_desc' => 'High-performance compression leggings with pocket.',
                    'description' => 'Engineered for peak performance with 4-way stretch compression fabric. Features side pockets, high waistband, moisture-wicking technology, and flatlock seams for chafe-free comfort.',
                    'price' => 42.99,
                    'compare_price' => 69.99,
                    'brand' => 'Adidas',
                    'categories' => ['Sportswear'],
                    'images' => [
                        'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Adjustable Dumbbell Set 20kg',
                    'short_desc' => 'Quick-change adjustable dumbbells from 2.5kg to 20kg.',
                    'description' => 'Replace an entire rack of dumbbells with one set. Quick-change mechanism allows you to adjust from 2.5kg to 20kg in seconds. Compact design, anti-slip grip, and durable steel construction.',
                    'price' => 149.99,
                    'compare_price' => 249.99,
                    'categories' => ['Exercise Equipment'],
                    'images' => [
                        'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Camping Tent 4-Person Waterproof',
                    'short_desc' => 'Easy-setup 4-person tent with rain fly and vestibule.',
                    'description' => 'Adventure awaits with this spacious 4-person camping tent. Features instant pop-up setup, waterproof rain fly, mesh windows for ventilation, and a large vestibule for gear storage.',
                    'price' => 89.99,
                    'compare_price' => 159.99,
                    'categories' => ['Camping & Hiking'],
                    'images' => [
                        'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Running Backpack Lightweight 15L',
                    'short_desc' => 'Breathable running backpack with hydration pocket.',
                    'description' => 'Ultra-lightweight 15L backpack designed for runners and hikers. Breathable mesh back panel, hydration bladder compatible, reflective elements, and multiple pockets for organization.',
                    'price' => 39.99,
                    'compare_price' => 64.99,
                    'brand' => 'Nike',
                    'categories' => ['Running'],
                    'images' => [
                        'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&h=500&fit=crop',
                    ],
                ],
                [
                    'name' => 'Cycling Helmet Lightweight Safety',
                    'short_desc' => 'Ventilated cycling helmet with MIPS safety system.',
                    'description' => 'Safety meets style with this lightweight cycling helmet. Features MIPS brain protection system, 18 ventilation channels, adjustable fit dial, removable visor, and integrated LED rear light.',
                    'price' => 59.99,
                    'compare_price' => 99.99,
                    'categories' => ['Cycling'],
                    'images' => [
                        'https://images.unsplash.com/photo-1557803175-2dfb2e4ad2c7?w=500&h=500&fit=crop',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param  array<string, int>  $brands
     */
    private function resolveBrandId(?string $brandName, array $brands): ?int
    {
        if (! $brandName || ! isset($brands[$brandName])) {
            return Arr::random(array_values($brands));
        }

        return $brands[$brandName];
    }
}
