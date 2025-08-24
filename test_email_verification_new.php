<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Start the application
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    echo "Testing new Laravel email verification system...\n";
    
    // Test the EmailVerificationService directly (without Firebase)
    $emailService = new \App\Services\EmailVerificationService();
    
    echo "✅ EmailVerificationService loaded successfully\n";
    
    // Test sending verification email
    $result = $emailService->sendVerificationEmail(
        'test@example.com',
        'vendor_registration',
        ['name' => 'Test Vendor', 'phone' => '+971501234567']
    );
    
    echo "Email service result:\n";
    print_r($result);
    
    if ($result['success']) {
        echo "✅ Email verification sent successfully without Firebase!\n";
    } else {
        echo "❌ Email verification failed: " . $result['message'] . "\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
