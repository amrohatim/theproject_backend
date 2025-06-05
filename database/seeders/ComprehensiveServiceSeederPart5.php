<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComprehensiveServiceSeederPart5 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all service subcategories (only child categories with parent_id)
        $subcategories = Category::where('type', 'service')
            ->whereNotNull('parent_id')
            ->get();

        // Get all branches
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->error('No branches found. Please run BranchesTableSeeder first.');
            return;
        }

        // Define comprehensive service data for final categories
        $servicesByCategory = $this->getServicesByCategory();

        $totalServicesCreated = 0;

        foreach ($subcategories as $subcategory) {
            $categoryName = $subcategory->name;
            
            if (!isset($servicesByCategory[$categoryName])) {
                continue; // Skip categories not in this part
            }

            $services = $servicesByCategory[$categoryName];
            $this->command->info("Creating services for: {$categoryName}");

            foreach ($services as $serviceData) {
                // Check if service already exists
                $existingService = Service::where('name', $serviceData['name'])
                    ->where('category_id', $subcategory->id)
                    ->first();
                    
                if ($existingService) {
                    continue; // Skip if already exists
                }
                
                // Randomly assign to a branch
                $branch = $branches->random();
                
                Service::create([
                    'branch_id' => $branch->id,
                    'category_id' => $subcategory->id,
                    'name' => $serviceData['name'],
                    'price' => $serviceData['price'],
                    'duration' => $serviceData['duration'],
                    'description' => $serviceData['description'],
                    'image' => $this->getServiceImage($subcategory, $serviceData['name']),
                    'rating' => $serviceData['rating'],
                    'is_available' => true,
                    'featured' => $serviceData['featured'] ?? false,
                    'home_service' => $serviceData['home_service'] ?? false,
                ]);
                
                $totalServicesCreated++;
            }
        }

        $this->command->info("Final service seeding completed! Created {$totalServicesCreated} additional services.");
    }

    /**
     * Get service image from local service category images
     */
    private function getServiceImage(Category $category, string $serviceName): string
    {
        // Determine the correct folder based on subcategory
        $imageFolder = $this->determineImageFolder($category->name);
        
        if (!$imageFolder) {
            return '/images/services/placeholder.jpg';
        }

        $imagePath = base_path("app service category images/{$imageFolder}");
        
        if (File::exists($imagePath)) {
            $imageFiles = File::files($imagePath);
            if (!empty($imageFiles)) {
                // Try to find an image that matches the service name or category
                $matchingImage = collect($imageFiles)->first(function ($file) use ($serviceName, $category) {
                    $filename = $file->getFilename();
                    return Str::contains($filename, $serviceName) || 
                           Str::contains($filename, $category->name);
                });
                
                if ($matchingImage) {
                    return "app service category images/{$imageFolder}/{$matchingImage->getFilename()}";
                }
                
                // Fallback to first image in directory
                return "app service category images/{$imageFolder}/{$imageFiles[0]->getFilename()}";
            }
        }
        
        return '/images/services/placeholder.jpg';
    }

    /**
     * Determine the correct image folder for a subcategory
     */
    private function determineImageFolder(string $subcategoryName): ?string
    {
        $folderMap = [
            // Nutrition Counseling
            'Nutrition Counseling' => 'Nutrition Counseling',
            'Diet plans' => 'Nutrition Counseling',
            'Weight management programs' => 'Nutrition Counseling',
            
            // Artistic Services
            'Photography sessions' => 'Artistic Services',
            'Painting classes' => 'Artistic Services',
            'Pottery making' => 'Artistic Services',
            'Craft workshops' => 'Artistic Services',
            
            // Elderly Care & Companionship Services
            'In-home care' => 'Elderly Care & Companionship Services',
            'Companionship visits' => 'Elderly Care & Companionship Services',
        ];

        return $folderMap[$subcategoryName] ?? null;
    }

    /**
     * Get comprehensive service data for final categories
     */
    private function getServicesByCategory(): array
    {
        return [
            // Nutrition Counseling Services
            'Nutrition Counseling' => [
                [
                    'name' => 'Nutritional Assessment',
                    'price' => 100.00,
                    'duration' => 60,
                    'description' => 'Comprehensive nutritional assessment and planning',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Nutrition Consultation',
                    'price' => 80.00,
                    'duration' => 45,
                    'description' => 'Professional nutrition consultation and guidance',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Diet plans' => [
                [
                    'name' => 'Personalized Diet Plan',
                    'price' => 120.00,
                    'duration' => 90,
                    'description' => 'Custom diet plan based on individual needs',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Weight Loss Diet Plan',
                    'price' => 100.00,
                    'duration' => 75,
                    'description' => 'Specialized diet plan for healthy weight loss',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Pregnancy Nutrition Plan',
                    'price' => 110.00,
                    'duration' => 60,
                    'description' => 'Nutritional planning for expecting mothers',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
            ],
            
            'Weight management programs' => [
                [
                    'name' => 'Weight Management Program',
                    'price' => 200.00,
                    'duration' => 120,
                    'description' => 'Comprehensive weight management and lifestyle program',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Healthy Weight Maintenance',
                    'price' => 150.00,
                    'duration' => 90,
                    'description' => 'Program for maintaining healthy weight long-term',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],

            // Creative & Artistic Services
            'Photography sessions' => [
                [
                    'name' => 'Portrait Photography Session',
                    'price' => 150.00,
                    'duration' => 90,
                    'description' => 'Professional portrait photography session',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Family Photography',
                    'price' => 200.00,
                    'duration' => 120,
                    'description' => 'Family portrait and lifestyle photography',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Maternity Photography',
                    'price' => 180.00,
                    'duration' => 90,
                    'description' => 'Beautiful maternity photography session',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Newborn Photography',
                    'price' => 220.00,
                    'duration' => 150,
                    'description' => 'Gentle newborn photography session',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
            ],
            
            'Painting classes' => [
                [
                    'name' => 'Watercolor Painting Class',
                    'price' => 45.00,
                    'duration' => 120,
                    'description' => 'Learn watercolor painting techniques',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Acrylic Painting Workshop',
                    'price' => 50.00,
                    'duration' => 150,
                    'description' => 'Acrylic painting workshop for beginners',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Private Painting Lesson',
                    'price' => 80.00,
                    'duration' => 90,
                    'description' => 'One-on-one painting instruction',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Pottery making' => [
                [
                    'name' => 'Beginner Pottery Class',
                    'price' => 55.00,
                    'duration' => 120,
                    'description' => 'Introduction to pottery and ceramics',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Wheel Throwing Workshop',
                    'price' => 70.00,
                    'duration' => 150,
                    'description' => 'Learn pottery wheel throwing techniques',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
            ],
            
            'Craft workshops' => [
                [
                    'name' => 'Jewelry Making Workshop',
                    'price' => 60.00,
                    'duration' => 120,
                    'description' => 'Create your own jewelry pieces',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Scrapbooking Class',
                    'price' => 40.00,
                    'duration' => 90,
                    'description' => 'Learn creative scrapbooking techniques',
                    'rating' => 4.5,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Knitting & Crochet Workshop',
                    'price' => 35.00,
                    'duration' => 90,
                    'description' => 'Learn knitting and crochet basics',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            // Care Services
            'In-home care' => [
                [
                    'name' => 'Personal Care Assistant',
                    'price' => 25.00,
                    'duration' => 60,
                    'description' => 'Personal care assistance for daily activities',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Medication Management',
                    'price' => 30.00,
                    'duration' => 30,
                    'description' => 'Assistance with medication management',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Meal Preparation Service',
                    'price' => 35.00,
                    'duration' => 90,
                    'description' => 'Healthy meal preparation for seniors',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Companionship visits' => [
                [
                    'name' => 'Social Companionship',
                    'price' => 20.00,
                    'duration' => 60,
                    'description' => 'Friendly companionship and conversation',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Activity Companion',
                    'price' => 25.00,
                    'duration' => 90,
                    'description' => 'Companion for activities and outings',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Reading Companion',
                    'price' => 18.00,
                    'duration' => 45,
                    'description' => 'Reading and storytelling companionship',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
        ];
    }
}
