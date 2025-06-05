<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class AdditionalSubcategoriesSeeder extends Seeder
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
            
        // Add subcategories for Automotive
        $automotive = $productCategories->where('name', 'Automotive')->first();
        if ($automotive) {
            $this->createSubcategories($automotive, [
                'Car Parts' => 'Replacement parts for vehicles',
                'Car Accessories' => 'Accessories for vehicles',
                'Car Care' => 'Products for vehicle maintenance',
                'Motorcycle Parts' => 'Parts for motorcycles',
                'Tools & Equipment' => 'Tools for automotive repair',
            ]);
        }
        
        // Add subcategories for Books
        $books = $productCategories->where('name', 'Books')->first();
        if ($books) {
            $this->createSubcategories($books, [
                'Fiction' => 'Fictional books and novels',
                'Non-Fiction' => 'Non-fictional books',
                'Children\'s Books' => 'Books for children',
                'Textbooks' => 'Educational textbooks',
                'E-books' => 'Digital books',
            ]);
        }
        
        // Add subcategories for Toys & Games
        $toysGames = $productCategories->where('name', 'Toys & Games')->first();
        if ($toysGames) {
            $this->createSubcategories($toysGames, [
                'Board Games' => 'Games played on a board',
                'Puzzles' => 'Puzzle games',
                'Action Figures' => 'Toy action figures',
                'Dolls' => 'Toy dolls',
                'Educational Toys' => 'Toys for learning',
            ]);
        }
        
        // Add subcategories for Food & Beverages
        $foodBeverages = $productCategories->where('name', 'Food & Beverages')->first();
        if ($foodBeverages) {
            $this->createSubcategories($foodBeverages, [
                'Snacks' => 'Snack foods',
                'Beverages' => 'Drinks and beverages',
                'Baking Supplies' => 'Supplies for baking',
                'Canned Goods' => 'Canned food products',
                'Dairy Products' => 'Dairy-based food products',
            ]);
        }
        
        // Add subcategories for Health & Wellness
        $healthWellness = $productCategories->where('name', 'Health & Wellness')->first();
        if ($healthWellness) {
            $this->createSubcategories($healthWellness, [
                'Vitamins & Supplements' => 'Nutritional supplements',
                'Fitness Equipment' => 'Equipment for exercise',
                'Personal Care' => 'Personal care products',
                'First Aid' => 'First aid supplies',
                'Wellness Devices' => 'Devices for health monitoring',
            ]);
        }
        
        // Add subcategories for Education & Tutoring
        $education = $serviceCategories->where('name', 'Education & Tutoring')->first();
        if ($education) {
            $this->createSubcategories($education, [
                'Academic Tutoring' => 'Tutoring for academic subjects',
                'Language Learning' => 'Services for learning languages',
                'Test Preparation' => 'Preparation for standardized tests',
                'Music Lessons' => 'Lessons for musical instruments',
                'Art Classes' => 'Classes for art and creativity',
            ]);
        }
        
        // Add subcategories for Home Services
        $homeServices = $serviceCategories->where('name', 'Home Services')->first();
        if ($homeServices) {
            $this->createSubcategories($homeServices, [
                'Plumbing' => 'Plumbing services',
                'Electrical' => 'Electrical services',
                'Landscaping' => 'Landscaping and gardening services',
                'Pest Control' => 'Pest control services',
                'Interior Design' => 'Interior design services',
            ]);
        }
        
        // Add subcategories for Professional Services
        $professionalServices = $serviceCategories->where('name', 'Professional Services')->first();
        if ($professionalServices) {
            $this->createSubcategories($professionalServices, [
                'Legal Services' => 'Legal advice and services',
                'Accounting' => 'Accounting and bookkeeping services',
                'Consulting' => 'Business consulting services',
                'Marketing' => 'Marketing and advertising services',
                'Web Development' => 'Website development services',
            ]);
        }
        
        // Add subcategories for Health Services
        $healthServices = $serviceCategories->where('name', 'Health Services')->first();
        if ($healthServices) {
            $this->createSubcategories($healthServices, [
                'Medical Consultation' => 'Medical consultation services',
                'Dental Services' => 'Dental care services',
                'Physical Therapy' => 'Physical therapy services',
                'Mental Health' => 'Mental health services',
                'Nutrition Counseling' => 'Nutrition and diet counseling',
            ]);
        }
        
        // Add subcategories for Transportation
        $transportation = $serviceCategories->where('name', 'Transportation')->first();
        if ($transportation) {
            $this->createSubcategories($transportation, [
                'Ride Sharing' => 'Ride sharing services',
                'Delivery' => 'Delivery services',
                'Moving Services' => 'Services for moving homes or offices',
                'Courier Services' => 'Courier and package delivery',
                'Vehicle Rental' => 'Vehicle rental services',
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
