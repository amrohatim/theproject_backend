<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Product;

class ProductDealService
{
    /**
     * Get all active deals for a product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Support\Collection
     */
    public function getActiveDealsForProduct(Product $product)
    {
        // Get current date
        $today = now()->format('Y-m-d');

        $deals = collect();

        // Handle individual merchant products (no branch/company)
        if (!$product->branch_id && $product->user_id) {
            // Individual merchant product - look for deals by the product owner
            $deals = Deal::where('user_id', $product->user_id)
                ->where('status', 'active')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->get();
        }
        // Handle company-based products
        elseif ($product->branch && $product->branch->company) {
            $companyOwnerId = $product->branch->company->user_id;

            // Get all user IDs in the same company (includes company owner, products managers, etc.)
            $companyUserIds = Deal::getCompanyUserIds($companyOwnerId);

            // Find active deals from any user in this company
            $deals = Deal::whereIn('user_id', $companyUserIds)
                ->where('status', 'active')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->get();
        }
        // No valid owner found
        else {
            return collect(); // Return empty collection if no valid owner
        }

        // Filter deals based on application scope
        $applicableDeals = $deals->filter(function ($deal) use ($product) {
            // Deal applies to all products
            if ($deal->applies_to === 'all') {
                return true;
            }

            // Deal applies to specific products
            if ($deal->applies_to === 'products') {
                $productIds = is_string($deal->product_ids)
                    ? json_decode($deal->product_ids, true)
                    : $deal->product_ids;

                return in_array($product->id, $productIds ?: []);
            }

            // Deal applies to both products and services
            if ($deal->applies_to === 'products_and_services') {
                $productIds = is_string($deal->product_ids)
                    ? json_decode($deal->product_ids, true)
                    : $deal->product_ids;

                return in_array($product->id, $productIds ?: []);
            }

            // Deal applies to specific categories (check if product category matches)
            if ($deal->applies_to === 'categories') {
                $categoryIds = is_string($deal->category_ids)
                    ? json_decode($deal->category_ids, true)
                    : $deal->category_ids;

                return in_array($product->category_id, $categoryIds ?: []);
            }

            return false;
        });

        return $applicableDeals;
    }

    /**
     * Get the best deal for a product.
     *
     * @param  \App\Models\Product  $product
     * @return \App\Models\Deal|null
     */
    public function getBestDealForProduct(Product $product)
    {
        $deals = $this->getActiveDealsForProduct($product);

        if ($deals->isEmpty()) {
            return null;
        }

        // Return the deal with the highest discount percentage
        return $deals->sortByDesc('discount_percentage')->first();
    }

    /**
     * Calculate the discounted price for a product.
     *
     * @param  \App\Models\Product  $product
     * @return array
     */
    public function calculateDiscountedPrice(Product $product)
    {
        $bestDeal = $this->getBestDealForProduct($product);

        // If the product already has an original_price set in the database, use that
        $originalPrice = $product->original_price ?? $product->price;

        // Check if the product already has a base discount
        $hasBaseDiscount = $originalPrice > $product->price;
        $basePrice = $product->price; // Current price before applying deal discount

        if (!$bestDeal) {
            // No deal, but might still have a base discount
            if ($hasBaseDiscount) {
                // Calculate the base discount percentage
                $baseDiscountPercentage = (($originalPrice - $basePrice) / $originalPrice) * 100;

                return [
                    'original_price' => $originalPrice,
                    'discounted_price' => $basePrice,
                    'discount_percentage' => round($baseDiscountPercentage, 2),
                    'discount_amount' => $originalPrice - $basePrice,
                    'has_discount' => true,
                    'deal' => null,
                ];
            }

            return [
                'original_price' => $originalPrice,
                'discounted_price' => $basePrice,
                'discount_percentage' => 0,
                'discount_amount' => 0,
                'has_discount' => false,
                'deal' => null,
            ];
        }

        // Apply deal discount to the current price (which might already be discounted)
        $dealDiscountPercentage = $bestDeal->discount_percentage;
        $dealDiscountAmount = $basePrice * ($dealDiscountPercentage / 100);
        $finalPrice = $basePrice - $dealDiscountAmount;

        // Calculate the total effective discount percentage relative to the original price
        $totalDiscountAmount = $originalPrice - $finalPrice;
        $totalDiscountPercentage = ($totalDiscountAmount / $originalPrice) * 100;

        return [
            'original_price' => $originalPrice,
            'discounted_price' => $finalPrice,
            'discount_percentage' => round($totalDiscountPercentage, 2), // Total effective discount
            'discount_amount' => $totalDiscountAmount,
            'has_discount' => true,
            'deal' => $bestDeal,
            'base_discount_percentage' => $hasBaseDiscount ? round((($originalPrice - $basePrice) / $originalPrice) * 100, 2) : 0,
            'deal_discount_percentage' => $dealDiscountPercentage,
        ];
    }

    /**
     * Get all products with active deals.
     *
     * @param  int|null  $limit
     * @return \Illuminate\Support\Collection
     */
    public function getProductsWithActiveDeals($limit = null)
    {
        // Get current date
        $today = now()->format('Y-m-d');

        // Get all active deals
        $activeDeals = Deal::where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        // Get products with deals
        $productsWithDeals = collect();

        foreach ($activeDeals as $deal) {
            $products = collect();

            // Deal applies to specific products
            if ($deal->applies_to === 'products') {
                $productIds = is_string($deal->product_ids)
                    ? json_decode($deal->product_ids, true)
                    : $deal->product_ids;

                if (!empty($productIds)) {
                    $products = Product::whereIn('id', $productIds)->get();
                }
            }

            // Add products to collection with deal information
            foreach ($products as $product) {
                // Use the calculateDiscountedPrice method to ensure consistent discount calculation
                $dealInfo = $this->calculateDiscountedPrice($product);

                // Apply the calculated discount information to the product
                $product->deal = $deal;
                $product->discount_percentage = $dealInfo['discount_percentage'];
                $product->discounted_price = $dealInfo['discounted_price'];
                $product->original_price = $dealInfo['original_price'];
                $product->has_discount = $dealInfo['has_discount'];
                $product->discount_amount = $dealInfo['discount_amount'];

                // Add additional discount information for debugging and UI display
                if (isset($dealInfo['base_discount_percentage'])) {
                    $product->base_discount_percentage = $dealInfo['base_discount_percentage'];
                }
                if (isset($dealInfo['deal_discount_percentage'])) {
                    $product->deal_discount_percentage = $dealInfo['deal_discount_percentage'];
                }

                $productsWithDeals->push($product);
            }
        }

        // Remove duplicates and get the best deal for each product
        $uniqueProducts = $productsWithDeals->unique('id')->values();

        // Sort by discount percentage (highest first)
        $sortedProducts = $uniqueProducts->sortByDesc('discount_percentage')->values();

        // Limit results if specified
        if ($limit) {
            $sortedProducts = $sortedProducts->take($limit);
        }

        return $sortedProducts;
    }
}
