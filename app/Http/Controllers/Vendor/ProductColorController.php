<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductColorController extends Controller
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
     * Store a newly created color for a product.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:10',
            'price_adjustment' => 'nullable|numeric',
            'stock' => 'required|integer|min:0',
            'display_order' => 'nullable|integer',
            'is_default' => 'nullable|boolean',
        ]);

        // Verify the product belongs to the authenticated vendor or managed by products manager
        $product = Product::where('user_id', $this->getActingVendorUserId())->findOrFail($request->product_id);

        try {
            DB::beginTransaction();

            // Create the color
            $color = ProductColor::create([
                'product_id' => $product->id,
                'name' => $request->name,
                'color_code' => $request->color_code ?? '#000000',
                'price_adjustment' => $request->price_adjustment ?? 0,
                'stock' => $request->stock,
                'display_order' => $request->display_order ?? 0,
                'is_default' => $request->is_default ?? false,
                'image' => null, // Image will be handled separately if needed
            ]);

            DB::commit();

            Log::info('Vendor color created successfully', [
                'color_id' => $color->id,
                'product_id' => $product->id,
                'vendor_id' => $this->getActingVendorUserId(),
                'color_name' => $color->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Color saved successfully',
                'color' => $color
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create vendor color', [
                'product_id' => $request->product_id,
                'vendor_id' => $this->getActingVendorUserId(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save color: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified color.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'color_id' => 'required|exists:product_colors,id',
            'name' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:10',
            'price_adjustment' => 'nullable|numeric',
            'stock' => 'required|integer|min:0',
            'display_order' => 'nullable|integer',
            'is_default' => 'nullable|boolean',
        ]);

        $color = ProductColor::findOrFail($request->color_id);
        
        // Verify the color belongs to a product owned by the authenticated vendor or managed by products manager
        $product = Product::where('user_id', $this->getActingVendorUserId())->findOrFail($color->product_id);

        try {
            DB::beginTransaction();

            $color->update([
                'name' => $request->name,
                'color_code' => $request->color_code ?? $color->color_code,
                'price_adjustment' => $request->price_adjustment ?? 0,
                'stock' => $request->stock,
                'display_order' => $request->display_order ?? $color->display_order,
                'is_default' => $request->is_default ?? false,
            ]);

            DB::commit();

            Log::info('Vendor color updated successfully', [
                'color_id' => $color->id,
                'product_id' => $product->id,
                'vendor_id' => $this->getActingVendorUserId(),
                'color_name' => $color->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Color updated successfully',
                'color' => $color
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to update vendor color', [
                'color_id' => $request->color_id,
                'vendor_id' => $this->getActingVendorUserId(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update color: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified color.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'color_id' => 'required|exists:product_colors,id',
        ]);

        $color = ProductColor::findOrFail($request->color_id);
        
        // Verify the color belongs to a product owned by the authenticated vendor or managed by products manager
        $product = Product::where('user_id', $this->getActingVendorUserId())->findOrFail($color->product_id);

        try {
            DB::beginTransaction();

            // Delete associated color-size combinations
            $color->colorSizes()->delete();
            
            // Delete the color
            $color->delete();

            DB::commit();

            Log::info('Vendor color deleted successfully', [
                'color_id' => $color->id,
                'product_id' => $product->id,
                'vendor_id' => $this->getActingVendorUserId(),
                'color_name' => $color->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Color deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to delete vendor color', [
                'color_id' => $request->color_id,
                'vendor_id' => $this->getActingVendorUserId(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete color: ' . $e->getMessage()
            ], 500);
        }
    }
}
