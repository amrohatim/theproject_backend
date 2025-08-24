<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of services with filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Service::with(['branch.company', 'category']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('branch.company', function ($companyQuery) use ($search) {
                      $companyQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Apply company filter
        if ($request->filled('company_id')) {
            $query->whereHas('branch', function ($branchQuery) use ($request) {
                $branchQuery->where('company_id', $request->company_id);
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('is_available', true);
            } elseif ($request->status === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        // Apply featured filter
        if ($request->filled('featured')) {
            if ($request->featured === '1') {
                $query->where('featured', true);
            } elseif ($request->featured === '0') {
                $query->where('featured', false);
            }
        }

        $services = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::where('type', 'service')->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        
        // Preserve query parameters in pagination
        $services->appends($request->query());

        return view('admin.services.index', compact('services', 'categories', 'companies'));
    }

    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get service categories with their children
        $parentCategories = Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        
        $companies = Company::with('branches')->orderBy('name')->get();
        
        return view('admin.services.create', compact('parentCategories', 'companies'));
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'duration' => 'nullable|integer|min:1',
            'duration_unit' => 'nullable|string|in:minutes,hours,days',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'booking_required' => 'boolean',
            'max_bookings_per_day' => 'nullable|integer|min:1',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('services', 'public');
                $imagePaths[] = $imagePath;
            }
        }
        
        $validated['images'] = json_encode($imagePaths);

        Service::create($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully');
    }

    /**
     * Display the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = Service::with(['branch.company', 'category'])->findOrFail($id);
        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $service = Service::findOrFail($id);
        
        // Get service categories with their children
        $parentCategories = Category::where('type', 'service')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        
        $companies = Company::with('branches')->orderBy('name')->get();
        
        return view('admin.services.edit', compact('service', 'parentCategories', 'companies'));
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'duration' => 'nullable|integer|min:1',
            'duration_unit' => 'nullable|string|in:minutes,hours,days',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'booking_required' => 'boolean',
            'max_bookings_per_day' => 'nullable|integer|min:1',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            $oldImages = json_decode($service->images, true) ?? [];
            foreach ($oldImages as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            
            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('services', 'public');
                $imagePaths[] = $imagePath;
            }
            $validated['images'] = json_encode($imagePaths);
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully');
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
        
        // Delete images if exist
        $images = json_decode($service->images, true) ?? [];
        foreach ($images as $image) {
            Storage::disk('public')->delete($image);
        }
        
        $service->delete();
        
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully');
    }
}
