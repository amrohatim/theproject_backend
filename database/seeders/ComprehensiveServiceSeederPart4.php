<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComprehensiveServiceSeederPart4 extends Seeder
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

        $this->command->info("Part 4 service seeding completed! Created {$totalServicesCreated} additional services.");
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
     * Get comprehensive service data for final categories
     */
    private function getServicesByCategory(): array
    {
        return [
            // Makeup Services
            'Makeup Services' => [
                [
                    'name' => 'Professional Makeup Application',
                    'price' => 60.00,
                    'duration' => 60,
                    'description' => 'Professional makeup application for any occasion',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Makeup Consultation',
                    'price' => 40.00,
                    'duration' => 45,
                    'description' => 'Personalized makeup consultation and tips',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Bridal makeup' => [
                [
                    'name' => 'Bridal Makeup Package',
                    'price' => 150.00,
                    'duration' => 120,
                    'description' => 'Complete bridal makeup with trial session',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Bridal Trial Makeup',
                    'price' => 80.00,
                    'duration' => 90,
                    'description' => 'Bridal makeup trial and consultation',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Event makeup' => [
                [
                    'name' => 'Party Makeup',
                    'price' => 50.00,
                    'duration' => 60,
                    'description' => 'Glamorous makeup for parties and events',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Photoshoot Makeup',
                    'price' => 70.00,
                    'duration' => 75,
                    'description' => 'Professional makeup for photoshoots',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
            ],

            // Nail Care Services
            'Nail Care' => [
                [
                    'name' => 'Complete Nail Care Package',
                    'price' => 60.00,
                    'duration' => 90,
                    'description' => 'Full nail care including manicure and pedicure',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
            ],
            
            'Manicures' => [
                [
                    'name' => 'Classic Manicure',
                    'price' => 25.00,
                    'duration' => 45,
                    'description' => 'Traditional manicure with nail polish',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Gel Manicure',
                    'price' => 35.00,
                    'duration' => 60,
                    'description' => 'Long-lasting gel manicure',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'French Manicure',
                    'price' => 30.00,
                    'duration' => 50,
                    'description' => 'Classic French manicure style',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Pedicures' => [
                [
                    'name' => 'Spa Pedicure',
                    'price' => 40.00,
                    'duration' => 60,
                    'description' => 'Relaxing spa pedicure with foot massage',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Classic Pedicure',
                    'price' => 30.00,
                    'duration' => 45,
                    'description' => 'Traditional pedicure with nail polish',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Gel Pedicure',
                    'price' => 45.00,
                    'duration' => 60,
                    'description' => 'Long-lasting gel pedicure',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Nail art' => [
                [
                    'name' => 'Custom Nail Art Design',
                    'price' => 50.00,
                    'duration' => 90,
                    'description' => 'Personalized nail art and design',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Simple Nail Art',
                    'price' => 20.00,
                    'duration' => 30,
                    'description' => 'Basic nail art and decorations',
                    'rating' => 4.5,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            // Therapy & Support Services
            'Individual Therapy' => [
                [
                    'name' => 'Individual Counseling Session',
                    'price' => 120.00,
                    'duration' => 60,
                    'description' => 'One-on-one therapy and counseling session',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Cognitive Behavioral Therapy',
                    'price' => 140.00,
                    'duration' => 75,
                    'description' => 'Specialized CBT therapy session',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
            
            'Couple Therapy' => [
                [
                    'name' => 'Couples Counseling Session',
                    'price' => 160.00,
                    'duration' => 90,
                    'description' => 'Professional couples therapy and counseling',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Relationship Workshop',
                    'price' => 200.00,
                    'duration' => 120,
                    'description' => 'Intensive relationship building workshop',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],
            
            'Family therapy' => [
                [
                    'name' => 'Family Counseling Session',
                    'price' => 180.00,
                    'duration' => 90,
                    'description' => 'Family therapy and conflict resolution',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
            ],
        ];
    }
}
