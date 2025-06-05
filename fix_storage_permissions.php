<?php
/**
 * This script fixes storage permissions and ensures the storage link is properly set up.
 * Run this script from the Laravel project root directory.
 */

// Check if we're in the Laravel project root
if (!file_exists('artisan')) {
    die("Error: This script must be run from the Laravel project root directory.\n");
}

echo "Starting storage permissions fix...\n";

// 1. Check if the storage link exists
$publicStoragePath = __DIR__ . '/public/storage';
$storageAppPublicPath = __DIR__ . '/storage/app/public';

echo "Checking storage link...\n";

if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        echo "Storage link exists and is a symbolic link.\n";
    } else {
        echo "Storage path exists but is not a symbolic link. Removing and recreating...\n";
        
        // Remove the existing directory
        if (is_dir($publicStoragePath)) {
            // On Windows, we need to use rmdir for directories
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                system('rmdir /S /Q "' . $publicStoragePath . '"');
            } else {
                system('rm -rf "' . $publicStoragePath . '"');
            }
        } else {
            unlink($publicStoragePath);
        }
        
        // Create the symbolic link
        echo "Creating symbolic link...\n";
        system('php artisan storage:link');
    }
} else {
    echo "Storage link doesn't exist. Creating...\n";
    system('php artisan storage:link');
}

// 2. Ensure the product-colors directory exists
$productColorsPath = $storageAppPublicPath . '/product-colors';
if (!file_exists($productColorsPath)) {
    echo "Creating product-colors directory...\n";
    mkdir($productColorsPath, 0755, true);
}

// 3. Set proper permissions for storage directories
echo "Setting proper permissions for storage directories...\n";

// On Windows, permissions work differently
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo "Windows detected. Skipping chmod commands.\n";
} else {
    system('chmod -R 755 storage');
    system('chmod -R 755 public/storage');
    system('chmod -R 755 ' . $productColorsPath);
    
    // Make sure the web server can write to these directories
    system('chmod -R 775 storage/app/public');
    system('chmod -R 775 ' . $productColorsPath);
    system('chmod -R 775 storage/logs');
    system('chmod -R 775 storage/framework');
}

// 4. Check if the product-colors directory is accessible
echo "Checking if product-colors directory is accessible...\n";

// Create a test file
$testFile = $productColorsPath . '/test.txt';
file_put_contents($testFile, 'This is a test file to check permissions.');

if (file_exists($testFile)) {
    echo "Successfully created test file. Directory is writable.\n";
    unlink($testFile); // Remove the test file
} else {
    echo "Failed to create test file. Check permissions.\n";
}

// 5. Create a .htaccess file to allow access to the storage directory
$htaccessPath = $publicStoragePath . '/.htaccess';
$htaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Allow access to all files
    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Allow from all
    </IfModule>
</IfModule>
EOT;

file_put_contents($htaccessPath, $htaccessContent);
echo "Created .htaccess file to allow access to storage directory.\n";

echo "Storage permissions fix completed.\n";
echo "If you're still having issues, make sure your web server has proper permissions to access these directories.\n";
