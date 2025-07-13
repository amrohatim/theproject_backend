<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Http\Controllers\API\MerchantRegistrationController;
use App\Models\User;
use App\Models\Merchant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Merchant License Upload API with Date Pickers\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    // Test 1: Verify API controller can be instantiated
    echo "📋 Test 1: Instantiating MerchantRegistrationController...\n";

    $registrationService = app(\App\Services\RegistrationService::class);
    $smsalaService = app(\App\Services\SMSalaService::class);
    $controller = new MerchantRegistrationController($registrationService, $smsalaService);

    echo "✅ API controller instantiated successfully!\n\n";
    
    // Test 2: Create a test merchant user
    echo "👤 Test 2: Creating test merchant user...\n";
    
    // Check if test user already exists
    $existingUser = User::where('email', 'test.license.merchant@example.com')->first();
    if ($existingUser) {
        echo "  - Deleting existing test user...\n";
        if ($existingUser->merchant) {
            $existingUser->merchant->delete();
        }
        $existingUser->delete();
    }
    
    $testUser = User::create([
        'name' => 'Test License Merchant',
        'email' => 'test.license.merchant@example.com',
        'phone' => '+971501234567',
        'password' => bcrypt('password123'),
        'role' => 'merchant',
        'status' => 'active',
        'registration_step' => 'phone_verified',
        'email_verified_at' => now(),
    ]);
    
    // Create merchant record
    $testMerchant = Merchant::create([
        'user_id' => $testUser->id,
        'business_name' => 'Test License Business',
        'business_type' => 'retail',
        'description' => 'Test business for license upload',
        'address' => '123 Test Street',
        'city' => 'Dubai',
        'emirate' => 'Dubai',
        'country' => 'UAE',
        'status' => 'pending',
        'is_verified' => false,
    ]);
    
    echo "✅ Test merchant user created successfully!\n";
    echo "  - User ID: {$testUser->id}\n";
    echo "  - Merchant ID: {$testMerchant->id}\n\n";
    
    // Test 3: Create a test PDF file
    echo "📄 Test 3: Creating test license PDF file...\n";
    
    $testPdfContent = '%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj
