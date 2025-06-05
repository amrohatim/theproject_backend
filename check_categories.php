<?php

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;

// Get all parent categories
$parentCategories = Category::whereNull('parent_id')->get();

echo "Found " . $parentCategories->count() . " parent categories:\n";
foreach ($parentCategories as $category) {
    echo "- {$category->name} ({$category->type})\n";
    
    // Get subcategories
    $subcategories = Category::where('parent_id', $category->id)->get();
    echo "  Found " . $subcategories->count() . " subcategories:\n";
    foreach ($subcategories as $subcategory) {
        echo "  - {$subcategory->name}\n";
    }
    echo "\n";
}
