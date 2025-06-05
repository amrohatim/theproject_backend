<?php

// This script diagnoses and fixes image display issues in the provider dashboard

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

echo "=== Provider Dashboard Image Fix Tool ===\n\n";

// 1. Check storage link
echo "CHECKING STORAGE LINK:\n";
$publicPath = public_path();
$storagePath = storage_path('app/public');
$storageLink = public_path('storage');

echo "Public path: $publicPath\n";
echo "Storage path: $storagePath\n";
echo "Storage link path: $storageLink\n";

if (!file_exists($storagePath)) {
    echo "Creating storage/app/public directory...\n";
    File::makeDirectory($storagePath, 0755, true);
    echo "Created storage/app/public directory.\n";
}

if (!file_exists($storageLink)) {
    echo "Storage link doesn't exist. Creating it...\n";
    
    // Create the storage link
    try {
        symlink($storagePath, $storageLink);
        echo "✓ Storage link created successfully.\n";
    } catch (\Exception $e) {
        echo "✗ Failed to create storage link: " . $e->getMessage() . "\n";
        echo "  Trying artisan command instead...\n";
        
        exec('php artisan storage:link', $output, $returnCode);
        if ($returnCode === 0) {
            echo "✓ Storage link created successfully using artisan command.\n";
        } else {
            echo "✗ Failed to create storage link using artisan command.\n";
            echo "  Error: " . implode("\n", $output) . "\n";
        }
    }
} else {
    echo "Storage link exists.\n";
    
    // Check if it's a symbolic link
    if (is_link($storageLink)) {
        echo "✓ It's a symbolic link pointing to: " . readlink($storageLink) . "\n";
        
        // Check if it points to the correct location
        if (readlink($storageLink) !== $storagePath) {
            echo "✗ Storage link points to incorrect location.\n";
            echo "  Current: " . readlink($storageLink) . "\n";
            echo "  Expected: $storagePath\n";
            echo "  Removing and recreating...\n";
            
            unlink($storageLink);
            symlink($storagePath, $storageLink);
            echo "✓ Storage link recreated successfully.\n";
        }
    } else {
        echo "✗ Storage path exists but is not a symbolic link.\n";
        echo "  Removing and recreating...\n";
        
        // If it's a directory, remove it recursively
        if (is_dir($storageLink)) {
            File::deleteDirectory($storageLink);
        } else {
            File::delete($storageLink);
        }
        
        // Create the symbolic link
        symlink($storagePath, $storageLink);
        echo "✓ Storage link recreated successfully.\n";
    }
}

// 2. Check and create required directories
echo "\nCHECKING REQUIRED DIRECTORIES:\n";
$requiredDirs = [
    'storage/app/public/products',
    'storage/app/public/product-colors',
    'storage/app/public/providers',
    'storage/app/public/users',
    'public/images/products',
    'public/images/product-colors',
    'public/images/provider_products',
    'public/images/providers',
    'public/images/users',
];

foreach ($requiredDirs as $dir) {
    $fullPath = base_path($dir);
    if (!file_exists($fullPath)) {
        echo "Creating directory: $dir\n";
        File::makeDirectory($fullPath, 0755, true);
        echo "✓ Directory created: $dir\n";
    } else {
        echo "✓ Directory exists: $dir\n";
    }
}

// 3. Check .env configuration
echo "\nCHECKING ENV CONFIGURATION:\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Check APP_URL
    $appUrl = Config::get('app.url');
    echo "Current APP_URL: $appUrl\n";
    
    // Determine the correct APP_URL based on the server
    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $serverPort = $_SERVER['SERVER_PORT'] ?? '8000';
    $correctAppUrl = "http://{$serverName}:{$serverPort}";
    
    if ($appUrl !== $correctAppUrl) {
        echo "Updating APP_URL in .env to $correctAppUrl\n";
        $envContent = preg_replace('/APP_URL=.*/', "APP_URL={$correctAppUrl}", $envContent);
    } else {
        echo "✓ APP_URL is correctly set.\n";
    }
    
    // Check FILESYSTEM_DISK
    if (strpos($envContent, 'FILESYSTEM_DISK=public') === false) {
        echo "Updating FILESYSTEM_DISK in .env to public\n";
        if (strpos($envContent, 'FILESYSTEM_DISK=') !== false) {
            $envContent = preg_replace('/FILESYSTEM_DISK=.*/', 'FILESYSTEM_DISK=public', $envContent);
        } else {
            $envContent .= "\nFILESYSTEM_DISK=public\n";
        }
    } else {
        echo "✓ FILESYSTEM_DISK is correctly set to public\n";
    }
    
    file_put_contents($envPath, $envContent);
}

// 4. Clear configuration cache
echo "\nCLEARING CONFIGURATION CACHE:\n";
exec('php artisan config:clear', $output, $returnCode);
if ($returnCode === 0) {
    echo "✓ Configuration cache cleared successfully.\n";
} else {
    echo "✗ Failed to clear configuration cache.\n";
}

