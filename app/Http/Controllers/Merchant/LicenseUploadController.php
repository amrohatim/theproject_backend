<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Services\LicenseManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LicenseUploadController extends Controller
{
    protected $licenseService;

    public function __construct(LicenseManagementService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    /**
     * Show the license upload form for merchants who completed phone verification
     * but haven't uploaded their license yet.
     */
    public function show()
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Merchant profile not found. Please contact support.');
        }

        // Check if user should be on this page
        // Allow access if:
        // 1. User has completed phone verification (registration_step = 'phone_verified')
        // 2. User has license_completed but license is rejected or expired (needs to re-upload)
        // 3. User is verified but has expired/rejected license (needs to renew)
        $allowedSteps = ['phone_verified', 'license_completed', 'verified'];

        if (!in_array($user->registration_step, $allowedSteps)) {
            // If they haven't completed phone verification, redirect to registration
            return redirect()->route('merchant.registration.status')
                ->with('error', 'Please complete your registration steps first.');
        }

        // For users with license_completed or verified status, check if license needs renewal
        if (in_array($user->registration_step, ['license_completed', 'verified'])) {
            // Check if license is actually expired based on expiry date
            $isExpired = $merchant->license_expiry_date && $merchant->license_expiry_date < now()->toDateString();

            // Allow access if license is rejected, expired in database, or expired by date
            if (!in_array($merchant->license_status, ['rejected', 'expired']) && !$isExpired) {
                // If license is active, pending, or checking and not expired by date, redirect to dashboard
                return redirect()->route('merchant.dashboard')
                    ->with('info', 'Your license is already active or under review.');
            }
        }

        // For phone_verified users, check if they already have an active license
        if ($user->registration_step === 'phone_verified' && $merchant->license_file && !in_array($merchant->license_status, ['rejected', 'expired'])) {
            return redirect()->route('merchant.dashboard')
                ->with('info', 'Your license is already active or under review.');
        }

        return view('merchant.license.upload', [
            'merchant' => $merchant,
            'user' => $user,
            'isRejectedLicense' => $merchant->license_status === 'rejected',
            'rejectionReason' => $merchant->license_rejection_reason,
        ]);
    }

    /**
     * Handle the license upload submission.
     */
    public function upload(Request $request)
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Merchant profile not found. Please contact support.');
        }

        // Validate the request
        $validator = Validator::make($request->all(), [
            'license_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'license_start_date' => 'required|date|after_or_equal:today',
            'license_end_date' => 'required|date|after:license_start_date',
            'license_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
        ], [
            'license_file.required' => 'Please select a license file to upload.',
            'license_file.mimes' => 'License file must be a PDF, JPG, JPEG, or PNG file.',
            'license_file.max' => 'License file size cannot exceed 10MB.',
            'license_start_date.required' => 'License start date is required.',
            'license_start_date.date' => 'License start date must be a valid date.',
            'license_start_date.after_or_equal' => 'License start date cannot be in the past.',
            'license_end_date.required' => 'License end date is required.',
            'license_end_date.date' => 'License end date must be a valid date.',
            'license_end_date.after' => 'License end date must be after the start date.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below and try again.');
        }

        try {
            // Upload the license using the license management service
            $uploadResult = $this->licenseService->uploadLicense(
                $merchant, 
                $request->file('license_file'), 
                $request->license_end_date
            );

            if ($uploadResult) {
                // Update merchant license details
                $merchant->update([
                    'license_start_date' => $request->license_start_date,
                    'license_expiry_date' => $request->license_end_date,
                    'license_status' => 'checking',
                    'license_uploaded_at' => now(),
                    'license_rejection_reason' => null, // Clear any previous rejection reason
                ]);

                // Update user registration step
                $user->update([
                    'registration_step' => 'license_completed'
                ]);

                return redirect()->route('merchant.license.status', ['status' => 'checking'])
                    ->with('success', 'License uploaded successfully! Your license is now under review by our admin team. You will receive an email notification once approved.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to upload license. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('License upload error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while uploading your license. Please try again.');
        }
    }

    /**
     * Show license upload progress/status after upload.
     */
    public function status()
    {
        $user = Auth::user();
        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return redirect()->route('merchant.dashboard')
                ->with('error', 'Merchant profile not found. Please contact support.');
        }

        return view('merchant.license.upload-status', [
            'merchant' => $merchant,
            'user' => $user,
        ]);
    }
}
