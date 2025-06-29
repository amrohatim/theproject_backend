<?php

// This script fixes image paths and copies images to the correct locations

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\Service;
use App\Models\Deal;
use App\Models\Company;

echo "Starting image fix script...\n";

// Create necessary directories
$directories = [
    'public/images',
    'public/images/products',
    'public/images/services',
    'public/images/deals',
    'public/images/companies'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "Creating directory: $dir\n";
        mkdir($dir, 0755, true);
    } else {
        echo "Directory already exists: $dir\n";
    }
}

// Function to copy images from storage to public
function copyImagesToPublic($sourceDir, $destDir, $model, $column) {
    echo "\nCopying images from $sourceDir to $destDir\n";
    
    // Get all files from storage
    if (is_dir(storage_path("app/public/$sourceDir"))) {
        $files = scandir(storage_path("app/public/$sourceDir"));
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && is_file(storage_path("app/public/$sourceDir/$file"))) {
                $source = storage_path("app/public/$sourceDir/$file");
                $destination = public_path("images/$destDir/$file");
                
                if (copy($source, $destination)) {
                    echo "Copied: $file\n";
                } else {
                    echo "Failed to copy: $file\n";
                }
            }
        }
    } else {
        echo "Source directory does not exist: storage/app/public/$sourceDir\n";
    }
    
    // Update database records
    $items = $model::all();
    foreach ($items as $item) {
        $oldPath = $item->$column;
        if ($oldPath) {
            $filename = basename($oldPath);
            $newPath = "images/$destDir/$filename";
            
            // Check if the file exists in the new location
            if (file_exists(public_path($newPath))) {
                echo "Updating database record for $model ID: {$item->id}\n";
                echo "  Old path: $oldPath\n";
                echo "  New path: $newPath\n";
                
                // Update the database directly to avoid model accessors/mutators
                DB::table($item->getTable())
                    ->where('id', $item->id)
                    ->update([$column => $newPath]);
            } else {
                echo "File does not exist in new location for $model ID: {$item->id}\n";
            }
        }
    }
}

// Copy and update product images
copyImagesToPublic('products', 'products', Product::class, 'image');

// Copy and update service images
copyImagesToPublic('services', 'services', Service::class, 'image');

// Copy and update deal images
copyImagesToPublic('deals', 'deals', Deal::class, 'image');

// Copy and update company images
copyImagesToPublic('companies', 'companies', Company::class, 'logo');

echo "\nImage fix script completed\n";
