<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\API\EmailVerificationController;
use Illuminate\Http\Request;

echo "🧪 Testing Email Verification Endpoints Directly\n";
echo "================================================\n\n";

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "📧 Test 1: Setting up vendor registration session...\n";

// Simulate vendor registration session data
$vendorData = [
    'step' => 1,
    'vendor_info' => [
        'name' => 'Amro Osman',
        'email' => 'gogoh3296@gmail.com',
        'phone' => '+971501234567',
        'password' => 'TestPassword123!'
    ]
];

Session::put('vendor_registration', $vendorData);
echo "✅ Vendor registration session created\n";
echo "✓ Name: " . $vendorData['vendor_info']['name'] . "\n";
echo "✓ Email: " . $vendorData['vendor_info']['email'] . "\n";
echo "✓ Phone: " . $vendorData['vendor_info']['phone'] . "\n\n";

echo "📧 Test 2: Testing send email verification directly...\n";

try {
    // Create controller instance
    $controller = app(EmailVerificationController::class);
    
    // Create a mock request
    $request = new Request();
    $request->setMethod('POST');
    
    // Call the send email verification method
    $response = $controller->sendVendorEmailVerification($request);
    
    echo "✅ Controller method called successfully\n";
    echo "📤 Response status: " . $response->getStatusCode() . "\n";
    echo "📤 Response content: " . $response->getContent() . "\n";
    
    $responseData = json_decode($response->getContent(), true);
    if ($responseData && isset($responseData['success']) && $responseData['success']) {
        echo "✅ Email verification sent successfully!\n";
    } else {
        echo "❌ Email verification failed\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error calling send email verification: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n📧 Test 3: Testing check email verification directly...\n";

try {
    // Create a mock request
    $checkRequest = new Request();
    $checkRequest->setMethod('POST');
    
    // Call the check email verification method
    $checkResponse = $controller->checkVendorEmailVerification($checkRequest);
    
    echo "✅ Controller method called successfully\n";
    echo "📤 Response status: " . $checkResponse->getStatusCode() . "\n";
    echo "📤 Response content: " . $checkResponse->getContent() . "\n";
    
    $checkResponseData = json_decode($checkResponse->getContent(), true);
    if ($checkResponseData && isset($checkResponseData['success']) && $checkResponseData['success']) {
        if (isset($checkResponseData['verified']) && $checkResponseData['verified']) {
            echo "✅ Email verification status: VERIFIED\n";
        } else {
            echo "⏳ Email verification status: PENDING\n";
        }
    } else {
        echo "❌ Email verification check failed\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error calling check email verification: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n📧 Test 4: Checking session data after tests...\n";

$updatedVendorData = Session::get('vendor_registration');
if ($updatedVendorData) {
    echo "✅ Session data exists\n";
    echo "✓ Current step: " . ($updatedVendorData['step'] ?? 'unknown') . "\n";
    echo "✓ Email verification sent: " . (isset($updatedVendorData['email_verification_sent']) && $updatedVendorData['email_verification_sent'] ? 'Yes' : 'No') . "\n";
    echo "✓ Email verified: " . (isset($updatedVendorData['email_verified']) && $updatedVendorData['email_verified'] ? 'Yes' : 'No') . "\n";
    
    if (isset($updatedVendorData['verification_code'])) {
        echo "✓ Verification code: " . $updatedVendorData['verification_code'] . "\n";
    }
    
    if (isset($updatedVendorData['verification_code_expires'])) {
        echo "✓ Verification code expires: " . date('Y-m-d H:i:s', $updatedVendorData['verification_code_expires']) . "\n";
    }
} else {
    echo "❌ No session data found\n";
}

echo "\n🎯 Summary:\n";
echo "- Test the new Laravel-based email verification endpoints directly\n";
echo "- Verify that emails are sent using the working SMTP system\n";
echo "- Check that session data is properly updated\n";
echo "- Confirm the verification flow works end-to-end\n";
