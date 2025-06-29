<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VendorRating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VendorRatingController extends Controller
{
    /**
     * Get ratings for a specific vendor.
     */
    public function index(Request $request, $vendorId)
    {
        try {
            $vendor = User::where('role', 'vendor')->findOrFail($vendorId);
            
            $ratings = VendorRating::where('vendor_id', $vendorId)
                ->with(['customer:id,name,profile_image'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'vendor' => [
                        'id' => $vendor->id,
                        'name' => $vendor->name,
                        'average_rating' => $vendor->average_rating,
                        'total_ratings' => $vendor->total_ratings,
                    ],
                    'ratings' => $ratings,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch vendor ratings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new vendor rating.
     */
    public function store(Request $request, $vendorId)
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
                    'message' => 'Only customers can rate vendors',
                ], 403);
            }

            // Check if vendor exists
            $vendor = User::where('role', 'vendor')->findOrFail($vendorId);

            // Check if customer already rated this vendor
            $existingRating = VendorRating::where('customer_id', $customer->id)
                ->where('vendor_id', $vendorId)
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
                $rating = VendorRating::create([
                    'customer_id' => $customer->id,
                    'vendor_id' => $vendorId,
                    'rating' => $request->rating,
                    'review_text' => $request->review_text,
                ]);
            }

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
     * Get the current user's rating for a vendor.
     */
    public function show(Request $request, $vendorId)
    {
        try {
            $customer = Auth::user();
            
            $rating = VendorRating::where('customer_id', $customer->id)
                ->where('vendor_id', $vendorId)
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
     * Delete a vendor rating.
     */
    public function destroy(Request $request, $vendorId)
    {
        try {
            $customer = Auth::user();
            
            $rating = VendorRating::where('customer_id', $customer->id)
                ->where('vendor_id', $vendorId)
                ->first();

            if (!$rating) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rating not found',
                ], 404);
            }

            $rating->delete();

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
}
