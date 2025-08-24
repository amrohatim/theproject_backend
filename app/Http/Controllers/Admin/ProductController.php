<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filtering.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::with(['branch.company', 'category']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
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

        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        
        // Preserve query parameters in pagination
        $products->appends($request->query());

        return view('admin.products.index', compact('products', 'categories', 'companies'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get product categories with their children
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        
        $companies = Company::with('branches')->orderBy('name')->get();
        
        return view('admin.products.create', compact('parentCategories', 'companies'));
    }

    /**
     * Store a newly created product in storage.
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
            'sku' => 'nullable|string|max:255|unique:products',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $imagePaths[] = $imagePath;
            }
        }
        
        $validated['images'] = json_encode($imagePaths);

        // Set merchant tracking fields (admin dashboard = not merchant)
        $validated['is_merchant'] = false;
        $validated['merchant_name'] = null;

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with(['branch.company', 'category'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        // Get product categories with their children
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();
        
        $companies = Company::with('branches')->orderBy('name')->get();
        
        return view('admin.products.edit', compact('product', 'parentCategories', 'companies'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'required|exists:branches,id',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            $oldImages = json_decode($product->images, true) ?? [];
            foreach ($oldImages as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            
            // Upload new images
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('products', 'public');
                $imagePaths[] = $imagePath;
            }
            $validated['images'] = json_encode($imagePaths);
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete additional images if exist (legacy field)
            if (isset($product->images)) {
                $images = json_decode($product->images, true) ?? [];
                foreach ($images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            // The Product model's deleting event will handle cascading deletion
            // of colors, sizes, color-size combinations, specifications, and images
            $product->delete();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product and all related data deleted successfully');

        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage(), [
                'product_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('admin.products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
