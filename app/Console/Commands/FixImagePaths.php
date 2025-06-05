<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Product;
use App\Models\Service;

class FixImagePaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix image paths and copy images to the correct locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image path fix...');

        // Create necessary directories
        $this->createDirectories();

        // Fix product images
        $this->fixProductImages();

        // Fix service images
        $this->fixServiceImages();

        $this->info('Image path fix completed successfully!');
    }

    /**
     * Create necessary directories
     */
    private function createDirectories()
    {
        $directories = [
            public_path('images'),
            public_path('images/products'),
            public_path('images/services'),
        ];

        foreach ($directories as $dir) {
            if (!File::isDirectory($dir)) {
                $this->info("Creating directory: $dir");
                File::makeDirectory($dir, 0755, true);
            } else {
                $this->info("Directory already exists: $dir");
            }
        }
    }

    /**
     * Fix product images
     */
    private function fixProductImages()
    {
        $this->info('Fixing product images...');
        
        // Get all products
        $products = Product::all();
        $this->info("Found {$products->count()} products");
        
        foreach ($products as $product) {
            if ($product->image) {
                $this->info("Processing product ID: {$product->id}");
                
                // Get the filename from the current path
                $filename = basename($product->image);
                
                // Source path in storage
                $sourcePath = storage_path('app/public/products/' . $filename);
                
                // Destination path in public
                $destinationPath = public_path('images/products/' . $filename);
                
                // Check if source file exists
                if (File::exists($sourcePath)) {
                    $this->info("  Source file exists: $sourcePath");
                    
                    // Copy the file
                    if (!File::exists($destinationPath)) {
                        File::copy($sourcePath, $destinationPath);
                        $this->info("  Copied to: $destinationPath");
                    } else {
                        $this->info("  Destination file already exists");
                    }
                    
                    // Update the database record
                    $newPath = 'images/products/' . $filename;
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['image' => $newPath]);
                    
                    $this->info("  Updated database record: $newPath");
                } else {
                    $this->warn("  Source file does not exist: $sourcePath");
                    
                    // Try to find the file in storage
                    $files = File::glob(storage_path('app/public/products/*'));
                    if (count($files) > 0) {
                        $randomFile = basename($files[0]);
                        $this->info("  Using random file instead: $randomFile");
                        
                        // Copy the random file
                        File::copy($files[0], public_path('images/products/' . $randomFile));
                        
                        // Update the database record
                        $newPath = 'images/products/' . $randomFile;
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update(['image' => $newPath]);
                        
                        $this->info("  Updated database record with random file: $newPath");
                    }
                }
            }
        }
    }

    /**
     * Fix service images
     */
    private function fixServiceImages()
    {
        $this->info('Fixing service images...');
        
        // Get all services
        $services = Service::all();
        $this->info("Found {$services->count()} services");
        
        foreach ($services as $service) {
            if ($service->image) {
                $this->info("Processing service ID: {$service->id}");
                
                // Get the filename from the current path
                $filename = basename($service->image);
                
                // Source path in storage
                $sourcePath = storage_path('app/public/services/' . $filename);
                
                // Destination path in public
                $destinationPath = public_path('images/services/' . $filename);
                
                // Check if source file exists
                if (File::exists($sourcePath)) {
                    $this->info("  Source file exists: $sourcePath");
                    
                    // Copy the file
                    if (!File::exists($destinationPath)) {
                        File::copy($sourcePath, $destinationPath);
                        $this->info("  Copied to: $destinationPath");
                    } else {
                        $this->info("  Destination file already exists");
                    }
                    
                    // Update the database record
                    $newPath = 'images/services/' . $filename;
                    DB::table('services')
                        ->where('id', $service->id)
                        ->update(['image' => $newPath]);
                    
                    $this->info("  Updated database record: $newPath");
                } else {
                    $this->warn("  Source file does not exist: $sourcePath");
                    
                    // Try to find the file in storage
                    $files = File::glob(storage_path('app/public/services/*'));
                    if (count($files) > 0) {
                        $randomFile = basename($files[0]);
                        $this->info("  Using random file instead: $randomFile");
                        
                        // Copy the random file
                        File::copy($files[0], public_path('images/services/' . $randomFile));
                        
                        // Update the database record
                        $newPath = 'images/services/' . $randomFile;
                        DB::table('services')
                            ->where('id', $service->id)
                            ->update(['image' => $newPath]);
                        
                        $this->info("  Updated database record with random file: $newPath");
                    }
                }
            }
        }
    }
}
