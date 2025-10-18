<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;
use App\Services\WebPImageService;

class ServiceController extends Controller
{
    /**
     * Display a listing of the merchant's services.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get services owned directly by this merchant
        $query = Service::where('merchant_id', $user->id)->with('category');

        // Apply search if provided
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Apply filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_available', true);
            } elseif ($status === 'inactive') {
                $query->where('is_available', false);
            }
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        if ($request->filled('service_type')) {
            $serviceType = $request->get('service_type');
            if ($serviceType === 'home_service') {
                $query->where('home_service', true);
            } elseif ($serviceType === 'in_store') {
                $query->where('home_service', false);
            }
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->get('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->get('price_max'));
        }

        if ($request->filled('duration_min')) {
            $query->where('duration', '>=', $request->get('duration_min'));
        }

        if ($request->filled('duration_max')) {
            $query->where('duration', '<=', $request->get('duration_max'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSortFields = ['created_at', 'updated_at', 'name', 'price', 'duration'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $services = $query->paginate(15)->appends($request->query());

        // Get categories for filter dropdown
        $categories = \App\Models\Category::where('type', 'service')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // If this is an AJAX request, return JSON response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('merchant.services.partials.services-table', compact('services'))->render(),
                'pagination' => view('merchant.services.partials.pagination', compact('services'))->render(),
                'total' => $services->total(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
            ]);
        }

        return view('merchant.services.index', compact('services', 'categories'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        // Get service categories with their children
        $parentCategories = Category::where('type', 'service')
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
            
        return view('merchant.services.create', compact('parentCategories'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'service_name_arabic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_description_arabic' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'duration' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
            'home_service' => 'boolean',
            'available_days' => 'required|array|min:1',
            'available_days.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: JPEG, PNG, JPG, GIF, SVG.',
            'image.max' => 'The image size must not exceed 20MB.',
            'name.required' => 'Service name in English is required.',
            'service_name_arabic.required' => 'Service name in Arabic is required.',
            'price.required' => 'Service price is required.',
            'price.numeric' => 'Service price must be a valid number.',
            'price.min' => 'Service price must be greater than or equal to 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'duration.min' => 'Duration must be at least 1 minute.',
            'available_days.required' => __('merchant.select_at_least_one_day'),
            'available_days.min' => __('merchant.select_at_least_one_day'),
            'end_time.after' => __('merchant.end_time_after_start_time'),
        ]);

        // Custom validation: if description is provided in one language, it must be provided in both
        if ($request->filled('description') || $request->filled('service_description_arabic')) {
            $request->validate([
                'description' => 'required|string',
                'service_description_arabic' => 'required|string',
            ], [
                'description.required' => 'If you provide a description in Arabic, you must also provide it in English.',
                'service_description_arabic.required' => 'If you provide a description in English, you must also provide it in Arabic.',
            ]);
        }

        $user = Auth::user();

        $data = $request->except(['branch_id']);
        // Set merchant_id for direct merchant ownership
        $data['merchant_id'] = $user->id;

        // Set merchant_name from the merchant's business_name
        $merchantProfile = $user->merchant;
        if ($merchantProfile && $merchantProfile->business_name) {
            $data['merchant_name'] = $merchantProfile->business_name;
        }

        $data['is_available'] = $request->boolean('is_available');
        $data['home_service'] = $request->boolean('home_service');
        $data['available_days'] = collect($request->input('available_days', []))
            ->map(fn($day) => (int) $day)
            ->values()
            ->all();
        $data['start_time'] = $request->input('start_time');
        $data['end_time'] = $request->input('end_time');

        // Handle image upload with enhanced validation and error handling
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');

                // Additional server-side validation
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The uploaded image file is corrupted or invalid.']);
                }

                // Validate file size (additional check)
                if ($image->getSize() > 20480 * 1024) { // 20MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 20MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF, SVG.']);
                }

                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($image, 'services');

                if (!$imagePath) {
                    // Fallback to original upload method if WebP conversion fails
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $fallbackPath = $image->storeAs('services', $imageName, 'public');
                    $imagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                    \Log::warning('WebP conversion failed for merchant service, using fallback method', [
                        'original_name' => $image->getClientOriginalName(),
                        'fallback_path' => $imagePath
                    ]);
                }

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

                \Log::info('Merchant service image uploaded successfully', [
                    'image_path' => $imagePath,
                    'original_name' => $image->getClientOriginalName()
                ]);

            } catch (\Exception $e) {
                \Log::error('Service image upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        try {
            Service::create($data);
            return redirect()->route('merchant.services.index')
                ->with('success', 'Service created successfully.');
        } catch (\Exception $e) {
            \Log::error('Service creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create service. Please try again.']);
        }
    }

    /**
     * Display the specified service.
     */
    public function show($id)
    {
        $user = Auth::user();

        $service = Service::where('merchant_id', $user->id)
            ->with('category')
            ->findOrFail($id);

        return view('merchant.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit($id)
    {
        $user = Auth::user();

        $service = Service::where('merchant_id', $user->id)->findOrFail($id);
        
        // Get service categories with their children
        $parentCategories = Category::where('type', 'service')
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('merchant.services.edit', compact('service', 'parentCategories'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $service = Service::where('merchant_id', $user->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'service_name_arabic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_description_arabic' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'duration' => 'nullable|integer|min:1',
            'duration_unit' => 'nullable|in:minutes,hours,days',
            'status' => 'in:active,inactive',
            'is_available' => 'boolean',
            'home_service' => 'boolean',
            'available_days' => 'required|array|min:1',
            'available_days.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ], [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: JPEG, PNG, JPG, GIF, SVG.',
            'image.max' => 'The image size must not exceed 20MB.',
            'name.required' => 'Service name in English is required.',
            'service_name_arabic.required' => 'Service name in Arabic is required.',
            'price.required' => 'Service price is required.',
            'price.numeric' => 'Service price must be a valid number.',
            'price.min' => 'Service price must be greater than or equal to 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'duration.min' => 'Duration must be at least 1 minute.',
            'available_days.required' => __('merchant.select_at_least_one_day'),
            'available_days.min' => __('merchant.select_at_least_one_day'),
            'end_time.after' => __('merchant.end_time_after_start_time'),
        ]);

        // Custom validation: if description is provided in one language, it must be provided in both
        if ($request->filled('description') || $request->filled('service_description_arabic')) {
            $request->validate([
                'description' => 'required|string',
                'service_description_arabic' => 'required|string',
            ], [
                'description.required' => 'If you provide a description in Arabic, you must also provide it in English.',
                'service_description_arabic.required' => 'If you provide a description in English, you must also provide it in Arabic.',
            ]);
        }

        $data = $request->except(['branch_id']);

        // Handle checkbox fields properly - checkboxes don't send values when unchecked
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['home_service'] = $request->has('home_service') ? true : false;
        $data['available_days'] = collect($request->input('available_days', []))
            ->map(fn($day) => (int) $day)
            ->values()
            ->all();
        $data['start_time'] = $request->input('start_time');
        $data['end_time'] = $request->input('end_time');

        // Handle image upload with enhanced validation and error handling
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');

                // Additional server-side validation
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The uploaded image file is corrupted or invalid.']);
                }

                // Validate file size (additional check)
                if ($image->getSize() > 20480 * 1024) { // 20MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 20MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF, SVG.']);
                }

                // Store old image path for cleanup
                $oldImagePath = $service->getRawImagePath();

                // Delete old image if exists and new conversion is successful
                if ($oldImagePath) {
                    $webpService = new WebPImageService();
                    $webpService->deleteImage($oldImagePath);
                }

                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($image, 'services');

                if (!$imagePath) {
                    // Fallback to original upload method if WebP conversion fails
                    $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                    $fallbackPath = $image->storeAs('services', $imageName, 'public');
                    $imagePath = $fallbackPath ? '/storage/' . $fallbackPath : null;

                    \Log::warning('WebP conversion failed for merchant service update, using fallback method', [
                        'original_name' => $image->getClientOriginalName(),
                        'fallback_path' => $imagePath
                    ]);
                }

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

                \Log::info('Merchant service image updated successfully', [
                    'image_path' => $imagePath,
                    'original_name' => $image->getClientOriginalName()
                ]);

            } catch (\Exception $e) {
                \Log::error('Service image upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        try {
            $service->update($data);
            return redirect()->route('merchant.services.index')
                ->with('success', 'Service updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Service update failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Failed to update service. Please try again.']);
        }
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $service = Service::where('merchant_id', $user->id)->findOrFail($id);

        // Image cleanup is handled automatically by the Service model's booted method
        $service->delete();

        return redirect()->route('merchant.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    /**
     * Get search suggestions for services.
     */
    public function searchSuggestions(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'suggestions' => []
            ]);
        }

        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');
        $services = Service::whereIn('branch_id', $userBranches)
            ->where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'image', 'price', 'duration')
            ->limit(10)
            ->get();

