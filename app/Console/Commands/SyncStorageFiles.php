<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SyncStorageFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:sync {--force : Force sync all files regardless of modification time}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync files from storage/app/public to public/storage for shared hosting compatibility';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting storage sync...');

        $sourceDir = storage_path('app/public');
        $destDir = public_path('storage');
        $force = $this->option('force');

        // Ensure destination directory exists
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
            $this->info("Created destination directory: {$destDir}");
        }

        $syncedCount = 0;
        $skippedCount = 0;

        if (!is_dir($sourceDir)) {
            $this->error("Source directory does not exist: {$sourceDir}");
            return 1;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = $iterator->getSubPathName();
            $destPath = $destDir . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                if (!is_dir($destPath)) {
                    mkdir($destPath, 0755, true);
                    $this->line("Created directory: {$relativePath}");
                }
            } else {
                // Only copy if file doesn't exist, source is newer, or force is enabled
                if (!file_exists($destPath) || filemtime($item) > filemtime($destPath) || $force) {
                    copy($item, $destPath);
                    chmod($destPath, 0644);
                    $this->line("Synced: {$relativePath}");
                    $syncedCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        // Ensure .htaccess exists
        $this->ensureHtaccessExists($destDir);

        $this->info("Sync completed: {$syncedCount} files synced, {$skippedCount} files skipped");
        return 0;
    }

    /**
     * Ensure .htaccess file exists in the storage directory
     */
    private function ensureHtaccessExists($destDir)
    {
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
            $this->info('Created .htaccess file in storage directory');
        }
    }
}
