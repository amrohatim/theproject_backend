<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderLocation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * Display the provider locations page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $provider = $user->providerRecord;

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

        $locations = ProviderLocation::where('provider_id', $provider->id)->get();

        return view('provider.locations.index', compact('locations'));
    }

    /**
     * Store a new provider location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'locations' => 'required|array',
            'locations.*.label' => 'nullable|string|max:255',
            'locations.*.emirate' => 'required|string|max:255',
            'locations.*.latitude' => 'required|numeric',
            'locations.*.longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $provider = $user->providerRecord;

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

        $savedLocations = [];

        foreach ($request->locations as $locationData) {
            $location = ProviderLocation::create([
                'provider_id' => $provider->id,
                'label' => $locationData['label'] ?? null,
                'emirate' => $locationData['emirate'],
                'latitude' => $locationData['latitude'],
                'longitude' => $locationData['longitude'],
            ]);

            $savedLocations[] = $location;
        }

        return response()->json([
            'success' => true,
            'message' => 'Locations saved successfully',
            'locations' => $savedLocations
        ]);
    }

    /**
     * Update a provider location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'nullable|string|max:255',
            'emirate' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            return response()->json(['error' => 'Provider record not found'], 404);
        }

        $location = ProviderLocation::where('id', $id)
            ->where('provider_id', $provider->id)
            ->first();

        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        $location->update([
            'label' => $request->label,
            'emirate' => $request->emirate,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => $location
        ]);
    }

    /**
     * Delete a provider location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            return response()->json(['error' => 'Provider record not found'], 404);
        }

        $location = ProviderLocation::where('id', $id)
            ->where('provider_id', $provider->id)
            ->first();

        if (!$location) {
            return response()->json(['error' => 'Location not found'], 404);
        }

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }
}
