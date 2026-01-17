<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            if (!Schema::hasColumn('wishlists', 'user_id')) {
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('wishlists', 'product_id')) {
                $table->foreignId('product_id')->constrained('provider_products')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('wishlists', 'created_at')) {
                $table->timestamps();
            }

            $table->unique(['user_id', 'product_id'], 'wishlists_user_product_unique');
        });
    }

    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            if (Schema::hasColumn('wishlists', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('wishlists', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            $table->dropUnique('wishlists_user_product_unique');
        });
    }
};
