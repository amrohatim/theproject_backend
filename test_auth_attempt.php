<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "🔐 Testing Auth::attempt for gogofifa56@gmail.com\n";
echo "=================================================\n\n";

$email = 'gogofifa56@gmail.com';
$password = 'Fifa2021';

$credentials = [
    'email' => $email,
    'password' => $password
];

echo "📧 Testing credentials:\n";
echo "  - Email: {$email}\n";
echo "  - Password: {$password}\n\n";

// Clear any existing authentication
Auth::logout();
echo "✅ Cleared existing authentication\n";

// Test Auth::attempt
echo "\n🔐 Testing Auth::attempt...\n";
$attemptResult = Auth::attempt($credentials);

echo "  - Auth::attempt result: " . ($attemptResult ? 'SUCCESS ✅' : 'FAILED ❌') . "\n";

if ($attemptResult) {
    $user = Auth::user();
    echo "  - Authenticated user: {$user->name} ({$user->email})\n";
    echo "  - User role: {$user->role}\n";
    echo "  - User status: {$user->status}\n";
    echo "  - Registration step: {$user->registration_step}\n";
    echo "  - Email verified: " . ($user->email_verified_at ? 'Yes' : 'No') . "\n";
    echo "  - Phone verified: " . ($user->phone_verified_at ? 'Yes' : 'No') . "\n";
} else {
    echo "  - Authentication failed!\n";
    
    // Check user details
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "\n🔍 User details:\n";
        echo "  - Name: {$user->name}\n";
        echo "  - Email: {$user->email}\n";
        echo "  - Role: {$user->role}\n";
        echo "  - Status: {$user->status}\n";
        echo "  - Registration step: {$user->registration_step}\n";
        echo "  - Email verified: " . ($user->email_verified_at ? 'Yes (' . $user->email_verified_at . ')' : 'No') . "\n";
        echo "  - Phone verified: " . ($user->phone_verified_at ? 'Yes (' . $user->phone_verified_at . ')' : 'No') . "\n";
        echo "  - Created at: {$user->created_at}\n";
        echo "  - Updated at: {$user->updated_at}\n";
        
        // Check if password verification works manually
        $passwordCheck = password_verify($password, $user->password);
        echo "  - Manual password check: " . ($passwordCheck ? 'SUCCESS' : 'FAILED') . "\n";
    }
}

echo "\n✅ Auth attempt test completed!\n";
