<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ComprehensiveServiceCategorySeeder extends Seeder
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

        $this->command->info("Found {$subcategories->count()} service subcategories to populate.");
        $this->command->info("Found {$branches->count()} branches available.");

        // Define comprehensive service data for each subcategory
        $servicesByCategory = $this->getServicesByCategory();

        $totalServicesCreated = 0;

        foreach ($subcategories as $subcategory) {
            $categoryName = $subcategory->name;
            
            if (!isset($servicesByCategory[$categoryName])) {
                $this->command->warn("No services defined for subcategory: {$categoryName}");
                continue;
            }

            $services = $servicesByCategory[$categoryName];
            $this->command->info("Creating services for: {$categoryName}");

            foreach ($services as $serviceData) {
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

        $this->command->info("Service seeding completed successfully! Created {$totalServicesCreated} services.");
    }

    /**
     * Get service image from local service category images
     */
    private function getServiceImage(Category $category, string $serviceName): string
    {
        // Get parent category name for image folder
        $parentCategory = $category->parent;
        if (!$parentCategory) {
            return '/images/services/placeholder.jpg';
        }

        // Map parent category names to image folder names
        $folderMapping = [
            'Healthcare & Femtech' => 'Healthcare & Femtech',
            'Beauty & Wellness' => 'Spa Treatments',
            'Fitness & Wellness' => 'Fitness Classes',
            'Personal Care Services' => 'Salon Services',
            'Therapy & Support' => 'Therapy Sessions',
            'Creative & Artistic' => 'Artistic Services',
            'Care Services' => 'Elderly Care & Companionship Services',
        ];

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
            // Healthcare & Femtech
            'Women\'s Health' => 'Healthcare & Femtech',
            'Mental Health Support' => 'Healthcare & Femtech',
            'Fertility monitoring' => 'Healthcare & Femtech',
            'Pregnancy guides' => 'Healthcare & Femtech',
            'Menstrual tracking' => 'Healthcare & Femtech',
            
            // Spa Treatments
            'Spa Treatments' => 'Spa Treatments',
            'Facials' => 'Spa Treatments',
            'Massages' => 'Spa Treatments',
            'Body scrubs' => 'Spa Treatments',
            
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
     * Get comprehensive service data organized by category name
     */
    private function getServicesByCategory(): array
    {
        return [
            // Healthcare & Femtech services
            'Women\'s Health' => [
                [
                    'name' => 'Women\'s Health Consultation',
                    'price' => 120.00,
                    'duration' => 60,
                    'description' => 'Comprehensive women\'s health consultation and screening',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Gynecological Checkup',
                    'price' => 150.00,
                    'duration' => 45,
                    'description' => 'Regular gynecological examination and health assessment',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Reproductive Health Counseling',
                    'price' => 100.00,
                    'duration' => 50,
                    'description' => 'Expert guidance on reproductive health and family planning',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
            
            'Mental Health Support' => [
                [
                    'name' => 'Mental Health Assessment',
                    'price' => 180.00,
                    'duration' => 90,
                    'description' => 'Comprehensive mental health evaluation and support planning',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Anxiety Management Session',
                    'price' => 130.00,
                    'duration' => 60,
                    'description' => 'Specialized therapy session for anxiety management',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Depression Support Therapy',
                    'price' => 140.00,
                    'duration' => 75,
                    'description' => 'Professional therapy for depression and mood disorders',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],

            'Fertility monitoring' => [
                [
                    'name' => 'Fertility Tracking Consultation',
                    'price' => 200.00,
                    'duration' => 60,
                    'description' => 'Professional fertility monitoring and tracking services',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Ovulation Monitoring',
                    'price' => 80.00,
                    'duration' => 30,
                    'description' => 'Precise ovulation tracking and timing guidance',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            'Pregnancy guides' => [
                [
                    'name' => 'Prenatal Care Consultation',
                    'price' => 150.00,
                    'duration' => 60,
                    'description' => 'Comprehensive prenatal care and guidance',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Birth Preparation Class',
                    'price' => 120.00,
                    'duration' => 90,
                    'description' => 'Complete birth preparation and education class',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => false,
                ],
            ],

            'Menstrual tracking' => [
                [
                    'name' => 'Menstrual Health Consultation',
                    'price' => 100.00,
                    'duration' => 45,
                    'description' => 'Expert guidance on menstrual health and cycle tracking',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            // Beauty & Wellness services
            'Spa Treatments' => [
                [
                    'name' => 'Full Body Spa Package',
                    'price' => 250.00,
                    'duration' => 180,
                    'description' => 'Complete spa experience with multiple treatments',
                    'rating' => 4.9,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Relaxation Spa Session',
                    'price' => 120.00,
                    'duration' => 90,
                    'description' => 'Relaxing spa treatment for stress relief',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            'Facials' => [
                [
                    'name' => 'Deep Cleansing Facial',
                    'price' => 80.00,
                    'duration' => 60,
                    'description' => 'Professional deep cleansing facial treatment',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Anti-Aging Facial',
                    'price' => 120.00,
                    'duration' => 75,
                    'description' => 'Advanced anti-aging facial with premium products',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => false,
                ],
                [
                    'name' => 'Hydrating Facial',
                    'price' => 90.00,
                    'duration' => 60,
                    'description' => 'Moisturizing facial for dry and dehydrated skin',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            'Massages' => [
                [
                    'name' => 'Swedish Massage',
                    'price' => 100.00,
                    'duration' => 60,
                    'description' => 'Classic Swedish massage for relaxation',
                    'rating' => 4.8,
                    'featured' => true,
                    'home_service' => true,
                ],
                [
                    'name' => 'Deep Tissue Massage',
                    'price' => 120.00,
                    'duration' => 75,
                    'description' => 'Therapeutic deep tissue massage for muscle tension',
                    'rating' => 4.9,
                    'featured' => false,
                    'home_service' => true,
                ],
                [
                    'name' => 'Hot Stone Massage',
                    'price' => 140.00,
                    'duration' => 90,
                    'description' => 'Relaxing hot stone massage therapy',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Prenatal Massage',
                    'price' => 110.00,
                    'duration' => 60,
                    'description' => 'Specialized massage for expecting mothers',
                    'rating' => 4.8,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],

            'Body scrubs' => [
                [
                    'name' => 'Exfoliating Body Scrub',
                    'price' => 70.00,
                    'duration' => 45,
                    'description' => 'Full body exfoliation treatment',
                    'rating' => 4.6,
                    'featured' => false,
                    'home_service' => false,
                ],
                [
                    'name' => 'Sugar Scrub Treatment',
                    'price' => 80.00,
                    'duration' => 50,
                    'description' => 'Gentle sugar scrub for smooth skin',
                    'rating' => 4.7,
                    'featured' => false,
                    'home_service' => true,
                ],
            ],
        ];
    }
}
