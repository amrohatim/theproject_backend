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
        // First, check if the provider_id column has a foreign key constraint
        $foreignKey = DB::select("
            SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'provider_profiles' 
            AND COLUMN_NAME = 'provider_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        // If there's a foreign key, drop it
        if (!empty($foreignKey)) {
            $constraintName = $foreignKey[0]->CONSTRAINT_NAME;
            Schema::table('provider_profiles', function (Blueprint $table) use ($constraintName) {
                $table->dropForeign($constraintName);
            });
        }

        // Make the provider_id column nullable
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->nullable()->change();
        });

        // Re-add the foreign key constraint with nullable option
        if (!empty($foreignKey) && Schema::hasTable('providers')) {
            Schema::table('provider_profiles', function (Blueprint $table) {
                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, check if the provider_id column has a foreign key constraint
        $foreignKey = DB::select("
            SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'provider_profiles' 
            AND COLUMN_NAME = 'provider_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        // If there's a foreign key, drop it
        if (!empty($foreignKey)) {
            $constraintName = $foreignKey[0]->CONSTRAINT_NAME;
            Schema::table('provider_profiles', function (Blueprint $table) use ($constraintName) {
                $table->dropForeign($constraintName);
            });
        }

        // Make the provider_id column non-nullable
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->nullable(false)->change();
        });

        // Re-add the foreign key constraint without nullable option
        if (!empty($foreignKey) && Schema::hasTable('providers')) {
            Schema::table('provider_profiles', function (Blueprint $table) {
                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers')
                    ->onDelete('cascade');
            });
        }
    }
};
