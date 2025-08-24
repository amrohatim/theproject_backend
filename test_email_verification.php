<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Services\EmailVerificationService;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Email Verification with Mailgun\n";
echo "==========================================\n\n";

try {
    // Test 1: Create a test user for email verification
    echo "📧 Test 1: Creating test user for email verification...\n";
    
    $testUser = new User();
    $testUser->name = 'Test User';
    $testUser->email = 'gogoh3296@gmail.com';
    $testUser->password = bcrypt('password123');
    $testUser->role = 'vendor';
    $testUser->phone = '+971556441299';
    $testUser->id = 999; // Temporary ID for testing
    
    echo "✓ Test user created:\n";
    echo "  - Name: {$testUser->name}\n";
    echo "  - Email: {$testUser->email}\n";
    echo "  - Role: {$testUser->role}\n";
    echo "  - Phone: {$testUser->phone}\n\n";
    
    // Test 2: Test EmailVerificationService
    echo "📧 Test 2: Testing EmailVerificationService...\n";
    
    $emailService = new EmailVerificationService();
    $result = $emailService->sendVerificationEmail($testUser, 'vendor');
    
    if ($result['success']) {
        echo "✅ Email verification sent successfully!\n";
        echo "  - Message: {$result['message']}\n";
        echo "  - Check the Laravel logs for the verification code\n";
        echo "  - Check your email: {$testUser->email}\n\n";
    } else {
        echo "❌ Email verification failed!\n";
        echo "  - Error: {$result['message']}\n\n";
    }
    
    // Test 3: Test temporary registration email
    echo "📧 Test 3: Testing temporary registration email...\n";
    
    $tempResult = $emailService->sendVerificationEmailForTempRegistration(
        'gogoh3296@gmail.com',
        'Test User Temp',
        '123456',
        'vendor'
    );
    
    if ($tempResult['success']) {
        echo "✅ Temporary registration email sent successfully!\n";
        echo "  - Message: {$tempResult['message']}\n";
        echo "  - Check your email: gogoh3296@gmail.com\n\n";
    } else {
        echo "❌ Temporary registration email failed!\n";
        echo "  - Error: {$tempResult['message']}\n\n";
    }
    
    // Test 4: Check mail configuration
    echo "📧 Test 4: Checking mail configuration...\n";
    
    $mailConfig = config('mail');
    echo "✓ Mail Configuration:\n";
    echo "  - Default Mailer: " . $mailConfig['default'] . "\n";
    echo "  - From Address: " . $mailConfig['from']['address'] . "\n";
    echo "  - From Name: " . $mailConfig['from']['name'] . "\n\n";
    
    $mailgunConfig = config('services.mailgun');
    echo "✓ Mailgun Configuration:\n";
    echo "  - Domain: " . $mailgunConfig['domain'] . "\n";
    echo "  - Endpoint: " . $mailgunConfig['endpoint'] . "\n";
    echo "  - Secret: " . (strlen($mailgunConfig['secret']) > 10 ? substr($mailgunConfig['secret'], 0, 10) . '...' : 'Not set') . "\n\n";
    
    echo "🎉 Email verification test completed!\n";
    echo "📬 Please check your email at gogoh3296@gmail.com for verification emails.\n";
    echo "📋 Check Laravel logs for verification codes and any error messages.\n\n";
    
    echo "Next Steps:\n";
    echo "1. Check your email inbox for verification emails\n";
    echo "2. Confirm if you received the emails\n";
    echo "3. If emails are received, the Mailgun configuration is working correctly\n";
    echo "4. If no emails, check Laravel logs for error messages\n\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
