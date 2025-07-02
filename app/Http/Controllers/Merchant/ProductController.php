<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;

class ProductController extends Controller
{
    /**
     * Display a listing of the merchant's products.
     */
    public function index()
    {
        $user = Auth::user();
        $products = Product::where('user_id', $user->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('merchant.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('merchant.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'status' => 'nullable|in:active,inactive',
        ], [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.',
            'image.max' => 'The image size must not exceed 2MB.',
            'name.required' => 'Product name is required.',
            'description.required' => 'Product description is required.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Product price must be a valid number.',
            'price.min' => 'Product price must be greater than or equal to 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'sku.unique' => 'This SKU is already in use.',
        ]);

        // Prepare data for product creation - only include fields that exist in the database
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock_quantity ?? 0, // Map stock_quantity to stock
            'sku' => $request->sku,
            'user_id' => Auth::id(),
            'is_available' => ($request->status ?? 'active') === 'active', // Map status to is_available, default to active
        ];

        // Get or create a branch for the merchant
        $user = Auth::user();
        $userBranch = $user->branches()->first();

        // If no branch exists, create a default one for the merchant
        if (!$userBranch) {
            $merchant = $user->merchantRecord;
            $branchName = $merchant ? $merchant->business_name : $user->name . "'s Store";

            $userBranch = Branch::create([
                'user_id' => $user->id,
                'name' => $branchName,
                'address' => $merchant->store_location_address ?? 'Default Address',
                'emirate' => $merchant->emirate ?? 'Dubai',
                'lat' => $merchant->store_location_lat ?? 25.2048,
                'lng' => $merchant->store_location_lng ?? 55.2708,
                'status' => 'active',
                'phone' => $user->phone,
                'email' => $user->email,
            ]);
        }

        $data['branch_id'] = $userBranch->id;

        // Handle image upload with enhanced validation and error handling
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');

                // Additional server-side validation
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The uploaded image file is corrupted or invalid.']);
                }

                // Validate file size (additional check)
                if ($image->getSize() > 2048 * 1024) { // 2MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 2MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.']);
                }

                // Generate unique filename
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Store the image
                $imagePath = $image->storeAs('products', $imageName, 'public');

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

                // Automatically sync the uploaded image to public/storage
                ImageHelper::syncUploadedImage($imagePath);

            } catch (\Exception $e) {
                \Log::error('Product image upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        try {
            Product::create($data);
            return redirect()->route('merchant.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            \Log::error('Product creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Failed to create product. Please try again.']);
        }
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::where('user_id', Auth::id())
            ->with([
                'category',
                'specifications' => function($query) {
                    $query->orderBy('display_order');
                },
                'colors' => function($query) {
                    $query->orderBy('display_order');
                },
                'sizes' => function($query) {
                    $query->orderBy('display_order');
                },
                'colorSizes.color',
                'colorSizes.size'
            ])
            ->findOrFail($id);

        return view('merchant.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('merchant.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock_quantity' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'status' => 'in:active,inactive',
        ], [
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.',
            'image.max' => 'The image size must not exceed 2MB.',
            'name.required' => 'Product name is required.',
            'description.required' => 'Product description is required.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Product price must be a valid number.',
            'price.min' => 'Product price must be greater than or equal to 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'sku.unique' => 'This SKU is already in use.',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Handle image upload with enhanced validation and error handling
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');

                // Additional server-side validation
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The uploaded image file is corrupted or invalid.']);
                }

                // Validate file size (additional check)
                if ($image->getSize() > 2048 * 1024) { // 2MB in bytes
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image size must not exceed 2MB.']);
                }

                // Validate MIME type (additional check)
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!in_array($image->getMimeType(), $allowedMimes)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'The image must be a file of type: JPEG, PNG, JPG, GIF.']);
                }

                // Store old image path for cleanup
                $oldImagePath = $product->getRawImagePath();

                // Generate unique filename
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Store the new image
                $imagePath = $image->storeAs('products', $imageName, 'public');

                if (!$imagePath) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Failed to upload image. Please try again.']);
                }

                $data['image'] = $imagePath;

                // Automatically sync the uploaded image to public/storage
                ImageHelper::syncUploadedImage($imagePath);

                // Delete old image only after successful upload
                if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                    // Also delete from public/storage if it exists
                    $oldPublicPath = public_path('storage/' . $oldImagePath);
                    if (file_exists($oldPublicPath)) {
                        unlink($oldPublicPath);
                    }
                }

            } catch (\Exception $e) {
                \Log::error('Product image upload failed: ' . $e->getMessage());
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' => 'Failed to upload image. Please try again.']);
            }
        }

        try {
            $product->update($data);
            return redirect()->route('merchant.products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Product update failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Failed to update product. Please try again.']);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy($id)
    {
        $product = Product::where('user_id', Auth::id())->findOrFail($id);

        // Image cleanup is handled automatically by the Product model's booted method
        $product->delete();

        return redirect()->route('merchant.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
