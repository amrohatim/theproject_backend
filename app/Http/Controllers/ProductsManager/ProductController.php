<?php

namespace App\Http\Controllers\ProductsManager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Category;
use App\Services\WebPImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of products that the products manager can manage.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        $query = Product::whereHas('branch', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        })->with(['branch', 'category']);

        // Filter by branch if provided
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by category if provided
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get branches and categories for filters
        $branches = Branch::where('company_id', $company->id)->get();
        $categories = Category::all();

        // Check if this is an AJAX request for dynamic content loading
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('products-manager.products.index-content', compact('products', 'branches', 'categories', 'productsManager'));
        }

        return view('products-manager.products.index', compact('products', 'branches', 'categories', 'productsManager'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;
        $branches = Branch::where('company_id', $company->id)->get();
        $categories = Category::all();

        return view('products-manager.products.create', compact('branches', 'categories', 'productsManager'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $company = $productsManager->company;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:255|unique:products',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        // Verify the branch belongs to the company
        $branch = Branch::where('id', $request->branch_id)
            ->where('company_id', $company->id)
            ->first();

        if (!$branch) {
            return redirect()->back()
                ->withErrors(['branch_id' => 'Invalid branch selected.'])
                ->withInput();
        }

        $productData = $request->except('image');
        $productData['status'] = 'available';

        // Handle is_available checkbox properly - convert boolean to integer
        // Check the actual value, not just presence, and convert to integer (1 or 0)
        $isAvailable = $request->input('is_available');
        if ($isAvailable === 'true' || $isAvailable === true || $isAvailable === '1' || $isAvailable === 1) {
            $productData['is_available'] = 1;
        } else {
            $productData['is_available'] = 0;
        }

        // Handle image upload with WebP conversion
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'products');

                if ($imagePath) {
                    $productData['image'] = $imagePath;
                    Log::info('ProductsManager: WebP conversion successful for product image', [
                        'original_name' => $file->getClientOriginalName(),
                        'converted_path' => $imagePath
                    ]);
                } else {
                    // Fallback to original upload method if WebP conversion fails
                    $imageName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('images/products'), $imageName);
                    $productData['image'] = 'images/products/' . $imageName;

                    Log::warning('ProductsManager: WebP conversion failed, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $productData['image']
                    ]);
                }
            } catch (\Exception $e) {
                // Fallback to original upload method on exception
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/products'), $imageName);
                $productData['image'] = 'images/products/' . $imageName;

                Log::error('ProductsManager: WebP conversion exception, using fallback method', [
                    'error' => $e->getMessage(),
                    'original_name' => $file->getClientOriginalName(),
                    'fallback_path' => $productData['image']
                ]);
            }
        }

        $product = Product::create($productData);

        // Check if this is an AJAX request (from Vue component)
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => $product
            ], 201);
        }

        return redirect()->route('products-manager.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        // Check if the product belongs to the company
        if ($product->branch->company_id !== $productsManager->company_id) {
            abort(403, 'You do not have access to this product.');
        }

        $product->load(['branch', 'category', 'orderItems' => function ($query) {
            $query->with('order')->orderBy('created_at', 'desc')->limit(10);
        }]);

        return view('products-manager.products.show', compact('product', 'productsManager'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Request $request, Product $product)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        // Check if the product belongs to the company
        if ($product->branch->company_id !== $productsManager->company_id) {
            abort(403, 'You do not have access to this product.');
        }

        $company = $productsManager->company;
        $branches = Branch::where('company_id', $company->id)->get();
        $categories = Category::all();

        // Check if this is an AJAX request for dynamic content loading
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('products-manager.products.edit-content', compact('product', 'branches', 'categories', 'productsManager'));
        }

        return view('products-manager.products.edit', compact('product', 'branches', 'categories', 'productsManager'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        // Check if the product belongs to the company
        if ($product->branch->company_id !== $productsManager->company_id) {
            abort(403, 'You do not have access to this product.');
        }

        $company = $productsManager->company;

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'status' => 'required|in:available,unavailable,out_of_stock',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        // Verify the branch belongs to the company
        $branch = Branch::where('id', $request->branch_id)
            ->where('company_id', $company->id)
            ->first();

        if (!$branch) {
            return redirect()->back()
                ->withErrors(['branch_id' => 'Invalid branch selected.'])
                ->withInput();
        }

        $productData = $request->except('image');

        // Handle is_available checkbox properly - convert boolean to integer
        // Check the actual value, not just presence, and convert to integer (1 or 0)
        $isAvailable = $request->input('is_available');
        if ($isAvailable === 'true' || $isAvailable === true || $isAvailable === '1' || $isAvailable === 1) {
            $productData['is_available'] = 1;
        } else {
            $productData['is_available'] = 0;
        }

        // Handle image upload with WebP conversion
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            try {
                // Convert to WebP and store
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'products');

                if ($imagePath) {
                    // Delete old image if exists and new conversion is successful
                    if ($product->image && file_exists(public_path($product->image))) {
                        unlink(public_path($product->image));
                    }

                    $productData['image'] = $imagePath;
                    Log::info('ProductsManager: WebP conversion successful for product image update', [
                        'product_id' => $product->id,
                        'original_name' => $file->getClientOriginalName(),
                        'converted_path' => $imagePath
                    ]);
                } else {
                    // Fallback to original upload method if WebP conversion fails
                    // Delete old image if exists
                    if ($product->image && file_exists(public_path($product->image))) {
                        unlink(public_path($product->image));
                    }

                    $imageName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('images/products'), $imageName);
                    $productData['image'] = 'images/products/' . $imageName;

                    Log::warning('ProductsManager: WebP conversion failed for update, using fallback method', [
                        'product_id' => $product->id,
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $productData['image']
                    ]);
                }
            } catch (\Exception $e) {
                // Fallback to original upload method on exception
                // Delete old image if exists
                if ($product->image && file_exists(public_path($product->image))) {
                    unlink(public_path($product->image));
                }

                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/products'), $imageName);
                $productData['image'] = 'images/products/' . $imageName;

                Log::error('ProductsManager: WebP conversion exception for update, using fallback method', [
                    'product_id' => $product->id,
                    'error' => $e->getMessage(),
                    'original_name' => $file->getClientOriginalName(),
                    'fallback_path' => $productData['image']
                ]);
            }
        }

        $product->update($productData);

        // Check if this is an AJAX request (from Vue component)
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'product' => $product->fresh()
            ], 200);
        }

        return redirect()->route('products-manager.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Request $request, Product $product)
    {
        try {
            $user = Auth::user();
            $productsManager = $user->productsManager;

            if (!$productsManager) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Products manager profile not found.'], 403);
                }
                return redirect('/')->with('error', 'Products manager profile not found.');
            }

            // Check if the product belongs to the company
            if ($product->branch->company_id !== $productsManager->company_id) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'You do not have access to this product.'], 403);
                }
                abort(403, 'You do not have access to this product.');
            }

            // The Product model's deleting event will handle cascading deletion
            // of colors, sizes, color-size combinations, specifications, and images
            // including proper image cleanup using WebPImageService
            $product->delete();

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Product deleted successfully.']);
            }

            return redirect()->route('products-manager.products.index')
                ->with('success', 'Product deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting product in Products Manager: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete product: ' . $e->getMessage()], 500);
            }

            return redirect()->route('products-manager.products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update product statuses.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $user = Auth::user();
        $productsManager = $user->productsManager;

        if (!$productsManager) {
            return redirect('/')->with('error', 'Products manager profile not found.');
        }

        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'status' => 'required|in:available,unavailable,out_of_stock',
        ]);

        $company = $productsManager->company;

        // Verify all products belong to the company
        $products = Product::whereIn('id', $request->product_ids)
            ->whereHas('branch', function ($query) use ($company) {
                $query->where('company_id', $company->id);
            })->get();

        if ($products->count() !== count($request->product_ids)) {
            return redirect()->back()->with('error', 'Some products do not belong to your company.');
        }

        $products->each(function ($product) use ($request) {
            $product->update(['status' => $request->status]);
        });

        return redirect()->back()->with('success', 'Product statuses updated successfully.');
    }
}
