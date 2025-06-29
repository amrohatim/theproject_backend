<?php

// This script fixes product image paths and ensures proper storage configuration

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

echo "Starting image path fix script...\n";

// 1. Check and fix storage link
$publicPath = public_path();
$storagePath = storage_path('app/public');
$linkPath = public_path('storage');

echo "Public path: $publicPath\n";
echo "Storage path: $storagePath\n";
echo "Link path: $linkPath\n";

// Ensure storage/app/public directory exists
if (!File::exists($storagePath)) {
    echo "Creating storage/app/public directory...\n";
    File::makeDirectory($storagePath, 0755, true);
    echo "Created storage/app/public directory.\n";
}

// Ensure products directory exists
$productsPath = $storagePath . '/products';
if (!File::exists($productsPath)) {
    echo "Creating products directory...\n";
    File::makeDirectory($productsPath, 0755, true);
    echo "Created products directory.\n";
}

// Check if the link already exists
if (File::exists($linkPath)) {
    echo "Storage link exists.\n";
    
    // Check if it's a symbolic link
    if (is_link($linkPath)) {
        echo "It's a symbolic link pointing to: " . readlink($linkPath) . "\n";
    } else {
        echo "It's not a symbolic link. Removing it...\n";
        
        // If it's a directory, remove it recursively
        if (is_dir($linkPath)) {
            File::deleteDirectory($linkPath);
        } else {
            File::delete($linkPath);
        }
        
        echo "Removed. Creating symbolic link...\n";
        try {
            symlink($storagePath, $linkPath);
            echo "Symbolic link created.\n";
        } catch (Exception $e) {
            echo "Error creating symbolic link: " . $e->getMessage() . "\n";
            echo "Trying to create a directory copy instead...\n";
            
            // If symlink fails (common on Windows), create a directory copy
            File::copyDirectory($storagePath, $linkPath);
            echo "Directory copy created as fallback.\n";
        }
    }
} else {
    echo "Link doesn't exist. Creating symbolic link...\n";
    try {
        symlink($storagePath, $linkPath);
        echo "Symbolic link created.\n";
    } catch (Exception $e) {
        echo "Error creating symbolic link: " . $e->getMessage() . "\n";
        echo "Trying to create a directory copy instead...\n";
        
        // If symlink fails (common on Windows), create a directory copy
        File::copyDirectory($storagePath, $linkPath);
        echo "Directory copy created as fallback.\n";
    }
}

// 2. Fix product image paths in the database
echo "\nFixing product image paths in the database...\n";

try {
    $products = DB::table('products')->get();
    echo "Found " . count($products) . " products.\n";
    
    $updatedCount = 0;
    
    foreach ($products as $product) {
        if (empty($product->image)) {
            echo "Product ID {$product->id}: No image path to fix.\n";
            continue;
        }
        
        $originalPath = $product->image;
        $filename = basename($originalPath);
        $newPath = "/storage/products/{$filename}";
        
        // Check if the file exists in storage/app/public/products
        $storageFilePath = $storagePath . '/products/' . $filename;
        
        if (!File::exists($storageFilePath)) {
            echo "Product ID {$product->id}: Image file not found at {$storageFilePath}.\n";
            
            // Try to find the file in other locations
            $possiblePaths = [
                public_path("images/products/{$filename}"),
                public_path($originalPath),
                storage_path("app/{$filename}"),
                storage_path("app/public/{$filename}"),
                public_path(ltrim($originalPath, '/')),
            ];
            
            $foundPath = null;
            foreach ($possiblePaths as $path) {
                if (File::exists($path)) {
                    $foundPath = $path;
                    break;
                }
            }
            
            if ($foundPath) {
                echo "Found image at {$foundPath}. Copying to {$storageFilePath}...\n";
                
                // Ensure the directory exists
                if (!File::exists(dirname($storageFilePath))) {
                    File::makeDirectory(dirname($storageFilePath), 0755, true);
                }
                
                // Copy the file
                File::copy($foundPath, $storageFilePath);
                echo "Copied image to {$storageFilePath}.\n";
            } else {
                echo "Could not find image file for product ID {$product->id}. Creating a placeholder...\n";
                
                // Create a placeholder image
                $placeholderPath = public_path('images/placeholder.jpg');
                if (File::exists($placeholderPath)) {
                    File::copy($placeholderPath, $storageFilePath);
                    echo "Copied placeholder image to {$storageFilePath}.\n";
                } else {
                    echo "Placeholder image not found at {$placeholderPath}.\n";
                }
            }
        } else {
            echo "Product ID {$product->id}: Image file exists at {$storageFilePath}.\n";
        }
        
        // Update the database record
        DB::table('products')
            ->where('id', $product->id)
            ->update(['image' => $newPath]);
        
        $updatedCount++;
        echo "Updated product ID {$product->id} image path from {$originalPath} to {$newPath}.\n";
    }
    
    echo "\nUpdated {$updatedCount} product image paths.\n";
    
} catch (Exception $e) {
    echo "Error fixing product image paths: " . $e->getMessage() . "\n";
}

echo "\nImage path fix script completed.\n";