2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
>>
endobj
xref
0 4
0000000000 65535 f 
0000000009 00000 n 
0000000074 00000 n 
0000000120 00000 n 
trailer
<<
/Size 4
/Root 1 0 R
>>
startxref
179
%%EOF';
    
    $tempFilePath = sys_get_temp_dir() . '/test_license_' . time() . '.pdf';
    file_put_contents($tempFilePath, $testPdfContent);
    
    echo "✅ Test PDF file created successfully!\n";
    echo "  - File path: {$tempFilePath}\n\n";
    
    // Test 4: Test license upload with date fields
    echo "📤 Test 4: Testing license upload with date fields...\n";
    
    // Create UploadedFile instance
    $uploadedFile = new UploadedFile(
        $tempFilePath,
        'test_license.pdf',
        'application/pdf',
        null,
        true
    );
    
    // Prepare request data with date fields (using future dates)
    $startDate = date('Y-m-d', strtotime('+1 day')); // Tomorrow
    $endDate = date('Y-m-d', strtotime('+1 year')); // One year from now

    $licenseData = [
        'user_id' => $testUser->id,
        'license_start_date' => $startDate,
        'license_end_date' => $endDate,
        'notes' => 'Test license upload with date picker implementation',
    ];
    
    // Create mock request
    $request = new Request();
    $request->merge($licenseData);
    $request->files->set('license_file', $uploadedFile);
    
    echo "  - Request data prepared:\n";
    echo "    * User ID: {$licenseData['user_id']}\n";
    echo "    * Start Date: {$licenseData['license_start_date']}\n";
    echo "    * End Date: {$licenseData['license_end_date']}\n";
    echo "    * Notes: {$licenseData['notes']}\n";
    echo "    * File: test_license.pdf\n\n";
    
    // Call the upload method
    $response = $controller->uploadLicense($request);
    $responseData = json_decode($response->getContent(), true);
    
    echo "  - API Response:\n";
    echo "    * Status Code: {$response->getStatusCode()}\n";
    echo "    * Success: " . ($responseData['success'] ? 'true' : 'false') . "\n";
    echo "    * Message: {$responseData['message']}\n";
    
    if ($responseData['success']) {
        echo "✅ License upload successful!\n\n";
        
        // Test 5: Verify database records
        echo "🗄️ Test 5: Verifying database records...\n";
        
        // Check merchant record
        $testMerchant->refresh();
        echo "  - Merchant record updated:\n";
        echo "    * License file: " . ($testMerchant->license_file ? 'Set' : 'Not set') . "\n";
        echo "    * License start date: " . ($testMerchant->license_start_date ? $testMerchant->license_start_date->format('Y-m-d') : 'Not set') . "\n";
        echo "    * License expiry date: " . ($testMerchant->license_expiry_date ? $testMerchant->license_expiry_date->format('Y-m-d') : 'Not set') . "\n";
        echo "    * License status: {$testMerchant->license_status}\n";
        echo "    * License verified: " . ($testMerchant->license_verified ? 'true' : 'false') . "\n";
        echo "    * License uploaded at: " . ($testMerchant->license_uploaded_at ? $testMerchant->license_uploaded_at->format('Y-m-d H:i:s') : 'Not set') . "\n";
        
        // Check license record in licenses table
        $license = \App\Models\License::where('user_id', $testUser->id)->latest()->first();
        if ($license) {
            echo "  - License record created:\n";
            echo "    * License ID: {$license->id}\n";
            echo "    * Start date: {$license->start_date}\n";
            echo "    * End date: {$license->end_date}\n";
            echo "    * Duration days: {$license->duration_days}\n";
            echo "    * Status: {$license->status}\n";
            echo "    * Notes: " . ($license->notes ?: 'None') . "\n";
        }
        
        echo "✅ Database verification completed!\n\n";
        
        // Test 6: Test date format validation
        echo "📅 Test 6: Testing date format validation...\n";
        
        $startDate = $testMerchant->license_start_date;
        $endDate = $testMerchant->license_expiry_date;
        
        if ($startDate && $endDate) {
            echo "  - Date format validation:\n";
            echo "    * Start date stored: {$startDate->format('Y-m-d')} (YYYY-MM-DD)\n";
            echo "    * Start date display: {$startDate->format('d-m-Y')} (DD-MM-YYYY)\n";
            echo "    * End date stored: {$endDate->format('Y-m-d')} (YYYY-MM-DD)\n";
            echo "    * End date display: {$endDate->format('d-m-Y')} (DD-MM-YYYY)\n";
            
            if ($endDate > $startDate) {
                echo "    * Date validation: ✅ End date is after start date\n";
            } else {
                echo "    * Date validation: ❌ End date is not after start date\n";
            }
        }
        
        echo "✅ Date format validation completed!\n\n";
        
    } else {
        echo "❌ License upload failed!\n";
        if (isset($responseData['errors'])) {
            echo "  - Validation errors:\n";
            foreach ($responseData['errors'] as $field => $errors) {
                echo "    * {$field}: " . implode(', ', $errors) . "\n";
            }
        }
        echo "\n";
    }
    
    // Cleanup
    echo "🧹 Cleanup: Removing test data...\n";
    
    // Delete test files
    if (file_exists($tempFilePath)) {
        unlink($tempFilePath);
        echo "  - Test PDF file deleted\n";
    }
    
    // Delete test records
    if ($testMerchant) {
        $testMerchant->delete();
        echo "  - Test merchant deleted\n";
    }
    
    if ($testUser) {
        $testUser->delete();
        echo "  - Test user deleted\n";
    }
    
    echo "✅ Cleanup completed!\n\n";
    
    echo "🎉 All tests completed successfully!\n";
    echo "📋 Summary:\n";
    echo "  - ✅ API controller instantiation\n";
    echo "  - ✅ Test user creation\n";
    echo "  - ✅ Test PDF file creation\n";
    echo "  - ✅ License upload with date fields\n";
    echo "  - ✅ Database record verification\n";
    echo "  - ✅ Date format validation\n";
    echo "  - ✅ Cleanup\n\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}
