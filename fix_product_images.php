<?php

// This script fixes product image URLs and ensures the storage link is properly set up

// 1. Update the .env file to use localhost instead of the IP address
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    // Replace the APP_URL with localhost
    $envContent = preg_replace('/APP_URL=http:\/\/192\.168\.70\.48:8000/', 'APP_URL=http://localhost:8000', $envContent);
    
    // Update FILESYSTEM_DISK to use 'public' instead of 'local'
    $envContent = preg_replace('/FILESYSTEM_DISK=local/', 'FILESYSTEM_DISK=public', $envContent);
    
    file_put_contents('.env', $envContent);
    echo "Updated .env file with correct APP_URL and FILESYSTEM_DISK settings.\n";
}

// 2. Make sure the storage link is created
if (!is_link('public/storage')) {
    echo "Storage link doesn't exist. Creating it...\n";
    exec('php artisan storage:link', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "Successfully created storage link.\n";
    } else {
        echo "Failed to create storage link. Error: " . implode("\n", $output) . "\n";
    }
} else {
    echo "Storage link already exists.\n";
}

// 3. Clear the application cache to ensure new settings take effect
exec('php artisan config:clear', $output, $returnCode);
if ($returnCode === 0) {
    echo "Successfully cleared config cache.\n";
} else {
    echo "Failed to clear config cache.\n";
}

exec('php artisan cache:clear', $output, $returnCode);
if ($returnCode === 0) {
    echo "Successfully cleared application cache.\n";
} else {
    echo "Failed to clear application cache.\n";
}

// 4. Check if the vendor/products route exists and is accessible
echo "\nChecking routes...\n";
$routes = [];
exec('php artisan route:list --name=vendor.products', $routes, $returnCode);

if (!empty($routes)) {
    echo "Vendor products route exists:\n";
    foreach ($routes as $route) {
        echo $route . "\n";
    }
} else {
    echo "Warning: Vendor products route not found. Make sure the route is properly defined.\n";
}

echo "\nFix completed. Product images should now be visible at http://localhost:8000/vendor/products\n";
echo "If images are still not visible, please check that:\n";
echo "1. The image files exist in storage/app/public/products/\n";
echo "2. The web server has proper permissions to access the storage directory\n";
echo "3. The symbolic link is correctly set up between public/storage and storage/app/public\n";