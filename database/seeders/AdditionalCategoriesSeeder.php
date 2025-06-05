<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class AdditionalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Additional product categories
        $productCategories = [
            [
                'name' => 'Automotive',
                'description' => 'Car parts, accessories, and maintenance products',
                'image' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Books',
                'description' => 'Books, e-books, and audiobooks',
                'image' => 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Toys & Games',
                'description' => 'Toys, games, and entertainment products for all ages',
                'image' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Food & Beverages',
                'description' => 'Food products, drinks, and groceries',
                'image' => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
            [
                'name' => 'Health & Wellness',
                'description' => 'Health supplements, fitness equipment, and wellness products',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'product',
            ],
        ];
        
        // Additional service categories
        $serviceCategories = [
            [
                'name' => 'Education & Tutoring',
                'description' => 'Educational services and tutoring',
                'image' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1122&q=80',
                'is_active' => true,
                'type' => 'service',
            ],
            [
                'name' => 'Home Services',
                'description' => 'Services for home maintenance and improvement',
                'image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'service',
            ],
            [
                'name' => 'Professional Services',
                'description' => 'Professional services like consulting, legal, and accounting',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'service',
            ],
            [
                'name' => 'Health Services',
                'description' => 'Health and medical services',
                'image' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
                'is_active' => true,
                'type' => 'service',
            ],
            [
                'name' => 'Transportation',
                'description' => 'Transportation and delivery services',
                'image' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80',
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
