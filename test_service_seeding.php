<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

// Test the service category image mapping
function testServiceCategoryImageMapping()
{
    echo "Testing Service Category Image Mapping\n";
    echo "=====================================\n\n";

    $serviceImagePath = __DIR__ . "/app service category images";
    
    if (!File::exists($serviceImagePath)) {
        echo "❌ Service category images directory not found at: {$serviceImagePath}\n";
        return;
    }

    echo "✅ Service category images directory found\n\n";

    // Get all service category folders
    $folders = File::directories($serviceImagePath);
    
    foreach ($folders as $folder) {
        $folderName = basename($folder);
        echo "📁 Category: {$folderName}\n";
        
        $imageFiles = File::files($folder);
        foreach ($imageFiles as $imageFile) {
            echo "   📷 {$imageFile->getFilename()}\n";
        }
        echo "\n";
    }

    echo "Total service categories found: " . count($folders) . "\n";
}

function testImagePathGeneration()
{
    echo "\nTesting Image Path Generation\n";
    echo "============================\n\n";

    $testCategories = [
        'Healthcare & Femtech' => 'Women\'s Health',
        'Spa Treatments' => 'Facials',
        'Fitness Classes' => 'Yoga',
        'Salon Services' => 'Haircuts',
        'Makeup Services' => 'Bridal makeup',
        'Nail Care' => 'Manicures',
        'Therapy Sessions' => 'Individual Therapy',
        'Nutrition Counseling' => 'Diet plans',
        'Artistic Services' => 'Photography sessions',
        'Elderly Care & Companionship Services' => 'In-home care',
    ];

    foreach ($testCategories as $categoryName => $subcategoryName) {
        $imagePath = getServiceCategoryImage($categoryName, $subcategoryName);
        echo "Category: {$categoryName} -> Subcategory: {$subcategoryName}\n";
        echo "Generated path: {$imagePath}\n";
        
        // Check if the file actually exists
        $fullPath = __DIR__ . "/" . $imagePath;
        if (file_exists($fullPath)) {
            echo "✅ Image file exists\n";
        } else {
            echo "❌ Image file not found at: {$fullPath}\n";
        }
        echo "\n";
    }
}

function getServiceCategoryImage(string $categoryName, ?string $subcategoryName = null): string
{
    $imagePath = __DIR__ . "/app service category images/{$categoryName}";
    
    if (File::exists($imagePath)) {
        $imageFiles = File::files($imagePath);
        if (!empty($imageFiles)) {
            // If subcategory is specified, look for specific image
            if ($subcategoryName) {
                $specificImage = collect($imageFiles)->first(function ($file) use ($subcategoryName) {
                    return Str::contains($file->getFilename(), $subcategoryName);
                });
                
                if ($specificImage) {
                    return "app service category images/{$categoryName}/{$specificImage->getFilename()}";
                }
            }
            
            // Look for main category image
            $mainImage = collect($imageFiles)->first(function ($file) use ($categoryName) {
                return Str::contains($file->getFilename(), $categoryName);
            });
            
            if ($mainImage) {
                return "app service category images/{$categoryName}/{$mainImage->getFilename()}";
            }
            
            // Fallback to first image in directory
            return "app service category images/{$categoryName}/{$imageFiles[0]->getFilename()}";
        }
    }
    
    // Fallback to placeholder
    return '/images/categories/placeholder.jpg';
}

// Run the tests
testServiceCategoryImageMapping();
testImagePathGeneration();

echo "\n✅ Service seeding test completed!\n";
