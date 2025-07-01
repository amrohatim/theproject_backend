<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestMerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test merchant user
        $merchantUser = User::firstOrCreate(
            ['email' => 'merchant@test.com'],
            [
                'name' => 'Sarah Al-Zahra',
                'password' => Hash::make('password123'),
                'role' => 'merchant',
                'phone' => '+971501234567',
                'email_verified_at' => now(),
                'phone_verified' => true,
                'phone_verified_at' => now(),
                'registration_step' => 'verified',
                'status' => 'active',
            ]
        );

        // Create merchant profile
        $merchant = Merchant::firstOrCreate(
            ['user_id' => $merchantUser->id],
            [
            'user_id' => $merchantUser->id,
            'business_name' => 'Sarah\'s Handmade Crafts',
            'business_type' => 'Handicrafts & Accessories',
            'description' => 'Beautiful handmade jewelry, accessories, and home decor items crafted with love and attention to detail. Supporting local artisans and traditional craftsmanship.',
            'address' => 'Al Wasl Road, Jumeirah',
            'city' => 'Dubai',
            'state' => 'Dubai',
            'postal_code' => '12345',
            'country' => 'UAE',
            'emirate' => 'Dubai',
            'website' => 'https://sarahscrafts.ae',
            'status' => 'active',
            'is_verified' => true,
            'average_rating' => 4.8,
            'total_ratings' => 127,
            'view_count' => 1250,
            'order_count' => 89,
            'merchant_score' => 850,
            'last_score_calculation' => now(),
            'store_location_lat' => 25.2048,
            'store_location_lng' => 55.2708,
            'store_location_address' => 'Al Wasl Road, Jumeirah, Dubai, UAE',
            'delivery_capability' => true,
            'delivery_fees' => [
                'Dubai' => 15.00,
                'Abu Dhabi' => 25.00,
                'Sharjah' => 20.00,
                'Ajman' => 25.00,
                'Ras Al Khaimah' => 30.00,
                'Fujairah' => 35.00,
                'Umm Al Quwain' => 30.00,
            ],
            ]
        );

        // Get or create categories
        $jewelryCategory = Category::firstOrCreate(
            ['name' => 'Jewelry & Accessories'],
            ['description' => 'Handmade jewelry and fashion accessories', 'is_active' => true, 'type' => 'product']
        );

        $homeDecorCategory = Category::firstOrCreate(
            ['name' => 'Home Decor'],
            ['description' => 'Decorative items for home and office', 'is_active' => true, 'type' => 'product']
        );

        $servicesCategory = Category::firstOrCreate(
            ['name' => 'Custom Services'],
            ['description' => 'Custom design and crafting services', 'is_active' => true, 'type' => 'service']
        );

        // Create sample products
        $products = [
            [
                'name' => 'Handmade Silver Earrings',
                'description' => 'Elegant silver earrings with traditional Arabic patterns. Handcrafted using sterling silver and featuring intricate geometric designs inspired by Islamic art.',
                'price' => 125.00,
                'category_id' => $jewelryCategory->id,
                'stock' => 15,
                'sku' => 'HSE001',
                'is_available' => true,
            ],
            [
                'name' => 'Beaded Bracelet Set',
                'description' => 'Set of 3 beautiful beaded bracelets made with natural gemstones including turquoise, amethyst, and rose quartz. Perfect for layering or wearing individually.',
                'price' => 85.00,
                'category_id' => $jewelryCategory->id,
                'stock' => 22,
                'sku' => 'BBS002',
                'is_available' => true,
            ],
            [
                'name' => 'Moroccan Lantern',
                'description' => 'Authentic Moroccan-style lantern handcrafted with brass and colored glass. Creates beautiful ambient lighting for any space.',
                'price' => 180.00,
                'category_id' => $homeDecorCategory->id,
                'stock' => 8,
                'sku' => 'ML003',
                'is_available' => true,
            ],
            [
                'name' => 'Embroidered Cushion Cover',
                'description' => 'Luxurious cushion cover with traditional embroidery work. Made from high-quality cotton with gold thread detailing.',
                'price' => 65.00,
                'category_id' => $homeDecorCategory->id,
                'stock' => 30,
                'sku' => 'ECC004',
                'is_available' => true,
            ],
            [
                'name' => 'Pearl Necklace',
                'description' => 'Classic pearl necklace with freshwater pearls. Timeless elegance suitable for both casual and formal occasions.',
                'price' => 220.00,
                'category_id' => $jewelryCategory->id,
                'stock' => 5,
                'sku' => 'PN005',
                'is_available' => true,
            ],
            [
                'name' => 'Ceramic Vase Set',
                'description' => 'Set of 2 handpainted ceramic vases with traditional patterns. Perfect for displaying flowers or as standalone decorative pieces.',
                'price' => 95.00,
                'category_id' => $homeDecorCategory->id,
                'stock' => 12,
                'sku' => 'CVS006',
                'is_available' => true,
            ],
        ];

        // Get a branch for the merchant (we'll use the first available branch or create one)
        $branch = \App\Models\Branch::first();
        if (!$branch) {
            // Create a default branch if none exists
            $branch = \App\Models\Branch::create([
                'user_id' => $merchantUser->id,
                'name' => 'Main Store',
                'address' => 'Al Wasl Road, Jumeirah, Dubai',
                'emirate' => 'Dubai',
                'lat' => 25.2048,
                'lng' => 55.2708,
                'status' => 'active',
                'phone' => '+971501234567',
                'email' => 'merchant@test.com',
            ]);
        }

        foreach ($products as $productData) {
            $productData['user_id'] = $merchantUser->id;
            $productData['branch_id'] = $branch->id;
            Product::create($productData);
        }

        // Create sample services
        $services = [
            [
                'name' => 'Custom Jewelry Design',
                'description' => 'Personalized jewelry design service. Work with our artisan to create a unique piece tailored to your preferences and style.',
                'price' => 300.00,
                'category_id' => $servicesCategory->id,
                'duration' => 2016, // 2 weeks in minutes
                'is_available' => true,
            ],
            [
                'name' => 'Jewelry Repair & Restoration',
                'description' => 'Professional jewelry repair and restoration services. Bring new life to your precious pieces with expert craftsmanship.',
                'price' => 75.00,
                'category_id' => $servicesCategory->id,
                'duration' => 4320, // 3 days in minutes
                'is_available' => true,
            ],
            [
                'name' => 'Home Decor Consultation',
                'description' => 'Personalized home decor consultation to help you choose the perfect handmade pieces for your space.',
                'price' => 150.00,
                'category_id' => $servicesCategory->id,
                'duration' => 120, // 2 hours in minutes
                'is_available' => true,
            ],
            [
                'name' => 'Custom Embroidery Work',
                'description' => 'Custom embroidery services for cushions, wall hangings, and other textile items. Choose your own patterns and colors.',
                'price' => 120.00,
                'category_id' => $servicesCategory->id,
                'duration' => 1008, // 1 week in minutes
                'is_available' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            $serviceData['branch_id'] = $branch->id;
            Service::create($serviceData);
        }

        $this->command->info('✅ Test merchant account created successfully!');
        $this->command->info('📧 Email: merchant@test.com');
        $this->command->info('🔑 Password: password123');
        $this->command->info('🏪 Business: Sarah\'s Handmade Crafts');
        $this->command->info('📊 Status: Verified and Active');
        $this->command->info('📦 Products: 6 sample products created');
        $this->command->info('🛠️ Services: 4 sample services created');
        $this->command->info('🌐 Access: https://dala3chic.com/merchant/dashboard');
    }
}
