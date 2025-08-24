<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Middleware\MerchantMiddleware;

echo "🧪 Testing Login Flow for Merchant\n";
echo "==================================\n\n";

// Test user credentials
$email = 'gogofifa56@gmail.com';
$password = 'Fifa2021';

echo "📧 Testing login with: {$email}\n";

// Step 1: Check if user exists and credentials are correct
$user = User::where('email', $email)->first();

if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}

echo "✅ User found: {$user->name} (Role: {$user->role})\n";

// Step 2: Test password
if (!password_verify($password, $user->password)) {
    echo "❌ Password verification failed!\n";
    exit(1);
}

echo "✅ Password verification successful\n";

// Step 3: Simulate login
Auth::login($user);
echo "✅ User logged in successfully\n";

// Step 4: Test redirect logic
echo "\n🔍 Testing redirect logic...\n";

if ($user->role === 'merchant') {
    $dashboardRoute = route('merchant.dashboard');
    echo "✅ Should redirect to: {$dashboardRoute}\n";
} else {
    echo "❌ User role '{$user->role}' would not redirect to merchant dashboard\n";
}

// Step 5: Test MerchantMiddleware conditions
echo "\n🔍 Testing MerchantMiddleware conditions...\n";

$middleware = new MerchantMiddleware();

// Create a mock request to the merchant dashboard
$request = Request::create('/merchant/dashboard', 'GET');

try {
    // Test the middleware
    $response = $middleware->handle($request, function ($req) {
        return response('Dashboard accessed successfully');
    });
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "❌ Middleware redirected to: " . $response->getTargetUrl() . "\n";
        echo "   This means the user cannot access the dashboard directly\n";
        
        // Check what condition failed
        if ($user->registration_step !== 'verified') {
            echo "   - Registration step issue: {$user->registration_step} (should be 'verified')\n";
        }
        
        if (!$user->phone_verified_at) {
            echo "   - Phone not verified\n";
        }
        
        $merchant = $user->merchantRecord;
        if (!$merchant) {
            echo "   - No merchant record found\n";
        } elseif ($merchant->status !== 'active') {
            echo "   - Merchant status issue: {$merchant->status} (should be 'active')\n";
        }
        
    } else {
        echo "✅ Middleware allows access to dashboard\n";
    }
    
} catch (Exception $e) {
    echo "❌ Middleware error: " . $e->getMessage() . "\n";
}

// Step 6: Test the actual route
echo "\n🔍 Testing actual dashboard route access...\n";

try {
    $dashboardController = new \App\Http\Controllers\Merchant\DashboardController();
    $dashboardResponse = $dashboardController->index();
    
    if ($dashboardResponse instanceof \Illuminate\Http\RedirectResponse) {
        echo "❌ Dashboard controller redirected to: " . $dashboardResponse->getTargetUrl() . "\n";
    } else {
        echo "✅ Dashboard controller allows access\n";
    }
    
} catch (Exception $e) {
    echo "❌ Dashboard controller error: " . $e->getMessage() . "\n";
}

echo "\n✅ Login flow test completed!\n";
