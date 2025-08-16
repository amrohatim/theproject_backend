<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductSizeController extends Controller
{
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
            'price_adjustment' => 'nullable|numeric',
            'display_order' => 'nullable|integer',
        ]);

        // Verify the product belongs to the authenticated vendor or managed by products manager
        $product = Product::where('user_id', $this->getActingVendorUserId())->findOrFail($request->product_id);

        try {
            DB::beginTransaction();

            // Create the size
            $size = ProductSize::create([
                'product_id' => $product->id,
                'name' => $request->name,
                'value' => $request->value,
                'stock' => $request->stock,
                'price_adjustment' => $request->price_adjustment ?? 0,
                'display_order' => $request->display_order ?? 0,
            ]);

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
        $product = Product::where('user_id', $this->getActingVendorUserId())->findOrFail($size->product_id);

        try {
            DB::beginTransaction();

            $size->update([
                'name' => $request->name,
                'value' => $request->value,
                'stock' => $request->stock,
                'price_adjustment' => $request->price_adjustment ?? 0,
                'display_order' => $request->display_order ?? $size->display_order,
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
        $product = Product::where('user_id', $this->getActingVendorUserId())->findOrFail($size->product_id);

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
}
