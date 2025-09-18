<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Deal;
use App\Models\Product;
use App\Services\ProductDealService;

echo "=== DEBUG: INDIVIDUAL MERCHANT DEAL ISSUE ===\n\n";

try {
    // 1. Find the "Test Deal for FIFA" deal
    echo "1. SEARCHING FOR 'Test Deal for FIFA' DEAL:\n";
    $fifaDeal = Deal::where('title', 'Test Deal for FIFA')->first();
    
    if (!$fifaDeal) {
        echo "❌ Deal 'Test Deal for FIFA' not found in database\n";
        echo "   Searching for deals with similar names...\n";
        $similarDeals = Deal::where('title', 'like', '%FIFA%')->get();
        if ($similarDeals->count() > 0) {
            echo "   Found similar deals:\n";
            foreach ($similarDeals as $deal) {
                echo "   - ID: {$deal->id}, Title: '{$deal->title}', Status: {$deal->status}, User: {$deal->user_id}\n";
            }
        } else {
            echo "   No deals found with 'FIFA' in the title\n";
        }
        
        // Show recent deals
        echo "\n   Recent deals:\n";
        $recentDeals = Deal::orderBy('created_at', 'desc')->take(10)->get();
        foreach ($recentDeals as $deal) {
            echo "   - ID: {$deal->id}, Title: '{$deal->title}', Status: {$deal->status}, User: {$deal->user_id}\n";
        }
    } else {
        echo "✅ Found 'Test Deal for FIFA' deal:\n";
        echo "   - ID: {$fifaDeal->id}\n";
        echo "   - Title: {$fifaDeal->title}\n";
        echo "   - Discount: {$fifaDeal->discount_percentage}%\n";
        echo "   - Fixed Amount: " . ($fifaDeal->discount_amount ?? 'null') . "\n";
        echo "   - Status: {$fifaDeal->status}\n";
        echo "   - Start Date: {$fifaDeal->start_date}\n";
        echo "   - End Date: {$fifaDeal->end_date}\n";
        echo "   - Applies To: {$fifaDeal->applies_to}\n";
        echo "   - Product IDs: " . json_encode($fifaDeal->product_ids) . "\n";
        echo "   - User ID: {$fifaDeal->user_id}\n";
        echo "   - Created: {$fifaDeal->created_at}\n";
    }

    // 2. Find the products
    echo "\n2. SEARCHING FOR PRODUCTS 'FIFA EN' AND 'KOORA':\n";
    $fifaProduct = Product::where('name', 'FIFA EN')->first();
    $kooraProduct = Product::where('name', 'KOORA')->first();
    
    if (!$fifaProduct) {
        echo "❌ Product 'FIFA EN' not found\n";
        $similarProducts = Product::where('name', 'like', '%FIFA%')->get();
        if ($similarProducts->count() > 0) {
            echo "   Found similar products:\n";
            foreach ($similarProducts as $product) {
                echo "   - ID: {$product->id}, Name: '{$product->name}', Price: \${$product->price}\n";
            }
        }
    } else {
        echo "✅ Found 'FIFA EN' product:\n";
        echo "   - ID: {$fifaProduct->id}\n";
        echo "   - Name: {$fifaProduct->name}\n";
        echo "   - Price: \${$fifaProduct->price}\n";
        echo "   - Branch ID: " . ($fifaProduct->branch_id ?? 'null') . "\n";
        echo "   - User ID: " . ($fifaProduct->user_id ?? 'null') . "\n";
        
        if ($fifaProduct->branch_id) {
            $fifaProduct->load(['branch.company']);
            echo "   - Branch: " . ($fifaProduct->branch ? $fifaProduct->branch->name : "No branch") . "\n";
            echo "   - Company: " . ($fifaProduct->branch && $fifaProduct->branch->company ? $fifaProduct->branch->company->name : "No company") . "\n";
            echo "   - Company User ID: " . ($fifaProduct->branch && $fifaProduct->branch->company ? $fifaProduct->branch->company->user_id : "No company") . "\n";
        } else {
            echo "   - Individual merchant product (no branch/company)\n";
        }
    }
    
    if (!$kooraProduct) {
        echo "\n❌ Product 'KOORA' not found\n";
        $similarProducts = Product::where('name', 'like', '%KOORA%')->get();
        if ($similarProducts->count() > 0) {
            echo "   Found similar products:\n";
            foreach ($similarProducts as $product) {
                echo "   - ID: {$product->id}, Name: '{$product->name}', Price: \${$product->price}\n";
            }
        }
    } else {
        echo "\n✅ Found 'KOORA' product:\n";
        echo "   - ID: {$kooraProduct->id}\n";
        echo "   - Name: {$kooraProduct->name}\n";
        echo "   - Price: \${$kooraProduct->price}\n";
        echo "   - Branch ID: " . ($kooraProduct->branch_id ?? 'null') . "\n";
        echo "   - User ID: " . ($kooraProduct->user_id ?? 'null') . "\n";
        
        if ($kooraProduct->branch_id) {
            $kooraProduct->load(['branch.company']);
            echo "   - Branch: " . ($kooraProduct->branch ? $kooraProduct->branch->name : "No branch") . "\n";
            echo "   - Company: " . ($kooraProduct->branch && $kooraProduct->branch->company ? $kooraProduct->branch->company->name : "No company") . "\n";
            echo "   - Company User ID: " . ($kooraProduct->branch && $kooraProduct->branch->company ? $kooraProduct->branch->company->user_id : "No company") . "\n";
        } else {
            echo "   - Individual merchant product (no branch/company)\n";
        }
    }

    // 3. Check deal-product associations
    if ($fifaDeal && ($fifaProduct || $kooraProduct)) {
        echo "\n3. CHECKING DEAL-PRODUCT ASSOCIATIONS:\n";
        
        $dealProductIds = is_array($fifaDeal->product_ids) ? $fifaDeal->product_ids : json_decode($fifaDeal->product_ids, true);
        echo "   Deal product IDs: " . json_encode($dealProductIds) . "\n";
        
        if ($fifaProduct) {
            $fifaInDeal = in_array($fifaProduct->id, $dealProductIds ?: []);
            echo "   - FIFA EN (ID: {$fifaProduct->id}) in deal: " . ($fifaInDeal ? "✅ Yes" : "❌ No") . "\n";
        }
        
        if ($kooraProduct) {
            $kooraInDeal = in_array($kooraProduct->id, $dealProductIds ?: []);
            echo "   - KOORA (ID: {$kooraProduct->id}) in deal: " . ($kooraInDeal ? "✅ Yes" : "❌ No") . "\n";
        }
    }

    // 4. Check deal creator user details
    if ($fifaDeal) {
        echo "\n4. INVESTIGATING DEAL CREATOR:\n";
        $dealUser = \App\Models\User::find($fifaDeal->user_id);
        
        if ($dealUser) {
            echo "   Deal Creator (ID: {$fifaDeal->user_id}):\n";
            echo "   - Name: {$dealUser->name}\n";
            echo "   - Email: {$dealUser->email}\n";
            echo "   - Role: {$dealUser->role}\n";
            
            // Check user relationships
            $dealUser->load(['company', 'productsManager', 'serviceProvider']);
            echo "   - Has Company: " . ($dealUser->company ? "Yes (ID: {$dealUser->company->id})" : "No") . "\n";
            echo "   - Is Products Manager: " . ($dealUser->productsManager ? "Yes (Company ID: {$dealUser->productsManager->company_id})" : "No") . "\n";
            echo "   - Is Service Provider: " . ($dealUser->serviceProvider ? "Yes (Company ID: {$dealUser->serviceProvider->company_id})" : "No") . "\n";
            echo "   - Individual Merchant: " . (!$dealUser->company && !$dealUser->productsManager && !$dealUser->serviceProvider ? "✅ Yes" : "❌ No") . "\n";
        }
    }

    // 5. Test ProductDealService for each product
    if ($fifaProduct) {
        echo "\n5. TESTING PRODUCTDEALSERVICE FOR FIFA EN:\n";
        
        $dealService = new ProductDealService();
        $activeDeals = $dealService->getActiveDealsForProduct($fifaProduct);
        echo "   - Active deals found: {$activeDeals->count()}\n";
        
        if ($activeDeals->count() > 0) {
            foreach ($activeDeals as $deal) {
                echo "     * Deal ID: {$deal->id}, Title: '{$deal->title}', Discount: {$deal->discount_percentage}%\n";
            }
        }
        
        $dealInfo = $dealService->calculateDiscountedPrice($fifaProduct);
        echo "   - Deal calculation result:\n";
        echo "     * Original Price: \${$dealInfo['original_price']}\n";
        echo "     * Discounted Price: \${$dealInfo['discounted_price']}\n";
        echo "     * Discount Percentage: {$dealInfo['discount_percentage']}%\n";
        echo "     * Has Discount: " . ($dealInfo['has_discount'] ? "Yes" : "No") . "\n";
        echo "     * Deal: " . ($dealInfo['deal'] ? "ID {$dealInfo['deal']->id}" : "None") . "\n";
    }
    
    if ($kooraProduct) {
        echo "\n6. TESTING PRODUCTDEALSERVICE FOR KOORA:\n";
        
        $dealService = new ProductDealService();
        $activeDeals = $dealService->getActiveDealsForProduct($kooraProduct);
        echo "   - Active deals found: {$activeDeals->count()}\n";
        
        if ($activeDeals->count() > 0) {
            foreach ($activeDeals as $deal) {
                echo "     * Deal ID: {$deal->id}, Title: '{$deal->title}', Discount: {$deal->discount_percentage}%\n";
            }
        }
        
        $dealInfo = $dealService->calculateDiscountedPrice($kooraProduct);
        echo "   - Deal calculation result:\n";
        echo "     * Original Price: \${$dealInfo['original_price']}\n";
        echo "     * Discounted Price: \${$dealInfo['discounted_price']}\n";
        echo "     * Discount Percentage: {$dealInfo['discount_percentage']}%\n";
        echo "     * Has Discount: " . ($dealInfo['has_discount'] ? "Yes" : "No") . "\n";
        echo "     * Deal: " . ($dealInfo['deal'] ? "ID {$dealInfo['deal']->id}" : "None") . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
