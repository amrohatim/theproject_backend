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
        Schema::table('branches', function (Blueprint $table) {
            // Check if rating column already exists (it might from previous migrations)
            if (!Schema::hasColumn('branches', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->default(0)->after('rating');
            }
            
            if (!Schema::hasColumn('branches', 'total_ratings')) {
                $table->integer('total_ratings')->default(0)->after('average_rating');
            }
            
            // Add index for performance
            $table->index(['average_rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropIndex(['average_rating']);
            
            if (Schema::hasColumn('branches', 'average_rating')) {
                $table->dropColumn('average_rating');
            }
            
            if (Schema::hasColumn('branches', 'total_ratings')) {
                $table->dropColumn('total_ratings');
            }
        });
    }
};
