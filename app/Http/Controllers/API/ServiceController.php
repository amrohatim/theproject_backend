<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\ServiceDealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    protected $serviceDealService;

    public function __construct(ServiceDealService $serviceDealService)
    {
        $this->serviceDealService = $serviceDealService;
    }
    /**
     * Display a listing of the services with advanced filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Service::with(['branch', 'category']);

        // Apply filters
        if ($request->has('branch_id')) {
            $query->filterByBranch($request->branch_id);
        }

        if ($request->has('category_id')) {
            $includeSubcategories = $request->boolean('include_subcategories', false);
            $query->filterByCategory($request->category_id, $includeSubcategories);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->filterByPrice($request->min_price, $request->max_price);
        }

        // Support both old (min_duration/max_duration) and new (min_minutes/max_minutes) parameter names
        if ($request->has('min_minutes') && $request->has('max_minutes')) {
            $query->filterByDuration($request->min_minutes, $request->max_minutes);
        } elseif ($request->has('min_duration') && $request->has('max_duration')) {
            $query->filterByDuration($request->min_duration, $request->max_duration);
        }

        if ($request->has('min_rating')) {
            $query->filterByRating($request->min_rating);
        }

        if ($request->has('only_available')) {
            $query->filterByAvailability($request->boolean('only_available'));
        }

        if ($request->has('featured')) {
            $query->filterByFeatured($request->boolean('featured'));
        }

        if ($request->has('home_service')) {
            $query->filterByHomeService($request->boolean('home_service'));
        }

        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Apply emirate filter - only if provided and not empty
        if ($request->filled('emirate')) {
            $query->filterByEmirate($request->emirate);
        }

        // Apply sorting
        $sortBy = $request->input('sort_by', 'popularity');
        $query->sortBy($sortBy);

        // Paginate results
        $perPage = $request->input('per_page', 10);
        $services = $query->paginate($perPage);

        // Add branch_name and deal information to each service
        $services->getCollection()->transform(function ($service) {
            $service->branch_name = $service->branch->name;

            // Calculate deal information for this service
            $dealInfo = $this->serviceDealService->calculateDiscountedPrice($service);

            // Apply deal information to the service
            $service->has_discount = $dealInfo['has_discount'];
            $service->discounted_price = $dealInfo['discounted_price'];
            $service->discount_percentage = $dealInfo['discount_percentage'];
            $service->deal = $dealInfo['deal'];

            return $service;
        });

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    /**
     * Display the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::with(['branch', 'category', 'reviews.user'])->findOrFail($id);

        // Add branch_name to the service
        $service->branch_name = $service->branch->name;

        // Calculate deal information for this service
        $dealInfo = $this->serviceDealService->calculateDiscountedPrice($service);

        // Apply deal information to the service
        $service->has_discount = $dealInfo['has_discount'];
        $service->discounted_price = $dealInfo['discounted_price'];
        $service->discount_percentage = $dealInfo['discount_percentage'];
        $service->deal = $dealInfo['deal'];

        return response()->json([
            'success' => true,
            'service' => $service,
        ]);
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'featured' => 'boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
            'home_service' => 'boolean',
        ]);

        $service = Service::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'service' => $service,
        ], 201);
    }

    /**
     * Update the specified service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'branch_id' => 'exists:branches,id',
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'price' => 'numeric|min:0',
            'duration' => 'integer|min:1',
            'featured' => 'boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
            'home_service' => 'boolean',
        ]);

        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    /**
     * Remove the specified service from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }

    /**
     * Get featured services for the homepage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 10);

        $services = Service::with(['branch', 'category'])
            ->where('featured', true)
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        // Add branch_name and deal information to each service
        $services->transform(function ($service) {
            $service->branch_name = $service->branch->name;

            // Calculate deal information for this service
            $dealInfo = $this->serviceDealService->calculateDiscountedPrice($service);

            // Apply deal information to the service
            $service->has_discount = $dealInfo['has_discount'];
            $service->discounted_price = $dealInfo['discounted_price'];
            $service->discount_percentage = $dealInfo['discount_percentage'];
            $service->deal = $dealInfo['deal'];

            return $service;
        });

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    /**
     * Update the featured status of a service.
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

        $service = Service::findOrFail($id);

        $request->validate([
            'featured' => 'required|boolean',
        ]);

        $service->featured = $request->featured;
        $service->save();

        return response()->json([
            'success' => true,
            'message' => 'Service featured status updated successfully',
            'service' => $service,
        ]);
    }

    /**
     * Get services with active deals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function servicesWithDeals(Request $request)
    {
        $limit = $request->input('limit', 10);

        // Get services with active deals using the ServiceDealService
        $services = $this->serviceDealService->getServicesWithActiveDeals($limit);

        // Add branch_name to each service
        $services->transform(function ($service) {
            if ($service->branch) {
                $service->branch_name = $service->branch->name;
            }
            return $service;
        });

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }
}
