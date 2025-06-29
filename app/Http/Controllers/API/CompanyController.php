<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only admin can view all companies
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $companies = Company::with('user')->paginate(10);

        return response()->json([
            'success' => true,
            'companies' => $companies,
        ]);
    }

    /**
     * Store a newly created company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Only vendors can create companies
        if (!Auth::user()->isVendor()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        // Check if user already has a company
        $existingCompany = Company::where('user_id', Auth::id())->first();
        if ($existingCompany) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a company registered',
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'can_deliver' => 'nullable|boolean',
        ]);

        // Add user_id to request data
        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'active';

        $company = Company::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'company' => $company,
        ], 201);
    }

    /**
     * Display the specified company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Company::with(['user', 'branches'])->findOrFail($id);

        // Only admin or company owner can view company details
        if (!Auth::user()->isAdmin() && Auth::id() !== $company->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'company' => $company,
        ]);
    }

    /**
     * Update the specified company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);

        // Only admin or company owner can update company
        if (!Auth::user()->isAdmin() && Auth::id() !== $company->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:50',
            'status' => 'nullable|in:active,inactive,pending',
            'can_deliver' => 'nullable|boolean',
        ]);

        $company->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully',
            'company' => $company,
        ]);
    }

    /**
     * Remove the specified company from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        // Only admin or company owner can delete company
        if (!Auth::user()->isAdmin() && Auth::id() !== $company->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => 'Company deleted successfully',
        ]);
    }

    /**
     * Get the company of the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function myCompany()
    {
        $company = Company::with('branches')->where('user_id', Auth::id())->first();

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'No company found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'company' => $company,
        ]);
    }
    /**
     * Get top vendors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function topVendors(Request $request)
    {
        $limit = $request->input('limit', 10);

        $companies = Company::where('status', 'active')
            ->orderBy('vendor_score', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'vendors' => $companies,
        ]);
    }

    /**
     * Track a view for a company.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function trackView($id, Request $request)
    {
        $company = Company::findOrFail($id);

        // Use the new view tracking service with duplicate prevention
        $viewTrackingService = app(\App\Services\ViewTrackingService::class);
        $tracked = $viewTrackingService->trackView('vendor', $id, $request);

        return response()->json([
            'success' => true,
            'message' => $tracked ? 'Vendor view tracked successfully' : 'View already tracked recently',
            'tracked' => $tracked,
        ]);
    }

    /**
     * Track an order for a company.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function trackOrder($id)
    {
        $company = Company::findOrFail($id);

        // Use the trending service to track the order
        app(\App\Services\TrendingService::class)->incrementVendorOrder($id);

        return response()->json([
            'success' => true,
            'message' => 'Vendor order tracked successfully',
        ]);
    }

    /**
     * Add a rating for a company.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addRating(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $company = Company::findOrFail($id);

        // Use the trending service to add the rating
        app(\App\Services\TrendingService::class)->addVendorRating($id, $request->rating);

        return response()->json([
            'success' => true,
            'message' => 'Vendor rating added successfully',
        ]);
    }

    /**
     * Display the specified company for public access (no auth required).
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publicShow($id, Request $request)
    {
        $company = Company::with(['user', 'branches'])->findOrFail($id);

        // Track the view for analytics using the new service
        try {
            $viewTrackingService = app(\App\Services\ViewTrackingService::class);
            $viewTrackingService->trackView('vendor', $id, $request);
        } catch (\Exception $e) {
            // Silently fail if tracking fails
        }

        return response()->json([
            'success' => true,
            'company' => $company,
        ]);
    }

    /**
     * Get branches for a company for public access (no auth required).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function publicCompanyBranches($id)
    {
        $company = Company::findOrFail($id);
        $branches = $company->branches;

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }
}