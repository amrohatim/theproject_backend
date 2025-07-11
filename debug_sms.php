<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\SMSalaService;

echo "Testing Mock OTP System:\n";
echo "Debug Mode: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "Environment: " . config('app.env') . "\n\n";

// Test SMSala service with mock OTP
$smsalaService = new SMSalaService();

// Test sending OTP
echo "1. Testing sendOTP():\n";
$sendResult = $smsalaService->sendOTP('971501234567', 'registration');
echo "Send Result:\n";
print_r($sendResult);
echo "\n";

if ($sendResult['success']) {
    $requestId = $sendResult['request_id'];

    // Test verifying with wrong OTP
    echo "2. Testing verifyOTP() with wrong code (123456):\n";
    $verifyWrongResult = $smsalaService->verifyOTP($requestId, '123456');
    echo "Verify Wrong Result:\n";
    print_r($verifyWrongResult);
    echo "\n";

    // Test verifying with correct OTP
    echo "3. Testing verifyOTP() with correct code (666666):\n";
    $verifyCorrectResult = $smsalaService->verifyOTP($requestId, '666666');
    echo "Verify Correct Result:\n";
    print_r($verifyCorrectResult);
    echo "\n";
} else {
    echo "Failed to send OTP, cannot test verification.\n";
}
