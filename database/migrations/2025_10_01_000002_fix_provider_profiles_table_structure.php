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
        // First, check if the provider_id column is a self-reference
        $selfReference = DB::select("
            SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'provider_profiles' 
            AND COLUMN_NAME = 'provider_id' 
            AND REFERENCED_TABLE_NAME = 'provider_profiles'
        ");

        if (!empty($selfReference)) {
            // Drop the self-reference foreign key
            Schema::table('provider_profiles', function (Blueprint $table) {
                $table->dropForeign(['provider_id']);
            });
        }

        // Now modify the provider_id column to reference the providers table
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->foreign('provider_id')
                ->references('id')
                ->on('providers')
                ->onDelete('cascade');
        });

        // Create provider profiles for users who have a provider record but no provider profile
        $providers = DB::table('providers')->get();
        foreach ($providers as $provider) {
            $existingProfile = DB::table('provider_profiles')
                ->where('user_id', $provider->user_id)
                ->first();

            if (!$existingProfile) {
                try {
                    DB::table('provider_profiles')->insert([
                        'user_id' => $provider->user_id,
                        'provider_id' => $provider->id,
                        'business_name' => $provider->business_name,
                        'company_name' => $provider->business_name,
                        'status' => 'active',
                        'product_name' => 'Default Product',
                        'stock' => 0,
                        'price' => 0.00,
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } catch (\Exception $e) {
                    // Log the error but continue with the migration
                    error_log('Error creating provider profile for provider ID ' . $provider->id . ': ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the foreign key change
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
        });

        // Restore the original self-reference
        Schema::table('provider_profiles', function (Blueprint $table) {
            $table->foreign('provider_id')
                ->references('id')
                ->on('provider_profiles')
                ->onDelete('cascade');
        });
    }
};
