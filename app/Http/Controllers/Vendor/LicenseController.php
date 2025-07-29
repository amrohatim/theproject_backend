<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LicenseController extends Controller
{
    /**
     * Display the vendor license management page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        // Get the latest license
        $license = $user->latestLicense;
        $licenseStatus = $user->getLicenseStatus();
        
        // Calculate days until expiration for active licenses
        $daysUntilExpiry = null;
        if ($license && $license->status === 'active' && !$license->isExpired()) {
            $daysUntilExpiry = $license->daysUntilExpiration();
        }

        // Check if there's already a pending renewal
        $hasPendingRenewal = $user->licenses()->where('status', 'pending')->exists();
        
        // Check if user can upload a new license (for renewal)
        $canUploadNew = !$hasPendingRenewal && (
            in_array($licenseStatus, ['rejected', 'expired']) || 
            ($license && $license->status === 'active' && $daysUntilExpiry <= 30)
        );

        return view('vendor.license.index', compact(
            'license',
            'licenseStatus', 
            'daysUntilExpiry',
            'canUploadNew',
            'hasPendingRenewal'
        ));
    }

    /**
     * Show the license renewal form.
     */
    public function showRenewal()
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        $license = $user->latestLicense;
        $licenseStatus = $user->getLicenseStatus();
        
        // Check if there's already a pending renewal
        $hasPendingRenewal = $user->licenses()->where('status', 'pending')->exists();
        
        if ($hasPendingRenewal) {
            return redirect()->route('vendor.license.index')
                ->with('error', 'You already have a pending license renewal. Please wait for admin approval before submitting another renewal.');
        }
        
        // Check if renewal is allowed
        $canRenew = in_array($licenseStatus, ['rejected', 'expired']) || 
                   ($license && $license->status === 'active' && $license->daysUntilExpiration() <= 30);
        
        if (!$canRenew) {
            return redirect()->route('vendor.license.index')
                ->with('error', 'License renewal is not available at this time.');
        }

        return view('vendor.license.renewal', compact('license'));
    }

    /**
     * Handle license renewal upload.
     */
    public function storeRenewal(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        // Check if there's already a pending renewal
        $hasPendingRenewal = $user->licenses()->where('status', 'pending')->exists();
        
        if ($hasPendingRenewal) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending license renewal. Please wait for admin approval before submitting another renewal.'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max, PDF only
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'license_file.required' => 'License file is required.',
            'license_file.mimes' => 'License file must be a PDF file only.',
            'license_file.max' => 'License file size cannot exceed 10MB.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.required' => 'End date is required.',
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after' => 'End date must be after the start date.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Store the license file
            $file = $request->file('license_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('licenses/vendor', $fileName, 'public');

            // Calculate duration in days
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $durationDays = $startDate->diffInDays($endDate);

            // Create new license record (keeping historical data)
            $license = License::create([
                'user_id' => $user->id,
                'license_type' => 'registration',
                'license_file_path' => $filePath,
                'license_file_name' => $fileName,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration_days' => $durationDays,
                'renewal_date' => $request->end_date,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'License renewal submitted successfully! Your license is now under review.',
                'redirect' => route('vendor.license.index')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the license. Please try again.'
            ], 500);
        }
    }

    /**
     * View license document in full screen.
     */
    public function viewDocument($id)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        // Find the license and ensure it belongs to the current user
        $license = License::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        $filePath = storage_path('app/public/' . $license->license_file_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'License document not found.');
        }

        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $license->license_file_name . '"'
        ]);
    }

    /**
     * Show license document preview page.
     */
    public function preview($id)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        // Find the license and ensure it belongs to the current user
        $license = License::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        return view('vendor.license.preview', compact('license'));
    }
}
