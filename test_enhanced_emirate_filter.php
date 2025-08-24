<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\API\SearchController;
use Illuminate\Support\Facades\DB;

echo "🧪 Testing Enhanced Emirate Filter\n";
echo "==================================\n\n";

// First, let's analyze the current data structure
echo "📊 Current Data Analysis:\n";
echo "--------------------------\n";

// Check products with branch_id = null
$productsWithNullBranch = DB::table('products')
    ->whereNull('branch_id')
    ->whereNotNull('merchant_id')
    ->get(['id', 'name', 'merchant_id']);

echo "🔍 Products with branch_id = NULL:\n";
foreach ($productsWithNullBranch as $product) {
    // Get merchant info
    $merchant = DB::table('merchants')->where('id', $product->merchant_id)->first();
    if ($merchant) {
        echo "   - Product ID {$product->id}: '{$product->name}' -> Merchant: {$merchant->business_name} (Emirate: {$merchant->emirate})\n";
    } else {
        echo "   - Product ID {$product->id}: '{$product->name}' -> No merchant found for ID {$product->merchant_id}\n";
    }
}

// Check services with branch_id = null
$servicesWithNullBranch = DB::table('services')
    ->whereNull('branch_id')
    ->whereNotNull('merchant_id')
    ->get(['id', 'name', 'merchant_id']);

echo "\n🔍 Services with branch_id = NULL:\n";
foreach ($servicesWithNullBranch as $service) {
    // Get user info (since services.merchant_id points to users.id)
    $user = DB::table('users')->where('id', $service->merchant_id)->first();
    if ($user) {
        // Get merchant record for this user
        $merchant = DB::table('merchants')->where('user_id', $user->id)->first();
        if ($merchant) {
            echo "   - Service ID {$service->id}: '{$service->name}' -> User: {$user->name} -> Merchant: {$merchant->business_name} (Emirate: {$merchant->emirate})\n";
        } else {
            echo "   - Service ID {$service->id}: '{$service->name}' -> User: {$user->name} -> No merchant record found\n";
        }
    } else {
        echo "   - Service ID {$service->id}: '{$service->name}' -> No user found for ID {$service->merchant_id}\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🧪 Testing Enhanced Emirate Filter Logic\n";
echo str_repeat("=", 50) . "\n\n";

// Test cases for different emirates
$testCases = [
    [
        'name' => 'Dubai Products Filter',
        'type' => 'product',
        'emirate' => 'Dubai',
        'description' => 'Should include products from Dubai branches AND products with null branch_id where merchant.emirate = Dubai'
    ],
    [
        'name' => 'Sharjah Products Filter', 
        'type' => 'product',
        'emirate' => 'Sharjah',
        'description' => 'Should include products from Sharjah branches AND products with null branch_id where merchant.emirate = Sharjah'
    ],
    [
        'name' => 'Dubai Services Filter',
        'type' => 'service', 
        'emirate' => 'Dubai',
        'description' => 'Should include services from Dubai branches AND services with null branch_id where merchant.emirate = Dubai'
    ],
    [
        'name' => 'Sharjah Services Filter',
        'type' => 'service',
        'emirate' => 'Sharjah', 
        'description' => 'Should include services from Sharjah branches AND services with null branch_id where merchant.emirate = Sharjah'
    ]
];

$searchController = new SearchController();

foreach ($testCases as $testCase) {
    echo "🔍 {$testCase['name']}\n";
    echo "   Description: {$testCase['description']}\n";
    
    try {
        // Create request
        $request = Request::create('/api/search/filter', 'POST', [
            'type' => $testCase['type'],
            'emirate' => $testCase['emirate'],
            'per_page' => 20
        ]);
        
        // Execute filter
        $response = $searchController->filter($request);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            $items = $data['data'] ?? [];
            echo "   ✅ Filter executed successfully\n";
            echo "   📊 Results: " . count($items) . " {$testCase['type']}s found\n";
            
            if (!empty($items)) {
                echo "   📋 Sample results:\n";
                
                foreach (array_slice($items, 0, 3) as $item) {
                    $itemId = $item['id'];
                    $itemName = $item['name'];
                    $branchId = $item['branch_id'] ?? 'NULL';
                    
                    if ($testCase['type'] === 'product') {
                        $merchantId = $item['merchant_id'] ?? 'NULL';
                        echo "      - Product ID {$itemId}: '{$itemName}' (branch_id: {$branchId}, merchant_id: {$merchantId})\n";
                        
                        // Verify the logic
                        if ($branchId === 'NULL' || $branchId === null) {
                            // This should be included because merchant has matching emirate
                            if ($merchantId !== 'NULL' && $merchantId !== null) {
                                $merchant = DB::table('merchants')->where('id', $merchantId)->first();
                                if ($merchant) {
                                    echo "         → Included via merchant emirate: {$merchant->emirate}\n";
                                }
                            }
                        } else {
                            // This should be included because branch has matching emirate
                            $branch = DB::table('branches')->where('id', $branchId)->first();
                            if ($branch) {
                                echo "         → Included via branch emirate: {$branch->emirate}\n";
                            }
                        }
                    } else {
                        $merchantId = $item['merchant_id'] ?? 'NULL';
                        echo "      - Service ID {$itemId}: '{$itemName}' (branch_id: {$branchId}, merchant_id: {$merchantId})\n";
                        
                        // Verify the logic for services
                        if ($branchId === 'NULL' || $branchId === null) {
                            // This should be included because merchant has matching emirate
                            if ($merchantId !== 'NULL' && $merchantId !== null) {
                                $user = DB::table('users')->where('id', $merchantId)->first();
                                if ($user) {
                                    $merchant = DB::table('merchants')->where('user_id', $user->id)->first();
                                    if ($merchant) {
                                        echo "         → Included via merchant emirate: {$merchant->emirate}\n";
                                    }
                                }
                            }
                        } else {
                            // This should be included because branch has matching emirate
                            $branch = DB::table('branches')->where('id', $branchId)->first();
                            if ($branch) {
                                echo "         → Included via branch emirate: {$branch->emirate}\n";
                            }
                        }
                    }
                }
                
                if (count($items) > 3) {
                    echo "      ... and " . (count($items) - 3) . " more\n";
                }
            }
            
        } else {
            echo "   ❌ Filter failed: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
        
    } catch (Exception $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 50) . "\n";
echo "✅ Enhanced Emirate Filter Testing Complete!\n";
echo str_repeat("=", 50) . "\n";