        $categories = \App\Models\Category::where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->select('id', 'name')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'suggestions' => [
                'services' => $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->price,
                        'duration' => $service->duration,
                        'image' => $service->image,
                        'type' => 'service'
                    ];
                }),
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'type' => 'category'
                    ];
                })
            ]
        ]);
    }

    /**
     * Get filter options for services.
     */
    public function getFilterOptions(Request $request)
    {
        $user = Auth::user();
        $userBranches = \App\Models\Branch::where('user_id', $user->id)->pluck('id');

        $categories = \App\Models\Category::whereHas('services', function ($query) use ($userBranches) {
            $query->whereIn('branch_id', $userBranches);
        })
        ->where('is_active', true)
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

        $priceRange = Service::whereIn('branch_id', $userBranches)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $durationRange = Service::whereIn('branch_id', $userBranches)
            ->selectRaw('MIN(duration) as min_duration, MAX(duration) as max_duration')
            ->first();

        return response()->json([
            'success' => true,
            'options' => [
                'categories' => $categories,
                'price_range' => [
                    'min' => $priceRange->min_price ?? 0,
                    'max' => $priceRange->max_price ?? 1000
                ],
                'duration_range' => [
                    'min' => $durationRange->min_duration ?? 0,
                    'max' => $durationRange->max_duration ?? 480
                ],
                'service_types' => [
                    ['value' => 'home_service', 'label' => 'Home Service'],
                    ['value' => 'in_store', 'label' => 'In-Store Service']
                ],
                'statuses' => [
                    ['value' => 'active', 'label' => 'Active'],
                    ['value' => 'inactive', 'label' => 'Inactive']
                ]
            ]
        ]);
    }
}
