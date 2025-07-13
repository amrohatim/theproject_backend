<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Services\RegistrationService;
use App\Services\TemporaryRegistrationService;
use App\Services\EmailVerificationService;
use App\Services\SMSalaService;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Request::capture();
$kernel->bootstrap();

echo "🧪 Testing Merchant File Upload Fix\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Create test image files
    echo "📁 Creating test image files...\n";
    
    // Create temporary test images
    $logoContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    $uaeIdContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
    
    $logoPath = sys_get_temp_dir() . '/test_logo.png';
    $uaeIdFrontPath = sys_get_temp_dir() . '/test_uae_id_front.png';
    $uaeIdBackPath = sys_get_temp_dir() . '/test_uae_id_back.png';
    
    file_put_contents($logoPath, $logoContent);
    file_put_contents($uaeIdFrontPath, $uaeIdContent);
    file_put_contents($uaeIdBackPath, $uaeIdContent);
    
    // Create UploadedFile instances
    $logoFile = new UploadedFile($logoPath, 'test_logo.png', 'image/png', null, true);
    $uaeIdFrontFile = new UploadedFile($uaeIdFrontPath, 'test_uae_id_front.png', 'image/png', null, true);
    $uaeIdBackFile = new UploadedFile($uaeIdBackPath, 'test_uae_id_back.png', 'image/png', null, true);
    
    echo "✅ Test image files created\n\n";
    
    // Initialize services
    $tempRegistrationService = new TemporaryRegistrationService();
    $emailVerificationService = new EmailVerificationService();
    $smsalaService = new SMSalaService();
    $registrationService = new RegistrationService($tempRegistrationService, $emailVerificationService, $smsalaService);
    
    // Test data
    $testEmail = 'test_merchant_' . time() . '@example.com';
    $testPhone = '+971501234' . rand(100, 999);
    
    $userData = [
        'name' => 'Test Merchant Store',
        'email' => $testEmail,
        'phone' => $testPhone,
        'password' => 'TestPassword123',
        'password_confirmation' => 'TestPassword123',
        'logo' => $logoFile,
        'uae_id_front' => $uaeIdFrontFile,
        'uae_id_back' => $uaeIdBackFile,
        'delivery_capability' => true,
        'store_location_lat' => 25.2048,
        'store_location_lng' => 55.2708,
        'store_location_address' => 'Dubai, UAE',
    ];
    
    echo "📝 Test Data:\n";
    echo "   Name: {$userData['name']}\n";
    echo "   Email: {$userData['email']}\n";
    echo "   Phone: {$userData['phone']}\n";
    echo "   Files: logo, uae_id_front, uae_id_back\n\n";
    
    // Step 1: Start merchant registration
    echo "🚀 Step 1: Starting merchant registration...\n";
    $result = $registrationService->startMerchantRegistration($userData);
    
    if (!$result['success']) {
        throw new Exception('Failed to start registration: ' . $result['message']);
    }
    
    $registrationToken = $result['registration_token'];
    echo "✅ Registration started successfully\n";
    echo "   Token: {$registrationToken}\n\n";
    
    // Step 2: Verify email and create user (this is the method that creates the merchant profile)
    echo "📧 Step 2: Simulating email verification and creating user...\n";

    // Get the verification code that was generated and stored
    $cacheKey = "temp_email_verification_{$registrationToken}";
    $verificationCode = \Illuminate\Support\Facades\Cache::get($cacheKey);

    if (!$verificationCode) {
        throw new Exception('Verification code not found in cache');
    }

    echo "   Using verification code: {$verificationCode}\n";

    // Verify email and create user (this should create the merchant profile with files)
    $result = $registrationService->verifyEmailAndCreateUser($registrationToken, $verificationCode);
    
    if (!$result['success']) {
        throw new Exception('Failed to verify phone and create user: ' . $result['message']);
    }
    
    echo "✅ User created successfully\n";
    echo "   User ID: {$result['user_id']}\n\n";
    
    // Step 4: Check if files were saved to database
    echo "🔍 Step 4: Checking if files were saved to database...\n";
    
    $user = User::find($result['user_id']);
    if (!$user) {
        throw new Exception('User not found');
    }
    
    $merchant = $user->merchant;
    if (!$merchant) {
        throw new Exception('Merchant profile not found');
    }
    
    // Get raw database values (without accessors that convert to URLs)
    $rawLogo = $merchant->getRawOriginal('logo');
    $rawUaeIdFront = $merchant->getRawOriginal('uae_id_front');
    $rawUaeIdBack = $merchant->getRawOriginal('uae_id_back');

    echo "📊 Database Results (Raw Values):\n";
    echo "   Merchant ID: {$merchant->id}\n";
    echo "   Logo: " . ($rawLogo ? "✅ {$rawLogo}" : "❌ NULL") . "\n";
    echo "   UAE ID Front: " . ($rawUaeIdFront ? "✅ {$rawUaeIdFront}" : "❌ NULL") . "\n";
    echo "   UAE ID Back: " . ($rawUaeIdBack ? "✅ {$rawUaeIdBack}" : "❌ NULL") . "\n\n";

    echo "📊 Database Results (With Accessors - URLs):\n";
    echo "   Logo: " . ($merchant->logo ? "✅ {$merchant->logo}" : "❌ NULL") . "\n";
    echo "   UAE ID Front: " . ($merchant->uae_id_front ? "✅ {$merchant->uae_id_front}" : "❌ NULL") . "\n";
    echo "   UAE ID Back: " . ($merchant->uae_id_back ? "✅ {$merchant->uae_id_back}" : "❌ NULL") . "\n\n";

    // Step 5: Check if files exist in storage
    echo "💾 Step 5: Checking if files exist in storage...\n";

    $filesExist = true;
    if ($rawLogo) {
        $logoExists = Storage::disk('public')->exists($rawLogo);
        echo "   Logo file exists: " . ($logoExists ? "✅ Yes" : "❌ No") . " (Path: {$rawLogo})\n";
        $filesExist = $filesExist && $logoExists;
    }

    if ($rawUaeIdFront) {
        $frontExists = Storage::disk('public')->exists($rawUaeIdFront);
        echo "   UAE ID Front file exists: " . ($frontExists ? "✅ Yes" : "❌ No") . " (Path: {$rawUaeIdFront})\n";
        $filesExist = $filesExist && $frontExists;
    }

    if ($rawUaeIdBack) {
        $backExists = Storage::disk('public')->exists($rawUaeIdBack);
        echo "   UAE ID Back file exists: " . ($backExists ? "✅ Yes" : "❌ No") . " (Path: {$rawUaeIdBack})\n";
        $filesExist = $filesExist && $backExists;
    }
    
    echo "\n";
    
    // Final result
    $allFilesSaved = $rawLogo && $rawUaeIdFront && $rawUaeIdBack;
    
    if ($allFilesSaved && $filesExist) {
        echo "🎉 SUCCESS: All files were saved to database and exist in storage!\n";
        echo "✅ The fix is working correctly.\n";
    } else {
        echo "❌ FAILURE: Some files were not saved properly.\n";
        if (!$allFilesSaved) {
            echo "   - Database paths missing\n";
        }
        if (!$filesExist) {
            echo "   - Storage files missing\n";
        }
    }
    
    // Cleanup
    echo "\n🧹 Cleaning up test data...\n";
    $merchant->delete();
    $user->delete();
    unlink($logoPath);
    unlink($uaeIdFrontPath);
    unlink($uaeIdBackPath);
    echo "✅ Cleanup completed\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    
    // Cleanup on error
    if (isset($logoPath) && file_exists($logoPath)) unlink($logoPath);
    if (isset($uaeIdFrontPath) && file_exists($uaeIdFrontPath)) unlink($uaeIdFrontPath);
    if (isset($uaeIdBackPath) && file_exists($uaeIdBackPath)) unlink($uaeIdBackPath);
}

echo "\n=== Test Complete ===\n";
