<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductColorSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockManagementService
{
    /**
     * Reduce stock for a product and its variations.
     *
     * @param int $productId
     * @param int $quantity
     * @param int|null $colorId
     * @param int|null $sizeId
     * @return bool
     * @throws \Exception
     */
    public function reduceStock($productId, $quantity, $colorId = null, $sizeId = null)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($productId);
            
            // Check if we have enough general stock
            if ($product->stock < $quantity) {
                throw new \Exception("Insufficient stock for product {$productId}. Available: {$product->stock}, Requested: {$quantity}");
            }

            // Reduce general product stock
            $product->decrement('stock', $quantity);
            Log::info("Reduced general stock for product {$productId} by {$quantity}. New stock: " . ($product->stock - $quantity));

            // If color is specified, reduce color stock
            if ($colorId) {
                $productColor = ProductColor::where('product_id', $productId)
                    ->where('id', $colorId)
                    ->first();

                if ($productColor) {
                    // Check if we have enough color stock
                    if ($productColor->stock < $quantity) {
                        throw new \Exception("Insufficient color stock for product {$productId}, color {$colorId}. Available: {$productColor->stock}, Requested: {$quantity}");
                    }

                    $productColor->decrement('stock', $quantity);
                    Log::info("Reduced color stock for product {$productId}, color {$colorId} by {$quantity}. New stock: " . ($productColor->stock - $quantity));

                    // If size is also specified, reduce color-size combination stock
                    if ($sizeId) {
                        $productColorSize = ProductColorSize::where('product_id', $productId)
                            ->where('product_color_id', $colorId)
                            ->where('product_size_id', $sizeId)
                            ->first();

                        if ($productColorSize) {
                            // Check if we have enough color-size stock
                            if ($productColorSize->stock < $quantity) {
                                throw new \Exception("Insufficient color-size stock for product {$productId}, color {$colorId}, size {$sizeId}. Available: {$productColorSize->stock}, Requested: {$quantity}");
                            }

                            $productColorSize->decrement('stock', $quantity);
                            Log::info("Reduced color-size stock for product {$productId}, color {$colorId}, size {$sizeId} by {$quantity}. New stock: " . ($productColorSize->stock - $quantity));

                            // Mark as unavailable if stock reaches zero
                            if ($productColorSize->stock <= 0) {
                                $productColorSize->update(['is_available' => false]);
                                Log::info("Marked color-size combination as unavailable for product {$productId}, color {$colorId}, size {$sizeId}");
                            }
                        } else {
                            Log::warning("ProductColorSize not found for product {$productId}, color {$colorId}, size {$sizeId}");
                        }
                    }
                } else {
                    Log::warning("ProductColor not found for product {$productId}, color {$colorId}");
                }
            }

            // Mark product as unavailable if general stock reaches zero
            if ($product->stock <= 0) {
                $product->update(['is_available' => false]);
                Log::info("Marked product {$productId} as unavailable due to zero stock");
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error reducing stock: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Increase stock for a product and its variations (for order cancellations).
     *
     * @param int $productId
     * @param int $quantity
     * @param int|null $colorId
     * @param int|null $sizeId
     * @return bool
     * @throws \Exception
     */
    public function increaseStock($productId, $quantity, $colorId = null, $sizeId = null)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($productId);
            
            // Increase general product stock
            $product->increment('stock', $quantity);
            Log::info("Increased general stock for product {$productId} by {$quantity}. New stock: " . ($product->stock + $quantity));

            // Mark product as available if it was unavailable
            if (!$product->is_available) {
                $product->update(['is_available' => true]);
                Log::info("Marked product {$productId} as available");
            }

            // If color is specified, increase color stock
            if ($colorId) {
                $productColor = ProductColor::where('product_id', $productId)
                    ->where('id', $colorId)
                    ->first();

                if ($productColor) {
                    $productColor->increment('stock', $quantity);
                    Log::info("Increased color stock for product {$productId}, color {$colorId} by {$quantity}. New stock: " . ($productColor->stock + $quantity));

                    // If size is also specified, increase color-size combination stock
                    if ($sizeId) {
                        $productColorSize = ProductColorSize::where('product_id', $productId)
                            ->where('product_color_id', $colorId)
                            ->where('product_size_id', $sizeId)
                            ->first();

                        if ($productColorSize) {
                            $productColorSize->increment('stock', $quantity);
                            Log::info("Increased color-size stock for product {$productId}, color {$colorId}, size {$sizeId} by {$quantity}. New stock: " . ($productColorSize->stock + $quantity));

                            // Mark as available if it was unavailable
                            if (!$productColorSize->is_available) {
                                $productColorSize->update(['is_available' => true]);
                                Log::info("Marked color-size combination as available for product {$productId}, color {$colorId}, size {$sizeId}");
                            }
                        }
                    }
                }
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error increasing stock: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if sufficient stock is available for a product and its variations.
     *
     * @param int $productId
     * @param int $quantity
     * @param int|null $colorId
     * @param int|null $sizeId
     * @return bool
     */
    public function checkStockAvailability($productId, $quantity, $colorId = null, $sizeId = null)
    {
        try {
            $product = Product::findOrFail($productId);
            
            // Check general product stock
            if ($product->stock < $quantity || !$product->is_available) {
                return false;
            }

            // If color is specified, check color stock
            if ($colorId) {
                $productColor = ProductColor::where('product_id', $productId)
                    ->where('id', $colorId)
                    ->first();

                if (!$productColor || $productColor->stock < $quantity) {
                    return false;
                }

                // If size is also specified, check color-size combination stock
                if ($sizeId) {
                    $productColorSize = ProductColorSize::where('product_id', $productId)
                        ->where('product_color_id', $colorId)
                        ->where('product_size_id', $sizeId)
                        ->first();

                    if (!$productColorSize || $productColorSize->stock < $quantity || !$productColorSize->is_available) {
                        return false;
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Error checking stock availability: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available stock for a product and its variations.
     *
     * @param int $productId
     * @param int|null $colorId
     * @param int|null $sizeId
     * @return int
     */
    public function getAvailableStock($productId, $colorId = null, $sizeId = null)
    {
        try {
            $product = Product::findOrFail($productId);
            
            // If no variations specified, return general stock
            if (!$colorId && !$sizeId) {
                return $product->is_available ? $product->stock : 0;
            }

            // If only color specified
            if ($colorId && !$sizeId) {
                $productColor = ProductColor::where('product_id', $productId)
                    ->where('id', $colorId)
                    ->first();

                return $productColor ? $productColor->stock : 0;
            }

            // If both color and size specified
            if ($colorId && $sizeId) {
                $productColorSize = ProductColorSize::where('product_id', $productId)
                    ->where('product_color_id', $colorId)
                    ->where('product_size_id', $sizeId)
                    ->first();

                return ($productColorSize && $productColorSize->is_available) ? $productColorSize->stock : 0;
            }

            return 0;

        } catch (\Exception $e) {
            Log::error("Error getting available stock: " . $e->getMessage());
            return 0;
        }
    }
}