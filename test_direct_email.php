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

echo "🧪 Testing Direct Email Sending with Mailgun SMTP\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // Test 1: Check mail configuration
    echo "📧 Test 1: Checking mail configuration...\n";
    
    echo "  - Mail Driver: " . config('mail.default') . "\n";
    echo "  - SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "  - SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "  - SMTP Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "  - From Address: " . config('mail.from.address') . "\n";
    echo "  - From Name: " . config('mail.from.name') . "\n";
    echo "✅ Mail configuration loaded successfully!\n\n";
    
    // Test 2: Create a test user
    echo "📧 Test 2: Creating test user for direct email...\n";
    
    $testUser = new User();
    $testUser->id = 999;
    $testUser->name = 'Test Merchant Direct';
    $testUser->email = 'test.direct@example.com';
    $testUser->role = 'merchant';
    
    echo "  - Test user created: {$testUser->name} ({$testUser->email})\n\n";
    
    // Test 3: Send email directly (not queued)
    echo "📧 Test 3: Sending email directly (synchronously)...\n";
    
    $verificationCode = '123456';
    $userType = 'merchant';
    
    // Create the mailable
    $mailable = new EmailVerification($testUser, $verificationCode, $userType);
    
    // Send directly without queue
    Mail::to($testUser->email)->send($mailable);
    
    echo "✅ Email sent directly without queue!\n";
    echo "  - Recipient: {$testUser->email}\n";
    echo "  - Verification Code: {$verificationCode}\n";
    echo "  - User Type: {$userType}\n\n";
    
    // Test 4: Test with a real email address (if you want to receive it)
    echo "📧 Test 4: Sending to a real email address...\n";
    
    $realEmail = 'brad@dala3chic.com'; // Using the from address as test
    
    $realTestUser = new User();
    $realTestUser->id = 998;
    $realTestUser->name = 'Brad Test';
    $realTestUser->email = $realEmail;
    $realTestUser->role = 'merchant';
    
    $realMailable = new EmailVerification($realTestUser, '654321', 'merchant');
    
    Mail::to($realEmail)->send($realMailable);
    
    echo "✅ Email sent to real address!\n";
    echo "  - Recipient: {$realEmail}\n";
    echo "  - Verification Code: 654321\n";
    echo "  - Check your email inbox!\n\n";
    
    echo "🎉 Direct email test completed successfully!\n";
    echo "📬 If you received the emails, Mailgun SMTP is working correctly!\n";
    echo "📋 Check Laravel logs for any additional information.\n\n";
    
    echo "Next Steps:\n";
    echo "1. Check your email inbox for verification emails\n";
    echo "2. If emails are received, the Mailgun configuration is working\n";
    echo "3. The queue issue might be a separate problem\n";
    echo "4. You can use sync queue driver for immediate email sending\n\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
