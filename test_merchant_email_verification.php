<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Services\EmailVerificationService;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$kernel->bootstrap();

echo "🧪 Testing Merchant Email Verification with Mailgun SMTP\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // Test 1: Check mail configuration
    echo "📧 Test 1: Checking mail configuration...\n";
    
    $mailConfig = config('mail');
    echo "  - Mail Driver: " . config('mail.default') . "\n";
    echo "  - SMTP Host: " . config('mail.mailers.smtp.host') . "\n";
    echo "  - SMTP Port: " . config('mail.mailers.smtp.port') . "\n";
    echo "  - SMTP Username: " . config('mail.mailers.smtp.username') . "\n";
    echo "  - From Address: " . config('mail.from.address') . "\n";
    echo "  - From Name: " . config('mail.from.name') . "\n";
    echo "✅ Mail configuration loaded successfully!\n\n";
    
    // Test 2: Test EmailVerificationService for merchant
    echo "📧 Test 2: Testing EmailVerificationService for merchant...\n";
    
    $emailService = new EmailVerificationService();
    
    // Test sending verification email for temporary merchant registration
    $tempResult = $emailService->sendVerificationEmailForTempRegistration(
        'test.merchant@example.com',
        'Test Merchant User',
        '123456',
        'merchant'
    );
    
    if ($tempResult['success']) {
        echo "✅ Merchant email verification sent successfully!\n";
        echo "  - Message: {$tempResult['message']}\n";
        echo "  - Check the Laravel logs for the verification code\n";
        echo "  - Check your email: test.merchant@example.com\n\n";
    } else {
        echo "❌ Merchant email verification failed!\n";
        echo "  - Error: {$tempResult['message']}\n\n";
    }
    
    // Test 3: Test with real user (if exists)
    echo "📧 Test 3: Testing with existing merchant user...\n";
    
    $testUser = User::where('role', 'merchant')->first();
    if ($testUser) {
        echo "  - Found merchant user: {$testUser->email}\n";
        
        $result = $emailService->sendVerificationEmail($testUser, 'merchant');
        
        if ($result['success']) {
            echo "✅ Email verification sent to existing merchant!\n";
            echo "  - Message: {$result['message']}\n";
            echo "  - User ID: {$testUser->id}\n";
            echo "  - Check the Laravel logs for the verification code\n\n";
        } else {
            echo "❌ Email verification failed for existing merchant!\n";
            echo "  - Error: {$result['message']}\n\n";
        }
    } else {
        echo "  - No existing merchant users found\n";
        echo "  - Creating a test merchant user...\n";
        
        $testUser = new User();
        $testUser->name = 'Test Merchant';
        $testUser->email = 'test.merchant.new@example.com';
        $testUser->role = 'merchant';
        $testUser->id = 999; // Temporary ID for testing
        
        $result = $emailService->sendVerificationEmail($testUser, 'merchant');
        
        if ($result['success']) {
            echo "✅ Email verification sent to test merchant!\n";
            echo "  - Message: {$result['message']}\n";
            echo "  - Check the Laravel logs for the verification code\n\n";
        } else {
            echo "❌ Email verification failed for test merchant!\n";
            echo "  - Error: {$result['message']}\n\n";
        }
    }
    
    // Test 4: Check mail queue (if using queues)
    echo "📧 Test 4: Checking mail queue status...\n";
    
    if (config('queue.default') !== 'sync') {
        echo "  - Queue driver: " . config('queue.default') . "\n";
        echo "  - Emails are queued for background processing\n";
        echo "  - Run 'php artisan queue:work' to process queued emails\n\n";
    } else {
        echo "  - Queue driver: sync (emails sent immediately)\n";
        echo "  - Emails are sent synchronously\n\n";
    }
    
    echo "🎉 Merchant email verification test completed!\n";
    echo "📬 Please check your email for verification emails.\n";
    echo "📋 Check Laravel logs for verification codes and any error messages.\n\n";
    
    echo "Next Steps:\n";
    echo "1. Check your email inbox for verification emails\n";
    echo "2. Confirm if you received the emails with merchant-specific content\n";
    echo "3. If emails are received, the Mailgun configuration is working correctly\n";
    echo "4. If no emails, check Laravel logs for error messages\n";
    echo "5. Test the merchant registration flow at https://dala3chic.com/register/merchant\n\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
