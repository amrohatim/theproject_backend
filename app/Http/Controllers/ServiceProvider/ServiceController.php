<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Branch;
use App\Models\BusinessType;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Build a map of business type name => allowed service category IDs.
     */
    private function getBusinessTypeServiceCategoryMap(): array
    {
        return BusinessType::query()
            ->pluck('service_categories', 'business_name')
            ->mapWithKeys(function ($categories, $businessName) {
                if (is_string($categories)) {
                    $decoded = json_decode($categories, true);
                    $ids = is_array($decoded) ? $decoded : [];
                } else {
                    $ids = is_array($categories) ? $categories : [];
                }
                $cleanIds = array_values(array_filter($ids, static fn ($id) => is_numeric($id)));
                $cleanIds = array_values(array_map('intval', $cleanIds));
                $normalizedName = strtolower(trim((string) $businessName));
                return [$normalizedName => $cleanIds];
            })
            ->toArray();
    }
    /**
     * Display a listing of services that the service provider can manage.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Start with services that this service provider can manage
        $query = Service::whereIn('id', $serviceProvider->service_ids ?? [])
            ->with(['branch', 'category']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('service_name_arabic', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('service_description_arabic', 'like', "%{$search}%");
            });
        }

        // Apply branch filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->get('branch_id'));
        }

        // Apply category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'available') {
                $query->where('is_available', true);
            } elseif ($status === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // Apply price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'duration_low':
                $query->orderBy('duration', 'asc');
                break;
            case 'duration_high':
                $query->orderBy('duration', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'name':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $services = $query->paginate(10)->appends($request->query());

        // Get branches this service provider can access
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])->get();

        // Get categories for services this provider manages
        $categories = \App\Models\Category::whereIn('id',
            Service::whereIn('id', $serviceProvider->service_ids ?? [])
                ->pluck('category_id')
                ->unique()
        )->orderBy('name')->get();

        // Count active filters
        $activeFilters = collect([
            'search' => $request->get('search'),
            'branch_id' => $request->get('branch_id'),
            'category_id' => $request->get('category_id'),
            'status' => $request->get('status'),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
        ])->filter()->count();

        return view('service-provider.services.index', compact(
            'services',
            'branches',
            'categories',
            'serviceProvider',
            'activeFilters'
        ));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        $businessTypeCategoryMap = $this->getBusinessTypeServiceCategoryMap();

        // Get service categories with their children - force a fresh query to get the latest data
        $parentCategories = \App\Models\Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        if ($parentCategories->isEmpty()) {
            $allCategoryIds = collect($businessTypeCategoryMap)
                ->flatten()
                ->filter()
                ->unique()
                ->values();

            if ($allCategoryIds->isNotEmpty()) {
                $parentIds = \App\Models\Category::whereIn('id', $allCategoryIds)
                    ->pluck('parent_id')
                    ->filter()
                    ->unique()
                    ->values();

                $parentCategories = \App\Models\Category::whereIn('id', $parentIds)
                    ->with(['children' => function($query) {
                        $query->orderBy('name');
                    }])
                    ->orderBy('name')
                    ->get();

                $parentCategories->each(function ($parent) use ($allCategoryIds) {
                    $parent->setRelation(
                        'children',
                        $parent->children->whereIn('id', $allCategoryIds)->values()
                    );
                });
                $parentCategories = $parentCategories->filter(function ($parent) {
                    return $parent->children->isNotEmpty();
                })->values();
            }
        }

        // Get branches this service provider can access with active licenses only
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])
            ->withActiveLicense()
            ->get();

        // Get all branches for license status information (for frontend display)
        $allBranches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])
            ->with('latestLicense')
            ->get();

        // Check if the service provider has any branches
        if ($branches->isEmpty()) {
            return redirect()->route('service-provider.dashboard')
                ->with('warning', 'You need access to branches before adding services. Please contact your vendor.');
        }

        return view('service-provider.services.create', compact('parentCategories', 'branches', 'allBranches', 'serviceProvider', 'businessTypeCategoryMap'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Custom validation for bilingual fields
        $request->validate([
            'name' => 'required|string|max:255',
            'service_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => ['required', 'exists:branches,id', new \App\Rules\ActiveBranchLicense()],
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'service_description_arabic' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'additional_images' => 'nullable|array|max:8',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'available_days' => 'required|array|min:1',
            'available_days.*' => 'integer|between:0,6',
        ]);

          // Only validate time fields if they are provided or if the service doesn't have existing times
        if ($request->filled('start_time') || $request->filled('end_time') || !$request->has('start_time') || !$request->has('end_time')) {
            $validationRules['start_time'] = 'required|date_format:H:i';
            $validationRules['end_time'] = 'required|date_format:H:i|after:start_time';
        } else {
            // If time fields are provided but empty, validate format
            if ($request->has('start_time') && $request->start_time !== null) {
                $validationRules['start_time'] = 'nullable|date_format:H:i';
            }
            if ($request->has('end_time') && $request->end_time !== null) {
                $validationRules['end_time'] = 'nullable|date_format:H:i|after:start_time';
            }
        }

        $request->validate($validationRules);

        // Verify the branch belongs to the service provider's accessible branches
        if (!in_array($request->branch_id, $serviceProvider->branch_ids ?? [])) {
            return redirect()->back()
                ->withErrors(['branch_id' => 'You do not have access to this branch.'])
                ->withInput();
        }

        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['home_service'] = $request->has('home_service') ? true : false;

          // Only update time fields if they are provided in the request
        if (!$request->filled('start_time')) {
            unset($data['start_time']);
        }
        if (!$request->filled('end_time')) {
            unset($data['end_time']);
        }

        // Handle image upload with WebP conversion
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'services');

                if ($imagePath) {
                    $data['image'] = $imagePath;
                    Log::info('ServiceProvider: WebP conversion successful for service image', [
                        'original_name' => $file->getClientOriginalName(),
                        'converted_path' => $imagePath
                    ]);
                } else {
                    // Fallback to original upload method if WebP conversion fails
                    $imagePath = $request->file('image')->store('services', 'public');
                    $data['image'] = \Illuminate\Support\Facades\Storage::url($imagePath);

                    Log::warning('ServiceProvider: WebP conversion failed, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $data['image']
                    ]);
                }
            } catch (\Exception $e) {
                // Fallback to original upload method on exception
                $imagePath = $request->file('image')->store('services', 'public');
                $data['image'] = \Illuminate\Support\Facades\Storage::url($imagePath);

                Log::error('ServiceProvider: WebP conversion exception, using fallback method', [
                    'error' => $e->getMessage(),
                    'original_name' => $file->getClientOriginalName(),
                    'fallback_path' => $data['image']
                ]);
            }
        }

        $service = Service::create($data);
        $this->storeAdditionalImages($request, $service);

        // Add this service to the service provider's service list
        $serviceIds = $serviceProvider->service_ids ?? [];
        if (!in_array($service->id, $serviceIds)) {
            $serviceIds[] = $service->id;
            $serviceProvider->update([
                'service_ids' => $serviceIds,
                'number_of_services' => count($serviceIds),
            ]);
        }

        return redirect()->route('service-provider.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        $service->load(['branch', 'bookings' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('service-provider.services.show', compact('service', 'serviceProvider'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        $businessTypeCategoryMap = $this->getBusinessTypeServiceCategoryMap();

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        // Get service categories with their children (same structure used on create view)
        $parentCategories = \App\Models\Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $branchBusinessType = strtolower(trim((string) ($service->branch?->business_type)));
        if ($branchBusinessType && isset($businessTypeCategoryMap[$branchBusinessType])) {
            $allowedCategoryIds = $businessTypeCategoryMap[$branchBusinessType];
            $parentCategories->each(function ($parent) use ($allowedCategoryIds) {
                $parent->setRelation(
                    'children',
                    $parent->children->whereIn('id', $allowedCategoryIds)->values()
                );
            });
            $parentCategories = $parentCategories->filter(function ($parent) {
                return $parent->children->isNotEmpty();
            })->values();
        }

        if ($parentCategories->isEmpty() && $branchBusinessType && isset($businessTypeCategoryMap[$branchBusinessType])) {
            $allowedCategoryIds = $businessTypeCategoryMap[$branchBusinessType];
            if (!empty($allowedCategoryIds)) {
                $parentIds = \App\Models\Category::whereIn('id', $allowedCategoryIds)
                    ->pluck('parent_id')
                    ->filter()
                    ->unique()
                    ->values();

                $parentCategories = \App\Models\Category::whereIn('id', $parentIds)
                    ->with(['children' => function($query) {
                        $query->orderBy('name');
                    }])
                    ->orderBy('name')
                    ->get();

                $parentCategories->each(function ($parent) use ($allowedCategoryIds) {
                    $parent->setRelation(
                        'children',
                        $parent->children->whereIn('id', $allowedCategoryIds)->values()
                    );
                });
                $parentCategories = $parentCategories->filter(function ($parent) {
                    return $parent->children->isNotEmpty();
                })->values();
            }
        }

        // Get branches this service provider can access with active licenses only
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])
            ->withActiveLicense()
            ->get();

        // Get all branches for license status information (for frontend display)
        $allBranches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])
            ->with('latestLicense')
            ->get();

        return view('service-provider.services.edit', compact('service', 'parentCategories', 'branches', 'allBranches', 'serviceProvider', 'businessTypeCategoryMap'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        // Custom validation for bilingual fields
        $request->validate([
            'name' => 'required|string|max:255',
            'service_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => ['required', 'exists:branches,id', new \App\Rules\ActiveBranchLicense()],
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'service_description_arabic' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'additional_images' => 'nullable|array|max:8',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'remove_additional_images' => 'nullable|array',
            'remove_additional_images.*' => 'integer|exists:service_images,id',
            'available_days' => 'required|array|min:1',
            'available_days.*' => 'integer|between:0,6',
        ]);


           // Handle time validation more carefully
        $startTimeProvided = $request->filled('start_time');
        $endTimeProvided = $request->filled('end_time');
        $serviceHasExistingTimes = $service->start_time && $service->end_time;
        
        // If both times are provided, validate them
        if ($startTimeProvided && $endTimeProvided) {
            $validationRules['start_time'] = 'required|date_format:H:i';
            $validationRules['end_time'] = 'required|date_format:H:i|after:start_time';
        }
        // If only one time is provided, require both
        elseif ($startTimeProvided || $endTimeProvided) {
            $validationRules['start_time'] = 'required|date_format:H:i';
            $validationRules['end_time'] = 'required|date_format:H:i|after:start_time';
        }
        // If no times are provided but service doesn't have existing times, require them
        elseif (!$serviceHasExistingTimes) {
            $validationRules['start_time'] = 'required|date_format:H:i';
            $validationRules['end_time'] = 'required|date_format:H:i|after:start_time';
        }
        // If no times provided and service has existing times, don't validate time fields

        $request->validate($validationRules);

        $additionalImages = $request->file('additional_images', []);
        $newAdditionalCount = 0;
        foreach ($additionalImages as $file) {
            if ($file) {
                $newAdditionalCount++;
            }
        }
        $existingAdditionalCount = $service->serviceImages()->count();
        $removeIds = $request->input('remove_additional_images', []);
        $removeCount = is_array($removeIds) ? count($removeIds) : 0;
        if (($existingAdditionalCount - $removeCount + $newAdditionalCount) > 8) {
            return redirect()->back()
                ->withErrors(['additional_images' => 'You can upload up to 8 additional images in total.'])
                ->withInput();
        }

        // Verify the branch belongs to the service provider's accessible branches
        if (!in_array($request->branch_id, $serviceProvider->branch_ids ?? [])) {
            return redirect()->back()
                ->withErrors(['branch_id' => 'You do not have access to this branch.'])
                ->withInput();
        }

        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['home_service'] = $request->has('home_service') ? true : false;

        // Handle image upload with WebP conversion
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'services');

                if ($imagePath) {
                    // Delete old image if exists and new conversion is successful
                    if ($service->image) {
                        $webpService->deleteImage($service->image);
                    }

                    $data['image'] = $imagePath;
                    Log::info('ServiceProvider: WebP conversion successful for service image update', [
                        'service_id' => $service->id,
                        'original_name' => $file->getClientOriginalName(),
                        'converted_path' => $imagePath
                    ]);
                } else {
                    // Fallback to original upload method if WebP conversion fails
                    // Delete old image if exists
                    if ($service->image && \Illuminate\Support\Facades\Storage::exists('public/' . str_replace('/storage/', '', $service->image))) {
                        \Illuminate\Support\Facades\Storage::delete('public/' . str_replace('/storage/', '', $service->image));
                    }

                    $imagePath = $request->file('image')->store('services', 'public');
                    $data['image'] = \Illuminate\Support\Facades\Storage::url($imagePath);

                    Log::warning('ServiceProvider: WebP conversion failed for update, using fallback method', [
                        'service_id' => $service->id,
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $data['image']
                    ]);
                }
            } catch (\Exception $e) {
                // Fallback to original upload method on exception
                // Delete old image if exists
                if ($service->image && \Illuminate\Support\Facades\Storage::exists('public/' . str_replace('/storage/', '', $service->image))) {
                    \Illuminate\Support\Facades\Storage::delete('public/' . str_replace('/storage/', '', $service->image));
                }

                $imagePath = $request->file('image')->store('services', 'public');
                $data['image'] = \Illuminate\Support\Facades\Storage::url($imagePath);

                Log::error('ServiceProvider: WebP conversion exception for update, using fallback method', [
                    'service_id' => $service->id,
                    'error' => $e->getMessage(),
                    'original_name' => $file->getClientOriginalName(),
                    'fallback_path' => $data['image']
                ]);
            }
        }

        $service->update($data);
        $this->deleteAdditionalImages($request, $service);
        $this->storeAdditionalImages($request, $service);

        return redirect()->route('service-provider.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        // Remove image if exists using WebP service for proper cleanup
        if ($service->image) {
            $webpService = new WebPImageService();
            $webpService->deleteImage($service->image);
        }

        // Remove service from service provider's list
        $serviceIds = $serviceProvider->service_ids ?? [];
        $serviceIds = array_filter($serviceIds, function ($id) use ($service) {
            return $id != $service->id;
        });

        $serviceProvider->update([
            'service_ids' => array_values($serviceIds),
            'number_of_services' => count($serviceIds),
        ]);

        $service->delete();

        return redirect()->route('service-provider.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Delete selected additional images from storage and database.
     */
    private function deleteAdditionalImages(Request $request, Service $service): void
    {
        $removeIds = $request->input('remove_additional_images', []);
        if (empty($removeIds) || !is_array($removeIds)) {
            return;
        }

        $images = $service->serviceImages()->whereIn('id', $removeIds)->get();
        if ($images->isEmpty()) {
            return;
        }

        $webpService = new WebPImageService();
        foreach ($images as $image) {
            if ($image->image_path) {
                try {
                    $webpService->deleteImage($image->image_path);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete service additional image', [
                        'service_id' => $service->id,
                        'image_id' => $image->id,
                        'image_path' => $image->image_path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $image->delete();
        }
    }

    /**
     * Store additional service images using WebPImageService.
     */
    private function storeAdditionalImages(Request $request, Service $service): void
    {
        $additionalImages = $request->file('additional_images', []);
        if (empty($additionalImages)) {
            return;
        }

        $webpService = new WebPImageService();
        foreach ($additionalImages as $file) {
            if (!$file) {
                continue;
            }

            $imagePath = $webpService->convertAndStoreWithUrl($file, 'services');
            if (!$imagePath) {
                $storagePath = $file->store('services', 'public');
                $imagePath = Storage::url($storagePath);
            }

            ServiceImage::create([
                'service_id' => $service->id,
                'image_path' => $imagePath,
            ]);
        }
    }

    /**
     * AJAX endpoint for real-time filtering and searching.
     */
    public function filter(Request $request)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider profile not found.'], 403);
        }

        // Start with services that this service provider can manage
        $query = Service::whereIn('id', $serviceProvider->service_ids ?? [])
            ->with(['branch', 'category']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('service_name_arabic', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('service_description_arabic', 'like', "%{$search}%");
            });
        }

        // Apply branch filter
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->get('branch_id'));
        }

        // Apply category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        // Apply status filter
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'available') {
                $query->where('is_available', true);
            } elseif ($status === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // Apply price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'duration_low':
                $query->orderBy('duration', 'asc');
                break;
            case 'duration_high':
                $query->orderBy('duration', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'name':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $services = $query->paginate(10)->appends($request->query());

        // Return JSON response with the filtered services
        return response()->json([
            'success' => true,
            'html' => view('service-provider.services.partials.services-table', compact('services'))->render(),
            'pagination' => $services->links()->render(),
            'count' => $services->total(),
        ]);
    }
}
