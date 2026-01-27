<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\Service;
use App\Models\ProviderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    /**
     * Get reviews for a specific product or service.
     */
    public function index(Request $request, $type, $id)
    {
        $request->validate([
            'sort_by' => 'in:recent,oldest,rating_high,rating_low,helpful',
            'rating' => 'integer|min:1|max:5',
            'verified_only' => 'nullable|in:0,1,true,false',
            'with_images' => 'nullable|in:0,1,true,false',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:50',
        ]);

        try {
            // Validate type and get the reviewable model
            $reviewable = $this->getReviewableModel($type, $id);
            if (!$reviewable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid item type or ID',
                ], 404);
            }

            $query = Review::where('reviewable_type', get_class($reviewable))
                ->where('reviewable_id', $id)
                ->with(['user:id,name,profile_image']);

            // Apply filters
            if ($request->filled('rating')) {
                $query->where('rating', $request->rating);
            }

            if ($request->boolean('verified_only')) {
                $query->where('is_verified_purchase', true);
            }

            if ($request->boolean('with_images')) {
                $query->whereNotNull('images');
            }

            // Apply sorting
            switch ($request->get('sort_by', 'recent')) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'rating_high':
                    $query->orderBy('rating', 'desc');
                    break;
                case 'rating_low':
                    $query->orderBy('rating', 'asc');
                    break;
                case 'helpful':
                    $query->orderBy('likes', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }

            $perPage = $request->get('per_page', 10);
            $reviews = $query->paginate($perPage);

            // Get review statistics
            $stats = $this->getReviewStats($reviewable);

            return response()->json([
                'success' => true,
                'data' => [
                    'reviews' => $reviews,
                    'stats' => $stats,
                    'item' => [
                        'id' => $reviewable->id,
                        'name' => $reviewable->name,
                        'type' => $type,
                        'average_rating' => $reviewable->rating ?? 0,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new review.
     */
    public function store(Request $request, $type, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:2|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'string|url',
        ]);

        try {
            // Validate type and get the reviewable model
            $reviewable = $this->getReviewableModel($type, $id);
            if (!$reviewable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid item type or ID',
                ], 404);
            }

            $user = Auth::user();

            // Check if user already reviewed this item
            $existingReview = Review::where('user_id', $user->id)
                ->where('reviewable_type', get_class($reviewable))
                ->where('reviewable_id', $id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this item',
                ], 422);
            }

            // Check if user has purchased/used this item (for verified purchase)
            $isVerifiedPurchase = $this->checkVerifiedPurchase($user->id, $reviewable);

            DB::beginTransaction();

            // Create the review
            $review = Review::create([
                'user_id' => $user->id,
                'reviewable_type' => get_class($reviewable),
                'reviewable_id' => $id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $request->images,
                'is_verified_purchase' => $isVerifiedPurchase,
            ]);

            // Update the item's average rating
            $this->updateAverageRating($reviewable);

            DB::commit();

            // Load the review with user data
            $review->load('user:id,name,profile_image');

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully',
                'data' => $review,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, $reviewId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:2|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'string|url',
        ]);

        try {
            $review = Review::where('id', $reviewId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found or unauthorized',
                ], 404);
            }

            DB::beginTransaction();

            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $request->images,
            ]);

            // Update the item's average rating
            $reviewable = $review->reviewable;
            $this->updateAverageRating($reviewable);

            DB::commit();

            $review->load('user:id,name,profile_image');

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully',
                'data' => $review,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a review.
     */
    public function destroy($reviewId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        try {
            $review = Review::where('id', $reviewId)
                ->where('user_id', Auth::id())
                ->first();

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found or unauthorized',
                ], 404);
            }

            DB::beginTransaction();

            $reviewable = $review->reviewable;
            $review->delete();

            // Update the item's average rating
            $this->updateAverageRating($reviewable);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's review for a specific item.
     */
    public function getUserReview($type, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
            ], 401);
        }

        try {
            $reviewable = $this->getReviewableModel($type, $id);
            if (!$reviewable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid item type or ID',
                ], 404);
            }

            $review = Review::where('user_id', Auth::id())
                ->where('reviewable_type', get_class($reviewable))
                ->where('reviewable_id', $id)
                ->with('user:id,name,profile_image')
                ->first();

            return response()->json([
                'success' => true,
                'data' => $review,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Like or unlike a review.
     */
    public function toggleLike($reviewId)
    {
        try {
            $review = Review::findOrFail($reviewId);

            // For simplicity, we'll just increment/decrement likes
            // In a real app, you'd track which users liked which reviews
            $review->increment('likes');

            return response()->json([
                'success' => true,
                'message' => 'Review liked successfully',
                'data' => ['likes' => $review->likes],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like review',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the reviewable model based on type and ID.
     */
    private function getReviewableModel($type, $id)
    {
        switch ($type) {
            case 'product':
                return Product::find($id);
            case 'service':
                return Service::find($id);
            case 'provider_product':
                return ProviderProduct::find($id);
            case 'merchant':
                return \App\Models\Merchant::find($id);
            default:
                return null;
        }
    }

    /**
     * Get review statistics for an item.
     */
    private function getReviewStats($reviewable)
    {
        $baseQuery = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id);

        $totalReviews = $baseQuery->count();
        $averageRating = $baseQuery->avg('rating') ?? 0;

        $ratingBreakdown = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Review::where('reviewable_type', get_class($reviewable))
                ->where('reviewable_id', $reviewable->id)
                ->where('rating', $i)
                ->count();
            $ratingBreakdown[$i] = [
                'count' => $count,
                'percentage' => $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0,
            ];
        }

        $verifiedPurchases = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->where('is_verified_purchase', true)
            ->count();

        $withImages = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->whereNotNull('images')
            ->count();

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 2),
            'rating_breakdown' => $ratingBreakdown,
            'verified_purchases' => $verifiedPurchases,
            'with_images' => $withImages,
        ];
    }

    /**
     * Update the average rating for a reviewable item.
     */
    private function updateAverageRating($reviewable)
    {
        $averageRating = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->avg('rating');

        $totalRatings = Review::where('reviewable_type', get_class($reviewable))
            ->where('reviewable_id', $reviewable->id)
            ->count();

        // Determine which column to update based on the model type
        $updateData = [];

        if ($reviewable instanceof \App\Models\Merchant) {
            // Merchant model uses 'average_rating' and 'total_ratings'
            $updateData['average_rating'] = $averageRating;
            $updateData['total_ratings'] = $totalRatings;
        } else {
            // Product, Service, and ProviderProduct models use 'rating'
            $updateData['rating'] = $averageRating;

            // Check if the model has a total_ratings column
            if (in_array('total_ratings', $reviewable->getFillable())) {
                $updateData['total_ratings'] = $totalRatings;
            }
        }

        $reviewable->update($updateData);
    }

    /**
     * Check if the user has purchased/used this item.
     */
    private function checkVerifiedPurchase($userId, $reviewable)
    {
        // This would check order history, bookings, etc.
        // For now, we'll return false as a placeholder
        return false;
    }
}
