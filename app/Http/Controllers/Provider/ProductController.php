<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Helpers\ProviderDashboardHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the provider's products.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ensure the provider record exists
        $user = Auth::user();
        $provider = $user->providerRecord;
        if (!$provider) {
            $provider = \App\Models\Provider::create([
                'user_id' => $user->id,
                'business_name' => "{$user->name}'s Business",
                'company_name' => "{$user->name}'s Company",
                'status' => 'active',
                'is_verified' => false
            ]);
        }

        $query = Product::query()
            ->where('user_id', Auth::id())
            ->with(['category']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_available', $request->status == '1');
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::where('type', 'product')->orderBy('name')->get();

        // If no view exists yet, return to dashboard with a message
        if (!view()->exists('provider.products.index')) {
            return ProviderDashboardHelper::getDashboardData('Products management is under development');
        }

        return view('provider.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get product categories
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // If no view exists yet, return to dashboard with a message
        if (!view()->exists('provider.products.create')) {
            return ProviderDashboardHelper::getDashboardData('Product creation is under development');
        }

        return view('provider.products.create', compact('parentCategories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'product_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'product_description_arabic' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // If English description is provided, Arabic description becomes required
                    if ($request->filled('description') && empty($value)) {
                        $fail(__('provider.arabic_description_required_when_english_provided'));
                    }
                    // If Arabic description is provided, English description becomes required
                    if (!empty($value) && !$request->filled('description')) {
                        $fail(__('provider.english_description_required_when_arabic_provided'));
                    }
                },
            ],
            'branch_id' => 'nullable|exists:branches,id',
            // Colors validation - now required
            'colors' => 'required|array|min:1',
            'colors.*.name' => 'required|string|max:255',
            'colors.*.color_code' => 'nullable|string|max:10',
            'colors.*.price_adjustment' => 'nullable|numeric',
            'colors.*.stock' => 'nullable|integer|min:0',
            'colors.*.display_order' => 'nullable|integer',
            'colors.*.is_default' => 'nullable|boolean',
            'color_images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['image', 'colors', 'color_images']);
        $data['user_id'] = Auth::id();
        $data['is_available'] = $request->has('is_available') ? true : false;

        // Ensure Arabic fields are included
        $data['product_name_arabic'] = $request->input('product_name_arabic');
        $data['product_description_arabic'] = $request->input('product_description_arabic');

        // Set merchant tracking fields (provider dashboard = not merchant)
        $data['is_merchant'] = false;
        $data['merchant_name'] = null;

        // If branch_id is not provided, try to get the user's default branch
        if (!isset($data['branch_id'])) {
            $user = Auth::user();
            $userBranch = Branch::where('user_id', $user->id)->first();

            // If no branch exists, create a default one
            if (!$userBranch) {
                $userBranch = new Branch([
                    'name' => "{$user->name}'s Branch",
                    'address' => 'Default Address',
                    'user_id' => $user->id,
                    'status' => 'active'
                ]);
                $userBranch->save();
            }

            if ($userBranch) {
                $data['branch_id'] = $userBranch->id;
            }
            // If no branch is found or created, branch_id will remain null (which is now allowed)
        }

        // We'll set the image later from the default color image

        // Create the product first
        $product = Product::create($data);

        // Add colors with their images (required)
        $defaultColorImage = null;
        $hasDefaultColor = false;

        foreach ($request->colors as $index => $colorData) {
            $isDefault = isset($colorData['is_default']) ? true : false;

            // If this is marked as default or no default has been set yet
            if ($isDefault) {
                $hasDefaultColor = true;
            }

            // Process the color image (required)
            if ($request->hasFile("color_images.$index")) {
                $file = $request->file("color_images.$index");
                $path = $file->store('product-colors', 'public');

                // Ensure consistent path format: /storage/product-colors/filename.jpg
                $image = '/storage/' . $path;

                // Log the image path for debugging
                \Illuminate\Support\Facades\Log::debug("Stored color image at path: {$path}, URL: {$image}");

                // If this is the default color, save its image to use as the product's main image
                if ($isDefault) {
                    $defaultColorImage = $image;
                }

                // Ensure the file is accessible by copying it if needed
                $sourceFile = storage_path('app/public/' . $path);
                $publicFile = public_path('storage/' . $path);

                // Make sure the directory exists
                if (!file_exists(dirname($publicFile))) {
                    mkdir(dirname($publicFile), 0755, true);
                }

                // If the file doesn't exist in the public directory, copy it there
                if (!file_exists($publicFile) && file_exists($sourceFile)) {
                    copy($sourceFile, $publicFile);
                    \Illuminate\Support\Facades\Log::debug("Copied image from {$sourceFile} to {$publicFile}");
                }
            } else {
                // Return with error if image is missing
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Each color must have an associated image.');
            }

            $product->colors()->create([
                'name' => $colorData['name'],
                'color_code' => $colorData['color_code'] ?? null,
                'image' => $image,
                'price_adjustment' => $colorData['price_adjustment'] ?? 0,
                'stock' => $colorData['stock'] ?? 0,
                'display_order' => $colorData['display_order'] ?? $index,
                'is_default' => $isDefault,
            ]);
        }

        // If no color is marked as default, make the first one default
        if (!$hasDefaultColor) {
            $firstColor = $product->colors()->first();
            if ($firstColor) {
                $firstColor->update(['is_default' => true]);

                // Use the first color's image as the default
                $defaultColorImage = $firstColor->getRawOriginal('image');
            }
        }

        // Set the product's main image to the default color image
        if ($defaultColorImage) {
            \Illuminate\Support\Facades\Log::debug("Setting main product image to: {$defaultColorImage}");
            $product->updateMainImageFromColorImage($defaultColorImage);

            // Double-check that the image was set correctly
            $product->refresh();
            \Illuminate\Support\Facades\Log::debug("Product image after update: {$product->getRawOriginal('image')}");
        } else {
            // This shouldn't happen with our validation, but just in case
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please select a default color with an image.');
        }

        return redirect()->route('provider.products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $product = Product::where('user_id', Auth::id())
            ->with(['category'])
            ->findOrFail($id);

        return view('provider.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        // Get product categories
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('provider.products.edit', compact('product', 'parentCategories'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'product_name_arabic' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'product_description_arabic' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // If English description is provided, Arabic description becomes required
                    if ($request->filled('description') && empty($value)) {
                        $fail(__('provider.arabic_description_required_when_english_provided'));
                    }
                    // If Arabic description is provided, English description becomes required
                    if (!empty($value) && !$request->filled('description')) {
                        $fail(__('provider.english_description_required_when_arabic_provided'));
                    }
                },
            ],
        ]);

        $data = $request->all();
        $data['is_available'] = $request->has('is_available') ? true : false;
        $data['featured'] = $request->has('featured') ? true : false;

        // Ensure Arabic fields are included
        $data['product_name_arabic'] = $request->input('product_name_arabic');
        $data['product_description_arabic'] = $request->input('product_description_arabic');

        // The product's main image is always derived from the default color image
        // We'll update it in the ProductSpecificationController when colors are updated

        $product->update($data);

        return redirect()->route('provider.products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $product = Product::where('user_id', Auth::id())->findOrFail($id);

            // Delete legacy image if exists (old format)
            if ($product->image && Storage::exists('public/' . str_replace('/storage/', '', $product->image))) {
                Storage::delete('public/' . str_replace('/storage/', '', $product->image));
            }

            // The Product model's deleting event will handle cascading deletion
            // of colors, sizes, color-size combinations, specifications, and images
            $product->delete();

            return redirect()->route('provider.products.index')
                ->with('success', 'Product and all related data deleted successfully');

        } catch (\Exception $e) {
            Log::error('Error deleting provider product: ' . $e->getMessage(), [
                'product_id' => $id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('provider.products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }
}
