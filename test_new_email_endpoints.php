<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Session;

echo "🧪 Testing New Email Verification Endpoints\n";
echo "===========================================\n\n";

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

echo "📧 Test 2: Testing send email verification endpoint...\n";

// Test the send email verification endpoint
$url = 'http://localhost/api/vendor/register/send-firebase-email-verification';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Response Code: {$httpCode}\n";
if ($error) {
    echo "❌ cURL Error: {$error}\n";
} else {
    echo "📤 Response: {$response}\n";
    
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['success']) && $responseData['success']) {
        echo "✅ Email verification sent successfully!\n";
    } else {
        echo "❌ Email verification failed\n";
    }
}

echo "\n📧 Test 3: Testing check email verification endpoint...\n";

// Wait a moment then test the check verification endpoint
sleep(2);

$checkUrl = 'http://localhost/api/vendor/register/check-firebase-email-verification';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $checkUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-Requested-With: XMLHttpRequest'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));

$checkResponse = curl_exec($ch);
$checkHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$checkError = curl_error($ch);
curl_close($ch);

echo "HTTP Response Code: {$checkHttpCode}\n";
if ($checkError) {
    echo "❌ cURL Error: {$checkError}\n";
} else {
    echo "📤 Response: {$checkResponse}\n";
    
    $checkResponseData = json_decode($checkResponse, true);
    if ($checkResponseData && isset($checkResponseData['success']) && $checkResponseData['success']) {
        if (isset($checkResponseData['verified']) && $checkResponseData['verified']) {
            echo "✅ Email verification status: VERIFIED\n";
        } else {
            echo "⏳ Email verification status: PENDING\n";
        }
    } else {
        echo "❌ Email verification check failed\n";
    }
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
} else {
    echo "❌ No session data found\n";
}

echo "\n🎯 Summary:\n";
echo "- Test the new Laravel-based email verification endpoints\n";
echo "- Verify that emails are sent using the working SMTP system\n";
echo "- Check that session data is properly updated\n";
echo "- Confirm the verification flow works end-to-end\n";
