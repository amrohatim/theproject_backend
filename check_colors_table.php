<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "🔍 Checking product_colors table structure...\n\n";

try {
    // Get table structure
    $columns = Capsule::select("DESCRIBE product_colors");
    
    echo "📋 Table structure:\n";
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key} - {$column->Default}\n";
    }
    
    echo "\n📊 Sample data from product_colors table:\n";
    
    // Get sample data
    $colors = Capsule::select("SELECT * FROM product_colors LIMIT 5");
    
    foreach ($colors as $color) {
        echo "   🎨 ID: {$color->id}\n";
        
        // Check all properties of the color object
        foreach ((array)$color as $key => $value) {
            echo "      - {$key}: " . ($value ?? 'NULL') . "\n";
        }
        echo "\n";
    }
    
    echo "📈 Total colors in database: " . Capsule::table('product_colors')->count() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
