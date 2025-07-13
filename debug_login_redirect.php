<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

echo "🔍 Debug Login Redirect Issue\n";
echo "=============================\n\n";

// Test user credentials
$email = 'gogofifa56@gmail.com';
$password = 'Fifa2021';

echo "📧 Testing login redirect for: {$email}\n";

// Step 1: Clear any existing authentication
Auth::logout();
echo "✅ Cleared existing authentication\n";

// Step 2: Attempt login
$credentials = [
    'email' => $email,
    'password' => $password
];

echo "\n🔐 Attempting login...\n";

if (Auth::attempt($credentials)) {
    echo "✅ Login successful!\n";
    
    $user = Auth::user();
    echo "  - User: {$user->name}\n";
    echo "  - Role: {$user->role}\n";
    echo "  - ID: {$user->id}\n";
    
    // Test the redirect logic from the route
    echo "\n🔄 Testing redirect logic...\n";
    
    if ($user->role === 'admin') {
        $redirectUrl = route('admin.dashboard');
        echo "  - Admin redirect: {$redirectUrl}\n";
    } elseif ($user->role === 'vendor') {
        $redirectUrl = route('vendor.dashboard');
        echo "  - Vendor redirect: {$redirectUrl}\n";
    } elseif ($user->role === 'provider') {
        $redirectUrl = route('provider.dashboard');
        echo "  - Provider redirect: {$redirectUrl}\n";
    } elseif ($user->role === 'merchant') {
        $redirectUrl = route('merchant.dashboard');
        echo "  - Merchant redirect: {$redirectUrl}\n";
    } else {
        $redirectUrl = '/';
        echo "  - Default redirect: {$redirectUrl}\n";
    }
    
    echo "✅ Expected redirect URL: {$redirectUrl}\n";
    
    // Test if the route exists and is accessible
    echo "\n🧪 Testing route accessibility...\n";
    
    try {
        $routeExists = \Illuminate\Support\Facades\Route::has('merchant.dashboard');
        echo "  - Route 'merchant.dashboard' exists: " . ($routeExists ? 'Yes' : 'No') . "\n";
        
        if ($routeExists) {
            $routeUrl = route('merchant.dashboard');
            echo "  - Route URL: {$routeUrl}\n";
        }
        
    } catch (Exception $e) {
        echo "  - Route test error: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "❌ Login failed!\n";
    
    // Check if user exists
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "  - User exists in database\n";
        echo "  - Password verification: " . (password_verify($password, $user->password) ? 'Success' : 'Failed') . "\n";
    } else {
        echo "  - User not found in database\n";
    }
}

echo "\n🔍 Checking current session state...\n";
echo "  - Auth check: " . (Auth::check() ? 'Authenticated' : 'Not authenticated') . "\n";

if (Auth::check()) {
    $currentUser = Auth::user();
    echo "  - Current user: {$currentUser->name} ({$currentUser->role})\n";
}

echo "\n✅ Debug completed!\n";
