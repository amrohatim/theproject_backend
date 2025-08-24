<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderRating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProviderRatingController extends Controller
{
    /**
     * Get ratings for a specific provider.
     */
    public function index(Request $request, $providerId)
    {
        try {
            // Find the provider record in the providers table
            $provider = Provider::with('user:id,name,profile_image')->findOrFail($providerId);

            $ratings = ProviderRating::where('provider_id', $providerId)
                ->with(['vendor:id,name,profile_image'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'provider' => [
                        'id' => $provider->id,
                        'name' => $provider->business_name,
                        'user_name' => $provider->user->name ?? 'Unknown',
                        'average_rating' => $provider->average_rating ?? 0,
                        'total_ratings' => $provider->total_ratings ?? 0,
                    ],
                    'ratings' => $ratings,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider ratings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new provider rating.
     */
    public function store(Request $request, $providerId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'review_text' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $vendor = Auth::user();

            // Check if vendor role
            if ($vendor->role !== 'vendor') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only vendors can rate providers',
                ], 403);
            }

            // Check if provider exists in the providers table
            $provider = Provider::findOrFail($providerId);

            // Check if vendor already rated this provider
            $existingRating = ProviderRating::where('vendor_id', $vendor->id)
                ->where('provider_id', $providerId)
                ->first();

            if ($existingRating) {
                // Update existing rating
                $existingRating->update([
                    'rating' => $request->rating,
                    'review_text' => $request->review_text,
                ]);
                $rating = $existingRating;
            } else {
                // Create new rating
                $rating = ProviderRating::create([
                    'vendor_id' => $vendor->id,
                    'provider_id' => $providerId,
                    'rating' => $request->rating,
                    'review_text' => $request->review_text,
                ]);
            }

            // Update provider's average rating and total ratings
            $this->updateProviderRatingStats($providerId);

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'data' => $rating->load('vendor:id,name,profile_image'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the current user's rating for a provider.
     */
    public function show(Request $request, $providerId)
    {
        try {
            $vendor = Auth::user();

            $rating = ProviderRating::where('vendor_id', $vendor->id)
                ->where('provider_id', $providerId)
                ->first();

            return response()->json([
                'success' => true,
                'data' => $rating,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rating',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a provider rating.
     */
    public function destroy(Request $request, $providerId)
    {
        try {
            $vendor = Auth::user();

            $rating = ProviderRating::where('vendor_id', $vendor->id)
                ->where('provider_id', $providerId)
                ->first();

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found',
                ], 404);
            }

            $rating->delete();

            // Update provider's average rating and total ratings
            $this->updateProviderRatingStats($providerId);

            return response()->json([
                'success' => true,
                'message' => 'Rating deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rating',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update provider's rating statistics.
     */
    private function updateProviderRatingStats($providerId)
    {
        try {
            $provider = Provider::findOrFail($providerId);

            $ratings = ProviderRating::where('provider_id', $providerId)->get();
            $totalRatings = $ratings->count();
            $averageRating = $totalRatings > 0 ? $ratings->avg('rating') : 0;

            $provider->update([
                'average_rating' => round($averageRating, 2),
                'total_ratings' => $totalRatings,
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the rating submission
            \Log::error('Failed to update provider rating stats: ' . $e->getMessage());
        }
    }
}
