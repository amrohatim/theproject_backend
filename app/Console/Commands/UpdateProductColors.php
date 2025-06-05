<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductColor;

class UpdateProductColors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-colors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product color variants with actual images and correct color information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting product color variants update process...');
        $this->newLine();

        // Comprehensive color mapping with hex codes
        $colorMappings = [
            'black' => ['name' => 'Black', 'hex' => '#000000'],
            'bk' => ['name' => 'Black', 'hex' => '#000000'],
            'balck' => ['name' => 'Black', 'hex' => '#000000'], // Handle typo
            'blue' => ['name' => 'Blue', 'hex' => '#0000FF'],
            'bluedark' => ['name' => 'Dark Blue', 'hex' => '#000080'],
            'darkblue' => ['name' => 'Dark Blue', 'hex' => '#000080'],
            'lightblue' => ['name' => 'Light Blue', 'hex' => '#ADD8E6'],
            'light blue' => ['name' => 'Light Blue', 'hex' => '#ADD8E6'],
            'likeblue' => ['name' => 'Light Blue', 'hex' => '#ADD8E6'],
            'bluegray' => ['name' => 'Blue Gray', 'hex' => '#6699CC'],
            'red' => ['name' => 'Red', 'hex' => '#FF0000'],
            'darkred' => ['name' => 'Dark Red', 'hex' => '#8B0000'],
            'likered' => ['name' => 'Light Red', 'hex' => '#FFB6C1'],
            'white' => ['name' => 'White', 'hex' => '#FFFFFF'],
            'green' => ['name' => 'Green', 'hex' => '#008000'],
            'lightgreen' => ['name' => 'Light Green', 'hex' => '#90EE90'],
            'lightggreen' => ['name' => 'Light Green', 'hex' => '#90EE90'], // Handle typo
            'light green' => ['name' => 'Light Green', 'hex' => '#90EE90'],
            'gray' => ['name' => 'Gray', 'hex' => '#808080'],
            'grey' => ['name' => 'Gray', 'hex' => '#808080'],
            'brown' => ['name' => 'Brown', 'hex' => '#A52A2A'],
            'orange' => ['name' => 'Orange', 'hex' => '#FFA500'],
            'darkorange' => ['name' => 'Dark Orange', 'hex' => '#FF8C00'],
            'yellow' => ['name' => 'Yellow', 'hex' => '#FFFF00'],
            'lightyellow' => ['name' => 'Light Yellow', 'hex' => '#FFFFE0'],
            'yellowcoo' => ['name' => 'Cool Yellow', 'hex' => '#FFFACD'],
            'violet' => ['name' => 'Violet', 'hex' => '#8A2BE2'],
            'pink' => ['name' => 'Pink', 'hex' => '#FFC0CB'],
            'bpink' => ['name' => 'Bright Pink', 'hex' => '#FF1493'],
            'cyan' => ['name' => 'Cyan', 'hex' => '#00FFFF'],
            'purple' => ['name' => 'Purple', 'hex' => '#800080'],
            'gold' => ['name' => 'Gold', 'hex' => '#FFD700'],
            'sky' => ['name' => 'Sky Blue', 'hex' => '#87CEEB'],
        ];

        // Get images directory
        $imagesDir = base_path('Products images');
        if (!is_dir($imagesDir)) {
            $this->error("❌ Products images directory not found at: {$imagesDir}");
            return 1;
        }

        // Get all image files
        $imageFiles = glob($imagesDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        $this->info("📁 Found " . count($imageFiles) . " image files");
        $this->newLine();

        // Create storage directory if it doesn't exist
        $storageDir = storage_path('app/public/product_images');
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
            $this->info("📁 Created storage directory: {$storageDir}");
        }

        try {
            // Get all products with their existing colors
            $products = Product::with('colors')->whereHas('colors')->get();
            
            if ($products->isEmpty()) {
                $this->error("❌ No products with color variants found in database.");
                return 1;
            }
            
            $this->info("🔍 Found " . $products->count() . " products with color variants");
            $this->newLine();
            
            $updatedCount = 0;
            $matchedImages = 0;
            $errors = [];
            
            foreach ($products as $product) {
                $this->info("🏷️  Processing product: {$product->name} (ID: {$product->id})");
                
                // Find matching images for this product
                $productImages = [];
                foreach ($imageFiles as $imagePath) {
                    $filename = basename($imagePath);
                    
                    // Check if image filename contains the product name
                    if (stripos($filename, $product->name) !== false) {
                        $productImages[] = $filename;
                    }
                }
                
                if (empty($productImages)) {
                    $this->warn("   ⚠️  No matching images found for product: {$product->name}");
                    continue;
                }
                
                $this->info("   📸 Found " . count($productImages) . " matching image(s)");
                
                // Create a mapping of available colors from images
                $availableColors = [];
                foreach ($productImages as $imageFilename) {
                    $filenameLower = strtolower($imageFilename);
                    $productNameLower = strtolower($product->name);
                    
                    // Remove product name and extension to get color part
                    $colorPart = str_replace($productNameLower, '', $filenameLower);
                    $colorPart = preg_replace('/\.(jpg|jpeg|png|gif)$/', '', $colorPart);
                    $colorPart = trim($colorPart, ' .-_');
                    
                    // Check if this color part matches any known color
                    foreach ($colorMappings as $colorKey => $colorData) {
                        if ($colorPart === $colorKey) {
                            $availableColors[$colorKey] = [
                                'filename' => $imageFilename,
                                'data' => $colorData
                            ];
                            break;
                        }
                    }
                }
                
                $this->info("   🎨 Available colors from images: " . implode(', ', array_keys($availableColors)));
                
                // Process existing color variants
                foreach ($product->colors as $color) {
                    $this->info("      🔍 Processing existing color: {$color->name}");
                    
                    $updated = false;
                    
                    // Try to match with available colors
                    foreach ($availableColors as $colorKey => $colorInfo) {
                        $colorData = $colorInfo['data'];
                        
                        // Check if current color matches this available color
                        if (strtolower($color->name) === strtolower($colorData['name']) || 
                            strtolower($color->name) === $colorKey) {
                            
                            $this->info("         ✅ Found matching image: {$colorInfo['filename']}");
                            
                            // Copy image to storage
                            $sourceImagePath = $imagesDir . '/' . $colorInfo['filename'];
                            $storageImagePath = 'product_images/' . $colorInfo['filename'];
                            $fullStorageImagePath = $storageDir . '/' . $colorInfo['filename'];
                            
                            if (copy($sourceImagePath, $fullStorageImagePath)) {
                                $this->info("         📁 Copied image to storage");
                                
                                // Update color variant
                                $color->update([
                                    'name' => $colorData['name'],
                                    'color_code' => $colorData['hex'],
                                    'image' => $storageImagePath,
                                ]);
                                
                                $this->info("         🔄 Updated color: {$colorData['name']} ({$colorData['hex']})");
                                $updatedCount++;
                                $matchedImages++;
                                $updated = true;
                                
                                // Remove from available colors to avoid duplicates
                                unset($availableColors[$colorKey]);
                                break;
                            } else {
                                $error = "Failed to copy image: {$colorInfo['filename']}";
                                $this->error("         ❌ {$error}");
                                $errors[] = $error;
                            }
                        }
                    }
                    
                    if (!$updated) {
                        $this->warn("         ⚠️  No matching image found for color: {$color->name}");
                    }
                }
                
                $this->newLine();
            }
            
            $this->info("📊 Update Summary:");
            $this->info("   - Products processed: " . $products->count());
            $this->info("   - Color variants updated: {$updatedCount}");
            $this->info("   - Images matched and copied: {$matchedImages}");
            $this->info("   - Errors: " . count($errors));
            
            if (!empty($errors)) {
                $this->newLine();
                $this->error("❌ Errors encountered:");
                foreach ($errors as $error) {
                    $this->error("   - {$error}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }

        $this->newLine();
        $this->info("✅ Product color variants update completed!");
        return 0;
    }
}
