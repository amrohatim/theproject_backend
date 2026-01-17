<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GeneralWishlistService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeneralWishlistServiceController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $serviceIds = GeneralWishlistService::where('user_id', $userId)
            ->pluck('service_id')
            ->toArray();

        if (empty($serviceIds)) {
            return response()->json([
                'success' => true,
                'services' => [],
            ]);
        }

        $services = Service::whereIn('id', $serviceIds)->get();

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
        ]);

        $userId = Auth::id();
        $serviceId = $validated['service_id'];

        GeneralWishlistService::firstOrCreate([
            'user_id' => $userId,
            'service_id' => $serviceId,
        ]);

        $service = Service::find($serviceId);

        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'service' => $service,
        ]);
    }

    public function destroy($serviceId)
    {
        $userId = Auth::id();

        GeneralWishlistService::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
