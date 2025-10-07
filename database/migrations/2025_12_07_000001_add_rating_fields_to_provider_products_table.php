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
        Schema::table('provider_products', function (Blueprint $table) {
            // Add rating fields for provider products
            if (!Schema::hasColumn('provider_products', 'rating')) {
                $table->decimal('rating', 3, 2)->nullable()->after('image');
            }
            
            if (!Schema::hasColumn('provider_products', 'total_ratings')) {
                $table->integer('total_ratings')->default(0)->after('rating');
            }
            
            // Add index for performance
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_products', function (Blueprint $table) {
            $table->dropIndex(['rating']);
            $table->dropColumn(['rating', 'total_ratings']);
        });
    }
};
