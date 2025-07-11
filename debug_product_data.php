<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING PRODUCT ID 12 DATA ===\n\n";

// Load product with relationships
$product = App\Models\Product::with([
    'colors' => function($query) {
        $query->orderBy('display_order');
    },
    'sizes' => function($query) {
        $query->orderBy('display_order');
    },
    'colorSizes.color',
    'colorSizes.size'
])->find(12);

if (!$product) {
    echo "Product with ID 12 not found!\n";
    exit(1);
}

echo "Product: {$product->name}\n";
echo "Colors count: " . $product->colors->count() . "\n";
echo "Sizes count: " . $product->sizes->count() . "\n";
echo "ColorSizes count: " . $product->colorSizes->count() . "\n\n";

echo "=== COLORS ===\n";
foreach($product->colors as $index => $color) {
    echo "[$index] {$color->name} (ID: {$color->id}, Stock: {$color->stock})\n";
    echo "     Color Code: {$color->color_code}\n";
    echo "     Default: " . ($color->is_default ? 'Yes' : 'No') . "\n";
    echo "     Display Order: {$color->display_order}\n\n";
}

echo "=== SIZES ===\n";
foreach($product->sizes as $index => $size) {
    echo "[$index] {$size->name} ({$size->value}) (ID: {$size->id}, Stock: {$size->stock})\n";
    echo "     Default: " . ($size->is_default ? 'Yes' : 'No') . "\n";
    echo "     Display Order: {$size->display_order}\n\n";
}

echo "=== COLOR-SIZE COMBINATIONS ===\n";
if ($product->colorSizes->count() > 0) {
    foreach($product->colorSizes as $index => $cs) {
        $colorName = $cs->color ? $cs->color->name : 'NULL';
        $sizeName = $cs->size ? $cs->size->name : 'NULL';
        echo "[$index] Color: {$colorName}, Size: {$sizeName}, Stock: {$cs->stock}\n";
        echo "     Available: " . ($cs->is_available ? 'Yes' : 'No') . "\n";
        echo "     Price Adjustment: {$cs->price_adjustment}\n\n";
    }
} else {
    echo "No color-size combinations found!\n\n";
}

echo "=== JSON DATA THAT WOULD BE PASSED TO JAVASCRIPT ===\n";
$jsData = [
    'colors' => $product->colors,
    'sizes' => $product->sizes,
    'colorSizes' => $product->colorSizes
];

echo json_encode($jsData, JSON_PRETTY_PRINT) . "\n";

echo "\n=== CHECKING RELATIONSHIPS ===\n";

// Check if colors have sizes relationship
foreach($product->colors as $color) {
    echo "Color '{$color->name}' sizes via relationship:\n";
    $colorSizes = $color->sizes;
    if ($colorSizes->count() > 0) {
        foreach($colorSizes as $size) {
            echo "  - {$size->name} (Stock: {$size->pivot->stock})\n";
        }
    } else {
        echo "  No sizes found via relationship\n";
    }
    echo "\n";
}

echo "=== DONE ===\n";
