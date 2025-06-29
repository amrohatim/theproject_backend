<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Provider\BranchController;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Provider;
use App\Models\ProviderProduct;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

        $providerProducts = ProviderProduct::where('provider_id', $provider->id)
            ->with('product.category')
            ->paginate(10);

        return view('provider.provider_products.index', compact('providerProducts'));
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
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // Try to get the user's default branch if they have one
        $userBranch = $user->branches()->first();

        // If no branch exists, create a default one
        if (!$userBranch) {
            $userBranch = BranchController::createDefaultBranch($user);
        }

        // Create the provider-specific product directly without creating a global product
        $providerProduct = new ProviderProduct();
        $providerProduct->provider_id = $provider->id;
        $providerProduct->product_id = null; // No longer linked to a product in the products table
        $providerProduct->product_name = $request->product_name;
        $providerProduct->description = $request->description;
        $providerProduct->price = $request->price;
        $providerProduct->original_price = $request->original_price;
        $providerProduct->stock = $request->stock;
        $providerProduct->sku = $request->sku;
        $providerProduct->category_id = $request->category_id;
        $providerProduct->is_active = $request->has('is_active') ? true : false;

        // Store branch information if we have a branch
        if ($userBranch) {
            $providerProduct->branch_id = $userBranch->id;
        }

        // Handle image upload directly for the provider product
        if ($request->hasFile('image')) {
            try {
                // Use the public images directory for direct access
                $destinationPath = public_path('images/provider_products');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Move the file directly to the public directory
                $file->move($destinationPath, $fileName);

                // Store the direct public URL path
                $providerProduct->image = 'images/provider_products/' . $fileName;

                // Log success
                \Log::info('Provider product image uploaded successfully to public directory: ' . $fileName);
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
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
        $providerProduct->description = $request->description;
        $providerProduct->price = $request->price;
        $providerProduct->original_price = $request->original_price;
        $providerProduct->stock = $request->stock;
        $providerProduct->sku = $request->sku;
        $providerProduct->category_id = $request->category_id;
        $providerProduct->is_active = $request->has('is_active') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Use the public images directory for direct access
                $destinationPath = public_path('images/provider_products');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }

                $file = $request->file('image');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Move the file directly to the public directory
                $file->move($destinationPath, $fileName);

                // Delete old image if it exists
                if ($providerProduct->image) {
                    $oldImagePath = public_path($providerProduct->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Store the direct public URL path
                $providerProduct->image = 'images/provider_products/' . $fileName;

                // Log success
                \Log::info('Provider product image updated successfully to public directory: ' . $fileName);
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

        // Delete the provider product
        $providerProduct->delete();

        return redirect()->route('provider.provider-products.index')
            ->with('success', 'Product removed from your inventory successfully');
    }
}
