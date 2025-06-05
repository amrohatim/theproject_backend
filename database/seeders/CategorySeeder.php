<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product categories
        $productCategories = [
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and accessories',
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and fashion items',
                'image' => 'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Home & Garden',
                'description' => 'Home decor and furniture',
                'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1169&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Beauty',
                'description' => 'Beauty and personal care products',
                'image' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Sports',
                'description' => 'Sports equipment and accessories',
                'image' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
        ];

        // Service categories
        $serviceCategories = [
            [
                'name' => 'Cleaning',
                'description' => 'Cleaning services for homes and businesses',
                'image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'service',
            ],
            [
                'name' => 'Repair',
                'description' => 'Repair services for various items',
                'image' => 'https://images.unsplash.com/photo-1581092921461-39b9d904a73c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'service',
            ],
            [
                'name' => 'Beauty & Spa',
                'description' => 'Beauty and spa services',
                'image' => 'https://images.unsplash.com/photo-1560750588-73207b1ef5b8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'service',
            ],
        ];

        $categories = array_merge($productCategories, $serviceCategories);

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
