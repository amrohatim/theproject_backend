<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BranchRating;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchRatingController extends Controller
{
    /**
     * Get ratings for a specific branch.
     */
    public function index(Request $request, $branchId)
    {
        try {
            $branch = Branch::findOrFail($branchId);
            
            $ratings = BranchRating::where('branch_id', $branchId)
                ->with(['customer:id,name,profile_image'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'branch' => [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'average_rating' => $branch->average_rating,
                        'total_ratings' => $branch->total_ratings,
                    ],
                    'ratings' => $ratings,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch branch ratings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new branch rating.
     */
    public function store(Request $request, $branchId)
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

            $customer = Auth::user();
            
            // Check if customer role
            if ($customer->role !== 'customer') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only customers can rate branches',
                ], 403);
            }

            // Check if branch exists
            $branch = Branch::findOrFail($branchId);

            // Check if customer already rated this branch
            $existingRating = BranchRating::where('customer_id', $customer->id)
                ->where('branch_id', $branchId)
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
                $rating = BranchRating::create([
                    'customer_id' => $customer->id,
                    'branch_id' => $branchId,
                    'rating' => $request->rating,
                    'review_text' => $request->review_text,
                ]);
            }

            // Update branch's average rating and total ratings
            $this->updateBranchRatingStats($branchId);

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'data' => $rating->load('customer:id,name,profile_image'),
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
     * Get the current user's rating for a branch.
     */
    public function show(Request $request, $branchId)
    {
        try {
            $customer = Auth::user();
            
            $rating = BranchRating::where('customer_id', $customer->id)
                ->where('branch_id', $branchId)
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
     * Delete a branch rating.
     */
    public function destroy(Request $request, $branchId)
    {
        try {
            $customer = Auth::user();
            
            $rating = BranchRating::where('customer_id', $customer->id)
                ->where('branch_id', $branchId)
                ->first();

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found',
                ], 404);
            }

            $rating->delete();

            // Update branch's average rating and total ratings
            $this->updateBranchRatingStats($branchId);

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
     * Update branch's average rating and total ratings.
     */
    private function updateBranchRatingStats($branchId)
    {
        $branch = Branch::find($branchId);
        if (!$branch) return;

        $ratings = BranchRating::where('branch_id', $branchId)->get();
        $totalRatings = $ratings->count();
        $averageRating = $totalRatings > 0 ? $ratings->avg('rating') : 0;

        $branch->update([
            'average_rating' => round($averageRating, 2),
            'total_ratings' => $totalRatings,
        ]);
    }
}
