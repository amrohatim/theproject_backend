<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Provider;

class ValidationController extends Controller
{
    /**
     * Validate business name uniqueness.
     */
    public function validateBusinessName(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255'
        ]);

        $businessName = $request->input('business_name');
        
        $existingProvider = Provider::where('business_name', $businessName)->first();
        
        if ($existingProvider) {
            return response()->json([
                'available' => false,
                'message' => 'Business name is already taken'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Business name is available'
        ]);
    }

    /**
     * Validate email registration status.
     */
    public function validateEmailStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $email = $request->input('email');
        
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            // Check if email has registration step 'verified'
            if ($existingUser->registration_step === 'verified') {
                return response()->json([
                    'available' => false,
                    'message' => 'You have a registered company with this email you cannot create two accounts with the same email'
                ]);
            }
            
            // Check if email has registration step 'license_completed'
            if ($existingUser->registration_step === 'license_completed') {
                return response()->json([
                    'available' => false,
                    'message' => 'You have a submit company information wait for admin approval you will receive an email or a call from our support team , Thank you for your patience.'
                ]);
            }
        }

        return response()->json([
            'available' => true,
            'message' => 'Email is available'
        ]);
    }

    /**
     * Validate phone registration status.
     */
    public function validatePhoneStatus(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20'
        ]);

        $phone = $request->input('phone');
        
        // Normalize phone number for comparison
        $normalizedPhone = $this->normalizePhoneNumber($phone);
        
        $existingUser = User::where('phone', $normalizedPhone)
                           ->orWhere('phone', $phone)
                           ->first();
        
        if ($existingUser && $existingUser->registration_step === 'verified') {
            return response()->json([
                'available' => false,
                'message' => 'You have a registered company with this phone you cannot create two accounts with the same phone'
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Phone number is available'
        ]);
    }

    /**
     * Normalize phone number for database storage and comparison.
     */
    protected function normalizePhoneNumber($phone)
    {
        // Remove spaces and normalize UAE phone format
        $cleanPhone = preg_replace('/\s/', '', $phone);
        
        // Convert to standard format (+971XXXXXXXXX)
        if (preg_match('/^0([0-9]{9})$/', $cleanPhone, $matches)) {
            return '+971' . $matches[1];
        } elseif (preg_match('/^971([0-9]{9})$/', $cleanPhone, $matches)) {
            return '+971' . $matches[1];
        } elseif (preg_match('/^\+971([0-9]{9})$/', $cleanPhone, $matches)) {
            return '+971' . $matches[1];
        }
        
        return $cleanPhone;
    }
}
