<?php

require_once 'vendor/autoload.php';

use App\Models\Category;
use Illuminate\Support\Facades\File;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 Starting category recovery test...\n";

// Test creating one category
try {
    $existingCategory = Category::where('name', 'Clothes')
        ->where('type', 'product')
        ->whereNull('parent_id')
        ->first();

    if (!$existingCategory) {
        $category = Category::create([
            'name' => 'Clothes',
            'description' => 'Women\'s clothing and apparel',
            'image' => '/images/categories/clothes.jpg',
            'is_active' => true,
            'type' => 'product',
            'icon' => 'fas fa-tshirt',
        ]);
        
        echo "✅ Created parent category: Clothes (ID: {$category->id})\n";
        
        // Create a subcategory
        $subcategory = Category::create([
            'name' => 'Activewear',
            'description' => 'Sports and fitness clothing',
            'image' => '/images/categories/activewear.jpg',
            'parent_id' => $category->id,
            'is_active' => true,
            'type' => 'product',
            'icon' => 'fas fa-tshirt',
        ]);
        
        echo "✅ Created subcategory: Activewear (ID: {$subcategory->id})\n";
    } else {
        echo "ℹ️  Category 'Clothes' already exists (ID: {$existingCategory->id})\n";
    }
    
    // Show current category count
    $totalCategories = Category::where('type', 'product')->count();
    echo "📊 Total Product Categories: {$totalCategories}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "✅ Category recovery test completed!\n";
