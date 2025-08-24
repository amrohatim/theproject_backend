<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Helpers\UnsplashImageHelper;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all product categories (including subcategories)
        $categories = Category::where('type', 'product')->get();

        // Get all branches
        $branches = Branch::all();

        // Product templates
        $productTemplates = [
            // Electronics - Smartphones
            [
                'name' => 'Premium Smartphone X',
                'price' => 999.99,
                'original_price' => 1099.99,
                'stock' => 50,
                'description' => 'The latest flagship smartphone with cutting-edge features.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('premium smartphone', 'public/images/products/smartphone-x.jpg', 800, 600),
                'rating' => 4.8,
                'is_available' => true,
                'category_type' => 'Smartphones',
            ],
            [
                'name' => 'Budget Smartphone Y',
                'price' => 299.99,
                'original_price' => null,
                'stock' => 100,
                'description' => 'Affordable smartphone with great features for everyday use.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('budget smartphone', 'public/images/products/smartphone-y.jpg', 800, 600),
                'rating' => 4.2,
                'is_available' => true,
                'category_type' => 'Smartphones',
            ],

            // Electronics - Laptops
            [
                'name' => 'UltraBook Pro',
                'price' => 1499.99,
                'original_price' => 1699.99,
                'stock' => 30,
                'description' => 'Ultra-thin, lightweight laptop with powerful performance.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('ultrabook laptop', 'public/images/products/ultrabook-pro.jpg', 800, 600),
                'rating' => 4.7,
                'is_available' => true,
                'category_type' => 'Laptops',
            ],
            [
                'name' => 'Gaming Laptop Extreme',
                'price' => 1999.99,
                'original_price' => 2199.99,
                'stock' => 20,
                'description' => 'High-performance gaming laptop with dedicated graphics.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('gaming laptop', 'public/images/products/gaming-laptop.jpg', 800, 600),
                'rating' => 4.9,
                'is_available' => true,
                'category_type' => 'Laptops',
            ],

            // Electronics - Audio
            [
                'name' => 'Wireless Noise-Cancelling Headphones',
                'price' => 249.99,
                'original_price' => 299.99,
                'stock' => 75,
                'description' => 'Premium wireless headphones with active noise cancellation.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('noise cancelling headphones', 'public/images/products/headphones.jpg', 800, 600),
                'rating' => 4.6,
                'is_available' => true,
                'category_type' => 'Audio',
            ],
            [
                'name' => 'Portable Bluetooth Speaker',
                'price' => 79.99,
                'original_price' => 99.99,
                'stock' => 120,
                'description' => 'Compact, waterproof Bluetooth speaker with amazing sound quality.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('portable bluetooth speaker', 'public/images/products/bluetooth-speaker.jpg', 800, 600),
                'rating' => 4.5,
                'is_available' => true,
                'category_type' => 'Audio',
            ],

            // Home & Kitchen - Furniture
            [
                'name' => 'Ergonomic Office Chair',
                'price' => 199.99,
                'original_price' => 249.99,
                'stock' => 40,
                'description' => 'Comfortable, adjustable office chair with lumbar support.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('ergonomic office chair', 'public/images/products/office-chair.jpg', 800, 600),
                'rating' => 4.4,
                'is_available' => true,
                'category_type' => 'Furniture',
            ],
            [
                'name' => 'Modern Coffee Table',
                'price' => 149.99,
                'original_price' => null,
                'stock' => 25,
                'description' => 'Stylish coffee table with storage compartments.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('modern coffee table', 'public/images/products/coffee-table.jpg', 800, 600),
                'rating' => 4.3,
                'is_available' => true,
                'category_type' => 'Furniture',
            ],

            // Home & Kitchen - Appliances
            [
                'name' => 'Smart Blender Pro',
                'price' => 129.99,
                'original_price' => 149.99,
                'stock' => 60,
                'description' => 'High-powered blender with smart features and multiple settings.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('smart blender', 'public/images/products/blender.jpg', 800, 600),
                'rating' => 4.7,
                'is_available' => true,
                'category_type' => 'Appliances',
            ],
            [
                'name' => 'Air Fryer Deluxe',
                'price' => 89.99,
                'original_price' => 119.99,
                'stock' => 80,
                'description' => 'Versatile air fryer for healthier cooking with less oil.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('air fryer kitchen', 'public/images/products/air-fryer.jpg', 800, 600),
                'rating' => 4.8,
                'is_available' => true,
                'category_type' => 'Appliances',
            ],

            // Food & Beverages - Snacks
            [
                'name' => 'Gourmet Mixed Nuts',
                'price' => 24.99,
                'original_price' => null,
                'stock' => 150,
                'description' => 'Premium assortment of roasted nuts in a reusable container.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('gourmet mixed nuts', 'public/images/products/mixed-nuts.jpg', 800, 600),
                'rating' => 4.6,
                'is_available' => true,
                'category_type' => 'Snacks',
            ],
            [
                'name' => 'Artisanal Potato Chips',
                'price' => 5.99,
                'original_price' => null,
                'stock' => 200,
                'description' => 'Hand-crafted potato chips with gourmet seasonings.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('artisanal potato chips', 'public/images/products/potato-chips.jpg', 800, 600),
                'rating' => 4.5,
                'is_available' => true,
                'category_type' => 'Snacks',
            ],

            // Food & Beverages - Beverages
            [
                'name' => 'Specialty Coffee Beans',
                'price' => 18.99,
                'original_price' => 22.99,
                'stock' => 100,
                'description' => 'Freshly roasted, single-origin coffee beans.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('specialty coffee beans', 'public/images/products/coffee-beans.jpg', 800, 600),
                'rating' => 4.9,
                'is_available' => true,
                'category_type' => 'Beverages',
            ],
            [
                'name' => 'Organic Herbal Tea Collection',
                'price' => 14.99,
                'original_price' => 17.99,
                'stock' => 120,
                'description' => 'Assortment of organic herbal teas in biodegradable tea bags.',
                'image' => UnsplashImageHelper::downloadAndSaveImage('organic herbal tea', 'public/images/products/herbal-tea.jpg', 800, 600),
                'rating' => 4.7,
                'is_available' => true,
                'category_type' => 'Beverages',
            ],
        ];

        // Create products
        foreach ($productTemplates as $productTemplate) {
            // Find the appropriate category
            $category = $categories->where('name', $productTemplate['category_type'])->first();

            if ($category) {
                // Remove the category_type from the template
                $productData = array_diff_key($productTemplate, ['category_type' => '']);

                // Assign to a random branch
                $branch = $branches->random();

                // Set the category and branch IDs
                $productData['category_id'] = $category->id;
                $productData['branch_id'] = $branch->id;

                // Create the product
                Product::create($productData);
            }
        }
    }
}
