<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating test data for product ID 12...\n";

try {
    // Create color-size combinations for product 12
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
        // Check if combination already exists
        $existing = App\Models\ProductColorSize::where('product_id', $data['product_id'])
            ->where('product_color_id', $data['product_color_id'])
            ->where('product_size_id', $data['product_size_id'])
            ->first();

        if (!$existing) {
            App\Models\ProductColorSize::create($data);
            echo "Created color-size combination: Product {$data['product_id']}, Color {$data['product_color_id']}, Size {$data['product_size_id']}, Stock {$data['stock']}\n";
        } else {
            echo "Color-size combination already exists: Product {$data['product_id']}, Color {$data['product_color_id']}, Size {$data['product_size_id']}\n";
        }
    }

    echo "\nVerifying data...\n";
    
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

    if ($product) {
        echo "Product: {$product->name}\n";
        echo "Colors: " . $product->colors->count() . "\n";
        echo "Sizes: " . $product->sizes->count() . "\n";
        echo "Color-Size combinations: " . $product->colorSizes->count() . "\n";
        
        foreach ($product->colorSizes as $cs) {
            echo "  - {$cs->color->name} + {$cs->size->name}: {$cs->stock} units\n";
        }
    }

    echo "\nTest data creation completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
