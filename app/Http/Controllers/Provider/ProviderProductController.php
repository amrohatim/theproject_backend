<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProviderProduct;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\WebPImageService;

class ProviderProductController extends Controller
{
    /**
     * Display a listing of the provider's products.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            // Create a provider record if it doesn't exist
            $provider = Provider::create([
                'user_id' => $user->id,
                'business_name' => "{$user->name}'s Business",
                'company_name' => "{$user->name}'s Company",
                'status' => 'active',
                'is_verified' => false
            ]);
        }

        $providerProductsQuery = ProviderProduct::where('provider_id', $provider->id)
            ->with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $providerProductsQuery->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_name_arabic', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $providerProductsQuery->where('is_active', $request->status == '1');
        }

        $providerProducts = $providerProductsQuery->paginate(10);

        return view('provider.provider_products.index', compact('providerProducts'));
    }

    /**
     * Get search suggestions for provider products.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            return response()->json([]);
        }

        $suggestions = ProviderProduct::query()
            ->where('provider_id', $provider->id)
            ->where(function ($q) use ($query) {
                $q->where('product_name', 'like', "%{$query}%")
                    ->orWhere('product_name_arabic', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($product) use ($query) {
                return [
                    'id' => $product->id,
                    'text' => $product->product_name,
                    'type' => 'provider_product',
                    'icon' => 'fas fa-box',
                    'subtitle' => $product->sku ?: ($product->product_name_arabic ?: __('provider.product_inventory')),
                    'highlight' => $this->highlightMatch($product->product_name, $query),
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

    /**
     * Show the form for creating a new provider product.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get available products for the provider to add to their inventory
        $products = Product::with('category')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();

        // Get categories for filtering
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('provider.provider_products.create', compact('products', 'parentCategories'));
    }

    /**
     * Store a newly created provider product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_name_arabic' => 'required|string|max:255',
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
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_order' => 'required|integer|min:1|lte:stock',
            'sku' => 'nullable|string|max:100',
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $category = \App\Models\Category::find($value);
                        if ($category && $category->isParentCategory()) {
                            $fail(__('provider.select_specific_subcategory_not_parent'));
                        }
                    }
                },
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'is_active' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            // Create a provider record if it doesn't exist
            $provider = Provider::create([
                'user_id' => $user->id,
                'business_name' => "{$user->name}'s Business",
                'company_name' => "{$user->name}'s Company",
                'status' => 'active',
                'is_verified' => false
            ]);
        }

        // Create the provider-specific product directly without creating a global product
        $providerProduct = new ProviderProduct();
        $providerProduct->provider_id = $provider->id;
        $providerProduct->product_name = $request->product_name;
        $providerProduct->product_name_arabic = $request->product_name_arabic;
        $providerProduct->description = $request->description;
        $providerProduct->product_description_arabic = $request->product_description_arabic;
        $providerProduct->price = $request->price;
        $providerProduct->original_price = $request->original_price;
        $providerProduct->stock = $request->stock;
        $providerProduct->min_order = $request->min_order;
        $providerProduct->sku = $request->sku;
        $providerProduct->category_id = $request->category_id;
        if ($request->has('is_active')) {
            $providerProduct->is_active = $request->boolean('is_active');
        }

        // Handle image upload directly for the provider product
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');

                // Validate file size (additional check)
                if ($file->getSize() > 20480 * 1024) { // 20MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 20MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF, SVG.']);
                }

                // Try WebP conversion first
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'provider_products');

                if ($imagePath) {
                    // WebP conversion successful
                    $providerProduct->image = $imagePath;
                    \Log::info('Provider product image uploaded successfully with WebP conversion', [
                        'image_path' => $imagePath,
                        'original_name' => $file->getClientOriginalName()
                    ]);
                } else {
                    // Fallback to original upload method if WebP conversion fails
                    $destinationPath = public_path('images/provider_products');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $fileName);
                    $providerProduct->image = 'images/provider_products/' . $fileName;

                    \Log::warning('WebP conversion failed for provider product, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $providerProduct->image
                    ]);
                }
            } catch (\Exception $e) {
                // Log error
                \Log::error('Provider product image upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Image upload failed: ' . $e->getMessage())->withInput();
            }
        }

        $providerProduct->save();

        return redirect()->route('provider.provider-products.index')
            ->with('success', 'Product added to your inventory successfully');
    }

    /**
     * Show the form for editing the specified provider product.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            return redirect()->route('provider.provider-products.index')
                ->with('error', 'Provider record not found.');
        }

        // Find the provider product
        $providerProduct = ProviderProduct::where('id', $id)
            ->where('provider_id', $provider->id)
            ->first();

        if (!$providerProduct) {
            return redirect()->route('provider.provider-products.index')
                ->with('error', 'Product not found in your inventory.');
        }

        // Get categories for the form
        $parentCategories = Category::where('type', 'product')
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('provider.provider_products.edit', compact('providerProduct', 'parentCategories'));
    }

    /**
     * Update the specified provider product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_name_arabic' => 'required|string|max:255',
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
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_order' => 'required|integer|min:1|lte:stock',
            'sku' => 'nullable|string|max:100',
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $category = \App\Models\Category::find($value);
                        if ($category && $category->isParentCategory()) {
                            $fail(__('provider.select_specific_subcategory_not_parent'));
                        }
                    }
                },
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:20480',
            'is_active' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            return redirect()->route('provider.provider-products.index')
                ->with('error', 'Provider record not found.');
        }

        // Find the provider product
        $providerProduct = ProviderProduct::where('id', $id)
            ->where('provider_id', $provider->id)
            ->first();

        if (!$providerProduct) {
            return redirect()->route('provider.provider-products.index')
                ->with('error', 'Product not found in your inventory.');
        }

        // Update provider product fields
        $providerProduct->product_name = $request->product_name;
        $providerProduct->product_name_arabic = $request->product_name_arabic;
        $providerProduct->description = $request->description;
        $providerProduct->product_description_arabic = $request->product_description_arabic;
        $providerProduct->price = $request->price;
        $providerProduct->original_price = $request->original_price;
        $providerProduct->stock = $request->stock;
        $providerProduct->min_order = $request->min_order;
        $providerProduct->sku = $request->sku;
        $providerProduct->category_id = $request->category_id;
        if ($request->has('is_active')) {
            $providerProduct->is_active = $request->boolean('is_active');
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');

                // Validate file size (additional check)
                if ($file->getSize() > 20480 * 1024) { // 20MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 20MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF, SVG.']);
                }

                // Delete old image if it exists
                if ($providerProduct->image) {
                    $webpService = new WebPImageService();
                    $webpService->deleteImage($providerProduct->image);
                }

                // Try WebP conversion first
                $webpService = new WebPImageService();
                $imagePath = $webpService->convertAndStoreWithUrl($file, 'provider_products');

                if ($imagePath) {
                    // WebP conversion successful
                    $providerProduct->image = $imagePath;
                    \Log::info('Provider product image updated successfully with WebP conversion', [
                        'image_path' => $imagePath,
                        'original_name' => $file->getClientOriginalName()
                    ]);
                } else {
                    // Fallback to original upload method if WebP conversion fails
                    $destinationPath = public_path('images/provider_products');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move($destinationPath, $fileName);
                    $providerProduct->image = 'images/provider_products/' . $fileName;

                    \Log::warning('WebP conversion failed for provider product update, using fallback method', [
                        'original_name' => $file->getClientOriginalName(),
                        'fallback_path' => $providerProduct->image
                    ]);
                }
            } catch (\Exception $e) {
                // Log error
                \Log::error('Provider product image update failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Image upload failed: ' . $e->getMessage())->withInput();
            }
        }

        $providerProduct->save();

        return redirect()->route('provider.provider-products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified provider product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $provider = $user->providerRecord;

        if (!$provider) {
            return redirect()->route('provider.provider-products.index')
                ->with('error', 'Provider record not found.');
        }

        // Find the provider product
        $providerProduct = ProviderProduct::where('id', $id)
            ->where('provider_id', $provider->id)
            ->first();

        if (!$providerProduct) {
            return redirect()->route('provider.provider-products.index')
                ->with('error', 'Product not found in your inventory.');
        }

        try {
            // Delete associated image if it exists
            if ($providerProduct->image) {
                $webpService = new WebPImageService();
                $imageDeleted = $webpService->deleteImage($providerProduct->image);

                if ($imageDeleted) {
                    Log::info('Provider product image deleted successfully', [
                        'provider_product_id' => $providerProduct->id,
                        'image_path' => $providerProduct->image,
                        'provider_id' => $provider->id
                    ]);
                } else {
                    Log::warning('Failed to delete provider product image', [
                        'provider_product_id' => $providerProduct->id,
                        'image_path' => $providerProduct->image,
                        'provider_id' => $provider->id
                    ]);
                }
            }

            // Delete the provider product record
            $providerProduct->delete();

            Log::info('Provider product deleted successfully', [
                'provider_product_id' => $id,
                'provider_id' => $provider->id,
                'had_image' => !empty($providerProduct->image)
            ]);

            return redirect()->route('provider.provider-products.index')
                ->with('success', 'Product removed from your inventory successfully');

        } catch (\Exception $e) {
            Log::error('Failed to delete provider product', [
                'provider_product_id' => $id,
                'provider_id' => $provider->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('provider.provider-products.index')
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }
}
