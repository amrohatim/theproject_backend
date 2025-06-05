<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\API\SearchController;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Emirate Filter Implementation\n";
echo "========================================\n\n";

try {
    $searchController = new SearchController();
    
    // Test cases for different emirates
    $testCases = [
        [
            'name' => 'No emirate filter (baseline)',
            'filters' => ['type' => 'product', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Dubai products (DXB code)',
            'filters' => ['type' => 'product', 'emirate' => 'DXB', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Dubai products (Dubai name)',
            'filters' => ['type' => 'product', 'emirate' => 'Dubai', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Abu Dhabi products (AUH code)',
            'filters' => ['type' => 'product', 'emirate' => 'AUH', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Abu Dhabi products (Abu Dhabi name)',
            'filters' => ['type' => 'product', 'emirate' => 'Abu Dhabi', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Sharjah products (SHJ code)',
            'filters' => ['type' => 'product', 'emirate' => 'SHJ', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Sharjah products (Sharjah name)',
            'filters' => ['type' => 'product', 'emirate' => 'Sharjah', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Ajman products (AJM code)',
            'filters' => ['type' => 'product', 'emirate' => 'AJM', 'min_price' => 0, 'max_price' => 10000]
        ],
    ];

    $results = [];
    
    echo "📋 Running emirate filter tests...\n\n";
    
    foreach ($testCases as $testCase) {
        echo "Testing: {$testCase['name']}\n";
        
        $request = Request::create('/api/search/filter', 'POST', $testCase['filters']);
        $response = $searchController->filter($request);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            $totalProducts = $data['pagination']['total'];
            $returnedProducts = count($data['data']);
            $productIds = array_column($data['data'], 'id');
            
            $results[] = [
                'name' => $testCase['name'],
                'total' => $totalProducts,
                'returned' => $returnedProducts,
                'product_ids' => $productIds,
                'emirate' => $testCase['filters']['emirate'] ?? 'none'
            ];
            
            echo "✅ {$testCase['name']}: {$totalProducts} total products, {$returnedProducts} returned\n";
            
            // Show sample products with their branch info
            if (!empty($data['data'])) {
                $sampleProduct = $data['data'][0];
                echo "   📦 Sample product: {$sampleProduct['name']} (ID: {$sampleProduct['id']})\n";
                if (isset($sampleProduct['branch'])) {
                    echo "   🏢 Branch: {$sampleProduct['branch']['name']} - Emirate: {$sampleProduct['branch']['emirate']}\n";
                }
            }
        } else {
            echo "❌ {$testCase['name']}: Failed - {$data['message']}\n";
            $results[] = [
                'name' => $testCase['name'],
                'total' => 0,
                'returned' => 0,
                'product_ids' => [],
                'emirate' => $testCase['filters']['emirate'] ?? 'none',
                'error' => $data['message']
            ];
        }
        echo "\n";
    }
    
    // Test services as well
    echo "📋 Testing emirate filter for services...\n\n";
    
    $serviceTestCases = [
        [
            'name' => 'No emirate filter (services baseline)',
            'filters' => ['type' => 'service', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Dubai services (DXB code)',
            'filters' => ['type' => 'service', 'emirate' => 'DXB', 'min_price' => 0, 'max_price' => 10000]
        ],
        [
            'name' => 'Abu Dhabi services (AUH code)',
            'filters' => ['type' => 'service', 'emirate' => 'AUH', 'min_price' => 0, 'max_price' => 10000]
        ],
    ];
    
    foreach ($serviceTestCases as $testCase) {
        echo "Testing: {$testCase['name']}\n";
        
        $request = Request::create('/api/search/filter', 'POST', $testCase['filters']);
        $response = $searchController->filter($request);
        $data = json_decode($response->getContent(), true);
        
        if ($data['success']) {
            $totalServices = $data['pagination']['total'];
            $returnedServices = count($data['data']);
            
            echo "✅ {$testCase['name']}: {$totalServices} total services, {$returnedServices} returned\n";
            
            // Show sample services with their branch info
            if (!empty($data['data'])) {
                $sampleService = $data['data'][0];
                echo "   🔧 Sample service: {$sampleService['name']} (ID: {$sampleService['id']})\n";
                if (isset($sampleService['branch'])) {
                    echo "   🏢 Branch: {$sampleService['branch']['name']} - Emirate: {$sampleService['branch']['emirate']}\n";
                }
            }
        } else {
            echo "❌ {$testCase['name']}: Failed - {$data['message']}\n";
        }
        echo "\n";
    }
    
    // Analysis
    echo "📊 Analysis of Results:\n";
    echo "========================\n";
    
    $baseline = array_filter($results, function($r) { return $r['emirate'] === 'none'; });
    $baselineCount = !empty($baseline) ? reset($baseline)['total'] : 0;
    
    echo "Baseline (no filter): {$baselineCount} products\n\n";
    
    $emirateGroups = [];
    foreach ($results as $result) {
        if ($result['emirate'] !== 'none') {
            $emirate = $result['emirate'];
            if (!isset($emirateGroups[$emirate])) {
                $emirateGroups[$emirate] = [];
            }
            $emirateGroups[$emirate][] = $result;
        }
    }
    
    foreach ($emirateGroups as $emirate => $emirateResults) {
        echo "Emirate: {$emirate}\n";
        foreach ($emirateResults as $result) {
            echo "  - {$result['name']}: {$result['total']} products\n";
        }
        echo "\n";
    }
    
    // Check if filtering is working (different emirates should return different results)
    $uniqueCounts = array_unique(array_column(array_filter($results, function($r) { 
        return $r['emirate'] !== 'none' && !isset($r['error']); 
    }), 'total'));
    
    if (count($uniqueCounts) > 1) {
        echo "🎉 SUCCESS: Emirate filtering is working! Different emirates return different product counts.\n";
    } else if (count($uniqueCounts) === 1 && reset($uniqueCounts) < $baselineCount) {
        echo "✅ PARTIAL SUCCESS: Emirate filtering is working, but all tested emirates have the same number of products.\n";
    } else {
        echo "⚠️  WARNING: Emirate filtering might not be working properly. All emirates return the same results as baseline.\n";
    }
    
    echo "\n🎯 Emirate filter testing completed!\n";

} catch (Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
