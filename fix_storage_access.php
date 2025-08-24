<?php

/**
 * Fix storage access issues by copying files from storage/app/public to public/storage
 * This is a workaround for shared hosting environments where symbolic links are restricted
 */

echo "Starting storage access fix...\n";

// Define source and destination paths
$sourceDir = __DIR__ . '/storage/app/public';
$destDir = __DIR__ . '/public/storage';

// Remove existing symbolic link if it exists
if (is_link($destDir)) {
    echo "Removing existing symbolic link...\n";
    unlink($destDir);
}

// Create destination directory if it doesn't exist
if (!is_dir($destDir)) {
    echo "Creating destination directory: {$destDir}\n";
    mkdir($destDir, 0755, true);
}

/**
 * Recursively copy directory contents
 */
function copyDirectory($source, $destination) {
    if (!is_dir($source)) {
        echo "Source directory does not exist: {$source}\n";
        return false;
    }

    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $destPath = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        
        if ($item->isDir()) {
            if (!is_dir($destPath)) {
                mkdir($destPath, 0755, true);
                echo "Created directory: {$destPath}\n";
            }
        } else {
            if (!file_exists($destPath) || filemtime($item) > filemtime($destPath)) {
                copy($item, $destPath);
                chmod($destPath, 0644);
                echo "Copied file: {$destPath}\n";
            }
        }
    }
    
    return true;
}

// Copy all files from storage/app/public to public/storage
echo "Copying files from {$sourceDir} to {$destDir}...\n";
$result = copyDirectory($sourceDir, $destDir);

if ($result) {
    echo "Successfully copied storage files!\n";
} else {
    echo "Failed to copy storage files.\n";
    exit(1);
}

// Create .htaccess file in the destination directory to ensure proper access
$htaccessPath = $destDir . '/.htaccess';
$htaccessContent = <<<EOT
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Allow access to all files in storage
    <IfModule mod_authz_core.c>
        Require all granted
    </IfModule>
    <IfModule !mod_authz_core.c>
        Order allow,deny
        Allow from all
    </IfModule>

    # Set proper MIME types for images
    <IfModule mod_mime.c>
        AddType image/jpeg .jpg .jpeg
        AddType image/png .png
        AddType image/gif .gif
        AddType image/webp .webp
    </IfModule>

    # Add CORS headers for images
    <IfModule mod_headers.c>
        <FilesMatch "\.(jpg|jpeg|png|gif|webp)$">
            Header set Access-Control-Allow-Origin "*"
            Header set Access-Control-Allow-Methods "GET, OPTIONS"
            Header set Access-Control-Allow-Headers "Origin, X-Requested-With, Content-Type, Accept"
            Header set Cache-Control "max-age=86400, public"
        </FilesMatch>
    </IfModule>
</IfModule>
EOT;

file_put_contents($htaccessPath, $htaccessContent);
chmod($htaccessPath, 0644);
echo "Created .htaccess file in storage directory.\n";

// Test a few image files
echo "\nTesting image access...\n";
$testDirs = ['services', 'products', 'product-colors'];

foreach ($testDirs as $dir) {
    $dirPath = $destDir . '/' . $dir;
    if (is_dir($dirPath)) {
        $files = array_diff(scandir($dirPath), array('.', '..'));
        if (!empty($files)) {
            $testFile = reset($files);
            $testPath = $dirPath . '/' . $testFile;
            if (file_exists($testPath)) {
                echo "✓ Test file exists: /storage/{$dir}/{$testFile}\n";
            }
        }
    }
}

echo "\nStorage access fix completed!\n";
echo "You can now test image access at: https://dala3chic.com/storage/services/[filename]\n";

?>
