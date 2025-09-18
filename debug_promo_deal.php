<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Deal;
use App\Models\Product;
use App\Services\ProductDealService;

echo "=== DEBUG: PROMO DEAL & PRPJAPS PRODUCT ===\n\n";

try {
    // 1. Find the "PROMO" deal
    echo "1. SEARCHING FOR 'PROMO' DEAL:\n";
    $promoDeal = Deal::where('title', 'PROMO')->first();
    
    if (!$promoDeal) {
        echo "❌ Deal 'PROMO' not found in database\n";
        echo "   Searching for deals with similar names...\n";
        $similarDeals = Deal::where('title', 'like', '%PROMO%')->get();
        if ($similarDeals->count() > 0) {
            echo "   Found similar deals:\n";
            foreach ($similarDeals as $deal) {
                echo "   - ID: {$deal->id}, Title: '{$deal->title}', Status: {$deal->status}\n";
            }
        } else {
            echo "   No deals found with 'PROMO' in the title\n";
        }
        
        // Show all active deals
        echo "\n   All active deals:\n";
        $activeDeals = Deal::where('status', 'active')->get();
        foreach ($activeDeals as $deal) {
            echo "   - ID: {$deal->id}, Title: '{$deal->title}', Discount: {$deal->discount_percentage}%\n";
        }
    } else {
        echo "✅ Found PROMO deal:\n";
        echo "   - ID: {$promoDeal->id}\n";
        echo "   - Title: {$promoDeal->title}\n";
        echo "   - Discount: {$promoDeal->discount_percentage}%\n";
        echo "   - Status: {$promoDeal->status}\n";
        echo "   - Start Date: {$promoDeal->start_date}\n";
        echo "   - End Date: {$promoDeal->end_date}\n";
        echo "   - Applies To: {$promoDeal->applies_to}\n";
        echo "   - Product IDs: " . json_encode($promoDeal->product_ids) . "\n";
        echo "   - User ID: {$promoDeal->user_id}\n";
    }

    echo "\n2. SEARCHING FOR 'PRPJAPS' PRODUCT:\n";
    $prpjapsProduct = Product::where('name', 'PRPJAPS')->first();
    
    if (!$prpjapsProduct) {
        echo "❌ Product 'PRPJAPS' not found in database\n";
        echo "   Searching for products with similar names...\n";
        $similarProducts = Product::where('name', 'like', '%PRPJAPS%')->get();
        if ($similarProducts->count() > 0) {
            echo "   Found similar products:\n";
            foreach ($similarProducts as $product) {
                echo "   - ID: {$product->id}, Name: '{$product->name}', Price: \${$product->price}\n";
            }
        } else {
            echo "   No products found with 'PRPJAPS' in the name\n";
        }
        
        // Show some sample products
        echo "\n   Sample products:\n";
        $sampleProducts = Product::take(5)->get();
        foreach ($sampleProducts as $product) {
            echo "   - ID: {$product->id}, Name: '{$product->name}', Price: \${$product->price}\n";
        }
    } else {
        echo "✅ Found PRPJAPS product:\n";
        echo "   - ID: {$prpjapsProduct->id}\n";
        echo "   - Name: {$prpjapsProduct->name}\n";
        echo "   - Price: \${$prpjapsProduct->price}\n";
        echo "   - Original Price: " . ($prpjapsProduct->original_price ? "\${$prpjapsProduct->original_price}" : "null") . "\n";
        echo "   - Category ID: {$prpjapsProduct->category_id}\n";
        echo "   - Branch ID: {$prpjapsProduct->branch_id}\n";
        echo "   - Is Available: " . ($prpjapsProduct->is_available ? "Yes" : "No") . "\n";
        
        // Load relationships
        $prpjapsProduct->load(['branch.company', 'category']);
        echo "   - Branch: " . ($prpjapsProduct->branch ? $prpjapsProduct->branch->name : "No branch") . "\n";
        echo "   - Company User ID: " . ($prpjapsProduct->branch && $prpjapsProduct->branch->company ? $prpjapsProduct->branch->company->user_id : "No company") . "\n";
    }

    // 3. Test deal application if both exist
    if ($promoDeal && $prpjapsProduct) {
        echo "\n3. TESTING DEAL APPLICATION:\n";
        
        // Check if deal should apply to this product
        echo "   Checking deal applicability:\n";
        echo "   - Deal applies to: {$promoDeal->applies_to}\n";
        echo "   - Deal user ID: {$promoDeal->user_id}\n";
        echo "   - Product company user ID: " . ($prpjapsProduct->branch && $prpjapsProduct->branch->company ? $prpjapsProduct->branch->company->user_id : "No company") . "\n";
        
        if ($promoDeal->applies_to === 'products') {
            $productIds = is_array($promoDeal->product_ids) ? $promoDeal->product_ids : json_decode($promoDeal->product_ids, true);
            echo "   - Deal product IDs: " . json_encode($productIds) . "\n";
            echo "   - Product ID in deal: " . (in_array($prpjapsProduct->id, $productIds ?: []) ? "Yes" : "No") . "\n";
        }
        
        // Test ProductDealService
        echo "\n   Testing ProductDealService:\n";
        $dealService = new ProductDealService();
        
        $activeDeals = $dealService->getActiveDealsForProduct($prpjapsProduct);
        echo "   - Active deals found: {$activeDeals->count()}\n";
        
        if ($activeDeals->count() > 0) {
            foreach ($activeDeals as $deal) {
                echo "     * Deal ID: {$deal->id}, Title: '{$deal->title}', Discount: {$deal->discount_percentage}%\n";
            }
        }
        
        $bestDeal = $dealService->getBestDealForProduct($prpjapsProduct);
        echo "   - Best deal: " . ($bestDeal ? "ID {$bestDeal->id} ({$bestDeal->title})" : "None") . "\n";
        
        $dealInfo = $dealService->calculateDiscountedPrice($prpjapsProduct);
        echo "   - Deal calculation result:\n";
        echo "     * Original Price: \${$dealInfo['original_price']}\n";
        echo "     * Discounted Price: \${$dealInfo['discounted_price']}\n";
        echo "     * Discount Percentage: {$dealInfo['discount_percentage']}%\n";
        echo "     * Has Discount: " . ($dealInfo['has_discount'] ? "Yes" : "No") . "\n";
        echo "     * Deal: " . ($dealInfo['deal'] ? "ID {$dealInfo['deal']->id}" : "None") . "\n";
        
        // Expected calculation
        $expectedDiscountedPrice = $prpjapsProduct->price * (1 - ($promoDeal->discount_percentage / 100));
        echo "\n   Expected calculation (22 - (22 × 0.12)):\n";
        echo "   - Expected discounted price: \${$expectedDiscountedPrice}\n";
        echo "   - Actual discounted price: \${$dealInfo['discounted_price']}\n";
        echo "   - Match: " . (abs($expectedDiscountedPrice - $dealInfo['discounted_price']) < 0.01 ? "Yes" : "No") . "\n";
    }

    echo "\n4. TESTING API RESPONSE:\n";
    
    // Simulate API controller logic
    if ($prpjapsProduct) {
        $dealService = new ProductDealService();
        $dealInfo = $dealService->calculateDiscountedPrice($prpjapsProduct);
        
        // Apply deal information to product (like the controller does)
        $prpjapsProduct->has_discount = $dealInfo['has_discount'];
        $prpjapsProduct->original_price = $dealInfo['original_price'];
        $prpjapsProduct->discounted_price = $dealInfo['discounted_price'];
        $prpjapsProduct->discount_percentage = $dealInfo['discount_percentage'];
        $prpjapsProduct->discount_amount = $dealInfo['discount_amount'];
        
        if ($dealInfo['deal']) {
            $prpjapsProduct->deal = $dealInfo['deal'];
        }
        
        echo "   Product after API processing:\n";
        echo "   - has_discount: " . ($prpjapsProduct->has_discount ? "true" : "false") . "\n";
        echo "   - original_price: " . ($prpjapsProduct->original_price ?? "null") . "\n";
        echo "   - discounted_price: " . ($prpjapsProduct->discounted_price ?? "null") . "\n";
        echo "   - discount_percentage: " . ($prpjapsProduct->discount_percentage ?? "null") . "\n";
        echo "   - deal: " . (isset($prpjapsProduct->deal) ? "present" : "null") . "\n";
        
        // Test Flutter model's getDisplayPrice equivalent
        $displayPrice = ($prpjapsProduct->has_discount && $prpjapsProduct->discounted_price !== null) 
            ? $prpjapsProduct->discounted_price 
            : $prpjapsProduct->price;
        echo "   - Display price (what UI should show): \${$displayPrice}\n";
    }

    echo "\n5. INVESTIGATING USER/COMPANY RELATIONSHIPS:\n";

    if ($promoDeal && $prpjapsProduct) {
        // Check user relationships
        $dealUser = \App\Models\User::find($promoDeal->user_id);
        $productCompanyUser = \App\Models\User::find($prpjapsProduct->branch->company->user_id);

        echo "   Deal User (ID: {$promoDeal->user_id}):\n";
        if ($dealUser) {
            echo "   - Name: {$dealUser->name}\n";
            echo "   - Email: {$dealUser->email}\n";
            echo "   - Role: {$dealUser->role}\n";

            // Check if deal user has company relationships
            $dealUser->load(['company', 'productsManager', 'serviceProvider']);
            if ($dealUser->company) {
                echo "   - Company ID: {$dealUser->company->id}\n";
                echo "   - Company Name: {$dealUser->company->name}\n";
            }
            if ($dealUser->productsManager) {
                echo "   - Products Manager Company ID: {$dealUser->productsManager->company_id}\n";
            }
            if ($dealUser->serviceProvider) {
                echo "   - Service Provider Company ID: {$dealUser->serviceProvider->company_id}\n";
            }
        }

        echo "\n   Product Company User (ID: {$prpjapsProduct->branch->company->user_id}):\n";
        if ($productCompanyUser) {
            echo "   - Name: {$productCompanyUser->name}\n";
            echo "   - Email: {$productCompanyUser->email}\n";
            echo "   - Role: {$productCompanyUser->role}\n";
            echo "   - Company ID: {$prpjapsProduct->branch->company->id}\n";
            echo "   - Company Name: {$prpjapsProduct->branch->company->name}\n";
        }

        // Check if they belong to the same company
        $sameCompany = false;
        if ($dealUser && $productCompanyUser) {
            $dealUserCompanyId = null;
            $productUserCompanyId = $prpjapsProduct->branch->company->id;

            if ($dealUser->company) {
                $dealUserCompanyId = $dealUser->company->id;
            } elseif ($dealUser->productsManager) {
                $dealUserCompanyId = $dealUser->productsManager->company_id;
            } elseif ($dealUser->serviceProvider) {
                $dealUserCompanyId = $dealUser->serviceProvider->company_id;
            }

            $sameCompany = $dealUserCompanyId === $productUserCompanyId;
            echo "\n   Same Company: " . ($sameCompany ? "Yes" : "No") . "\n";
            echo "   Deal User Company ID: " . ($dealUserCompanyId ?? "None") . "\n";
            echo "   Product Company ID: {$productUserCompanyId}\n";
        }

        // Test the fixed ProductDealService
        echo "\n6. TESTING FIXED PRODUCTDEALSERVICE:\n";

        $dealService = new ProductDealService();
        $activeDeals = $dealService->getActiveDealsForProduct($prpjapsProduct);
        echo "   - Active deals found (after fix): {$activeDeals->count()}\n";

        if ($activeDeals->count() > 0) {
            foreach ($activeDeals as $deal) {
                echo "     * Deal ID: {$deal->id}, Title: '{$deal->title}', Discount: {$deal->discount_percentage}%\n";
            }

            $dealInfo = $dealService->calculateDiscountedPrice($prpjapsProduct);
            echo "   - Deal calculation result (after fix):\n";
            echo "     * Original Price: \${$dealInfo['original_price']}\n";
            echo "     * Discounted Price: \${$dealInfo['discounted_price']}\n";
            echo "     * Discount Percentage: {$dealInfo['discount_percentage']}%\n";
            echo "     * Has Discount: " . ($dealInfo['has_discount'] ? "Yes" : "No") . "\n";

            // Expected calculation
            $expectedDiscountedPrice = 22 * (1 - (12 / 100));
            echo "\n   Expected vs Actual:\n";
            echo "   - Expected discounted price: \${$expectedDiscountedPrice}\n";
            echo "   - Actual discounted price: \${$dealInfo['discounted_price']}\n";
            echo "   - Match: " . (abs($expectedDiscountedPrice - $dealInfo['discounted_price']) < 0.01 ? "✅ Yes" : "❌ No") . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
