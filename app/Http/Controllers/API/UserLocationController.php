<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserLocationController extends Controller
{
    /**
     * Get all locations for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        $locations = UserLocation::where('user_id', $user->id)->get();
        
        return response()->json([
            'success' => true,
            'locations' => $locations,
        ]);
    }
    
    /**
     * Store a new location for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'emirate' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_default' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $user = Auth::user();
        
        // If this is the first location or is_default is true, set all other locations to not default
        if ($request->is_default || UserLocation::where('user_id', $user->id)->count() === 0) {
            UserLocation::where('user_id', $user->id)->update(['is_default' => false]);
        }
        
        $location = UserLocation::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'emirate' => $request->emirate,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_default' => $request->is_default ?? false,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'location' => $location,
        ], 201);
    }
    
    /**
     * Display the specified location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        $location = UserLocation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'location' => $location,
        ]);
    }
    
    /**
     * Update the specified location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'emirate' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_default' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        $user = Auth::user();
        $location = UserLocation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
            ], 404);
        }
        
        // If is_default is true, set all other locations to not default
        if ($request->is_default) {
            UserLocation::where('user_id', $user->id)
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }
        
        $location->update([
            'name' => $request->name,
            'emirate' => $request->emirate,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_default' => $request->is_default ?? $location->is_default,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => $location,
        ]);
    }
    
    /**
     * Remove the specified location.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $location = UserLocation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
            ], 404);
        }
        
        $wasDefault = $location->is_default;
        $location->delete();
        
        // If the deleted location was the default, set the first remaining location as default
        if ($wasDefault) {
            $firstLocation = UserLocation::where('user_id', $user->id)->first();
            if ($firstLocation) {
                $firstLocation->update(['is_default' => true]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully',
        ]);
    }
    
    /**
     * Set a location as the default.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDefault($id)
    {
        $user = Auth::user();
        $location = UserLocation::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found',
            ], 404);
        }
        
        // Set all locations to not default
        UserLocation::where('user_id', $user->id)->update(['is_default' => false]);
        
        // Set the specified location as default
        $location->update(['is_default' => true]);
        
        return response()->json([
            'success' => true,
            'message' => 'Default location updated successfully',
        ]);
    }
}
