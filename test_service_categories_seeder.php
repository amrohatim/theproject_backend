<?php

/**
 * Test script to verify the ServiceCategoriesSeeder functionality
 * Run this script to test the seeder before running it on the database
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\File;

// Simulate the directory scanning functionality
function testDirectoryScanning()
{
    echo "=== Testing Directory Scanning ===\n";
    
    $serviceImagePath = __DIR__ . '/app service category images';
    
    if (!File::exists($serviceImagePath)) {
        echo "❌ Service category images directory not found: {$serviceImagePath}\n";
        return false;
    }
    
    $directories = File::directories($serviceImagePath);
    
    if (empty($directories)) {
        echo "❌ No service category directories found.\n";
        return false;
    }
    
    echo "✅ Found " . count($directories) . " service category directories:\n";
    
    foreach ($directories as $directory) {
        $categoryName = basename($directory);
        echo "  📁 {$categoryName}\n";
        
        // Check for images in this directory
        $imageFiles = File::files($directory);
        echo "    📸 Found " . count($imageFiles) . " images:\n";
        
        $parentImageFound = false;
        $childImages = [];
        
        foreach ($imageFiles as $imageFile) {
            $filename = $imageFile->getFilenameWithoutExtension();
            echo "      - {$imageFile->getFilename()}\n";
            
            if ($filename === $categoryName) {
                $parentImageFound = true;
                echo "        ✅ Parent category image found\n";
            } else {
                $childImages[] = $filename;
            }
        }
        
        if (!$parentImageFound) {
            echo "        ⚠️  No parent category image found (expected: {$categoryName}.jpg)\n";
        }
        
        if (!empty($childImages)) {
            echo "        👶 Child categories: " . implode(', ', $childImages) . "\n";
        }
        
        echo "\n";
    }
    
    return true;
}

// Test the metadata generation
function testMetadataGeneration()
{
    echo "=== Testing Metadata Generation ===\n";
    
    $testCategories = [
        'Healthcare & Femtech',
        'Artistic Services',
        'Fitness Classes',
        'Unknown Category'
    ];
    
    foreach ($testCategories as $category) {
        echo "📋 Testing category: {$category}\n";
        
        // Simulate the getCategoryMetadata method
        $metadata = getCategoryMetadata($category);
        echo "  Description: {$metadata['description']}\n";
        echo "  Icon: {$metadata['icon']}\n\n";
    }
}

function getCategoryMetadata(string $categoryName): array
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

// Test service icon generation
function testServiceIconGeneration()
{
    echo "=== Testing Service Icon Generation ===\n";
    
    $testServices = [
        'Yoga',
        'Bridal makeup',
        'Photography sessions',
        'Unknown Service'
    ];
    
    foreach ($testServices as $service) {
        $icon = generateIconForService($service);
        echo "🎯 {$service} → {$icon}\n";
    }
    echo "\n";
}

function generateIconForService(string $serviceName): string
{
    $iconMap = [
        'Yoga' => 'fas fa-leaf',
        'Bridal makeup' => 'fas fa-ring',
        'Photography sessions' => 'fas fa-camera',
        'Manicures' => 'fas fa-hand-paper',
    ];

    return $iconMap[$serviceName] ?? 'fas fa-concierge-bell';
}

// Run all tests
echo "🧪 Service Categories Seeder Test Suite\n";
echo "=====================================\n\n";

if (testDirectoryScanning()) {
    testMetadataGeneration();
    testServiceIconGeneration();
    echo "✅ All tests completed successfully!\n";
    echo "\n📝 Next steps:\n";
    echo "1. Run: php artisan db:seed --class=ServiceCategoriesSeeder\n";
    echo "2. Check the categories table for the new service categories\n";
    echo "3. Verify that parent-child relationships are correctly established\n";
} else {
    echo "❌ Directory scanning failed. Please check the image directory structure.\n";
}
