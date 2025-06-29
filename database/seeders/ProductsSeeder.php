<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories for products
        $categories = Category::where('type', 'product')->get();

        // If no product categories, create one
        if ($categories->isEmpty()) {
            $category = Category::create([
                'name' => 'General Products',
                'type' => 'product',
                'icon' => 'fa-box',
            ]);
            $categories = collect([$category]);
        }

        // Get branches
        $branches = Branch::all();

        // If no branches, skip seeding
        if ($branches->isEmpty()) {
            $this->command->info('No branches found. Skipping product seeding.');
            return;
        }

        // Sample products data
        $productsData = [
            [
                'name' => 'Premium Shampoo',
                'description' => 'High-quality shampoo for all hair types',
                'price' => 12.99,
                'stock' => 100,
                'image' => 'https://images.unsplash.com/photo-1556227702-d1e4e7b5c232?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=987&q=80',
            ],
            [
                'name' => 'Luxury Conditioner',
                'description' => 'Nourishing conditioner for smooth hair',
                'price' => 14.99,
                'stock' => 80,
                'image' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80',
            ],
            [
                'name' => 'Hair Styling Gel',
                'description' => 'Strong hold styling gel for all day control',
                'price' => 9.99,
                'stock' => 120,
                'image' => 'https://images.unsplash.com/photo-1626015449158-e1fe3b33c793?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=987&q=80',
            ],
            [
                'name' => 'Facial Cleanser',
                'description' => 'Gentle facial cleanser for daily use',
                'price' => 19.99,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1570194065650-d99fb4ee0e57?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=987&q=80',
            ],
            [
                'name' => 'Moisturizing Cream',
                'description' => 'Hydrating face cream for all skin types',
                'price' => 24.99,
                'stock' => 40,
                'image' => 'https://images.unsplash.com/photo-1567721913486-6585f069b332?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=988&q=80',
            ],
        ];

        foreach ($productsData as $productData) {
            $branch = $branches->random();
            $category = $categories->random();

            Product::create([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'image' => $productData['image'],
                'branch_id' => $branch->id,
                'category_id' => $category->id,
                'is_available' => true,
            ]);
        }
    }
}
