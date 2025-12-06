<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductColorSize;
use App\Models\ProductSize;
use App\Models\ProductsManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSizeController extends Controller
{
    /**
     * Get company id for current user (vendor or products manager).
     */
    private function getCompanyIdForUser(): ?int
    {
        $user = Auth::user();
        if ($user->role === 'products_manager') {
            $pm = ProductsManager::where('user_id', $user->id)->first();
            return $pm ? $pm->company_id : null;
        }

        return $user->company_id;
    }

    /**
     * Get the acting vendor user ID (supports both vendor and products_manager roles).
     */
    private function getActingVendorUserId(): int
    {
        $user = Auth::user();
        
        if ($user->role === 'products_manager') {
            // Products manager acts on behalf of their company's vendor
            return $user->productsManager->company->user_id;
        }
        
        return $user->id;
    }

    /**
     * Store a newly created size for a product.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'color_id' => 'nullable|exists:product_colors,id',
            'category' => 'nullable|string|exists:size_categories,name',
            'additional_info' => 'nullable|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'display_order' => 'nullable|integer',
        ]);

        // Verify the product belongs to the authenticated vendor/products manager company
        $product = Product::findOrFail($request->product_id);

        $user = Auth::user();
        if ($user->role === 'products_manager') {
            $productsManager = ProductsManager::where('user_id', $user->id)->first();
            if (!$productsManager) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied - products manager record not found.'
                ], 403);
            }
            $companyId = $productsManager->company_id;
        } else {
            $companyId = $user->company_id;
        }

        $branch = Branch::where('id', $product->branch_id)
            ->where('company_id', $companyId)
            ->first();

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or access denied.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            $sizeCategoryId = null;
            if ($request->filled('category')) {
                $sizeCategoryId = \App\Models\SizeCategory::where('name', $request->category)->value('id');
            }
            if (!$sizeCategoryId) {
                $sizeCategoryId = \App\Models\SizeCategory::where('name', 'clothes')->value('id');
            }

            // Create the size
            $size = ProductSize::create([
                'product_id' => $product->id,
                'size_category_id' => $sizeCategoryId,
                'name' => $request->name,
                'value' => $request->value,
                'additional_info' => $request->additional_info,
                'stock' => $request->stock,
                'price_adjustment' => $request->price_adjustment ?? 0,
                'display_order' => $request->display_order ?? 0,
            ]);

            // If color_id provided, create combination
            if ($request->filled('color_id')) {
                ProductColorSize::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'product_color_id' => $request->color_id,
                        'product_size_id' => $size->id,
                    ],
                    [
                        'stock' => $request->stock,
                        'price_adjustment' => $request->price_adjustment ?? 0,
                        'is_available' => true,
                    ]
                );
            }

            DB::commit();

            Log::info('Vendor size created successfully', [
                'size_id' => $size->id,
                'product_id' => $product->id,
                'vendor_id' => $this->getActingVendorUserId(),
                'size_name' => $size->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Size saved successfully',
                'size' => $size
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create vendor size', [
                'product_id' => $request->product_id,
                'vendor_id' => $this->getActingVendorUserId(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save size: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified size.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'size_id' => 'required|exists:product_sizes,id',
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price_adjustment' => 'nullable|numeric',
            'display_order' => 'nullable|integer',
        ]);

        $size = ProductSize::findOrFail($request->size_id);

        // Verify the size belongs to a product owned by the authenticated vendor or managed by products manager
        $product = Product::findOrFail($size->product_id);

        // Get the company ID based on user role
        $user = Auth::user();
        if ($user->role === 'products_manager') {
            // For products manager, get company ID from products_managers table
            $productsManager = ProductsManager::where('user_id', $user->id)->first();
            if (!$productsManager) {
                Log::warning('ProductsManager record not found for user', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied - ProductsManager record not found'
                ], 403);
            }
            $companyId = $productsManager->company_id;
        } else {
            // For vendor, get company ID from user record
            $companyId = $user->company_id;
        }

        // Verify the product belongs to a branch owned by the vendor's company
        $branch = Branch::where('id', $product->branch_id)
                       ->where('company_id', $companyId)
                       ->first();

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or access denied'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Debug logging
            Log::info('Size update attempt', [
                'size_id' => $size->id,
                'current_stock' => $size->stock,
                'new_stock' => $request->stock,
                'request_data' => $request->all()
            ]);

            // Update the base size information
            $updateResult = $size->update([
                'name' => $request->name,
                'value' => $request->value,
                'stock' => $request->stock,
                'price_adjustment' => $request->price_adjustment ?? 0,
                'display_order' => $request->display_order ?? $size->display_order,
            ]);

            // CRITICAL FIX: Also update the corresponding ProductColorSize records
            // This ensures data consistency between product_sizes and product_color_sizes tables
            $colorSizeUpdates = \App\Models\ProductColorSize::where('product_size_id', $size->id)->update([
                'stock' => $request->stock,
                'price_adjustment' => $request->price_adjustment ?? 0,
            ]);

            Log::info('Size update result', [
                'size_id' => $size->id,
                'update_result' => $updateResult,
                'color_size_updates' => $colorSizeUpdates,
                'stock_after_update' => $size->fresh()->stock
            ]);

            DB::commit();

            Log::info('Vendor size updated successfully', [
                'size_id' => $size->id,
                'product_id' => $product->id,
                'vendor_id' => $this->getActingVendorUserId(),
                'size_name' => $size->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Size updated successfully',
                'size' => $size
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update vendor size', [
                'size_id' => $request->size_id,
                'vendor_id' => $this->getActingVendorUserId(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update size: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified size.
     */
    public function destroy(string $id): JsonResponse
    {
        $size = ProductSize::findOrFail($id);
        
        // Verify the size belongs to a product owned by the authenticated vendor or managed by products manager
        $product = Product::findOrFail($size->product_id);

        $companyId = $this->getCompanyIdForUser();
        $branch = Branch::where('id', $product->branch_id)
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->first();

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or access denied.'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Delete the size
            $size->delete();

            DB::commit();

            Log::info('Vendor size deleted successfully', [
                'size_id' => $size->id,
                'product_id' => $product->id,
                'vendor_id' => $this->getActingVendorUserId(),
                'size_name' => $size->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Size deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete vendor size', [
                'size_id' => $id,
                'vendor_id' => $this->getActingVendorUserId(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete size: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Destroy a size using a request payload (for POST-based delete endpoints).
     */
    public function destroyFromRequest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'size_id' => 'required|exists:product_sizes,id',
        ]);

        return $this->destroy((string) $data['size_id']);
    }
}
