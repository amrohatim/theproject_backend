<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class UpdateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update product categories
        $productCategories = [
            'Electronics', 'Clothing', 'Home & Garden', 'Books', 'Toys', 'Sports', 'Beauty', 'Health'
        ];
        
        foreach ($productCategories as $name) {
            $category = Category::where('name', $name)->first();
            if ($category) {
                $category->update(['type' => 'product']);
            } else {
                Category::create([
                    'name' => $name,
                    'type' => 'product',
                    'is_active' => true
                ]);
            }
        }
        
        // Update service categories
        $serviceCategories = [
            'Cleaning', 'Repair', 'Installation', 'Consultation', 'Training', 'Design', 'Maintenance', 'Delivery'
        ];
        
        foreach ($serviceCategories as $name) {
            $category = Category::where('name', $name)->first();
            if ($category) {
                $category->update(['type' => 'service']);
            } else {
                Category::create([
                    'name' => $name,
                    'type' => 'service',
                    'is_active' => true
                ]);
            }
        }
    }
}
