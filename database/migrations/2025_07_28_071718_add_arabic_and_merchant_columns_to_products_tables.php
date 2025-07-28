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
        // Add Arabic language support and merchant tracking columns to products table
        Schema::table('products', function (Blueprint $table) {
            // Merchant tracking columns
            if (!Schema::hasColumn('products', 'is_merchant')) {
                $table->boolean('is_merchant')->default(false)->after('is_available')
                    ->comment('Indicates if the product was created from a merchant dashboard');
            }

            if (!Schema::hasColumn('products', 'merchant_name')) {
                $table->string('merchant_name')->nullable()->after('is_merchant')
                    ->comment('Stores the merchant name when product is created from merchant dashboard');
            }

            // Arabic language support columns
            if (!Schema::hasColumn('products', 'product_name_arabic')) {
                $table->text('product_name_arabic')->nullable()->after('merchant_name')
                    ->comment('Arabic translation of the product name');
            }

            if (!Schema::hasColumn('products', 'product_description_arabic')) {
                $table->text('product_description_arabic')->nullable()->after('product_name_arabic')
                    ->comment('Arabic translation of the product description');
            }
        });

        // Add Arabic language support columns to provider_products table
        Schema::table('provider_products', function (Blueprint $table) {
            if (!Schema::hasColumn('provider_products', 'product_name_arabic')) {
                $table->text('product_name_arabic')->nullable()->after('product_name')
                    ->comment('Arabic translation of the product name');
            }

            if (!Schema::hasColumn('provider_products', 'product_description_arabic')) {
                $table->text('product_description_arabic')->nullable()->after('product_name_arabic')
                    ->comment('Arabic translation of the product description');
            }
        });

        // Add indexes for frequently queried columns
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'is_merchant')) {
                $table->index('is_merchant', 'products_is_merchant_index');
            }
            if (Schema::hasColumn('products', 'merchant_name')) {
                $table->index('merchant_name', 'products_merchant_name_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes first
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasIndex('products', 'products_is_merchant_index')) {
                $table->dropIndex('products_is_merchant_index');
            }
            if (Schema::hasIndex('products', 'products_merchant_name_index')) {
                $table->dropIndex('products_merchant_name_index');
            }
        });

        // Remove columns from products table
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'product_description_arabic')) {
                $table->dropColumn('product_description_arabic');
            }
            if (Schema::hasColumn('products', 'product_name_arabic')) {
                $table->dropColumn('product_name_arabic');
            }
            if (Schema::hasColumn('products', 'merchant_name')) {
                $table->dropColumn('merchant_name');
            }
            if (Schema::hasColumn('products', 'is_merchant')) {
                $table->dropColumn('is_merchant');
            }
        });

        // Remove columns from provider_products table
        Schema::table('provider_products', function (Blueprint $table) {
            if (Schema::hasColumn('provider_products', 'product_description_arabic')) {
                $table->dropColumn('product_description_arabic');
            }
            if (Schema::hasColumn('provider_products', 'product_name_arabic')) {
                $table->dropColumn('product_name_arabic');
            }
        });
    }
};
