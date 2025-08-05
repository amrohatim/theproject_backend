<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\ProductColor;

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

echo "🔍 Debugging ProductColor model...\n\n";

try {
    // Test 1: Raw SQL query
    echo "1️⃣ Raw SQL query:\n";
    $rawColors = Capsule::select("SELECT id, name, color_code FROM product_colors LIMIT 3");
    foreach ($rawColors as $color) {
        echo "   🎨 ID: {$color->id}, Name: '{$color->name}', Code: {$color->color_code}\n";
    }
    
    echo "\n2️⃣ Using ProductColor model:\n";
    
    // Test 2: Using the model
    $modelColors = ProductColor::select('id', 'name', 'color_code')->limit(3)->get();
    foreach ($modelColors as $color) {
        echo "   🎨 ID: {$color->id}, Name: '{$color->name}', Code: {$color->color_code}\n";
        echo "      Raw attributes: " . json_encode($color->getAttributes()) . "\n";
    }
    
    echo "\n3️⃣ Testing the exact query from controller:\n";
    
    // Test 3: Exact query from controller
    $controllerColors = ProductColor::select('id', 'name', 'color_code', 'image')
        ->distinct()
        ->orderBy('name')
        ->limit(3)
        ->get();
        
    foreach ($controllerColors as $color) {
        echo "   🎨 ID: {$color->id}, Name: '{$color->name}', Code: {$color->color_code}\n";
        echo "      Image: {$color->image}\n";
        echo "      Raw attributes: " . json_encode($color->getAttributes()) . "\n";
        echo "      To Array: " . json_encode($color->toArray()) . "\n";
    }
    
    echo "\n4️⃣ Testing formatted output like controller:\n";
    
    $formattedColors = $controllerColors->map(function($color) {
        return [
            'id' => $color->id,
            'name' => $color->name,
            'color_code' => $color->color_code,
            'hex_code' => $color->color_code,
            'image' => $color->image,
        ];
    });
    
    echo "   📋 Formatted colors: " . json_encode($formattedColors->toArray(), JSON_PRETTY_PRINT) . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
