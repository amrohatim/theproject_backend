<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Provider\LocationController;
use App\Models\User;
use App\Models\Provider;
use App\Models\ProviderLocation;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Provider Location Fix - New Locations Only ===\n\n";

try {
    // Find the test user and provider
    $user = User::where('email', 'test@provider.com')->first();
    $provider = $user->provider;

    // Clear existing locations for clean test
    ProviderLocation::where('provider_id', $provider->id)->delete();
    echo "✅ Cleared existing locations\n";

    // Test the controller with only new locations (no IDs)
    $controller = new LocationController();
    
    // Simulate authentication
    \Illuminate\Support\Facades\Auth::setUser($user);
    
    // Create request data with only new locations (no IDs)
    $requestData = [
        'locations' => [
            [
                'id' => null,
                'label' => 'First New Location',
                'emirate' => 'Dubai',
                'latitude' => 25.2048,
                'longitude' => 55.2708,
            ],
            [
                'id' => null,
                'label' => 'Second New Location',
                'emirate' => 'Sharjah',
                'latitude' => 25.3463,
                'longitude' => 55.4209,
            ]
        ]
    ];

    $request = new Request($requestData);
    
    echo "\n📋 Testing the store method with only new locations...\n";
    echo "   - Two new locations: no IDs (should create both)\n";

    // Count locations before
    $countBefore = ProviderLocation::where('provider_id', $provider->id)->count();
    echo "   - Locations before: $countBefore\n";

    // Call the store method
    $response = $controller->store($request);
    $responseData = json_decode($response->getContent(), true);

    // Count locations after
    $countAfter = ProviderLocation::where('provider_id', $provider->id)->count();
    echo "   - Locations after: $countAfter\n";

    if ($responseData['success']) {
        echo "✅ Store method succeeded\n";
        
        // Check if we have exactly 2 locations
        if ($countAfter == 2) {
            echo "✅ Correct number of locations (2) - both new locations created!\n";
            
            // Check if both locations were created
            $firstLocation = ProviderLocation::where('label', 'First New Location')->first();
            $secondLocation = ProviderLocation::where('label', 'Second New Location')->first();
            
            if ($firstLocation && $firstLocation->emirate == 'Dubai') {
                echo "✅ First new location was created correctly\n";
            } else {
                echo "❌ First new location was not created correctly\n";
            }
            
            if ($secondLocation && $secondLocation->emirate == 'Sharjah') {
                echo "✅ Second new location was created correctly\n";
            } else {
                echo "❌ Second new location was not created correctly\n";
            }
            
        } else {
            echo "❌ Wrong number of locations! Expected 2, got $countAfter\n";
        }
    } else {
        echo "❌ Store method failed: " . ($responseData['message'] ?? 'Unknown error') . "\n";
    }

    // Show all locations for debugging
    echo "\n📋 All locations for provider {$provider->id}:\n";
    $allLocations = ProviderLocation::where('provider_id', $provider->id)->get();
    foreach ($allLocations as $location) {
        echo "   - ID: {$location->id}, Label: '{$location->label}', Emirate: {$location->emirate}\n";
    }

    echo "\n=== Test completed ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
