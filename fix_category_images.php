<?php
// fix_category_images.php

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

echo "Starting category image fix script...\n";

// 1. Check and fix storage link
$publicPath = public_path();
$storagePath = storage_path('app/public');
$linkPath = public_path('storage');

echo "Public path: $publicPath\n";
echo "Storage path: $storagePath\n";
echo "Link path: $linkPath\n";

// Remove existing symbolic link if it's broken
if (is_link($linkPath) && !file_exists($linkPath)) {
    echo "Removing broken symbolic link...\n";
    unlink($linkPath);
}

// Create symbolic link if it doesn't exist
if (!file_exists($linkPath)) {
    echo "Creating symbolic link...\n";
    try {
        symlink($storagePath, $linkPath);
        echo "Symbolic link created successfully.\n";
    } catch (\Exception $e) {
        echo "Error creating symbolic link: " . $e->getMessage() . "\n";
        echo "Trying Artisan command...\n";
        
        // Try using the Artisan command
        \Artisan::call('storage:link');
        echo \Artisan::output();
    }
} else {
    echo "Symbolic link already exists.\n";
}

// 2. Create categories directory if it doesn't exist
$categoriesPath = storage_path('app/public/categories');
if (!file_exists($categoriesPath)) {
    echo "Creating categories directory in storage/app/public...\n";
    mkdir($categoriesPath, 0755, true);
    echo "Categories directory created.\n";
} else {
    echo "Categories directory already exists in storage/app/public.\n";
}

// Create categories directory in public/storage if it doesn't exist
$publicCategoriesPath = public_path('storage/categories');
if (!file_exists($publicCategoriesPath)) {
    echo "Creating categories directory in public/storage...\n";
    mkdir($publicCategoriesPath, 0755, true);
    echo "Categories directory created in public/storage.\n";
} else {
    echo "Categories directory already exists in public/storage.\n";
}

// 3. Fix category image paths in the database
echo "Fixing category image paths in the database...\n";
$categories = Category::all();
$count = 0;

foreach ($categories as $category) {
    $originalImage = $category->getRawOriginal('image');
    
    if (empty($originalImage)) {
        continue;
    }
    
    $newImage = $originalImage;
    $filename = basename($originalImage);
    
    // If image doesn't start with http:// or https:// or /storage/categories
    if (!str_starts_with($originalImage, 'http://') && 
        !str_starts_with($originalImage, 'https://') && 
        !str_starts_with($originalImage, '/storage/categories/')) {
        
        // Check if the image exists in various locations
        $possiblePaths = [
            "storage/categories/{$filename}",
            "images/categories/{$filename}",
            str_replace('/storage/', 'storage/', $originalImage),
            $originalImage,
        ];
        
        $found = false;
        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                // Copy the file to the correct location
                $destPath = public_path("storage/categories/{$filename}");
                if (!file_exists($destPath)) {
                    copy(public_path($path), $destPath);
                    echo "Copied image from " . public_path($path) . " to " . $destPath . "\n";
                }
                
                $newImage = "/storage/categories/{$filename}";
                $found = true;
                break;
            }
        }
        
        // If not found in public, check in storage
        if (!$found) {
            $storagePaths = [
                "app/public/categories/{$filename}",
                "app/public/" . str_replace('/storage/', '', $originalImage),
            ];
            
            foreach ($storagePaths as $path) {
                if (file_exists(storage_path($path))) {
                    // Copy the file to the correct location
                    $destPath = public_path("storage/categories/{$filename}");
                    if (!file_exists($destPath)) {
                        copy(storage_path($path), $destPath);
                        echo "Copied image from " . storage_path($path) . " to " . $destPath . "\n";
                    }
                    
                    $newImage = "/storage/categories/{$filename}";
                    $found = true;
                    break;
                }
            }
        }
        
        // If still not found, create a placeholder image
        if (!$found) {
            $placeholderPath = "storage/categories/placeholder_{$category->id}.jpg";
            $placeholderFullPath = public_path($placeholderPath);
            
            // Create directory if it doesn't exist
            if (!file_exists(dirname($placeholderFullPath))) {
                mkdir(dirname($placeholderFullPath), 0755, true);
            }
            
            // Create a simple placeholder image
            $image = imagecreatetruecolor(800, 600);
            $bgColor = imagecolorallocate($image, 240, 240, 240);
            $textColor = imagecolorallocate($image, 100, 100, 100);
            imagefill($image, 0, 0, $bgColor);
            imagestring($image, 5, 300, 280, "Category: " . $category->name, $textColor);
            imagejpeg($image, $placeholderFullPath);
            imagedestroy($image);
            
            echo "Created placeholder image for category {$category->name} at {$placeholderFullPath}\n";
            $newImage = '/' . $placeholderPath;
        }
        
        // Update the database
        if ($newImage !== $originalImage) {
            DB::table('categories')
                ->where('id', $category->id)
                ->update(['image' => $newImage]);
            
            $count++;
            echo "Updated image path for category {$category->name}: {$originalImage} -> {$newImage}\n";
        }
    }
}

echo "Fixed {$count} category image paths.\n";
echo "Category image fix script completed.\n";
