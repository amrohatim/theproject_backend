<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VendorRating;
use App\Models\User;
use App\Models\Company;
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

            // Update vendor's average rating and total ratings
            $this->updateVendorRatingStats($vendorId);

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

            // Update vendor's average rating and total ratings
            $this->updateVendorRatingStats($vendorId);

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
     * Get ratings for a vendor by company ID.
     * This is useful for public access where we don't want to expose user_id.
     */
    public function getByCompanyId(Request $request, $companyId)
    {
        try {
            // Find the company and get its user_id
            $company = Company::findOrFail($companyId);
            $vendorId = $company->user_id;

            // Find the vendor user
            $vendor = User::where('role', 'vendor')->findOrFail($vendorId);

            // Get ratings for this vendor
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
                    'company' => [
                        'id' => $company->id,
                        'name' => $company->name,
                    ],
                    'ratings' => $ratings,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch company vendor ratings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Submit a rating for a vendor by company ID.
     * This allows rating a company's vendor without knowing the vendor's user_id.
     */
    public function storeByCompanyId(Request $request, $companyId)
    {
        try {
            // Find the company and get its user_id (vendor_id)
            $company = Company::findOrFail($companyId);
            $vendorId = $company->user_id;

            if (!$vendorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This company does not have an associated vendor account',
                ], 400);
            }

            // Find the vendor user
            $vendor = User::where('role', 'vendor')->findOrFail($vendorId);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'rating' => 'required|integer|min:1|max:5',
                'review_text' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $customerId = Auth::id();

            // Check if the customer has already rated this vendor
            $existingRating = VendorRating::where('vendor_id', $vendorId)
                ->where('customer_id', $customerId)
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
                    'vendor_id' => $vendorId,
                    'customer_id' => $customerId,
                    'rating' => $request->rating,
                    'review_text' => $request->review_text,
                ]);
            }

            // Update vendor's average rating and total ratings
            $this->updateVendorRatingStats($vendorId);

            return response()->json([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'data' => [
                    'rating' => $rating,
                    'company' => [
                        'id' => $company->id,
                        'name' => $company->name,
                    ],
                    'vendor' => [
                        'id' => $vendor->id,
                        'name' => $vendor->name,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit company vendor rating',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get my rating for a vendor by company ID.
     */
    public function showByCompanyId(Request $request, $companyId)
    {
        try {
            // Find the company and get its user_id (vendor_id)
            $company = Company::findOrFail($companyId);
            $vendorId = $company->user_id;

            if (!$vendorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'This company does not have an associated vendor account',
                ], 400);
            }

            $customerId = Auth::id();

            $rating = VendorRating::where('vendor_id', $vendorId)
                ->where('customer_id', $customerId)
                ->first();

            if (!$rating) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'No rating found',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $rating,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch company vendor rating',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update vendor's average rating and total ratings.
     */
    private function updateVendorRatingStats($vendorId)
    {
        $vendor = User::where('role', 'vendor')->find($vendorId);
        if (!$vendor) return;

        $ratings = VendorRating::where('vendor_id', $vendorId)->get();
        $totalRatings = $ratings->count();
        $averageRating = $totalRatings > 0 ? $ratings->avg('rating') : 0;

        $vendor->update([
            'average_rating' => round($averageRating, 2),
            'total_ratings' => $totalRatings,
        ]);
    }
}
