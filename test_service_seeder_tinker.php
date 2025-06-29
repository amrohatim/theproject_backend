<?php

/**
 * Test script to run the ServiceCategoriesSeeder logic using Laravel
 * Run this with: php artisan tinker < test_service_seeder_tinker.php
 */

echo "Testing Service Categories Seeder\n";
echo "=================================\n\n";

// Test 1: Check if directory exists
$serviceImagePath = base_path('app service category images');
echo "Checking directory: {$serviceImagePath}\n";

if (!File::exists($serviceImagePath)) {
    echo "❌ Service category images directory not found\n";
    exit(1);
}

echo "✅ Directory found!\n\n";

// Test 2: Get directories
$directories = File::directories($serviceImagePath);
echo "Found " . count($directories) . " directories:\n";

foreach ($directories as $directory) {
    echo "  - " . basename($directory) . "\n";
}

echo "\n";

// Test 3: Check current service categories in database
$existingServiceCategories = App\Models\Category::where('type', 'service')->count();
echo "Current service categories in database: {$existingServiceCategories}\n\n";

// Test 4: Test the seeder logic (dry run)
echo "Testing seeder logic (dry run):\n";
echo "================================\n";

foreach ($directories as $directory) {
    $categoryName = basename($directory);
    echo "Processing: {$categoryName}\n";
    
    $imageFiles = File::files($directory);
    echo "  Images found: " . count($imageFiles) . "\n";
    
    $parentImageFound = false;
    $childImages = [];
    
    foreach ($imageFiles as $imageFile) {
        $filename = $imageFile->getFilenameWithoutExtension();
        if ($filename === $categoryName) {
            $parentImageFound = true;
        } else {
            $childImages[] = $filename;
        }
    }
    
    echo "  Parent image: " . ($parentImageFound ? "✅ Found" : "❌ Missing") . "\n";
    echo "  Child categories: " . count($childImages) . "\n";
    
    if (!empty($childImages)) {
        echo "    - " . implode("\n    - ", array_slice($childImages, 0, 3)) . "\n";
        if (count($childImages) > 3) {
            echo "    - ... and " . (count($childImages) - 3) . " more\n";
        }
    }
    
    echo "\n";
}

echo "✅ Dry run completed successfully!\n";
echo "\nTo run the actual seeder:\n";
echo "php artisan db:seed --class=ServiceCategoriesSeeder\n";
