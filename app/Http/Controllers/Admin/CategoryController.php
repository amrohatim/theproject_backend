<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories with filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // If this is an AJAX request for parent categories only
        if ($request->has('parents_only') && $request->ajax()) {
            $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
            return response()->json($parentCategories);
        }

        $query = Category::with('parent');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Apply parent filter
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }

        $categories = $query->orderBy('name')->paginate(10);
        
        // Preserve query parameters in pagination
        $categories->appends($request->query());

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info('CategoryController STORE method called');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_name_arabic' => 'required|string|max:255',
            'type' => 'required|string|in:product,service',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB max
        ]);

        // If parent_id is empty string, set it to null
        if ($validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }

        // Handle image upload with WebP compression
        if ($request->hasFile('image')) {
            Log::info('CategoryController: Image file detected, starting WebP conversion');
            $webpService = new WebPImageService();
            $imagePath = $webpService->convertAndStoreWithUrl($request->file('image'), 'categories');

            if ($imagePath) {
                Log::info('CategoryController: WebP conversion successful', ['path' => $imagePath]);
                $validated['image'] = $imagePath;
            } else {
                Log::error('CategoryController: WebP conversion failed');
            }
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        if (request()->ajax()) {
            // Get parent categories for the dropdown
            $parentCategories = Category::whereNull('parent_id')
                ->where('id', '!=', $id) // Exclude current category
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'category' => $category,
                'parentCategories' => $parentCategories
            ]);
        }

        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_name_arabic' => 'required|string|max:255',
            'type' => 'required|string|in:product,service',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480', // 20MB max
        ]);

        // If parent_id is empty string, set it to null
        if ($validated['parent_id'] === '') {
            $validated['parent_id'] = null;
        }

        // Handle image upload with WebP compression
        if ($request->hasFile('image')) {
            Log::info('CategoryController UPDATE: Image file detected, starting WebP conversion');
            // Delete old image if exists using WebPImageService
            if ($category->image) {
                $webpService = new WebPImageService();
                $webpService->deleteImage($category->image);
            }

            $webpService = new WebPImageService();
            $imagePath = $webpService->convertAndStoreWithUrl($request->file('image'), 'categories');

            if ($imagePath) {
                Log::info('CategoryController UPDATE: WebP conversion successful', ['path' => $imagePath]);
                $validated['image'] = $imagePath;
            } else {
                Log::error('CategoryController UPDATE: WebP conversion failed');
            }
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has children
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with subcategories. Please delete subcategories first.');
        }

        // Delete image if exists using WebPImageService
        if ($category->image) {
            $webpService = new WebPImageService();
            $webpService->deleteImage($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully');
    }
}
