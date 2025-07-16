<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\License;

class LicenseUploadController extends Controller
{
    /**
     * Show vendor license upload form.
     */
    public function show()
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        return view('vendor.license.upload', compact('user'));
    }

    /**
     * Handle vendor license upload.
     */
    public function upload(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user is authenticated and is a vendor
        if (!$user || $user->role !== 'vendor') {
            return redirect()->route('login')->withErrors(['error' => 'Please log in as a vendor to access this page.']);
        }

        $validator = Validator::make($request->all(), [
            'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max, PDF only
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Store the license file
            $file = $request->file('license_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('licenses/vendor', $fileName, 'public');

            // Calculate duration in days
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            $durationDays = $startDate->diffInDays($endDate);

            // Create or update license record
            $license = License::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'license_type' => 'registration', // Default to registration type
                    'license_file_path' => $filePath,
                    'license_file_name' => $fileName,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'duration_days' => $durationDays,
                    'renewal_date' => $request->end_date,
                    'status' => 'pending',
                    'notes' => $request->notes,
                ]
            );

            // Update user registration step from "company_completed" to "license_completed"
            $user->update([
                'registration_step' => 'license_completed'
            ]);

            return redirect()->route('vendor.license.status', ['status' => 'pending'])
                ->with('success', 'License uploaded successfully! Your license is now under review.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to upload license. Please try again.'])->withInput();
        }
    }
}
