<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Add discount-related columns if they don't exist
            if (!Schema::hasColumn('order_items', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('price')
                      ->comment('Original price before any discounts');
            }
            
            if (!Schema::hasColumn('order_items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('original_price')
                      ->comment('Discount percentage applied to this item');
            }
            
            if (!Schema::hasColumn('order_items', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_percentage')
                      ->comment('Discount amount in currency');
            }
            
            if (!Schema::hasColumn('order_items', 'applied_deal_id')) {
                $table->foreignId('applied_deal_id')->nullable()->after('discount_amount')
                      ->comment('ID of the deal that was applied to this item');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Remove columns in reverse order
            if (Schema::hasColumn('order_items', 'applied_deal_id')) {
                $table->dropColumn('applied_deal_id');
            }
            
            if (Schema::hasColumn('order_items', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            
            if (Schema::hasColumn('order_items', 'discount_percentage')) {
                $table->dropColumn('discount_percentage');
            }
            
            if (Schema::hasColumn('order_items', 'original_price')) {
                $table->dropColumn('original_price');
            }
        });
    }
};
