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
        Schema::table('provider_profiles', function (Blueprint $table) {
            // Add all potentially missing columns with nullable() to prevent errors
            if (!Schema::hasColumn('provider_profiles', 'company_name')) {
                $table->string('company_name')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'logo')) {
                $table->string('logo')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'description')) {
                $table->text('description')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'contact_phone')) {
                $table->string('contact_phone')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'address')) {
                $table->string('address')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'city')) {
                $table->string('city')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'state')) {
                $table->string('state')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'zip_code')) {
                $table->string('zip_code')->nullable();
            }
            
            if (!Schema::hasColumn('provider_profiles', 'country')) {
                $table->string('country')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provider_profiles', function (Blueprint $table) {
            // Remove all added columns
            $columns = [
                'company_name', 'logo', 'description', 'contact_email', 'contact_phone',
                'address', 'city', 'state', 'zip_code', 'country'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('provider_profiles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
