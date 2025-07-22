<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\License;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display the provider settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            // Create a provider record if it doesn't exist
            $provider = Provider::create([
                'user_id' => $user->id,
                'business_name' => "{$user->name}'s Business",
                'company_name' => "{$user->name}'s Company",
                'status' => 'active',
                'is_verified' => false
            ]);
        }

        // Get the latest license
        $license = License::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('provider.settings.index', compact('provider', 'license'));
    }

    /**
     * Update delivery capabilities and fees.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDelivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_capability' => 'required|boolean',
            'delivery_fees' => 'nullable|array',
            'delivery_fees.*.emirate' => 'required|string',
            'delivery_fees.*.fee' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $provider = $user->provider;

        if (!$provider) {
            return response()->json(['error' => 'Provider record not found'], 404);
        }

        // Format delivery fees
        $deliveryFees = [];
        if ($request->delivery_capability && $request->delivery_fees) {
            foreach ($request->delivery_fees as $fee) {
                $emirateKey = strtolower(str_replace(' ', '_', $fee['emirate']));
                $deliveryFees[$emirateKey] = (float) $fee['fee'];
            }
        }

        $provider->update([
            'delivery_capability' => $request->delivery_capability,
            'delivery_fees' => $deliveryFees,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Delivery settings updated successfully',
            'provider' => $provider
        ]);
    }

    /**
     * Upload a new license.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_file' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        try {
            // Store the license file
            $file = $request->file('license_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('licenses/provider', $fileName, 'public');

            // Calculate duration in days
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            $durationDays = $startDate->diffInDays($endDate);

            // Create license record
            $license = License::create([
                'user_id' => $user->id,
                'license_type' => 'registration',
                'license_file_path' => $filePath,
                'license_file_name' => $fileName,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'duration_days' => $durationDays,
                'status' => 'pending',
                'renewal_date' => $request->end_date, // Set renewal date to end date
                'notes' => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'License uploaded successfully and is pending approval',
                'license' => $license
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload license: ' . $e->getMessage()
            ], 500);
        }
    }
}