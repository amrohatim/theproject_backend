<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Services\ServiceDealService;
use App\Services\TrendingService;
use App\Services\ViewTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class ServiceController extends Controller
{
    protected $serviceDealService;
    protected $trendingService;
    protected $viewTrackingService;

    public function __construct(
        ServiceDealService $serviceDealService,
        TrendingService $trendingService,
        ViewTrackingService $viewTrackingService
    ) {
        $this->serviceDealService = $serviceDealService;
        $this->trendingService = $trendingService;
        $this->viewTrackingService = $viewTrackingService;
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
        $services->getCollection()->transform(fn ($service) => $this->transformService($service));

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    /**
     * Get branch services grouped by category with optional category filtering.
     */
    public function branchServicesByCategory(Request $request, $branchId)
    {
        $perPage = (int) $request->input('per_page', 10);
        $categoryId = $request->input('category_id');
        $includeServices = $request->boolean('include_services', true);

        $categoryIds = Service::query()
            ->where('branch_id', $branchId)
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id');

        $categoryRows = Category::query()
            ->select(['id', 'parent_id'])
            ->whereIn('id', $categoryIds)
            ->get();

        $categoryMap = $categoryRows->pluck('parent_id', 'id')->toArray();
        $pendingParentIds = array_values(array_filter($categoryMap));

        while (!empty($pendingParentIds)) {
            $pendingParentIds = array_values(array_diff(
                $pendingParentIds,
                array_keys($categoryMap)
            ));

            if (empty($pendingParentIds)) {
                break;
            }

            $parentRows = Category::query()
                ->select(['id', 'parent_id'])
                ->whereIn('id', $pendingParentIds)
                ->get();

            foreach ($parentRows as $row) {
                $categoryMap[$row->id] = $row->parent_id;
            }

            $pendingParentIds = $parentRows->pluck('parent_id')->filter()->values()->toArray();
        }

        $parentIds = collect($categoryIds)->map(function ($categoryId) use ($categoryMap) {
            $current = $categoryId;
            while (isset($categoryMap[$current]) && $categoryMap[$current]) {
                $current = $categoryMap[$current];
            }
            return $current;
        })->unique()->values();

        $categories = Category::query()
            ->whereIn('id', $parentIds)
            ->where('type', 'service')
            ->orderBy('name')
            ->get();

        $selectedCategoryId = $categoryId ?: ($categories->first()->id ?? null);

        if (!$includeServices || $selectedCategoryId === null) {
            return response()->json([
                'success' => true,
                'categories' => $categories,
                'selected_category_id' => $selectedCategoryId,
                'services' => [
                    'data' => [],
                ],
            ]);
        }

        $query = Service::with(['branch', 'category'])
            ->where('branch_id', $branchId);

        $includeSubcategories = $request->boolean('include_subcategories', true);
        $query->filterByCategory($selectedCategoryId, $includeSubcategories);

        if ($request->has('only_available')) {
            $query->filterByAvailability($request->boolean('only_available'));
        }

        $services = $query->paginate($perPage);

        $services->getCollection()->transform(fn ($service) => $this->transformService($service));

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'selected_category_id' => $selectedCategoryId,
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

        $service = $this->transformService($service);

        // Track view with duplicate prevention
        try {
            $this->viewTrackingService->trackView('service', $service->id, request());
        } catch (\Exception $e) {
            // Silent log to avoid user-facing errors
            \Log::warning('Failed to track service view', [
                'service_id' => $service->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'service' => $service,
        ]);
    }

    /**
     * Get trending services ordered by trending_score.
     */
    public function trendingServices(Request $request)
    {
        $limit = (int) $request->input('limit', 10);

        $services = Service::with(['branch', 'category'])
            ->where('is_available', true)
            ->where('trending_score', '>', 0)
            ->orderByDesc('trending_score')
            ->take($limit)
            ->get();

        if ($services->isEmpty()) {
            $services = Service::with(['branch', 'category'])
                ->where('is_available', true)
                ->orderByDesc('order_count')
                ->orderByDesc('view_count')
                ->orderByDesc('rating')
                ->take($limit)
                ->get();
        }

        $services->transform(fn ($service) => $this->transformService($service));

        return response()->json([
            'success' => true,
            'services' => $services,
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

        // Add branch_name, deal, and availability information to each service
        $services->transform(fn ($service) => $this->transformService($service));

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

        // Add branch_name, deal, and availability information to each service
        $services->transform(fn ($service) => $this->transformService($service));

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }

    /**
     * Normalize the service payload that is returned to API consumers.
     *
     * @param  \App\Models\Service  $service
     * @return \App\Models\Service
     */
    protected function transformService(Service $service): Service
    {
        $service->loadMissing(['branch', 'category']);

        $service->branch_name = $service->branch ? $service->branch->name : ($service->merchant_name ?? 'Unknown Branch');

        $dealInfo = $this->serviceDealService->calculateDiscountedPrice($service);
        $service->has_discount = $dealInfo['has_discount'];
        $service->discounted_price = $dealInfo['discounted_price'];
        $service->discount_percentage = $dealInfo['discount_percentage'];
        $service->deal = $dealInfo['deal'];

        $service->available_days = $this->normalizeAvailableDays($service->available_days);
        $service->start_time = $this->formatTimeField($service->start_time);
        $service->end_time = $this->formatTimeField($service->end_time);

        return $service;
    }

    /**
     * Ensure available days are always returned as an ordered array of integers.
     *
     * @param  mixed  $availableDays
     * @return array<int, int>
     */
    protected function normalizeAvailableDays($availableDays): array
    {
        if (is_null($availableDays)) {
            return [];
        }

        if (is_string($availableDays)) {
            $decoded = json_decode($availableDays, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $availableDays = $decoded;
            } else {
                return [];
            }
        }

        if ($availableDays instanceof \Illuminate\Support\Collection) {
            $availableDays = $availableDays->all();
        }

        if (!is_array($availableDays)) {
            return [];
        }

        return collect($availableDays)
            ->filter(fn ($day) => $day !== null && $day !== '')
            ->map(fn ($day) => (int) $day)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Format the start/end time values for consistent API responses.
     *
     * @param  mixed  $time
     * @return string|null
     */
    protected function formatTimeField($time): ?string
    {
        if (empty($time)) {
            return null;
        }

        if ($time instanceof Carbon) {
            return $time->format('H:i');
        }

        try {
            return Carbon::parse((string) $time)->format('H:i');
        } catch (\Exception $e) {
            // If parsing fails, return the original value to avoid hiding data.
            return is_string($time) ? $time : null;
        }
    }
}
