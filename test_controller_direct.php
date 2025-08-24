<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\API\ProductSpecificationController;
use Illuminate\Http\Request;

echo "🔍 Testing ProductSpecificationController directly...\n\n";

try {
    // Create a mock request
    $request = new Request();
    
    // Create controller instance
    $controller = new ProductSpecificationController();
    
    // Call the method directly
    $response = $controller->getAllProductColors();
    
    echo "📡 Response status: " . $response->getStatusCode() . "\n";
    
    $data = json_decode($response->getContent(), true);
    
    echo "✅ Response data:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    
    if (isset($data['colors']) && is_array($data['colors'])) {
        echo "\n📊 First few colors:\n";
        foreach (array_slice($data['colors'], 0, 3) as $color) {
            echo "   🎨 ID: {$color['id']}, Name: '{$color['name']}', Code: {$color['color_code']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
