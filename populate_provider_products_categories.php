<?php
// Script to populate provider_products table with category_id values from related products

// Load the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "Starting provider_products category population script...\n";

try {
    // Update provider_products with category_id from related products where category_id is null
    $updatedCount = DB::update("
        UPDATE provider_products pp
        INNER JOIN products p ON pp.product_id = p.id
        SET pp.category_id = p.category_id
        WHERE pp.category_id IS NULL
        AND p.category_id IS NOT NULL
    ");

    echo "Updated {$updatedCount} provider_products records with category_id from related products.\n";

    // Check how many provider_products now have category_id
    $totalWithCategory = DB::table('provider_products')
        ->whereNotNull('category_id')
        ->count();

    $totalProviderProducts = DB::table('provider_products')->count();

    echo "Total provider_products: {$totalProviderProducts}\n";
    echo "Provider_products with category_id: {$totalWithCategory}\n";

    // Show some sample data
    echo "\nSample provider_products with categories:\n";
    $samples = DB::select("
        SELECT pp.id, pp.product_name, pp.category_id, c.name as category_name, c.parent_id
        FROM provider_products pp
        LEFT JOIN categories c ON pp.category_id = c.id
        LIMIT 10
    ");

    foreach ($samples as $sample) {
        echo "ID: {$sample->id}, Product: {$sample->product_name}, Category: {$sample->category_name} (ID: {$sample->category_id}), Parent: {$sample->parent_id}\n";
    }

    // Check subcategories with provider products
    echo "\nSubcategories with provider products:\n";
    $subcategories = DB::select("
        SELECT DISTINCT
            c.id,
            c.name,
            c.parent_id,
            parent_cats.name as parent_name,
            COUNT(pp.id) as product_count
        FROM categories c
        INNER JOIN categories parent_cats ON c.parent_id = parent_cats.id
        LEFT JOIN provider_products pp ON pp.category_id = c.id
        WHERE c.is_active = 1
        AND c.parent_id IS NOT NULL
        GROUP BY c.id, c.name, c.parent_id, parent_cats.name
        HAVING product_count > 0
        ORDER BY parent_cats.name, c.name
        LIMIT 20
    ");

    foreach ($subcategories as $subcategory) {
        echo "Subcategory: {$subcategory->name} (ID: {$subcategory->id}), Parent: {$subcategory->parent_name}, Products: {$subcategory->product_count}\n";
    }

    echo "\n✅ Provider_products category population completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    Log::error('Provider products category population error: ' . $e->getMessage());
}
