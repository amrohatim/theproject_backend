<?php

/**
 * Sync storage files from storage/app/public to public/storage
 * This script should be run periodically to ensure new uploads are accessible
 */

echo "Starting storage sync...\n";

// Define source and destination paths
$sourceDir = __DIR__ . '/storage/app/public';
$destDir = __DIR__ . '/public/storage';

// Ensure destination directory exists
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
    echo "Created destination directory: {$destDir}\n";
}

/**
 * Sync files from source to destination
 */
function syncFiles($source, $destination) {
    if (!is_dir($source)) {
        echo "Source directory does not exist: {$source}\n";
        return false;
    }

    $syncedCount = 0;
    $skippedCount = 0;

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
            // Only copy if file doesn't exist or source is newer
            if (!file_exists($destPath) || filemtime($item) > filemtime($destPath)) {
                copy($item, $destPath);
                chmod($destPath, 0644);
                echo "Synced: {$iterator->getSubPathName()}\n";
                $syncedCount++;
            } else {
                $skippedCount++;
            }
        }
    }
    
    echo "Sync completed: {$syncedCount} files synced, {$skippedCount} files skipped\n";
    return true;
}

// Sync all files
$result = syncFiles($sourceDir, $destDir);

if ($result) {
    echo "✅ Storage sync completed successfully!\n";
} else {
    echo "❌ Storage sync failed.\n";
    exit(1);
}

// Update .htaccess if needed
$htaccessPath = $destDir . '/.htaccess';
if (!file_exists($htaccessPath)) {
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
}

echo "\n📝 To automate this process, add this to your crontab:\n";
echo "*/5 * * * * cd " . __DIR__ . " && php sync_storage_files.php > /dev/null 2>&1\n";

?>
