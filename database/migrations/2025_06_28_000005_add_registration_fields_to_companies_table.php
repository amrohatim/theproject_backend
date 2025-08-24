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
        Schema::table('companies', function (Blueprint $table) {
            // Add fields required for vendor registration
            $table->string('contact_number_1')->nullable()->after('phone'); // Primary contact
            $table->string('contact_number_2')->nullable()->after('contact_number_1'); // Secondary contact (optional)
            $table->string('emirate')->nullable()->after('state');
            $table->string('street')->nullable()->after('emirate');
            $table->boolean('delivery_capability')->default(false)->after('can_deliver');
            $table->json('delivery_areas')->nullable()->after('delivery_capability'); // JSON array of delivery areas
            
            // Add unique constraints for contact numbers
            $table->unique('contact_number_1');
            $table->index('contact_number_2');
            $table->index(['emirate', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['contact_number_1']);
            $table->dropIndex(['contact_number_2']);
            $table->dropIndex(['emirate', 'city']);
            $table->dropColumn([
                'contact_number_1',
                'contact_number_2',
                'emirate',
                'street',
                'delivery_capability',
                'delivery_areas'
            ]);
        });
    }
};
