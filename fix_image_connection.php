<?php

// This script fixes issues with image connections in the Laravel backend
// It ensures proper CORS headers are set and fixes any storage link issues

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Load the Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

echo "Starting image connection fix script...\n";

// 1. Check and fix storage link
echo "CHECKING STORAGE LINK:\n";
$storageLink = public_path('storage');
$storagePath = storage_path('app/public');

if (file_exists($storageLink)) {
    if (is_link($storageLink)) {
        $target = readlink($storageLink);
        echo "✓ Storage link exists and points to: $target\n";
        
        $expected = $storagePath;
        if (realpath($target) === realpath($expected)) {
            echo "✓ Link target is correct\n";
        } else {
            echo "✗ Link target is INCORRECT. Expected: $expected\n";
            echo "  Fixing link...\n";
            unlink($storageLink);
            symlink($expected, $storageLink);
            echo "  Link recreated.\n";
        }
    } else {
        echo "✗ Storage path exists but is NOT a symbolic link!\n";
        echo "  Removing and recreating...\n";
        if (is_dir($storageLink)) {
            File::deleteDirectory($storageLink);
        } else {
            unlink($storageLink);
        }
        symlink($storagePath, $storageLink);
        echo "  Link recreated.\n";
    }
} else {
    echo "✗ Storage link doesn't exist. Creating...\n";
    symlink($storagePath, $storageLink);
    echo "  Link created.\n";
}

// 2. Check and create required directories
echo "\nCHECKING REQUIRED DIRECTORIES:\n";
$directories = [
    'storage/app/public/products' => storage_path('app/public/products'),
    'storage/app/public/product-colors' => storage_path('app/public/product-colors'),
    'public/images/products' => public_path('images/products'),
    'public/images/product-colors' => public_path('images/product-colors'),
];

foreach ($directories as $name => $path) {
    if (!file_exists($path)) {
        echo "Creating directory: $name\n";
        mkdir($path, 0755, true);
    } else {
        echo "✓ Directory exists: $name\n";
    }
}

// 3. Check and fix .htaccess for CORS
echo "\nCHECKING HTACCESS FOR CORS:\n";
$htaccessPath = public_path('.htaccess');
$corsRules = "
# Allow CORS for image files
<IfModule mod_headers.c>
    <FilesMatch \"\.(jpg|jpeg|png|gif|webp)$\">
        Header set Access-Control-Allow-Origin \"*\"
        Header set Access-Control-Allow-Methods \"GET, OPTIONS\"
        Header set Access-Control-Allow-Headers \"Origin, X-Requested-With, Content-Type, Accept\"
        Header set Cache-Control \"max-age=86400, public\"
    </FilesMatch>
</IfModule>
";

if (file_exists($htaccessPath)) {
    $htaccessContent = file_get_contents($htaccessPath);
    if (strpos($htaccessContent, 'Access-Control-Allow-Origin') === false) {
        echo "Adding CORS rules to .htaccess\n";
        file_put_contents($htaccessPath, $htaccessContent . $corsRules);
    } else {
        echo "✓ CORS rules already exist in .htaccess\n";
    }
} else {
    echo "Creating .htaccess with CORS rules\n";
    file_put_contents($htaccessPath, $corsRules);
}

// 4. Create a test image in each directory to verify access
echo "\nCREATING TEST IMAGES:\n";
$testImageDirs = [
    storage_path('app/public/products'),
    storage_path('app/public/product-colors'),
    public_path('images/products'),
    public_path('images/product-colors'),
];

foreach ($testImageDirs as $dir) {
    $testImagePath = $dir . '/test_image.png';
    if (!file_exists($testImagePath)) {
        echo "Creating test image in: $dir\n";
        
        // Create a simple test image
        $image = imagecreatetruecolor(100, 100);
        $bgColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $bgColor);
        imagestring($image, 5, 10, 40, 'Test Image', $textColor);
        imagepng($image, $testImagePath);
        imagedestroy($image);
        
        echo "  Test image created: $testImagePath\n";
    } else {
        echo "✓ Test image already exists in: $dir\n";
    }
}

// 5. Check and update .env file
echo "\nCHECKING ENV CONFIGURATION:\n";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // Check APP_URL
    if (strpos($envContent, 'APP_URL=http://localhost:8000') === false) {
        echo "Updating APP_URL in .env to http://localhost:8000\n";
        $envContent = preg_replace('/APP_URL=.*/', 'APP_URL=http://localhost:8000', $envContent);
    } else {
        echo "✓ APP_URL is correctly set to http://localhost:8000\n";
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

echo "\nImage connection fix script completed successfully!\n";
echo "Please restart your Laravel server for changes to take effect.\n";
