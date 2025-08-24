<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if deals table exists before trying to modify it
        if (!Schema::hasTable('deals')) {
            // Deals table doesn't exist yet, skip this migration
            return;
        }

        // First, handle existing deals with 'all' or 'categories' applies_to values
        // We'll convert them to 'products' as the default choice
        // Vendors will need to manually review and update these if they want service deals

        $dealsToUpdate = DB::table('deals')
            ->whereIn('applies_to', ['all', 'categories'])
            ->get();

        foreach ($dealsToUpdate as $deal) {
            if ($deal->applies_to === 'all') {
                // For 'all' deals, convert to 'products' and populate product_ids with all vendor's products
                $productIds = DB::table('products')
                    ->join('branches', 'products.branch_id', '=', 'branches.id')
                    ->join('companies', 'branches.company_id', '=', 'companies.id')
                    ->where('companies.user_id', $deal->user_id)
                    ->pluck('products.id')
                    ->toArray();

                DB::table('deals')
                    ->where('id', $deal->id)
                    ->update([
                        'applies_to' => 'products',
                        'product_ids' => json_encode($productIds),
                        'category_ids' => null, // Clear category_ids
                        'service_ids' => null,  // Clear service_ids
                    ]);
            } elseif ($deal->applies_to === 'categories') {
                // For 'categories' deals, convert to 'products' and get products from those categories
                $categoryIds = json_decode($deal->category_ids, true) ?? [];
                
                if (!empty($categoryIds)) {
                    $productIds = DB::table('products')
                        ->join('branches', 'products.branch_id', '=', 'branches.id')
                        ->join('companies', 'branches.company_id', '=', 'companies.id')
                        ->where('companies.user_id', $deal->user_id)
                        ->whereIn('products.category_id', $categoryIds)
                        ->pluck('products.id')
                        ->toArray();

                    DB::table('deals')
                        ->where('id', $deal->id)
                        ->update([
                            'applies_to' => 'products',
                            'product_ids' => json_encode($productIds),
                            'category_ids' => null, // Clear category_ids
                            'service_ids' => null,  // Clear service_ids
                        ]);
                } else {
                    // If no category_ids, just convert to products with empty product_ids
                    DB::table('deals')
                        ->where('id', $deal->id)
                        ->update([
                            'applies_to' => 'products',
                            'product_ids' => json_encode([]),
                            'category_ids' => null,
                            'service_ids' => null,
                        ]);
                }
            }
        }

        // Update the applies_to enum to only allow 'products' and 'services'
        DB::statement("ALTER TABLE deals MODIFY COLUMN applies_to ENUM('products', 'services') NOT NULL");

        // Remove the category_ids column since we won't use it anymore
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('category_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deals')) {
            // Add back the category_ids column
            Schema::table('deals', function (Blueprint $table) {
                $table->json('category_ids')->nullable()->after('product_ids');
            });

            // Restore the applies_to enum to include all previous values
            DB::statement("ALTER TABLE deals MODIFY COLUMN applies_to ENUM('all', 'products', 'categories', 'services', 'products_and_services') DEFAULT 'all'");
        }
    }
};
