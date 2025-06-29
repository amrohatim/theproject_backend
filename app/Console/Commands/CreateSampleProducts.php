<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Console\Command;

class CreateSampleProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-sample-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample products and services for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating sample products and services...');

        // Get vendor user
        $vendor = User::where('email', 'vendor@example.com')->first();
        if (!$vendor) {
            $this->error('Vendor user not found! Please run app:create-vendor-user first.');
            return;
        }

        // Get branch
        $branch = Branch::where('user_id', $vendor->id)->first();
        if (!$branch) {
            $this->error('Branch not found! Please run app:create-vendor-user first.');
            return;
        }

        // Get categories
        $beautyCategory = Category::where('name', 'Beauty')->first();
        if (!$beautyCategory) {
            $this->error('Beauty category not found!');
            return;
        }

        // Create products
        $products = [
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Premium Shampoo',
                'price' => 19.99,
                'original_price' => 24.99,
                'stock' => 10,
                'description' => 'Luxury shampoo for all hair types',
                'image' => 'https://images.unsplash.com/photo-1556227834-09f1de5c1a31?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.5,
                'is_available' => true,
            ],
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Hair Conditioner',
                'price' => 29.99,
                'stock' => 5,
                'description' => 'Deep conditioning treatment',
                'image' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.2,
                'is_available' => true,
            ],
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Facial Cleanser',
                'price' => 39.99,
                'original_price' => 45.99,
                'stock' => 8,
                'description' => 'Gentle facial cleanser for sensitive skin',
                'image' => 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.8,
                'is_available' => true,
            ],
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Moisturizing Cream',
                'price' => 49.99,
                'stock' => 0,
                'description' => 'Hydrating face cream for dry skin',
                'image' => 'https://images.unsplash.com/photo-1570194065650-d99fb4a38b4b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.0,
                'is_available' => false,
            ],
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Hair Styling Gel',
                'price' => 14.99,
                'stock' => 15,
                'description' => 'Strong hold styling gel',
                'image' => 'https://images.unsplash.com/photo-1626015449158-ab95f3fb12df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 3.5,
                'is_available' => true,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::where('name', $productData['name'])->first();
            if (!$product) {
                $product = Product::create($productData);
                $this->info("Created product: {$product->name}");
            } else {
                $this->info("Product already exists: {$product->name}");
            }
        }

        // Create services
        $services = [
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Haircut & Styling',
                'price' => 49.99,
                'duration' => 60,
                'description' => 'Professional haircut and styling',
                'image' => 'https://images.unsplash.com/photo-1560869713-7d0a29430803?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.7,
                'is_available' => true,
            ],
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Facial Treatment',
                'price' => 79.99,
                'duration' => 90,
                'description' => 'Rejuvenating facial treatment',
                'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.9,
                'is_available' => true,
            ],
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Manicure & Pedicure',
                'price' => 59.99,
                'duration' => 75,
                'description' => 'Complete nail care treatment',
                'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.6,
                'is_available' => true,
            ],
            [
                'branch_id' => $branch->id,
                'category_id' => $beautyCategory->id,
                'name' => 'Full Body Massage',
                'price' => 89.99,
                'duration' => 120,
                'description' => 'Relaxing full body massage',
                'image' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'rating' => 4.8,
                'is_available' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            $service = Service::where('name', $serviceData['name'])->first();
            if (!$service) {
                $service = Service::create($serviceData);
                $this->info("Created service: {$service->name}");
            } else {
                $this->info("Service already exists: {$service->name}");
            }
        }

        $this->info('Sample products and services created successfully!');
    }
}
