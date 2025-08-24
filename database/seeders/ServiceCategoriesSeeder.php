<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class ServiceCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting Service Categories Seeder...');
        
        // Clear existing service categories if needed
        if ($this->command->confirm('Do you want to clear existing service categories before seeding?', false)) {
            Category::where('type', 'service')->delete();
            $this->command->info('Existing service categories cleared.');
        }

        $serviceImagePath = base_path('app service category images');
        
        if (!File::exists($serviceImagePath)) {
            $this->command->error("Service category images directory not found: {$serviceImagePath}");
            return;
        }

        $directories = File::directories($serviceImagePath);
        
        if (empty($directories)) {
            $this->command->error('No service category directories found.');
            return;
        }

        $this->command->info("Found " . count($directories) . " service category directories.");

        foreach ($directories as $directory) {
            $categoryName = basename($directory);
            $this->command->info("Processing category: {$categoryName}");

            // Create parent category
            $parentCategory = $this->createParentCategory($categoryName, $directory);
            if ($parentCategory) {
                $this->command->info("Created parent category: {$categoryName}");

                // Create child categories
                $childCount = $this->createChildCategories($categoryName, $directory, $parentCategory->id);
                $this->command->info("Created {$childCount} child categories for: {$categoryName}");
            }
        }

        $this->command->info('Service Categories Seeder completed successfully!');
    }

    /**
     * Create parent category from directory name
     */
    private function createParentCategory(string $categoryName, string $directoryPath): ?Category
    {
        try {
            // Get parent category image (image with same name as directory)
            $parentImagePath = $this->getParentCategoryImage($categoryName, $directoryPath);
            
            // Generate appropriate icon and description based on category name
            $categoryData = $this->getCategoryMetadata($categoryName);

            $category = Category::create([
                'name' => $categoryName,
                'description' => $categoryData['description'],
                'image' => $parentImagePath,
                'is_active' => true,
                'type' => 'service',
                'icon' => $categoryData['icon'],
                'parent_id' => null,
            ]);

            return $category;
        } catch (\Exception $e) {
            $this->command->error("Failed to create parent category {$categoryName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create child categories from image files in directory
     */
    private function createChildCategories(string $parentCategoryName, string $directoryPath, int $parentId): int
    {
        $imageFiles = File::files($directoryPath);
        $childCount = 0;

        foreach ($imageFiles as $imageFile) {
            $filename = $imageFile->getFilenameWithoutExtension();
            
            // Skip if this is the parent category image
            if ($filename === $parentCategoryName) {
                continue;
            }

            try {
                $imagePath = "app service category images/{$parentCategoryName}/{$imageFile->getFilename()}";
                
                // Generate appropriate icon based on service name
                $icon = $this->generateIconForService($filename);
                
                Category::create([
                    'name' => $filename,
                    'description' => $this->generateDescription($filename),
                    'image' => $imagePath,
                    'parent_id' => $parentId,
                    'is_active' => true,
                    'type' => 'service',
                    'icon' => $icon,
                ]);

                $childCount++;
            } catch (\Exception $e) {
                $this->command->error("Failed to create child category {$filename}: " . $e->getMessage());
            }
        }

        return $childCount;
    }

    /**
     * Get parent category image path
     */
    private function getParentCategoryImage(string $categoryName, string $directoryPath): string
    {
        $imageFiles = File::files($directoryPath);
        
        // Look for image with same name as category
        foreach ($imageFiles as $imageFile) {
            if ($imageFile->getFilenameWithoutExtension() === $categoryName) {
                return "app service category images/{$categoryName}/{$imageFile->getFilename()}";
            }
        }

        // Fallback to first image if no exact match
        if (!empty($imageFiles)) {
            return "app service category images/{$categoryName}/{$imageFiles[0]->getFilename()}";
        }

        // Final fallback
        return '/images/categories/placeholder.jpg';
    }

    /**
     * Get category metadata (description and icon) based on category name
     */
    private function getCategoryMetadata(string $categoryName): array
    {
        $metadata = [
            'Artistic Services' => [
                'description' => 'Creative and artistic services including photography, painting, and crafts',
                'icon' => 'fas fa-palette'
            ],
            'Elderly Care & Companionship Services' => [
                'description' => 'Elderly care and companionship services for seniors',
                'icon' => 'fas fa-heart'
            ],
            'Fitness Classes' => [
                'description' => 'Fitness classes and physical training sessions',
                'icon' => 'fas fa-dumbbell'
            ],
            'Healthcare & Femtech' => [
                'description' => 'Women\'s health and femtech services',
                'icon' => 'fas fa-heartbeat'
            ],
            'Makeup Services' => [
                'description' => 'Professional makeup and beauty services',
                'icon' => 'fas fa-paint-brush'
            ],
            'Nail Care' => [
                'description' => 'Professional nail care and beauty services',
                'icon' => 'fas fa-hand-sparkles'
            ],
            'Nutrition Counseling' => [
                'description' => 'Nutrition counseling and dietary guidance services',
                'icon' => 'fas fa-apple-alt'
            ],
            'Salon Services' => [
                'description' => 'Professional hair and beauty salon services',
                'icon' => 'fas fa-cut'
            ],
            'Spa Treatments' => [
                'description' => 'Relaxing spa treatments and wellness therapies',
                'icon' => 'fas fa-spa'
            ],
            'Therapy Sessions' => [
                'description' => 'Professional therapy and counseling sessions',
                'icon' => 'fas fa-hands-helping'
            ],
            'Wellness Workshops' => [
                'description' => 'Health and wellness workshops and classes',
                'icon' => 'fas fa-leaf'
            ],
        ];

        return $metadata[$categoryName] ?? [
            'description' => "Professional {$categoryName} services",
            'icon' => 'fas fa-concierge-bell'
        ];
    }

    /**
     * Generate appropriate icon for service based on service name
     */
    private function generateIconForService(string $serviceName): string
    {
        $iconMap = [
            // Healthcare & Femtech
            'Women\'s Health' => 'fas fa-female',
            'Mental Health Support' => 'fas fa-brain',
            'Fertility monitoring' => 'fas fa-heartbeat',
            'Pregnancy guides' => 'fas fa-baby',
            'Menstrual tracking' => 'fas fa-calendar-alt',

            // Spa Treatments
            'Facials' => 'fas fa-smile',
            'Massages' => 'fas fa-hands',
            'Body scrubs' => 'fas fa-shower',

            // Fitness Classes
            'Yoga' => 'fas fa-leaf',
            'Pilates' => 'fas fa-dumbbell',
            'Zumba' => 'fas fa-music',
            'Strength training' => 'fas fa-dumbbell',

            // Salon Services
            'Haircuts' => 'fas fa-cut',
            'Styling' => 'fas fa-magic',
            'Coloring' => 'fas fa-palette',

            // Makeup Services
            'Bridal makeup' => 'fas fa-ring',
            'Event makeup' => 'fas fa-star',
            'Tutorials' => 'fas fa-play-circle',

            // Nail Care
            'Manicures' => 'fas fa-hand-paper',
            'Pedicures' => 'fas fa-shoe-prints',
            'Nail art' => 'fas fa-paint-brush',

            // Nutrition Counseling
            'Diet plans' => 'fas fa-clipboard-list',
            'Weight management programs' => 'fas fa-weight',

            // Therapy Sessions
            'Individual Therapy' => 'fas fa-user',
            'Couple Therapy' => 'fas fa-heart',
            'Family therapy' => 'fas fa-users',

            // Wellness Workshops
            'Mindfulness' => 'fas fa-brain',
            'Stress management' => 'fas fa-leaf',
            'Healthy cooking' => 'fas fa-utensils',

            // Artistic Services
            'Photography sessions' => 'fas fa-camera',
            'Painting classes' => 'fas fa-paint-brush',
            'Pottery making' => 'fas fa-hands',
            'Craft workshops' => 'fas fa-cut',

            // Elderly Care & Companionship Services
            'In-home care' => 'fas fa-home',
            'Companionship visits' => 'fas fa-handshake',
        ];

        return $iconMap[$serviceName] ?? 'fas fa-concierge-bell';
    }

    /**
     * Generate description for service
     */
    private function generateDescription(string $serviceName): string
    {
        $descriptionMap = [
            // Healthcare & Femtech
            'Women\'s Health' => 'Women\'s health services and consultations',
            'Mental Health Support' => 'Mental health support and counseling',
            'Fertility monitoring' => 'Fertility tracking and monitoring services',
            'Pregnancy guides' => 'Pregnancy guidance and support services',
            'Menstrual tracking' => 'Menstrual cycle tracking and health services',

            // Spa Treatments
            'Facials' => 'Professional facial treatments',
            'Massages' => 'Therapeutic and relaxation massages',
            'Body scrubs' => 'Exfoliating body scrub treatments',

            // Fitness Classes
            'Yoga' => 'Yoga classes and sessions',
            'Pilates' => 'Pilates classes and training',
            'Zumba' => 'Zumba dance fitness classes',
            'Strength training' => 'Strength and resistance training',

            // Salon Services
            'Haircuts' => 'Professional hair cutting services',
            'Styling' => 'Hair styling and design services',
            'Coloring' => 'Hair coloring and highlighting services',

            // Makeup Services
            'Bridal makeup' => 'Bridal makeup and styling services',
            'Event makeup' => 'Special event makeup services',
            'Tutorials' => 'Makeup tutorials and training',

            // Nail Care
            'Manicures' => 'Professional manicure services',
            'Pedicures' => 'Professional pedicure services',
            'Nail art' => 'Creative nail art and design services',

            // Nutrition Counseling
            'Diet plans' => 'Personalized diet planning services',
            'Weight management programs' => 'Weight management and fitness programs',

            // Therapy Sessions
            'Individual Therapy' => 'Individual therapy and counseling sessions',
            'Couple Therapy' => 'Couples therapy and relationship counseling',
            'Family therapy' => 'Family therapy and counseling services',

            // Wellness Workshops
            'Mindfulness' => 'Mindfulness and meditation sessions',
            'Stress management' => 'Stress management workshops and techniques',
            'Healthy cooking' => 'Healthy cooking classes and workshops',

            // Artistic Services
            'Photography sessions' => 'Professional photography sessions',
            'Painting classes' => 'Art and painting classes',
            'Pottery making' => 'Pottery and ceramics classes',
            'Craft workshops' => 'Creative craft workshops and classes',

            // Elderly Care & Companionship Services
            'In-home care' => 'In-home elderly care services',
            'Companionship visits' => 'Companionship and social visits',
        ];

        return $descriptionMap[$serviceName] ?? "Professional {$serviceName} services";
    }
}
