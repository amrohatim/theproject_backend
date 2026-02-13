<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\Category;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class BusinessTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BusinessType::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('business_name', 'like', "%{$search}%");
        }

        // Order by name
        $query->orderBy('business_name');

        $businessTypes = $query->paginate(15);

        return view('admin.business-types.index', compact('businessTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all child categories (subcategories), regardless of associations
        $productCategories = Category::whereNotNull('parent_id')
            ->with('parent')
            ->orderBy('name')
            ->get();

        $serviceCategories = Category::whereNotNull('parent_id')
            ->with('parent')
            ->orderBy('name')
            ->get();

        return view('admin.business-types.create', compact('productCategories', 'serviceCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255|unique:business_types,business_name',
            'name_arabic' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB max
            'product_categories' => 'nullable|array',
            'product_categories.*' => 'exists:categories,id',
            'service_categories' => 'nullable|array',
            'service_categories.*' => 'exists:categories,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $webpService = new WebPImageService();
            $imagePath =  $webpService->convertAndStoreWithUrl($request->file('image'), 'business-types');

            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        BusinessType::create($validated);

        return redirect()->route('admin.business-types.index')
            ->with('success', 'Business type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BusinessType $businessType)
    {
        return view('admin.business-types.show', compact('businessType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessType $businessType)
    {
        // Get all child categories (subcategories), regardless of associations
        $productCategories = Category::whereNotNull('parent_id')
            ->with('parent')
            ->orderBy('name')
            ->get();

        $serviceCategories = Category::whereNotNull('parent_id')
            ->with('parent')
            ->orderBy('name')
            ->get();

        return view('admin.business-types.edit', compact('businessType', 'productCategories', 'serviceCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessType $businessType)
    {
        $validated = $request->validate([
            'business_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('business_types', 'business_name')->ignore($businessType->id),
            ],
            'name_arabic' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB max
            'product_categories' => 'nullable|array',
            'product_categories.*' => 'exists:categories,id',
            'service_categories' => 'nullable|array',
            'service_categories.*' => 'exists:categories,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
                     $webpService = new WebPImageService();
            // Delete old image if exists
            if ($businessType->image && Storage::disk('public')->exists($businessType->image)) {
                Storage::disk('public')->delete($businessType->image);
            }

            $imagePath = $webpService->convertAndStoreWithUrl($request->file('image'), 'business-types');

            if ($imagePath) {
                $validated['image'] = $imagePath;
            }
        }

        $businessType->update($validated);

        return redirect()->route('admin.business-types.index')
            ->with('success', 'Business type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessType $businessType)
    {
        try {
            // Delete associated image if exists
            if ($businessType->image && Storage::disk('public')->exists($businessType->image)) {
                Storage::disk('public')->delete($businessType->image);
            }

            $businessType->delete();
            return redirect()->route('admin.business-types.index')
                ->with('success', 'Business type deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.business-types.index')
                ->with('error', 'Error deleting business type. It may be in use.');
        }
    }
}
