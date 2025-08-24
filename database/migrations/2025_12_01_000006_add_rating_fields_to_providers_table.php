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
        Schema::table('providers', function (Blueprint $table) {
            // Add rating fields for providers
            $table->decimal('average_rating', 3, 2)->default(0)->after('is_verified');
            $table->integer('total_ratings')->default(0)->after('average_rating');
            
            // Add index for performance
            $table->index(['average_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            $table->dropIndex(['average_rating']);
            $table->dropColumn(['average_rating', 'total_ratings']);
        });
    }
};
