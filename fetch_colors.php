<?php

// Simple script to fetch and display product colors from the database

require __DIR__ . '/vendor/autoload.php';

// Initialize Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ProductColor;
use Illuminate\Support\Facades\DB;

echo "=== Product Colors Database Query ===\n\n";

try {
    // 1. Check if product_colors table exists
    echo "1. Checking if product_colors table exists...\n";
    $tableExists = DB::getSchemaBuilder()->hasTable('product_colors');
    if (!$tableExists) {
        echo "❌ product_colors table does not exist\n";
        exit(1);
    }
    echo "✅ product_colors table exists\n\n";

    // 2. Get total count
    echo "2. Total colors in database:\n";
    $totalCount = ProductColor::count();
    echo "Total colors: {$totalCount}\n\n";

    // 3. Get unique color names and their counts
    echo "3. Color distribution by name:\n";
    $colorCounts = DB::table('product_colors')
        ->select('name', DB::raw('count(*) as count'))
        ->groupBy('name')
        ->orderBy('count', 'desc')
        ->get();
    
    foreach ($colorCounts as $colorCount) {
        echo "  - {$colorCount->name}: {$colorCount->count} entries\n";
    }
    echo "\n";

    // 4. Get unique color codes and their counts
    echo "4. Color distribution by hex code:\n";
    $hexCounts = DB::table('product_colors')
        ->select('color_code', DB::raw('count(*) as count'))
        ->groupBy('color_code')
        ->orderBy('count', 'desc')
        ->limit(20)
        ->get();
    
    foreach ($hexCounts as $hexCount) {
        echo "  - {$hexCount->color_code}: {$hexCount->count} entries\n";
    }
    echo "\n";

    // 5. Show sample of all colors (first 50)
    echo "5. Sample of colors in database (first 50):\n";
    $sampleColors = ProductColor::select('id', 'name', 'color_code', 'product_id')
        ->orderBy('id')
        ->limit(50)
        ->get();
    
    foreach ($sampleColors as $color) {
        echo "  - ID: {$color->id}, Name: '{$color->name}', Code: '{$color->color_code}', Product: {$color->product_id}\n";
    }
    echo "\n";

    // 6. Check for diverse colors (non-black)
    echo "6. Non-black colors:\n";
    $nonBlackColors = ProductColor::where('color_code', '!=', '#000000')
        ->where('color_code', '!=', '#000')
        ->select('id', 'name', 'color_code')
        ->limit(20)
        ->get();
    
    if ($nonBlackColors->count() > 0) {
        echo "Found {$nonBlackColors->count()} non-black colors:\n";
        foreach ($nonBlackColors as $color) {
            echo "  - ID: {$color->id}, Name: '{$color->name}', Code: '{$color->color_code}'\n";
        }
    } else {
        echo "❌ No non-black colors found in database!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== End of Color Database Query ===\n";
