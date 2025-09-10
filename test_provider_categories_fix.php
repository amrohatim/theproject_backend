<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\API\ProviderController;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Provider Categories API Fix...\n\n";

try {
    $controller = new ProviderController();
    
    // Test the fixed endpoint
    echo "=== Testing Fixed Provider Categories Endpoint ===\n";
    $response = $controller->getCategoriesWithProducts();
    $data = json_decode($response->getContent(), true);
    
    if ($data['success']) {
        echo "✅ Success! Found " . count($data['categories']) . " parent categories with filtered children:\n";
        
        foreach ($data['categories'] as $parentCategory) {
            echo "  📂 Parent: {$parentCategory['name']} (ID: {$parentCategory['id']})\n";
            
            if (isset($parentCategory['children']) && !empty($parentCategory['children'])) {
                foreach ($parentCategory['children'] as $child) {
                    // Check if is_active is boolean
                    $isActiveType = gettype($child['is_active']);
                    $isActiveValue = $child['is_active'] ? 'true' : 'false';
                    
                    echo "    📁 Child: {$child['name']} (ID: {$child['id']}) - Products: {$child['product_count']}\n";
                    echo "       is_active: {$isActiveValue} (type: {$isActiveType})\n";
                }
            } else {
                echo "    ⚠️ No children found for this parent\n";
            }
        }
        
        echo "\n=== Data Type Verification ===\n";
        if (!empty($data['categories'])) {
            $firstParent = $data['categories'][0];
            if (!empty($firstParent['children'])) {
                $firstChild = $firstParent['children'][0];
                
                echo "✅ Parent is_active type: " . gettype($firstParent['is_active']) . " (value: " . ($firstParent['is_active'] ? 'true' : 'false') . ")\n";
                echo "✅ Child is_active type: " . gettype($firstChild['is_active']) . " (value: " . ($firstChild['is_active'] ? 'true' : 'false') . ")\n";
                
                if (is_bool($firstChild['is_active'])) {
                    echo "✅ Fix successful: Child is_active is now boolean type\n";
                } else {
                    echo "❌ Fix failed: Child is_active is still " . gettype($firstChild['is_active']) . " type\n";
                }
            }
        }
        
    } else {
        echo "❌ Failed: " . $data['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ Provider Categories API fix testing completed!\n";
