<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Providers\FirebaseServiceProvider;
use App\Services\FirebaseService;
use App\Services\FirebaseOTPService;

class FirebaseTestController extends Controller
{
    /**
     * Test Firebase configuration and connectivity
     */
    public function testFirebase()
    {
        $results = [];
        
        // Test 1: Check if Firebase is configured
        $results['configured'] = FirebaseServiceProvider::isConfigured();
        
        // Test 2: Check if Firebase Auth is available
        try {
            $auth = FirebaseServiceProvider::getAuth();
            $results['auth_available'] = $auth !== null;
            
            if ($auth) {
                // Test 3: Try to list users (basic connectivity test)
                try {
                    $users = $auth->listUsers(1);
                    $results['connection_test'] = 'success';
                } catch (\Exception $e) {
                    $results['connection_test'] = 'failed';
                    $results['connection_error'] = $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $results['auth_available'] = false;
            $results['auth_error'] = $e->getMessage();
        }
        
        // Test 4: Test Firebase Service
        try {
            $firebaseService = new FirebaseService();
            $results['service_available'] = $firebaseService->isAvailable();
        } catch (\Exception $e) {
            $results['service_available'] = false;
            $results['service_error'] = $e->getMessage();
        }
        
        // Test 5: Test Firebase OTP Service
        try {
            $otpService = new FirebaseOTPService();
            $otpResult = $otpService->sendOTP('+1234567890');
            $results['otp_test'] = $otpResult['success'] ? 'success' : 'failed';
            if (!$otpResult['success']) {
                $results['otp_error'] = $otpResult['message'] ?? 'Unknown error';
            }
        } catch (\Exception $e) {
            $results['otp_test'] = 'failed';
            $results['otp_error'] = $e->getMessage();
        }
        
        return response()->json([
            'status' => 'Firebase Test Results',
            'timestamp' => now()->toISOString(),
            'results' => $results
        ]);
    }
    
    /**
     * Test Firebase user creation (with SSL handling)
     */
    public function testUserCreation(Request $request)
    {
        $email = $request->input('email', 'test' . time() . '@example.com');
        $password = $request->input('password', 'testpassword123');
        
        try {
            $firebaseService = new FirebaseService();
            
            if (!$firebaseService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Firebase service not available'
                ], 503);
            }
            
            $result = $firebaseService->createUser($email, $password);
            
            // If user creation was successful, clean up by deleting the test user
            if ($result['success'] && isset($result['uid'])) {
                $deleteResult = $firebaseService->deleteUser($result['uid']);
                $result['cleanup'] = $deleteResult['success'] ? 'success' : 'failed';
            }
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Firebase user creation test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Test Firebase email verification
     */
    public function testEmailVerification(Request $request)
    {
        $uid = $request->input('uid');
        
        if (!$uid) {
            return response()->json([
                'success' => false,
                'error' => 'UID parameter is required'
            ], 400);
        }
        
        try {
            $firebaseService = new FirebaseService();
            
            if (!$firebaseService->isAvailable()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Firebase service not available'
                ], 503);
            }
            
            $result = $firebaseService->sendEmailVerification($uid);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Firebase email verification test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
