<?php

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

echo "=== CATEGORY IMAGE PATH ANALYSIS ===\n\n";

// Get all categories with images
$categories = Category::whereNotNull('image')->get();

echo "Found " . $categories->count() . " categories with images:\n\n";

$descriptiveNamePatterns = [];
$hashBasedPatterns = [];
$otherPatterns = [];

foreach ($categories as $category) {
    $imagePath = $category->image;
    echo "ID: {$category->id} | Name: {$category->name} | Image: {$imagePath}\n";
    
    // Categorize the image path patterns
    if (preg_match('/\/images\/categories\/[a-zA-Z0-9\-&\(\),\s]+\.(jpg|jpeg|png|gif)$/i', $imagePath)) {
        $descriptiveNamePatterns[] = [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $imagePath
        ];
    } elseif (preg_match('/\/images\/categories\/[a-zA-Z0-9]{40,}\.(jpg|jpeg|png|gif)$/i', $imagePath) || 
              preg_match('/\/storage\/categories\/[a-zA-Z0-9]{40,}\.(jpg|jpeg|png|gif)$/i', $imagePath)) {
        $hashBasedPatterns[] = [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $imagePath
        ];
    } else {
        $otherPatterns[] = [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $imagePath
        ];
    }
}

echo "\n=== PATTERN ANALYSIS ===\n";
echo "Descriptive name patterns: " . count($descriptiveNamePatterns) . "\n";
echo "Hash-based patterns: " . count($hashBasedPatterns) . "\n";
echo "Other patterns: " . count($otherPatterns) . "\n\n";

if (!empty($descriptiveNamePatterns)) {
    echo "=== DESCRIPTIVE NAME PATTERNS (NEED UPDATING) ===\n";
    foreach ($descriptiveNamePatterns as $item) {
        echo "ID: {$item['id']} | {$item['name']} | {$item['image']}\n";
    }
    echo "\n";
}

if (!empty($otherPatterns)) {
    echo "=== OTHER PATTERNS ===\n";
    foreach ($otherPatterns as $item) {
        echo "ID: {$item['id']} | {$item['name']} | {$item['image']}\n";
    }
    echo "\n";
}

// Check what files exist in storage
echo "=== STORAGE DIRECTORY ANALYSIS ===\n";
$storageDir = storage_path('app/public/categories');
if (File::exists($storageDir)) {
    $files = File::files($storageDir);
    echo "Found " . count($files) . " files in storage/app/public/categories/\n";
    
    // Show first 10 files as examples
    echo "First 10 files:\n";
    for ($i = 0; $i < min(10, count($files)); $i++) {
        echo "- " . $files[$i]->getFilename() . "\n";
    }
    
    if (count($files) > 10) {
        echo "... and " . (count($files) - 10) . " more files\n";
    }
} else {
    echo "Storage directory does not exist: $storageDir\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total categories with images: " . $categories->count() . "\n";
echo "Categories needing update (descriptive names): " . count($descriptiveNamePatterns) . "\n";
echo "Categories already using hash-based names: " . count($hashBasedPatterns) . "\n";
echo "Categories with other patterns: " . count($otherPatterns) . "\n";
