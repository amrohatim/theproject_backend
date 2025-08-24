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
        // Check if providers table exists before trying to modify it
        if (!Schema::hasTable('providers')) {
            // Providers table doesn't exist yet, skip this migration
            return;
        }

        // Step 1: Add missing fields to providers table from provider_profiles
        Schema::table('providers', function (Blueprint $table) {
            if (!Schema::hasColumn('providers', 'company_name')) {
                $table->string('company_name')->nullable()->after('business_name');
            }
            if (!Schema::hasColumn('providers', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('website');
            }
            if (!Schema::hasColumn('providers', 'contact_phone')) {
                $table->string('contact_phone')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('providers', 'zip_code')) {
                $table->string('zip_code')->nullable()->after('postal_code');
            }
        });

        // Step 2: Migrate data from provider_profiles to providers table
        if (Schema::hasTable('provider_profiles')) {
            DB::statement("
                UPDATE providers p
                INNER JOIN provider_profiles pf ON p.id = pf.provider_id
                SET
                    p.company_name = COALESCE(pf.company_name, p.company_name),
                    p.contact_email = COALESCE(pf.contact_email, p.contact_email),
                    p.contact_phone = COALESCE(pf.contact_phone, p.contact_phone),
                    p.zip_code = COALESCE(pf.zip_code, p.zip_code),
                    p.description = COALESCE(pf.description, p.description),
                    p.address = COALESCE(pf.address, p.address),
                    p.city = COALESCE(pf.city, p.city),
                    p.state = COALESCE(pf.state, p.state),
                    p.country = COALESCE(pf.country, p.country),
                    p.logo = COALESCE(pf.logo, p.logo)
                WHERE pf.provider_id IS NOT NULL
            ");
        }

        // Step 3: Fix provider_products table
        if (Schema::hasTable('provider_products')) {
            // Drop existing foreign key constraint if it exists
            try {
                Schema::table('provider_products', function (Blueprint $table) {
                    $table->dropForeign(['provider_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }

            // Update provider_id values to reference the correct providers table
            if (Schema::hasTable('provider_profiles')) {
                DB::statement("
                    UPDATE provider_products pp
                    INNER JOIN provider_profiles pf ON pp.provider_id = pf.id
                    INNER JOIN providers p ON pf.provider_id = p.id
                    SET pp.provider_id = pf.provider_id
                    WHERE pf.provider_id IS NOT NULL
                ");
            }

            // Delete orphaned provider_products that don't have a valid provider
            DB::statement("
                DELETE pp FROM provider_products pp
                LEFT JOIN providers p ON pp.provider_id = p.id
                WHERE p.id IS NULL
            ");

            // Add the correct foreign key constraint
            Schema::table('provider_products', function (Blueprint $table) {
                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers')
                    ->onDelete('cascade');
            });
        }

        // Step 4: Fix provider_locations table
        if (Schema::hasTable('provider_locations')) {
            // Drop existing foreign key constraint if it exists
            try {
                Schema::table('provider_locations', function (Blueprint $table) {
                    $table->dropForeign(['provider_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }

            // Update provider_id values to reference the correct providers table
            if (Schema::hasTable('provider_profiles')) {
                DB::statement("
                    UPDATE provider_locations pl
                    INNER JOIN provider_profiles pf ON pl.provider_id = pf.id
                    INNER JOIN providers p ON pf.provider_id = p.id
                    SET pl.provider_id = pf.provider_id
                    WHERE pf.provider_id IS NOT NULL
                ");
            }

            // Delete orphaned provider_locations that don't have a valid provider
            DB::statement("
                DELETE pl FROM provider_locations pl
                LEFT JOIN providers p ON pl.provider_id = p.id
                WHERE p.id IS NULL
            ");

            // Add the correct foreign key constraint
            Schema::table('provider_locations', function (Blueprint $table) {
                $table->foreign('provider_id')
                    ->references('id')
                    ->on('providers')
                    ->onDelete('cascade');
            });
        }

        // Step 5: Drop the provider_profiles table
        if (Schema::hasTable('provider_profiles')) {
            Schema::dropIfExists('provider_profiles');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible due to data migration and table dropping
        // We would need to recreate the provider_profiles table and migrate data back
        throw new \Exception('This migration cannot be reversed safely due to data migration and table dropping.');
    }
};
