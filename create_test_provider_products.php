<?php
// Script to create test provider products for different subcategories

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\ProviderProduct;
use App\Models\Category;

echo "Creating test provider products...\n";

try {
    // Get some subcategories (child categories)
    $subcategories = Category::where('parent_id', '!=', null)
        ->where('is_active', 1)
        ->where('type', 'product')
        ->take(5)
        ->get();

    echo "Found " . $subcategories->count() . " subcategories:\n";
    foreach ($subcategories as $subcategory) {
        echo "  - {$subcategory->name} (ID: {$subcategory->id})\n";
    }

    // Create test provider products for each subcategory
    $createdCount = 0;
    foreach ($subcategories as $subcategory) {
        // Check if this subcategory already has provider products
        $existingCount = ProviderProduct::where('category_id', $subcategory->id)->count();
        
        if ($existingCount == 0) {
            // Create 2-3 test products for this subcategory
            $productNames = [
                "Premium {$subcategory->name} Item 1",
                "Quality {$subcategory->name} Product",
                "Best {$subcategory->name} Deal"
            ];

            foreach (array_slice($productNames, 0, 2) as $index => $productName) {
                $providerProduct = ProviderProduct::create([
                    'provider_id' => 6, // Using existing provider ID
                    'product_id' => null, // Standalone provider product
                    'product_name' => $productName,
                    'description' => "High-quality {$subcategory->name} product for your needs.",
                    'price' => rand(10, 100) + (rand(0, 99) / 100), // Random price between $10-$100
                    'original_price' => null,
                    'stock' => rand(5, 50),
                    'sku' => strtoupper(substr($subcategory->name, 0, 3)) . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'category_id' => $subcategory->id,
                    'is_active' => true,
                    'image' => null,
                    'branch_id' => null,
                ]);

                echo "Created: {$productName} in {$subcategory->name} (ID: {$providerProduct->id})\n";
                $createdCount++;
            }
        } else {
            echo "Skipped {$subcategory->name} - already has {$existingCount} products\n";
        }
    }

    echo "\n✅ Created {$createdCount} test provider products!\n";

    // Show summary of subcategories with products
    echo "\nSubcategories with provider products:\n";
    $subcategoriesWithProducts = DB::select("
        SELECT DISTINCT
            c.id,
            c.name,
            parent_cats.name as parent_name,
            COUNT(pp.id) as product_count
        FROM categories c
        INNER JOIN categories parent_cats ON c.parent_id = parent_cats.id
        LEFT JOIN provider_products pp ON pp.category_id = c.id
        WHERE c.is_active = 1
        AND c.parent_id IS NOT NULL
        GROUP BY c.id, c.name, parent_cats.name
        HAVING product_count > 0
        ORDER BY parent_cats.name, c.name
    ");

    foreach ($subcategoriesWithProducts as $subcategory) {
        echo "  - {$subcategory->name} (Parent: {$subcategory->parent_name}) - {$subcategory->product_count} products\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
