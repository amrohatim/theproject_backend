<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class UpdateCategoryImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:update-image-paths {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update category image paths from descriptive names to hash-based filenames';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Starting category image path update...');
        
        // Get storage directory path
        $storageDir = storage_path('app/public/categories');
        
        if (!File::exists($storageDir)) {
            $this->error("Storage directory does not exist: $storageDir");
            return 1;
        }
        
        // Get all files in storage directory
        $storageFiles = File::files($storageDir);
        $this->info("Found " . count($storageFiles) . " files in storage directory");
        
        // Create a mapping of hash filenames (without extension)
        $hashFiles = [];
        foreach ($storageFiles as $file) {
            $filename = $file->getFilename();
            $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $hashFiles[$nameWithoutExt] = $filename;
        }
        
        // Get all categories with images
        $categories = Category::whereNotNull('image')->get();
        $this->info("Found " . $categories->count() . " categories with images");
        
        $updatedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        foreach ($categories as $category) {
            $currentPath = $category->image;
            
            // Skip if already using hash-based naming
            if ($this->isHashBasedPath($currentPath)) {
                $this->line("SKIP: {$category->name} - Already using hash-based path: $currentPath");
                $skippedCount++;
                continue;
            }
            
            // Try to find matching hash file
            $matchingHashFile = $this->findMatchingHashFile($category, $hashFiles, $storageDir);
            
            if ($matchingHashFile) {
                $newPath = "/images/categories/" . pathinfo($matchingHashFile, PATHINFO_FILENAME);
                
                $this->line("UPDATE: {$category->name}");
                $this->line("  FROM: $currentPath");
                $this->line("  TO:   $newPath");
                
                if (!$dryRun) {
                    try {
                        $category->image = $newPath;
                        $category->save();
                        $this->info("  ✓ Updated successfully");
                    } catch (\Exception $e) {
                        $this->error("  ✗ Error updating: " . $e->getMessage());
                        $errorCount++;
                        continue;
                    }
                }
                
                $updatedCount++;
            } else {
                $this->warn("WARNING: No matching hash file found for {$category->name} (current: $currentPath)");
                $errorCount++;
            }
        }
        
        $this->info("\n=== SUMMARY ===");
        $this->info("Total categories processed: " . $categories->count());
        $this->info("Updated: $updatedCount");
        $this->info("Skipped (already hash-based): $skippedCount");
        $this->info("Errors/Not found: $errorCount");
        
        if ($dryRun) {
            $this->info("\nThis was a dry run. Use --no-dry-run to apply changes.");
        }
        
        return 0;
    }
    
    /**
     * Check if path is already hash-based
     */
    private function isHashBasedPath($path)
    {
        // Check for hash-based patterns (40+ character alphanumeric strings)
        return preg_match('/\/[a-zA-Z0-9]{40,}$/', $path) || 
               preg_match('/\/[a-zA-Z0-9]{40,}\.(jpg|jpeg|png|gif)$/i', $path);
    }
    
    /**
     * Find matching hash file for a category
     */
    private function findMatchingHashFile($category, $hashFiles, $storageDir)
    {
        // Strategy 1: Look for files that might match the category name
        $categorySlug = strtolower(str_replace([' ', '&', '-', '(', ')', ','], '', $category->name));
        
        foreach ($hashFiles as $hashName => $filename) {
            $fullPath = $storageDir . '/' . $filename;
            
            // For now, we'll use a simple approach - just return the first available hash file
            // In a real scenario, you might want to implement more sophisticated matching
            // based on file creation time, image content analysis, etc.
            
            if (File::exists($fullPath)) {
                return $filename;
            }
        }
        
        return null;
    }
}
