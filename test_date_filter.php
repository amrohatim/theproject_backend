<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\API\SearchController;
use Illuminate\Support\Facades\DB;

echo "🧪 Testing Date Filter Functionality\n";
echo "====================================\n\n";

// First, let's analyze the current data structure
echo "📊 Current Data Analysis:\n";
echo "--------------------------\n";

// Check products creation dates
echo "🔍 Products by creation date:\n";
$products = DB::table('products')
    ->select('id', 'name', 'created_at')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($products as $product) {
    $createdDate = \Carbon\Carbon::parse($product->created_at);
    echo "   - Product ID {$product->id}: '{$product->name}' -> Created: {$createdDate->format('Y-m-d H:i:s')} ({$createdDate->diffForHumans()})\n";
}

// Check services creation dates
echo "\n🔍 Services by creation date:\n";
$services = DB::table('services')
    ->select('id', 'name', 'created_at')
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($services as $service) {
    $createdDate = \Carbon\Carbon::parse($service->created_at);
    echo "   - Service ID {$service->id}: '{$service->name}' -> Created: {$createdDate->format('Y-m-d H:i:s')} ({$createdDate->diffForHumans()})\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "🧪 Testing Date Filter Logic\n";
echo str_repeat("=", 50) . "\n\n";

// Test cases for different date ranges
$now = new DateTime();
$today = $now->format('Y-m-d');
$yesterday = $now->modify('-1 day')->format('Y-m-d');
$lastWeek = $now->modify('-6 days')->format('Y-m-d'); // 7 days ago from today
$lastMonth = $now->modify('-23 days')->format('Y-m-d'); // 30 days ago from today

$testCases = [
    [
        'name' => 'Products - Today Only',
        'type' => 'product',
        'from_date' => $today . 'T00:00:00.000Z',
        'to_date' => $today . 'T23:59:59.999Z',
        'description' => 'Should return products created today'
    ],
    [
        'name' => 'Products - Last 7 Days',
        'type' => 'product',
        'from_date' => $lastWeek . 'T00:00:00.000Z',
        'to_date' => $today . 'T23:59:59.999Z',
        'description' => 'Should return products created in the last 7 days'
    ],
    [
        'name' => 'Products - Last 30 Days',
        'type' => 'product',
        'from_date' => $lastMonth . 'T00:00:00.000Z',
        'to_date' => $today . 'T23:59:59.999Z',
        'description' => 'Should return products created in the last 30 days'
    ],
    [
        'name' => 'Services - Today Only',
        'type' => 'service',
        'from_date' => $today . 'T00:00:00.000Z',
        'to_date' => $today . 'T23:59:59.999Z',
        'description' => 'Should return services created today'
    ],
    [
        'name' => 'Services - Last 7 Days',
        'type' => 'service',
        'from_date' => $lastWeek . 'T00:00:00.000Z',
        'to_date' => $today . 'T23:59:59.999Z',
        'description' => 'Should return services created in the last 7 days'
    ],
    [
        'name' => 'Products - From Date Only',
        'type' => 'product',
        'from_date' => $lastWeek . 'T00:00:00.000Z',
        'description' => 'Should return products created from last week onwards'
    ],
    [
        'name' => 'Services - To Date Only',
        'type' => 'service',
        'to_date' => $yesterday . 'T23:59:59.999Z',
        'description' => 'Should return services created up to yesterday'
    ]
];

$searchController = new SearchController();

foreach ($testCases as $testCase) {
    echo "🔍 {$testCase['name']}\n";
    echo "   Description: {$testCase['description']}\n";
    
    // Prepare request data
    $requestData = [
        'type' => $testCase['type'],
        'per_page' => 20
    ];
    
    if (isset($testCase['from_date'])) {
        $requestData['from_date'] = $testCase['from_date'];
        echo "   From Date: {$testCase['from_date']}\n";
    }
    
    if (isset($testCase['to_date'])) {
        $requestData['to_date'] = $testCase['to_date'];
        echo "   To Date: {$testCase['to_date']}\n";
    }
    
    try {
        // Create request
        $request = Request::create('/api/search/filter', 'POST', $requestData);
        
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
                    $createdAt = $item['created_at'] ?? 'N/A';
                    
                    if ($createdAt !== 'N/A') {
                        $createdDate = \Carbon\Carbon::parse($createdAt);
                        echo "      - {$testCase['type']} ID {$itemId}: '{$itemName}' -> Created: {$createdDate->format('Y-m-d H:i:s')}\n";
                        
                        // Verify the date is within the expected range
                        if (isset($testCase['from_date'])) {
                            $fromDate = new DateTime($testCase['from_date']);
                            if ($createdDate->lt($fromDate)) {
                                echo "         ⚠️ WARNING: Item created before from_date!\n";
                            }
                        }
                        
                        if (isset($testCase['to_date'])) {
                            $toDate = new DateTime($testCase['to_date']);
                            if ($createdDate->gt($toDate)) {
                                echo "         ⚠️ WARNING: Item created after to_date!\n";
                            }
                        }
                    } else {
                        echo "      - {$testCase['type']} ID {$itemId}: '{$itemName}' -> Created: N/A\n";
                    }
                }
                
                if (count($items) > 3) {
                    echo "      ... and " . (count($items) - 3) . " more\n";
                }
            } else {
                echo "   📝 No {$testCase['type']}s found for this date range\n";
                
                // Check if there are any items in the database for comparison
                $totalCount = DB::table($testCase['type'] === 'product' ? 'products' : 'services')->count();
                echo "   📊 Total {$testCase['type']}s in database: {$totalCount}\n";
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
echo "🧪 Testing Date Filter Validation\n";
echo str_repeat("=", 50) . "\n\n";

// Test invalid date formats
$invalidTestCases = [
    [
        'name' => 'Invalid from_date format',
        'data' => ['type' => 'product', 'from_date' => 'invalid-date'],
        'expected_error' => 'Invalid from_date format'
    ],
    [
        'name' => 'Invalid to_date format',
        'data' => ['type' => 'product', 'to_date' => '2024-13-45'],
        'expected_error' => 'Invalid to_date format'
    ],
    [
        'name' => 'to_date before from_date',
        'data' => [
            'type' => 'product',
            'from_date' => '2024-12-31T00:00:00.000Z',
            'to_date' => '2024-01-01T00:00:00.000Z'
        ],
        'expected_error' => 'to_date must be greater than or equal to from_date'
    ]
];

foreach ($invalidTestCases as $testCase) {
    echo "🔍 {$testCase['name']}\n";
    
    try {
        $request = Request::create('/api/search/filter', 'POST', $testCase['data']);
        $response = $searchController->filter($request);
        $data = json_decode($response->getContent(), true);
        
        if (!$data['success']) {
            echo "   ✅ Validation correctly rejected invalid input\n";
            echo "   📝 Error message: {$data['message']}\n";
        } else {
            echo "   ❌ Validation should have failed but didn't\n";
        }
        
    } catch (Exception $e) {
        echo "   ✅ Exception caught as expected: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo str_repeat("=", 50) . "\n";
echo "✅ Date Filter Testing Complete!\n";
echo str_repeat("=", 50) . "\n";
