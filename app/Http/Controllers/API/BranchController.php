<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * Display a listing of the branches.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Branch::with(['user', 'company']);

        // Filter by user_id if provided
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by company_id if provided
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by featured if provided
        if ($request->has('featured')) {
            $query->filterByFeatured($request->boolean('featured'));
        }

        $branches = $query->paginate(10);

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Public endpoint to fetch all branches with optional search and pagination.
     */
    public function getAll(Request $request)
    {
        $perPage = $request->input('per_page', 50);
        $search = $request->input('search');

        $query = Branch::with(['user', 'company'])
            ->where('status', 'active')
            ->orderByDesc('created_at');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $branches = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'branches' => $branches->items(),
            'pagination' => [
                'current_page' => $branches->currentPage(),
                'last_page' => $branches->lastPage(),
                'per_page' => $branches->perPage(),
                'total' => $branches->total(),
                'has_more_pages' => $branches->hasMorePages(),
            ],
        ]);
    }

    /**
     * Store a newly created branch in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only vendors can create branches
        if (!Auth::user()->isVendor()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'branch_image' => 'nullable|string',
            'use_company_image' => 'nullable|boolean',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_hours' => 'nullable|json',
            'featured' => 'boolean',
        ]);

        // If company_id is provided, ensure it belongs to the authenticated user
        if ($request->has('company_id')) {
            $company = Company::findOrFail($request->company_id);
            if ($company->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to add branches to this company',
                ], 403);
            }
        }

        // Add user_id to request data
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'active';

        $branch = Branch::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Branch created successfully',
            'branch' => $branch,
        ], 201);
    }

    /**
     * Display the specified branch.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $branch = Branch::with(['user', 'company', 'products', 'services'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'branch' => $branch,
        ]);
    }

    /**
     * Update the specified branch in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        // Only admin or branch owner can update branch
        if (!Auth::user()->isAdmin() && Auth::id() !== $branch->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'string|max:255',
            'address' => 'string|max:255',
            'lat' => 'numeric',
            'lng' => 'numeric',
            'status' => 'nullable|in:active,inactive,pending',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'branch_image' => 'nullable|string',
            'use_company_image' => 'nullable|boolean',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'opening_hours' => 'nullable|json',
            'featured' => 'boolean',
        ]);

        // If company_id is provided, ensure it belongs to the authenticated user
        if ($request->has('company_id')) {
            $company = Company::findOrFail($request->company_id);
            if (!Auth::user()->isAdmin() && $company->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to assign this branch to this company',
                ], 403);
            }
        }

        $branch->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Branch updated successfully',
            'branch' => $branch,
        ]);
    }

    /**
     * Remove the specified branch from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        // Only admin or branch owner can delete branch
        if (!Auth::user()->isAdmin() && Auth::id() !== $branch->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully',
        ]);
    }

    /**
     * Get the branches of the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myBranches()
    {
        $branches = Branch::with('company')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Get featured branches for the homepage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 10);

        $branches = Branch::with(['company'])
            ->where('featured', true)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Update the featured status of a branch.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFeatured(Request $request, $id)
    {
        // Only admin can update featured status
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can update featured status.',
            ], 403);
        }

        $branch = Branch::findOrFail($id);

        $request->validate([
            'featured' => 'required|boolean',
        ]);

        $branch->featured = $request->featured;
        $branch->save();

        return response()->json([
            'success' => true,
            'message' => 'Branch featured status updated successfully',
            'branch' => $branch,
        ]);
    }
    /**
     * Get popular branches.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function popular(Request $request)
    {
        $limit = $request->input('limit', 10);

        $branches = Branch::with(['company'])
            ->where('status', 'active')
            ->orderBy('popularity_score', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Get all branches using the same data structure as popular branches.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function allBranches(Request $request)
    {
        $query = Branch::with(['company']);

        // Filter by status - only show active branches
        $query->where('status', 'active');

        // Search by name if provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Order by popularity score by default for better user experience
        $query->orderBy('popularity_score', 'desc')
              ->orderBy('created_at', 'desc');

        // Get all branches without pagination to match popular branches structure
        $branches = $query->get();

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Display a public listing of all branches (no auth required).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publicIndex(Request $request)
    {
        $query = Branch::with(['user', 'company']);

        // Filter by status - only show active branches for public access
        $query->where('status', 'active');

        // Filter by company_id if provided
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by featured if provided
        if ($request->has('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        // Search by name if provided
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Order by popularity score by default for better user experience
        $query->orderBy('popularity_score', 'desc')
              ->orderBy('created_at', 'desc');

        $branches = $query->paginate(10);

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Track a view for a branch.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackView($id, Request $request)
    {
        try {
            $branch = Branch::findOrFail($id);
            $userId = \Auth::id();

            // Log the current view count before any changes
            \Log::info("Branch {$id} view tracking - Initial view count: " .
                (is_null($branch->view_count) ? 'NULL' : $branch->view_count));

            // Initialize view_count if it's null or zero
            if ($branch->view_count === null || $branch->view_count === 0) {
                $branch->view_count = 1;
                $branch->save();
                \Log::info("Branch {$id} view tracking - Initialized view count to 1");
            }

            // Use the view tracking service with duplicate prevention
            $viewTrackingService = app(\App\Services\ViewTrackingService::class);
            $tracked = $viewTrackingService->trackView('branch', $id, $request);

            // Log whether the view was tracked
            \Log::info("Branch {$id} view tracking - View " . ($tracked ? "tracked" : "not tracked (duplicate)"));

            // Refresh the branch to get the updated view count
            $branch->refresh();

            // Log the view count after tracking
            \Log::info("Branch {$id} view tracking - View count after tracking: {$branch->view_count}");
            return response()->json([
                'success' => true,
                'message' => $tracked ? 'Branch view tracked successfully' : 'View already tracked recently',
                'tracked' => $tracked,
                'current_view_count' => $branch->view_count,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            \Log::error("Error tracking branch view: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Even if there's an error, try to return the current view count
            $currentCount = 1;
            try {
                $currentCount = Branch::find($id)->view_count ?? 1;
            } catch (\Exception $innerEx) {
                \Log::error("Error getting branch view count: " . $innerEx->getMessage());
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Error tracking branch view',
                'tracked' => false,
                'current_view_count' => $currentCount,
            ], 500);
        }
    }

    /**
     * Track an order for a branch.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trackOrder($id)
    {
        $branch = Branch::findOrFail($id);

        // Use the trending service to track the order
        app(\App\Services\TrendingService::class)->incrementBranchOrder($id);

        return response()->json([
            'success' => true,
            'message' => 'Branch order tracked successfully',
        ]);
    }

    /**
     * Get nearby branches based on user's location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getNearbyBranches(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0|max:100', // Optional radius in kilometers
            'limit' => 'nullable|integer|min:1|max:50', // Optional limit for results
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 50; // Default 50km radius
        $limit = $request->limit ?? 20; // Default 20 results

        try {
            // Use Haversine formula to calculate distances
            // Formula: distance = 6371 * acos(cos(radians(lat1)) * cos(radians(lat2)) * cos(radians(lng2) - radians(lng1)) + sin(radians(lat1)) * sin(radians(lat2)))
            $branches = Branch::selectRaw("
                    *,
                    (6371 * acos(
                        cos(radians(?)) * cos(radians(lat))
                        * cos(radians(lng) - radians(?))
                        + sin(radians(?)) * sin(radians(lat))
                    )) AS distance", [$latitude, $longitude, $latitude])
                ->with(['company']) // Include company data for image fallback
                ->where('status', 'active') // Only active branches
                ->having('distance', '<=', $radius) // Within specified radius
                ->orderBy('distance', 'asc') // Closest first
                ->take($limit)
                ->get();

            // Add some debug information
            $debugInfo = [
                'search_center' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
                'search_radius_km' => $radius,
                'total_found' => $branches->count(),
                'limit_applied' => $limit,
            ];

            return response()->json([
                'success' => true,
                'branches' => $branches,
                'debug_info' => $debugInfo,
                'message' => "Found {$branches->count()} branches within {$radius}km radius",
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching nearby branches: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch nearby branches',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function branchimageByid($id)
    {
       $branch = Branch::find($id);
$image = $branch ? $branch->branch_image : null;


        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'branch_image' => $image
        ]);
    }
}
