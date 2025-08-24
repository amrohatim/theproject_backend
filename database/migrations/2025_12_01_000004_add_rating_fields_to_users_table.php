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
        Schema::table('users', function (Blueprint $table) {
            // Add rating fields for vendors and providers
            $table->decimal('average_rating', 3, 2)->default(0)->after('status');
            $table->integer('total_ratings')->default(0)->after('average_rating');
            
            // Add indexes for performance
            $table->index(['role', 'average_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'average_rating']);
            $table->dropColumn(['average_rating', 'total_ratings']);
        });
    }
};
