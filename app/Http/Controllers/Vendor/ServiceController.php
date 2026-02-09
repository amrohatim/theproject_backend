<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\BusinessType;
use App\Services\WebPImageService;
use App\Rules\ActiveBranchLicense;
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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::query()
            ->with(['branch', 'category'])
            ->whereHas('branch', function ($query) {
                $query->whereHas('company', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            });

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->filterByCategory($request->category);
        }

        if ($request->filled('branch')) {
            $query->filterByBranch($request->branch);
        }

        $services = $query->latest()->paginate(10);

        // Get service categories for the filter dropdown
        $categories = Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get branches that belong to the vendor's company for the filter dropdown (include all for filtering)
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        return view('vendor.services.index', compact('services', 'categories', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $businessTypeCategoryMap = $this->getBusinessTypeServiceCategoryMap();

        // Get service categories with their children - force a fresh query to get the latest data
        $parentCategories = Category::where('type', 'service')
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
                $parentIds = Category::whereIn('id', $allCategoryIds)
                    ->pluck('parent_id')
                    ->filter()
                    ->unique()
                    ->values();

                $parentCategories = Category::whereIn('id', $parentIds)
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

        // Get branches that belong to the vendor's company and have active licenses
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->withActiveLicense()->orderBy('name')->get();

        // Check if the vendor has any branches
        if ($branches->isEmpty()) {
            return redirect()->route('vendor.branches.create')
                ->with('warning', 'You need to create a branch before adding services. Please create a branch first.');
        }

        // Check if there are any categories
        if ($parentCategories->isEmpty()) {
            return redirect()->route('vendor.services.index')
                ->with('warning', 'No service categories found. Please contact the administrator.');
        }

        return view('vendor.services.create', compact('parentCategories', 'branches', 'businessTypeCategoryMap'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Custom validation for bilingual fields
        $validationRules = [
            'name' => 'required|string|max:255',
            'service_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => ['required', 'exists:branches,id', new ActiveBranchLicense()],
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
        ];

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

        // Validate conditional description requirement
        $hasEnglishDescription = !empty($request->description);
        $hasArabicDescription = !empty($request->service_description_arabic);

        if ($hasEnglishDescription !== $hasArabicDescription) {
            return redirect()->back()
                ->withErrors(['description' => __('messages.description_both_or_none')])
                ->withInput();
        }

        // Verify that the branch belongs to the vendor's company
        $branch = Branch::findOrFail($request->branch_id);

        // Check if the branch's company belongs to the authenticated user
        $companyBelongsToUser = $branch->company && $branch->company->user_id === Auth::id();

        if (!$companyBelongsToUser) {
            return redirect()->back()->with('error', 'You do not have permission to add services to this branch.');
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

        // Handle image upload
        if ($request->hasFile('image')) {
            // Convert to WebP and store
            $webpService = new WebPImageService();
            $imagePath = $webpService->convertAndStoreWithUrl($request->file('image'), 'services');

            if (!$imagePath) {
                // Fallback to original upload method if WebP conversion fails
                $storagePath = $request->file('image')->store('services', 'public');
                $imagePath = Storage::url($storagePath);
            }

            $data['image'] = $imagePath;
        }

        $service = Service::create($data);
        $this->storeAdditionalImages($request, $service);

        return redirect()->route('vendor.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $businessTypeCategoryMap = $this->getBusinessTypeServiceCategoryMap();

        // Check if the service belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($service->branch_id, $userBranches)) {
            return redirect()->route('vendor.services.index')
                ->with('error', 'You do not have permission to edit this service.');
        }

        // Get service categories with their children - same as create method
        $parentCategories = Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
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
                $parentIds = Category::whereIn('id', $allowedCategoryIds)
                    ->pluck('parent_id')
                    ->filter()
                    ->unique()
                    ->values();

                $parentCategories = Category::whereIn('id', $parentIds)
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

        // Get branches that belong to the vendor's company and have active licenses
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->withActiveLicense()->orderBy('name')->get();

        return view('vendor.services.edit', compact('service', 'parentCategories', 'branches', 'businessTypeCategoryMap'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        // Check if the service belongs to the vendor's company
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($service->branch_id, $userBranches)) {
            return redirect()->route('vendor.services.index')
                ->with('error', 'You do not have permission to update this service.');
        }

        // Custom validation for bilingual fields
        $validationRules = [
            'name' => 'required|string|max:255',
            'service_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => ['required', 'exists:branches,id', new ActiveBranchLicense()],
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'service_description_arabic' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'additional_images' => 'nullable|array|max:8',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'available_days' => 'required|array|min:1',
            'available_days.*' => 'integer|between:0,6',
        ];

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

        // Validate conditional description requirement
        $hasEnglishDescription = !empty($request->description);
        $hasArabicDescription = !empty($request->service_description_arabic);

        if ($hasEnglishDescription !== $hasArabicDescription) {
            return redirect()->back()
                ->withErrors(['description' => __('messages.description_both_or_none')])
                ->withInput();
        }

        // Verify that the branch belongs to the vendor's company
        $branch = Branch::findOrFail($request->branch_id);

        // Check if the branch's company belongs to the authenticated user
        $companyBelongsToUser = $branch->company && $branch->company->user_id === Auth::id();

        if (!$companyBelongsToUser) {
            return redirect()->back()->with('error', 'You do not have permission to move services to this branch.');
        }

        $data = $request->except('image');
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['home_service'] = $request->has('home_service') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                $webpService = new WebPImageService();
                $webpService->deleteImage($service->image);
            }

            // Convert to WebP and store
            $webpService = new WebPImageService();
            $imagePath = $webpService->convertAndStoreWithUrl($request->file('image'), 'services');

            if (!$imagePath) {
                // Fallback to original upload method if WebP conversion fails
                $storagePath = $request->file('image')->store('services', 'public');
                $imagePath = Storage::url($storagePath);
            }

            $data['image'] = $imagePath;
        }

        $service->update($data);
        $this->deleteAdditionalImages($request, $service);
        $this->storeAdditionalImages($request, $service);

        return redirect()->route('vendor.services.index')
            ->with('success', 'Service updated successfully.');
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
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            // Check if the service belongs to the vendor's company
            $userBranches = Branch::whereHas('company', function ($query) {
                $query->where('user_id', Auth::id());
            })->pluck('id')->toArray();

            if (!in_array($service->branch_id, $userBranches)) {
                return redirect()->route('vendor.services.index')
                    ->with('error', 'You do not have permission to delete this service.');
            }

            // The Service model's deleting event will handle image cleanup using WebPImageService
            $service->delete();

            return redirect()->route('vendor.services.index')
                ->with('success', 'Service deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting vendor service: ' . $e->getMessage(), [
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('vendor.services.index')
                ->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }

    /**
     * Get search suggestions for services.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Service::query()
            ->with(['branch', 'category'])
            ->whereHas('branch', function ($q) {
                $q->whereHas('company', function ($q) {
                    $q->where('user_id', Auth::id());
                });
            })
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhereHas('category', function ($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($service) use ($query) {
                return [
                    'id' => $service->id,
                    'text' => $service->name,
                    'type' => 'service',
                    'icon' => 'fas fa-concierge-bell',
                    'subtitle' => $service->category->name ?? 'No Category',
                    'highlight' => $this->highlightMatch($service->name, $query),
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Highlight matching text in search results.
     */
    private function highlightMatch($text, $query)
    {
        return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
    }
}
