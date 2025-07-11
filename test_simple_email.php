<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\EmailVerification;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$kernel->bootstrap();

echo "🧪 Testing Simple Email Sending\n";
echo "=" . str_repeat("=", 40) . "\n\n";

try {
    // Test 1: Create a simple test user
    echo "📧 Test 1: Creating test user...\n";
    
    $testUser = new User();
    $testUser->id = 0; // Temporary ID
    $testUser->name = 'Test User Simple';
    $testUser->email = 'test.simple@example.com';
    $testUser->role = 'merchant';
    
    echo "  - Test user created: {$testUser->name} ({$testUser->email})\n";
    echo "  - User ID: {$testUser->id}\n\n";
    
    // Test 2: Create mailable and try to send
    echo "📧 Test 2: Creating mailable...\n";
    
    $verificationCode = '123456';
    $userType = 'merchant';
    
    try {
        $mailable = new EmailVerification($testUser, $verificationCode, $userType);
        echo "✅ Mailable created successfully!\n\n";
        
        // Test 3: Try to send the email
        echo "📧 Test 3: Sending email...\n";
        
        Mail::to($testUser->email)->send($mailable);
        
        echo "✅ Email sent successfully!\n";
        echo "  - Recipient: {$testUser->email}\n";
        echo "  - Verification Code: {$verificationCode}\n";
        echo "  - User Type: {$userType}\n\n";
        
    } catch (Exception $mailableException) {
        echo "❌ Mailable creation or sending failed!\n";
        echo "  - Error: {$mailableException->getMessage()}\n";
        echo "  - File: {$mailableException->getFile()}:{$mailableException->getLine()}\n";
        echo "  - Stack trace:\n{$mailableException->getTraceAsString()}\n\n";
    }
    
    echo "🎉 Simple email test completed!\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "📋 File: {$e->getFile()}:{$e->getLine()}\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
