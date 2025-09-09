<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProviderRatingController extends Controller
{
    /**
     * Get ratings for a specific provider.
     */
    public function index($providerId)
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not found',
                'error' => "Provider with ID {$providerId} does not exist",
            ], 404);
        } catch (\Exception $e) {
            Log::error('Provider ratings fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provider ratings',
                'error' => 'An unexpected error occurred',
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

            $user = Auth::user();

            // Check if vendor or merchant role
            if (!in_array($user->role, ['vendor', 'merchant'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only vendors and merchants can rate providers',
                ], 403);
            }

            // Check if provider exists in the providers table
            $provider = Provider::find($providerId);
            if (!$provider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider not found',
                    'error' => "Provider with ID {$providerId} does not exist",
                ], 404);
            }

            // Check if user already rated this provider
            $existingRating = ProviderRating::where('user_id', $user->id)
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
                    'user_id' => $user->id,
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not found',
                'error' => "Provider with ID {$providerId} does not exist",
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database constraint violations
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid data provided',
                    'error' => 'Referenced provider or user does not exist',
                ], 422);
            }
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred',
                'error' => 'Please try again later',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Provider rating submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating',
                'error' => 'An unexpected error occurred',
            ], 500);
        }
    }

    /**
     * Get the current user's rating for a provider.
     */
    public function show($providerId)
    {
        try {
            $user = Auth::user();

            $rating = ProviderRating::where('user_id', $user->id)
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
    public function destroy($providerId)
    {
        try {
            $user = Auth::user();

            $rating = ProviderRating::where('user_id', $user->id)
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
            Log::error('Failed to update provider rating stats: ' . $e->getMessage());
        }
    }
}
