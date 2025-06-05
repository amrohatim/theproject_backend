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
        $branch = Branch::findOrFail($id);

        // Use the new view tracking service with duplicate prevention
        $viewTrackingService = app(\App\Services\ViewTrackingService::class);
        $tracked = $viewTrackingService->trackView('branch', $id, $request);

        return response()->json([
            'success' => true,
            'message' => $tracked ? 'Branch view tracked successfully' : 'View already tracked recently',
            'tracked' => $tracked,
        ]);
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
}