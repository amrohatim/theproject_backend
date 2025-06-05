<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComprehensiveServiceSeederPart3 extends Seeder
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

        // Define comprehensive service data for remaining categories
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

        $this->command->info("Part 3 service seeding completed! Created {$totalServicesCreated} additional services.");
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
            // Wellness Workshops
            'Wellness Workshops' => 'Wellness Workshops',
            'Mindfulness' => 'Wellness Workshops',
            'Stress management' => 'Wellness Workshops',
            'Healthy cooking' => 'Wellness Workshops',
            
            // Salon Services
            'Haircuts' => 'Salon Services',
            'Styling' => 'Salon Services',
            'Coloring' => 'Salon Services',
            
            // Makeup Services
            'Makeup Services' => 'Makeup Services',
            'Bridal makeup' => 'Makeup Services',
            'Event makeup' => 'Makeup Services',
            
            // Nail Care
            'Nail Care' => 'Nail Care',
            'Manicures' => 'Nail Care',
            'Pedicures' => 'Nail Care',
            'Nail art' => 'Nail Care',
            
            // Therapy Sessions
            'Individual Therapy' => 'Therapy Sessions',
            'Couple Therapy' => 'Therapy Sessions',
            'Family therapy' => 'Therapy Sessions',
            
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
     * Get comprehensive service data for remaining categories
     */
    private function getServicesByCategory(): array
    {
        return [
            // Wellness Workshops
            'Wellness Workshops' => [
                [
                    'name' => 'Holistic Wellness Workshop',
                    'price' => 60.00,
                    'duration' => 120,
                    'description' => 'Comprehensive wellness workshop covering mind, body, and spirit',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Women\'s Wellness Circle',
                    'price' => 45.00,
                    'duration' => 90,
                    'description' => 'Supportive wellness circle for women\'s health and empowerment',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
            
            'Mindfulness' => [
                [
                    'name' => 'Mindfulness Meditation Session',
                    'price' => 35.00,
                    'duration' => 60,
                    'description' => 'Guided mindfulness meditation for stress reduction',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Mindful Living Workshop',
                    'price' => 50.00,
                    'duration' => 90,
                    'description' => 'Learn to incorporate mindfulness into daily life',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
            
            'Stress management' => [
                [
                    'name' => 'Stress Relief Workshop',
                    'price' => 40.00,
                    'duration' => 75,
                    'description' => 'Learn effective techniques for managing stress',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Workplace Stress Management',
                    'price' => 55.00,
                    'duration' => 90,
                    'description' => 'Specialized stress management for working professionals',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
            ],
            
            'Healthy cooking' => [
                [
                    'name' => 'Healthy Meal Prep Class',
                    'price' => 65.00,
                    'duration' => 120,
                    'description' => 'Learn to prepare healthy meals for the week',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Plant-Based Cooking Workshop',
                    'price' => 70.00,
                    'duration' => 150,
                    'description' => 'Comprehensive plant-based cooking techniques',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
            ],

            // Personal Care Services
            'Haircuts' => [
                [
                    'name' => 'Women\'s Haircut & Style',
                    'price' => 45.00,
                    'duration' => 60,
                    'description' => 'Professional haircut and styling service',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Trim & Touch-up',
                    'price' => 25.00,
                    'duration' => 30,
                    'description' => 'Quick trim and style touch-up',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Layered Cut & Blow Dry',
                    'price' => 55.00,
                    'duration' => 75,
                    'description' => 'Layered haircut with professional blow dry',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
            
            'Styling' => [
                [
                    'name' => 'Special Event Styling',
                    'price' => 60.00,
                    'duration' => 90,
                    'description' => 'Professional hair styling for special events',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Bridal Hair Styling',
                    'price' => 120.00,
                    'duration' => 120,
                    'description' => 'Elegant bridal hair styling and design',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Everyday Styling',
                    'price' => 35.00,
                    'duration' => 45,
                    'description' => 'Quick and easy everyday hair styling',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Coloring' => [
                [
                    'name' => 'Full Hair Color',
                    'price' => 80.00,
                    'duration' => 120,
                    'description' => 'Complete hair coloring service',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Highlights & Lowlights',
                    'price' => 100.00,
                    'duration' => 150,
                    'description' => 'Professional highlighting and lowlighting',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Root Touch-up',
                    'price' => 40.00,
                    'duration' => 60,
                    'description' => 'Quick root color touch-up service',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
        ];
    }
}
