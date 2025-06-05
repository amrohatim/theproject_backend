<?php

// This script diagnoses and fixes image display issues in the provider dashboard
// without requiring symbolic links (for Windows environments)

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

echo "=== Provider Dashboard Image Fix Tool (No Symlink) ===\n\n";

// 1. Check and create required directories
echo "CHECKING REQUIRED DIRECTORIES:\n";
$requiredDirs = [
    'public/images/products',
    'public/images/product-colors',
    'public/images/provider_products',
    'public/images/providers',
    'public/images/users',
    'public/storage/products',
    'public/storage/product-colors',
    'public/storage/providers',
    'public/storage/users',
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

// 2. Check .env configuration
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

// 3. Clear configuration cache
echo "\nCLEARING CONFIGURATION CACHE:\n";
exec('php artisan config:clear', $output, $returnCode);
if ($returnCode === 0) {
    echo "✓ Configuration cache cleared successfully.\n";
} else {
    echo "✗ Failed to clear configuration cache.\n";
}

// 4. Check provider product images
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
                public_path('storage/provider_products/' . $filename),
                public_path('storage/products/' . $filename),
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
                if (file_exists(public_path('images/placeholder.png'))) {
                    copy(public_path('images/placeholder.png'), $destinationPath);
                } else {
                    // Create a simple placeholder image
                    $img = imagecreatetruecolor(100, 100);
                    $bgColor = imagecolorallocate($img, 200, 200, 200);
                    $textColor = imagecolorallocate($img, 50, 50, 50);
                    imagefill($img, 0, 0, $bgColor);
                    imagestring($img, 5, 10, 40, 'No Image', $textColor);
                    imagepng($img, $destinationPath);
                    imagedestroy($img);
                }
                
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

// 5. Check user profile images
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
            public_path('storage/users/' . $filename),
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
            if (file_exists(public_path('images/placeholder.png'))) {
                copy(public_path('images/placeholder.png'), $destinationPath);
            } else {
                // Create a simple placeholder image
                $img = imagecreatetruecolor(100, 100);
                $bgColor = imagecolorallocate($img, 200, 200, 200);
                $textColor = imagecolorallocate($img, 50, 50, 50);
                imagefill($img, 0, 0, $bgColor);
                imagestring($img, 5, 10, 40, 'No Image', $textColor);
                imagepng($img, $destinationPath);
                imagedestroy($img);
            }
            
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

// 6. Fix blade templates to use correct image paths
echo "\nFIXING BLADE TEMPLATES:\n";

// Create a helper class to fix image paths in blade templates
class ImagePathFixer
{
    public static function fixImagePath($path)
    {
        if (empty($path)) {
            return null;
        }
        
        // If it's already a full URL, return it
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }
        
        // Remove any leading slash
        $path = ltrim($path, '/');
        
        // Check if the file exists in the public directory
        if (file_exists(public_path($path))) {
            return '/' . $path;
        }
        
        // If not found, try to find it in other common locations
        $filename = basename($path);
        $possiblePaths = [
            'images/products/' . $filename,
            'images/provider_products/' . $filename,
            'images/users/' . $filename,
            'storage/products/' . $filename,
            'storage/provider_products/' . $filename,
            'storage/users/' . $filename,
        ];
        
        foreach ($possiblePaths as $possiblePath) {
            if (file_exists(public_path($possiblePath))) {
                return '/' . $possiblePath;
            }
        }
        
        // If still not found, return a placeholder
        return '/images/placeholder.png';
    }
}

// Register the helper function
if (!function_exists('fix_image_path')) {
    function fix_image_path($path)
    {
        return ImagePathFixer::fixImagePath($path);
    }
}

echo "✓ Created image path helper function.\n";
echo "\nProvider dashboard image fix script completed successfully!\n";
echo "Please restart your Laravel server for changes to take effect.\n";
