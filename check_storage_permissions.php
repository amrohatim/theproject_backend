<?php

// This script checks the storage permissions

// Include the autoloader
require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

echo "Checking storage permissions...\n";

// Check if the storage directory exists
$storagePath = storage_path();
echo "Storage path: $storagePath\n";

if (file_exists($storagePath)) {
    echo "Storage directory exists.\n";
    
    // Check if the storage directory is writable
    if (is_writable($storagePath)) {
        echo "Storage directory is writable.\n";
    } else {
        echo "WARNING: Storage directory is not writable.\n";
    }
} else {
    echo "WARNING: Storage directory does not exist.\n";
}

// Check if the public storage directory exists
$publicStoragePath = storage_path('app/public');
echo "Public storage path: $publicStoragePath\n";

if (file_exists($publicStoragePath)) {
    echo "Public storage directory exists.\n";
    
    // Check if the public storage directory is writable
    if (is_writable($publicStoragePath)) {
        echo "Public storage directory is writable.\n";
    } else {
        echo "WARNING: Public storage directory is not writable.\n";
    }
} else {
    echo "WARNING: Public storage directory does not exist.\n";
    
    // Create the directory
    echo "Creating public storage directory...\n";
    if (mkdir($publicStoragePath, 0755, true)) {
        echo "Public storage directory created successfully.\n";
    } else {
        echo "Failed to create public storage directory.\n";
    }
}

// Check if the companies directory exists in storage
$companiesStoragePath = storage_path('app/public/companies');
echo "Companies storage path: $companiesStoragePath\n";

if (file_exists($companiesStoragePath)) {
    echo "Companies directory exists in storage.\n";
    
    // Check if the companies directory is writable
    if (is_writable($companiesStoragePath)) {
        echo "Companies directory in storage is writable.\n";
    } else {
        echo "WARNING: Companies directory in storage is not writable.\n";
    }
} else {
    echo "Companies directory does not exist in storage.\n";
    
    // Create the directory
    echo "Creating companies directory in storage...\n";
    if (mkdir($companiesStoragePath, 0755, true)) {
        echo "Companies directory in storage created successfully.\n";
    } else {
        echo "Failed to create companies directory in storage.\n";
    }
}

// Check if the public directory exists
$publicPath = public_path();
echo "Public path: $publicPath\n";

if (file_exists($publicPath)) {
    echo "Public directory exists.\n";
    
    // Check if the public directory is writable
    if (is_writable($publicPath)) {
        echo "Public directory is writable.\n";
    } else {
        echo "WARNING: Public directory is not writable.\n";
    }
} else {
    echo "WARNING: Public directory does not exist.\n";
}

// Check if the images directory exists in public
$imagesPublicPath = public_path('images');
echo "Images public path: $imagesPublicPath\n";

if (file_exists($imagesPublicPath)) {
    echo "Images directory exists in public.\n";
    
    // Check if the images directory is writable
    if (is_writable($imagesPublicPath)) {
        echo "Images directory in public is writable.\n";
    } else {
        echo "WARNING: Images directory in public is not writable.\n";
    }
} else {
    echo "Images directory does not exist in public.\n";
    
    // Create the directory
    echo "Creating images directory in public...\n";
    if (mkdir($imagesPublicPath, 0755, true)) {
        echo "Images directory in public created successfully.\n";
    } else {
        echo "Failed to create images directory in public.\n";
    }
}

// Check if the companies directory exists in public
$companiesPublicPath = public_path('images/companies');
echo "Companies public path: $companiesPublicPath\n";

if (file_exists($companiesPublicPath)) {
    echo "Companies directory exists in public.\n";
    
    // Check if the companies directory is writable
    if (is_writable($companiesPublicPath)) {
        echo "Companies directory in public is writable.\n";
    } else {
        echo "WARNING: Companies directory in public is not writable.\n";
    }
} else {
    echo "Companies directory does not exist in public.\n";
    
    // Create the directory
    echo "Creating companies directory in public...\n";
    if (mkdir($companiesPublicPath, 0755, true)) {
        echo "Companies directory in public created successfully.\n";
    } else {
        echo "Failed to create companies directory in public.\n";
    }
}

echo "\nStorage permissions check completed.\n";