// 5. Check provider product images
echo "\nCHECKING PROVIDER PRODUCT IMAGES:\n";
$providerProducts = DB::table('provider_products')->get();
echo "Found " . count($providerProducts) . " provider products.\n";

$fixedImages = 0;
foreach ($providerProducts as $product) {
    if (!empty($product->image)) {
        echo "Checking image: " . $product->image . "\n";
        
        // Check if the image exists
        $imagePath = public_path($product->image);
        if (!file_exists($imagePath)) {
            echo "✗ Image not found at: $imagePath\n";
            
            // Try to find the image in other locations
            $filename = basename($product->image);
            $possibleLocations = [
                public_path('images/provider_products/' . $filename),
                public_path('images/products/' . $filename),
                storage_path('app/public/provider_products/' . $filename),
                storage_path('app/public/products/' . $filename),
            ];
            
            $found = false;
            foreach ($possibleLocations as $location) {
                if (file_exists($location)) {
                    echo "✓ Found image at: $location\n";
                    
                    // Update the database record
                    $newPath = 'images/provider_products/' . $filename;
                    $destinationPath = public_path($newPath);
                    
                    // Ensure the directory exists
                    $dir = dirname($destinationPath);
                    if (!file_exists($dir)) {
                        mkdir($dir, 0755, true);
                    }
                    
                    // Copy the file if needed
                    if ($location !== $destinationPath) {
                        copy($location, $destinationPath);
                    }
                    
                    // Update the database
                    DB::table('provider_products')
                        ->where('id', $product->id)
                        ->update(['image' => $newPath]);
                    
                    echo "✓ Updated image path in database to: $newPath\n";
                    $fixedImages++;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                echo "✗ Could not find image in any location. Creating placeholder...\n";
                
                // Create a placeholder image
                $newPath = 'images/provider_products/placeholder_' . $product->id . '.png';
                $destinationPath = public_path($newPath);
                
                // Ensure the directory exists
                $dir = dirname($destinationPath);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                // Copy placeholder image
                copy(public_path('images/placeholder.png'), $destinationPath);
                
                // Update the database
                DB::table('provider_products')
                    ->where('id', $product->id)
                    ->update(['image' => $newPath]);
                
                echo "✓ Created placeholder image and updated database.\n";
                $fixedImages++;
            }
        } else {
            echo "✓ Image exists at: $imagePath\n";
        }
    }
}

echo "\nFixed $fixedImages provider product images.\n";

// 6. Check user profile images
echo "\nCHECKING USER PROFILE IMAGES:\n";
$users = DB::table('users')->whereNotNull('profile_image')->get();
echo "Found " . count($users) . " users with profile images.\n";

$fixedProfileImages = 0;
foreach ($users as $user) {
    echo "Checking profile image: " . $user->profile_image . "\n";
    
    // Check if the image exists
    $imagePath = public_path($user->profile_image);
    if (!file_exists($imagePath)) {
        echo "✗ Profile image not found at: $imagePath\n";
        
        // Try to find the image in other locations
        $filename = basename($user->profile_image);
        $possibleLocations = [
            public_path('images/users/' . $filename),
            storage_path('app/public/users/' . $filename),
        ];
        
        $found = false;
        foreach ($possibleLocations as $location) {
            if (file_exists($location)) {
                echo "✓ Found profile image at: $location\n";
                
                // Update the database record
                $newPath = 'images/users/' . $filename;
                $destinationPath = public_path($newPath);
                
                // Ensure the directory exists
                $dir = dirname($destinationPath);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                
                // Copy the file if needed
                if ($location !== $destinationPath) {
                    copy($location, $destinationPath);
                }
                
                // Update the database
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['profile_image' => $newPath]);
                
                echo "✓ Updated profile image path in database to: $newPath\n";
                $fixedProfileImages++;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            echo "✗ Could not find profile image in any location. Creating placeholder...\n";
            
            // Create a placeholder image
            $newPath = 'images/users/placeholder_' . $user->id . '.png';
            $destinationPath = public_path($newPath);
            
            // Ensure the directory exists
            $dir = dirname($destinationPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Copy placeholder image
            copy(public_path('images/placeholder.png'), $destinationPath);
            
            // Update the database
            DB::table('users')
                ->where('id', $user->id)
                ->update(['profile_image' => $newPath]);
            
            echo "✓ Created placeholder profile image and updated database.\n";
            $fixedProfileImages++;
        }
    } else {
        echo "✓ Profile image exists at: $imagePath\n";
    }
}

echo "\nFixed $fixedProfileImages user profile images.\n";

echo "\nProvider dashboard image fix script completed successfully!\n";
echo "Please restart your Laravel server for changes to take effect.\n";
