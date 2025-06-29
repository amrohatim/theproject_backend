<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComprehensiveServiceSeederPart2 extends Seeder
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

        $this->command->info("Part 2 service seeding completed! Created {$totalServicesCreated} additional services.");
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
            // Fitness Classes
            'Yoga' => 'Fitness Classes',
            'Pilates' => 'Fitness Classes',
            'Zumba' => 'Fitness Classes',
            'Strength training' => 'Fitness Classes',
            
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
            // Fitness & Wellness services
            'Yoga' => [
                [
                    'name' => 'Hatha Yoga Class',
                    'price' => 25.00,
                    'duration' => 60,
                    'description' => 'Gentle Hatha yoga class for all levels',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Vinyasa Flow Yoga',
                    'price' => 30.00,
                    'duration' => 75,
                    'description' => 'Dynamic Vinyasa flow yoga session',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Private Yoga Session',
                    'price' => 80.00,
                    'duration' => 60,
                    'description' => 'One-on-one personalized yoga instruction',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Prenatal Yoga',
                    'price' => 35.00,
                    'duration' => 60,
                    'description' => 'Specialized yoga for expecting mothers',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
            
            'Pilates' => [
                [
                    'name' => 'Mat Pilates Class',
                    'price' => 28.00,
                    'duration' => 50,
                    'description' => 'Core-strengthening mat Pilates workout',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Reformer Pilates',
                    'price' => 45.00,
                    'duration' => 50,
                    'description' => 'Equipment-based Pilates using reformer machines',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Private Pilates Session',
                    'price' => 90.00,
                    'duration' => 60,
                    'description' => 'Personalized one-on-one Pilates training',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Zumba' => [
                [
                    'name' => 'Zumba Fitness Class',
                    'price' => 20.00,
                    'duration' => 60,
                    'description' => 'High-energy Zumba dance fitness class',
                    'rating' => 4.6,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Zumba Gold (Seniors)',
                    'price' => 18.00,
                    'duration' => 45,
                    'description' => 'Low-impact Zumba designed for seniors',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
            
            'Strength training' => [
                [
                    'name' => 'Personal Training Session',
                    'price' => 75.00,
                    'duration' => 60,
                    'description' => 'One-on-one strength training with certified trainer',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Group Strength Training',
                    'price' => 35.00,
                    'duration' => 45,
                    'description' => 'Small group strength training class',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Functional Fitness Training',
                    'price' => 40.00,
                    'duration' => 50,
                    'description' => 'Functional movement and strength training',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
        ];
    }
}
