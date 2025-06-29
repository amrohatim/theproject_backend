<?php

use Illuminate\Support\Facades\Mail;
use App\Services\EmailVerificationService;
use App\Models\User;

echo "🧪 Testing Laravel Email with SMTP\n";
echo "==================================\n\n";

// Set environment to avoid encryption key issues
putenv('APP_ENV=testing');
putenv('APP_KEY=base64:OJgzQiW1sTR4cFb3k2iPvNHr/amRXIRSQmhYcbfiykA=');

try {
    require_once __DIR__ . '/vendor/autoload.php';

    // Bootstrap Laravel with minimal configuration
    $app = require_once __DIR__ . '/bootstrap/app.php';
    
    // Set the environment
    $app->detectEnvironment(function() {
        return 'testing';
    });
    
    $kernel = $app->make('Illuminate\Contracts\Console\Kernel');
    $kernel->bootstrap();

    echo "✅ Laravel bootstrapped successfully\n\n";

    // Test 1: Check mail configuration
    echo "📧 Test 1: Checking Laravel mail configuration...\n";
    
    $mailConfig = config('mail');
    echo "✓ Default Mailer: " . $mailConfig['default'] . "\n";
    echo "✓ From Address: " . $mailConfig['from']['address'] . "\n";
    echo "✓ From Name: " . $mailConfig['from']['name'] . "\n";
    
    $smtpConfig = $mailConfig['mailers']['smtp'];
    echo "✓ SMTP Host: " . $smtpConfig['host'] . "\n";
    echo "✓ SMTP Port: " . $smtpConfig['port'] . "\n";
    echo "✓ SMTP Username: " . $smtpConfig['username'] . "\n";
    echo "\n";

    // Test 2: Send a simple test email
    echo "📧 Test 2: Sending test email via Laravel Mail...\n";
    
    $toEmail = 'gogoh3296@gmail.com';
    $subject = 'Laravel SMTP Test - Dala3Chic';
    
    $emailData = [
        'subject' => $subject,
        'toEmail' => $toEmail,
        'fromEmail' => 'amro@www.dala3chic.com',
        'message' => 'This is a test email sent via Laravel Mail with SMTP configuration.',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    Mail::raw("Hello!\n\nThis is a test email to verify that Laravel Mail with SMTP is working correctly.\n\nTest Details:\n- From: {$emailData['fromEmail']}\n- To: {$emailData['toEmail']}\n- Time: {$emailData['timestamp']}\n- Method: Laravel Mail with SMTP\n\nIf you receive this email, the configuration is working perfectly!\n\nBest regards,\nDala3Chic Team", function ($message) use ($emailData) {
        $message->to($emailData['toEmail'])
                ->subject($emailData['subject'])
                ->from($emailData['fromEmail'], 'Dala3Chic Test');
    });
    
    echo "✅ Email sent successfully via Laravel Mail!\n";
    echo "📬 Please check your email at {$toEmail}\n";
    echo "📋 Subject: {$subject}\n\n";

    // Test 3: Test EmailVerificationService
    echo "📧 Test 3: Testing EmailVerificationService...\n";

    // Create a test user object (not saving to database)
    $testUser = new User();
    $testUser->id = 999;
    $testUser->name = 'Test User';
    $testUser->email = 'gogoh3296@gmail.com';
    $testUser->role = 'vendor';
    
    $emailService = new EmailVerificationService();
    $result = $emailService->sendVerificationEmail($testUser, 'vendor');
    
    if ($result['success']) {
        echo "✅ EmailVerificationService test successful!\n";
        echo "📬 Verification email sent to {$testUser->email}\n";
        echo "📋 Check Laravel logs for the verification code\n\n";
    } else {
        echo "❌ EmailVerificationService test failed!\n";
        echo "📋 Error: {$result['message']}\n\n";
    }

    echo "🎉 All tests completed!\n";
    echo "📬 Please check your email at gogoh3296@gmail.com\n";
    echo "📋 You should receive 2 emails:\n";
    echo "   1. Laravel SMTP Test email\n";
    echo "   2. Email verification email with 6-digit code\n\n";

} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "📋 Error details: " . $e->getFile() . " line " . $e->getLine() . "\n";
    
    // Check if it's the encryption key error
    if (strpos($e->getMessage(), 'encryption key') !== false) {
        echo "\n🔧 Encryption key issue detected. Let's try to fix it...\n";
        
        // Try to generate a new key
        echo "Attempting to generate new application key...\n";
        exec('php artisan key:generate --force 2>&1', $output, $returnCode);
        
        if ($returnCode === 0) {
            echo "✅ New application key generated successfully!\n";
            echo "Please run this test again.\n";
        } else {
            echo "❌ Failed to generate new key. Output:\n";
            echo implode("\n", $output) . "\n";
        }
    }
}
