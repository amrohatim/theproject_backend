<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Initialize database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "Checking database tables and counts...\n\n";

try {
    $productsCount = DB::table('products')->count();
    echo "Products count: $productsCount\n";
    
    if ($productsCount > 0) {
        $sampleProducts = DB::table('products')->limit(5)->get(['id', 'name', 'branch_id']);
        echo "\nSample products:\n";
        foreach ($sampleProducts as $product) {
            echo "  ID: {$product->id}, Name: {$product->name}, Branch ID: {$product->branch_id}\n";
        }
    }
    
    $colorsCount = DB::table('product_colors')->count();
    echo "\nProduct colors count: $colorsCount\n";
    
    $branchesCount = DB::table('branches')->count();
    echo "Branches count: $branchesCount\n";
    
    $companiesCount = DB::table('companies')->count();
    echo "Companies count: $companiesCount\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
