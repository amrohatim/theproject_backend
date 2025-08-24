<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use App\Models\Product;
use App\Models\Branch;

echo "🔄 Starting simple database recovery...\n";

try {
    // Check current state
    $currentProducts = Product::count();
    $currentCategories = Category::where('type', 'product')->count();
    
    echo "📊 Current state:\n";
    echo "   - Products: {$currentProducts}\n";
    echo "   - Product Categories: {$currentCategories}\n\n";
    
    // Get all branches
    $branches = Branch::all();
    echo "📍 Found {$branches->count()} branches\n\n";
    
    // Create a few key categories that are missing
    $keyCategories = [
        'Clothes' => 'Women\'s clothing and apparel',
        'Footwear' => 'Shoes and footwear',
        'Makeup' => 'Cosmetics and beauty products',
        'Skincare' => 'Skincare products',
        'Bags' => 'Handbags and purses',
        'Jewelry' => 'Fashion jewelry',
        'Accessories' => 'Fashion accessories',
    ];
    
    $createdCategories = [];
    
    foreach ($keyCategories as $name => $description) {
        $existing = Category::where('name', $name)
            ->where('type', 'product')
            ->whereNull('parent_id')
            ->first();
            
        if (!$existing) {
            $category = Category::create([
                'name' => $name,
                'description' => $description,
                'image' => '/images/categories/' . strtolower($name) . '.jpg',
                'is_active' => true,
                'type' => 'product',
                'icon' => 'fas fa-tag',
            ]);
            
            $createdCategories[] = $category;
            echo "✅ Created category: {$name} (ID: {$category->id})\n";
        } else {
            $createdCategories[] = $existing;
            echo "ℹ️  Category already exists: {$name} (ID: {$existing->id})\n";
        }
    }
    
    // Create some subcategories
    echo "\n📁 Creating subcategories...\n";
    
    $clothesCategory = collect($createdCategories)->firstWhere('name', 'Clothes');
    if ($clothesCategory) {
        $clothesSubcategories = [
            'Dresses' => 'Casual and formal dresses',
            'Activewear' => 'Sports and fitness clothing',
            'Tops (blouses, tunics)' => 'Shirts and blouses',
        ];
        
        foreach ($clothesSubcategories as $subName => $subDesc) {
            $existing = Category::where('name', $subName)
                ->where('parent_id', $clothesCategory->id)
                ->first();
                
            if (!$existing) {
                Category::create([
                    'name' => $subName,
                    'description' => $subDesc,
                    'image' => '/images/categories/' . strtolower(str_replace(' ', '-', $subName)) . '.jpg',
                    'parent_id' => $clothesCategory->id,
                    'is_active' => true,
                    'type' => 'product',
                    'icon' => 'fas fa-tshirt',
                ]);
                echo "  ✅ Created subcategory: {$subName}\n";
            } else {
                echo "  ℹ️  Subcategory already exists: {$subName}\n";
            }
        }
    }
    
    // Create some sample products to test the recovery
    echo "\n📦 Creating sample products...\n";
    
    $productImages = [
        'Elegant Maxi Dress.jpg',
        'Premium Activewear blue.jpg',
        'Classic Analog.jpg',
        'Oriental Perfume.jpg',
        'Full Coverage Foundation.jpg',
    ];
    
    $createdProducts = 0;
    
    foreach ($productImages as $imageName) {
        $imagePath = "Products images/{$imageName}";
        
        if (file_exists($imagePath)) {
            // Parse product name from filename
            $productName = pathinfo($imageName, PATHINFO_FILENAME);
            
            // Find appropriate category
            $category = null;
            if (strpos($productName, 'Dress') !== false) {
                $category = Category::where('name', 'Dresses')->first();
            } elseif (strpos($productName, 'Activewear') !== false) {
                $category = Category::where('name', 'Activewear')->first();
            } elseif (strpos($productName, 'Foundation') !== false) {
                $category = Category::where('name', 'Makeup')->first();
            } elseif (strpos($productName, 'Perfume') !== false) {
                $category = Category::where('name', 'Skincare')->first();
            } else {
                $category = $createdCategories[0]; // Default to first category
            }
            
            if ($category && $branches->isNotEmpty()) {
                // Check if product already exists
                $existing = Product::where('name', $productName)->first();
                
                if (!$existing) {
                    $product = Product::create([
                        'name' => $productName,
                        'description' => "High-quality {$productName} with excellent craftsmanship.",
                        'price' => rand(2000, 8000) / 100, // Random price between 20-80
                        'stock' => rand(20, 80),
                        'image' => $imagePath,
                        'branch_id' => $branches->random()->id,
                        'category_id' => $category->id,
                        'sku' => 'SKU' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'rating' => rand(35, 50) / 10,
                        'is_available' => true,
                        'featured' => rand(1, 10) <= 2, // 20% chance
                    ]);
                    
                    $createdProducts++;
                    echo "✅ Created product: {$productName} (ID: {$product->id})\n";
                } else {
                    echo "ℹ️  Product already exists: {$productName}\n";
                }
            }
        }
    }
    
    // Final statistics
    $finalProducts = Product::count();
    $finalCategories = Category::where('type', 'product')->count();
    
    echo "\n📊 Final Statistics:\n";
    echo "   - Products: {$finalProducts} (was {$currentProducts})\n";
    echo "   - Product Categories: {$finalCategories} (was {$currentCategories})\n";
    echo "   - New Products Created: {$createdProducts}\n";
    echo "   - New Categories Created: " . ($finalCategories - $currentCategories) . "\n";
    
    if ($finalProducts >= 102) {
        echo "\n🎉 SUCCESS! Database has been restored to 102+ products!\n";
    } else {
        echo "\n⚠️  Database recovery in progress. Current products: {$finalProducts}\n";
        echo "   Need to create " . (102 - $finalProducts) . " more products to reach target.\n";
    }
    
    echo "\n✅ Simple database recovery completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error during recovery: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
