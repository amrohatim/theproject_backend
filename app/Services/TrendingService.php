<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Provider;
use App\Models\Product;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TrendingService
{
    /**
     * Increment view count for a category.
     *
     * @param  int  $categoryId
     * @return void
     */
    public function incrementCategoryView($categoryId)
    {
        try {
            $category = Category::find($categoryId);
            if ($category) {
                $category->increment('view_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing category view: ' . $e->getMessage());
        }
    }

    /**
     * Increment purchase count for a category.
     *
     * @param  int  $categoryId
     * @return void
     */
    public function incrementCategoryPurchase($categoryId)
    {
        try {
            $category = Category::find($categoryId);
            if ($category) {
                $category->increment('purchase_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing category purchase: ' . $e->getMessage());
        }
    }

    /**
     * Calculate trending scores for all categories.
     *
     * @return void
     */
    public function calculateTrendingScores()
    {
        try {
            $categories = Category::all();

            foreach ($categories as $category) {
                // Calculate trending score based on views and purchases
                // with more weight given to recent activity
                $viewWeight = 1;
                $purchaseWeight = 5;

                $trendingScore = ($category->view_count * $viewWeight) +
                                ($category->purchase_count * $purchaseWeight);

                // Apply time decay factor (optional)
                // This reduces the score for older activity

                $category->trending_score = $trendingScore;
                $category->last_trending_calculation = Carbon::now();
                $category->save();
            }
        } catch (\Exception $e) {
            Log::error('Error calculating trending scores: ' . $e->getMessage());
        }
    }

    /**
     * Increment view count for a vendor.
     *
     * @param  int  $companyId
     * @return void
     */
    public function incrementVendorView($companyId)
    {
        try {
            $company = Company::find($companyId);
            if ($company) {
                $company->increment('view_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing vendor view: ' . $e->getMessage());
        }
    }

    /**
     * Increment order count for a vendor.
     *
     * @param  int  $companyId
     * @return void
     */
    public function incrementVendorOrder($companyId)
    {
        try {
            $company = Company::find($companyId);
            if ($company) {
                $company->increment('order_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing vendor order: ' . $e->getMessage());
        }
    }

    /**
     * Add rating for a vendor.
     *
     * @param  int  $companyId
     * @param  float  $rating
     * @return void
     */
    public function addVendorRating($companyId, $rating)
    {
        try {
            $company = Company::find($companyId);
            if ($company) {
                $totalRating = ($company->average_rating * $company->rating_count) + $rating;
                $company->rating_count += 1;
                $company->average_rating = $totalRating / $company->rating_count;
                $company->save();
            }
        } catch (\Exception $e) {
            Log::error('Error adding vendor rating: ' . $e->getMessage());
        }
    }

    /**
     * Calculate vendor scores.
     *
     * @return void
     */
    public function calculateVendorScores()
    {
        try {
            $companies = Company::all();

            foreach ($companies as $company) {
                // Calculate vendor score based on views, orders, and ratings
                $viewWeight = 1;
                $orderWeight = 10;
                $ratingWeight = 5;

                $vendorScore = ($company->view_count * $viewWeight) +
                            ($company->order_count * $orderWeight) +
                            ($company->average_rating * $ratingWeight * $company->rating_count);

                $company->vendor_score = $vendorScore;
                $company->last_score_calculation = Carbon::now();
                $company->save();
            }
        } catch (\Exception $e) {
            Log::error('Error calculating vendor scores: ' . $e->getMessage());
        }
    }

    /**
     * Increment view count for a branch.
     *
     * @param  int  $branchId
     * @return void
     */
    public function incrementBranchView($branchId)
    {
        try {
            $branch = Branch::find($branchId);
            if ($branch) {
                $branch->increment('view_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing branch view: ' . $e->getMessage());
        }
    }

    /**
     * Increment order count for a branch.
     *
     * @param  int  $branchId
     * @return void
     */
    public function incrementBranchOrder($branchId)
    {
        try {
            $branch = Branch::find($branchId);
            if ($branch) {
                $branch->increment('order_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing branch order: ' . $e->getMessage());
        }
    }

    /**
     * Calculate branch popularity scores.
     *
     * @return void
     */
    public function calculateBranchPopularityScores()
    {
        try {
            $branches = Branch::all();

            foreach ($branches as $branch) {
                // Calculate popularity score based on views and orders
                $viewWeight = 1;
                $orderWeight = 5;
                $ratingWeight = 10;

                $popularityScore = ($branch->view_count * $viewWeight) +
                                ($branch->order_count * $orderWeight);

                // Add rating component if rating exists
                if ($branch->rating) {
                    $popularityScore += ($branch->rating * $ratingWeight);
                }

                $branch->popularity_score = $popularityScore;
                $branch->last_score_calculation = Carbon::now();
                $branch->save();
            }
        } catch (\Exception $e) {
            Log::error('Error calculating branch popularity scores: ' . $e->getMessage());
        }
    }

    /**
     * Increment view count for a provider.
     *
     * @param  int  $providerId
     * @return void
     */
    public function incrementProviderView($providerId)
    {
        try {
            $provider = Provider::find($providerId);
            if ($provider) {
                $provider->increment('view_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing provider view: ' . $e->getMessage());
        }
    }

    /**
     * Increment order count for a provider.
     *
     * @param  int  $providerId
     * @return void
     */
    public function incrementProviderOrder($providerId)
    {
        try {
            $provider = Provider::find($providerId);
            if ($provider) {
                $provider->increment('order_count');
            }
        } catch (\Exception $e) {
            Log::error('Error incrementing provider order: ' . $e->getMessage());
        }
    }

    /**
     * Calculate provider scores.
     *
     * @return void
     */
    public function calculateProviderScores()
    {
        try {
            $providers = Provider::all();

            foreach ($providers as $provider) {
                // Calculate provider score based on views, orders, and ratings
                $viewWeight = 1;
                $orderWeight = 10;
                $ratingWeight = 5;

                $providerScore = ($provider->view_count * $viewWeight) +
                               ($provider->order_count * $orderWeight) +
                               ($provider->average_rating * $ratingWeight * $provider->total_ratings);

                $provider->provider_score = $providerScore;
                $provider->last_score_calculation = Carbon::now();
                $provider->save();
            }
        } catch (\Exception $e) {
            Log::error('Error calculating provider scores: ' . $e->getMessage());
        }
    }
}
