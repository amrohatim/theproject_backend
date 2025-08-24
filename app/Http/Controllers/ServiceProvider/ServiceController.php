<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Branch;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
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

        // Get service categories with their children - force a fresh query to get the latest data
        $parentCategories = \App\Models\Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Get branches this service provider can access
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])->get();

        // Check if the service provider has any branches
        if ($branches->isEmpty()) {
            return redirect()->route('service-provider.dashboard')
                ->with('warning', 'You need access to branches before adding services. Please contact your vendor.');
        }

        return view('service-provider.services.create', compact('parentCategories', 'branches', 'serviceProvider'));
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
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'service_description_arabic' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ]);

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

        // Check if the service provider can access this service
        if (!in_array($service->id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this service.');
        }

        // Get categories
        $categories = \App\Models\Category::orderBy('name')->get();

        // Get branches this service provider can access
        $branches = Branch::whereIn('id', $serviceProvider->branch_ids ?? [])->get();

        return view('service-provider.services.edit', compact('service', 'categories', 'branches', 'serviceProvider'));
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
            'branch_id' => 'required|exists:branches,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'service_description_arabic' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
        ]);

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
