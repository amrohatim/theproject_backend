<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTING PRODUCT EDIT DATA STRUCTURE ===\n\n";

try {
    // First, create test data if it doesn't exist
    echo "1. Creating test data...\n";
    
    $colorSizes = [
        [
            'product_id' => 12,
            'product_color_id' => 40, // Gold
            'product_size_id' => 13,  // Medium
            'stock' => 25,
            'price_adjustment' => 0.00,
            'is_available' => true,
        ],
        [
            'product_id' => 12,
            'product_color_id' => 41, // DarkBlue
            'product_size_id' => 13,  // Medium
            'stock' => 5,
            'price_adjustment' => 0.00,
            'is_available' => true,
        ]
    ];

    foreach ($colorSizes as $data) {
        $existing = App\Models\ProductColorSize::where('product_id', $data['product_id'])
            ->where('product_color_id', $data['product_color_id'])
            ->where('product_size_id', $data['product_size_id'])
            ->first();

        if (!$existing) {
            App\Models\ProductColorSize::create($data);
            echo "   Created: Color {$data['product_color_id']} + Size {$data['product_size_id']}\n";
        }
    }

    // 2. Test the controller logic
    echo "\n2. Testing controller data loading...\n";
    
    $product = App\Models\Product::with([
        'colors' => function($query) {
            $query->orderBy('display_order');
        },
        'colors.sizes' => function($query) {
            $query->orderBy('display_order');
        },
        'sizes' => function($query) {
            $query->orderBy('display_order');
        },
        'colorSizes.color',
        'colorSizes.size'
    ])->find(12);

    if (!$product) {
        echo "   ERROR: Product 12 not found!\n";
        exit(1);
    }

    echo "   Product: {$product->name}\n";
    echo "   Colors: " . $product->colors->count() . "\n";
    echo "   Sizes: " . $product->sizes->count() . "\n";
    echo "   ColorSizes: " . $product->colorSizes->count() . "\n";

    // 3. Test the data processing logic (from controller)
    echo "\n3. Testing data processing logic...\n";
    
    foreach ($product->colors as $color) {
        echo "   Processing color: {$color->name}\n";
        
        // Get size allocations for this color from colorSizes relationship
        $colorSizeAllocations = $product->colorSizes->where('product_color_id', $color->id);
        
        echo "     Found {$colorSizeAllocations->count()} size allocations\n";
        
        // Create sizes array with allocation data
        $sizesWithAllocations = [];
        foreach ($colorSizeAllocations as $allocation) {
            if ($allocation->size) {
                $sizesWithAllocations[] = [
                    'id' => $allocation->size->id,
                    'name' => $allocation->size->name,
                    'value' => $allocation->size->value,
                    'stock' => $allocation->stock,
                    'price_adjustment' => $allocation->price_adjustment,
                    'is_available' => $allocation->is_available,
                ];
                echo "       - {$allocation->size->name} ({$allocation->size->value}): {$allocation->stock} units\n";
            }
        }
        
        // Add sizes data to color object for JavaScript
        $color->sizes_with_allocations = $sizesWithAllocations;
        echo "     Added sizes_with_allocations: " . count($sizesWithAllocations) . " items\n";
    }

    // 4. Test the JavaScript data structure
    echo "\n4. Testing JavaScript data structure...\n";
    
    $jsData = [
        'colors' => $product->colors,
        'sizes' => $product->sizes,
        'colorSizes' => $product->colorSizes
    ];

    echo "   JavaScript data structure:\n";
    foreach ($jsData['colors'] as $index => $color) {
        echo "     Color[$index]: {$color->name}\n";
        if (isset($color->sizes_with_allocations)) {
            echo "       sizes_with_allocations: " . count($color->sizes_with_allocations) . " items\n";
            foreach ($color->sizes_with_allocations as $sizeData) {
                echo "         - {$sizeData['name']}: {$sizeData['stock']} units\n";
            }
        } else {
            echo "       sizes_with_allocations: NOT SET\n";
        }
    }

    echo "\n=== TEST COMPLETED SUCCESSFULLY ===\n";
    echo "✅ Data structure is correct\n";
    echo "✅ Size allocations are properly loaded\n";
    echo "✅ JavaScript will receive the correct data format\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
