<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class SubcategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all product categories
        $productCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->get();
            
        // Get all service categories
        $serviceCategories = Category::where('type', 'service')
            ->whereNull('parent_id')
            ->get();
            
        // Add subcategories for Electronics
        $electronics = $productCategories->where('name', 'Electronics')->first();
        if ($electronics) {
            $this->createSubcategories($electronics, [
                'Laptops' => 'Portable computers for work and play',
                'Smartphones' => 'Mobile phones with advanced features',
                'Tablets' => 'Portable touchscreen computers',
                'Headphones' => 'Audio devices for personal listening',
                'Cameras' => 'Devices for capturing photos and videos',
                'Smart Home' => 'Connected devices for home automation',
            ]);
        }
        
        // Add subcategories for Clothing
        $clothing = $productCategories->where('name', 'Clothing')->first();
        if ($clothing) {
            $this->createSubcategories($clothing, [
                'Men\'s Clothing' => 'Apparel for men',
                'Women\'s Clothing' => 'Apparel for women',
                'Children\'s Clothing' => 'Apparel for children',
                'Shoes' => 'Footwear for all ages',
                'Accessories' => 'Fashion accessories like belts, hats, etc.',
            ]);
        }
        
        // Add subcategories for Home & Garden
        $homeGarden = $productCategories->where('name', 'Home & Garden')->first();
        if ($homeGarden) {
            $this->createSubcategories($homeGarden, [
                'Furniture' => 'Items for home furnishing',
                'Kitchen' => 'Kitchen appliances and tools',
                'Gardening' => 'Tools and supplies for gardening',
                'Decor' => 'Decorative items for home',
                'Bedding' => 'Sheets, pillows, and bedding accessories',
            ]);
        }
        
        // Add subcategories for Cleaning services
        $cleaning = $serviceCategories->where('name', 'Cleaning')->first();
        if ($cleaning) {
            $this->createSubcategories($cleaning, [
                'Home Cleaning' => 'Cleaning services for residential properties',
                'Office Cleaning' => 'Cleaning services for commercial spaces',
                'Carpet Cleaning' => 'Specialized cleaning for carpets and rugs',
                'Window Cleaning' => 'Cleaning services for windows',
                'Deep Cleaning' => 'Thorough cleaning services',
            ]);
        }
        
        // Add subcategories for Repair services
        $repair = $serviceCategories->where('name', 'Repair')->first();
        if ($repair) {
            $this->createSubcategories($repair, [
                'Electronics Repair' => 'Repair services for electronic devices',
                'Appliance Repair' => 'Repair services for home appliances',
                'Furniture Repair' => 'Repair services for furniture',
                'Vehicle Repair' => 'Repair services for vehicles',
                'Home Repair' => 'Repair services for home maintenance',
            ]);
        }
    }
    
    /**
     * Create subcategories for a parent category
     *
     * @param Category $parent
     * @param array $subcategories
     * @return void
     */
    private function createSubcategories(Category $parent, array $subcategories)
    {
        foreach ($subcategories as $name => $description) {
            Category::create([
                'name' => $name,
                'description' => $description,
                'type' => $parent->type,
                'parent_id' => $parent->id,
                'is_active' => true,
            ]);
        }
    }
}
