<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Deal;
use Illuminate\Support\Facades\Log;

class SyncDealImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deals:sync-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize deal images from storage to public directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting deal image synchronization...');
        
        // Ensure the public deals directory exists
        $publicDealsDir = public_path('storage/deals');
        if (!File::exists($publicDealsDir)) {
            File::makeDirectory($publicDealsDir, 0755, true);
            $this->info("Created directory: {$publicDealsDir}");
        }
        
        // Get all deals
        $deals = Deal::all();
        $this->info("Found {$deals->count()} deals to process");
        
        $copied = 0;
        $errors = 0;
        
        foreach ($deals as $deal) {
            if (empty($deal->getRawOriginalImage())) {
                $this->warn("Deal #{$deal->id} has no image");
                continue;
            }
            
            // Get the raw image path
            $rawImagePath = $deal->getRawOriginalImage();
            $filename = basename($rawImagePath);
            
            // Source path in storage
            $sourcePath = storage_path("app/public/{$rawImagePath}");
            
            // If the source doesn't exist, try to find it in the deals directory
            if (!File::exists($sourcePath)) {
                $sourcePath = storage_path("app/public/deals/{$filename}");
            }
            
            // Destination path in public
            $destPath = public_path("storage/deals/{$filename}");
            
            // Check if source exists
            if (File::exists($sourcePath)) {
                try {
                    // Copy the file
                    File::copy($sourcePath, $destPath, true);
                    $this->info("Copied: {$sourcePath} -> {$destPath}");
                    $copied++;
                } catch (\Exception $e) {
                    $this->error("Error copying {$sourcePath}: " . $e->getMessage());
                    Log::error("Error copying deal image: " . $e->getMessage());
                    $errors++;
                }
            } else {
                $this->warn("Source file not found: {$sourcePath}");
                $errors++;
            }
        }
        
        $this->info("Synchronization complete. Copied: {$copied}, Errors: {$errors}");
        
        return Command::SUCCESS;
    }
}
