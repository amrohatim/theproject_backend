<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Check if the table exists
if (!Schema::hasTable('provider_locations')) {
    // Create the table
    Schema::create('provider_locations', function (Blueprint $table) {
        $table->id();
        $table->foreignId('provider_id')->constrained('provider_profiles')->onDelete('cascade');
        $table->string('label')->nullable();
        $table->string('emirate');
        $table->decimal('latitude', 10, 8);
        $table->decimal('longitude', 11, 8);
        $table->timestamps();
    });
    
    echo "provider_locations table created successfully.\n";
} else {
    echo "provider_locations table already exists.\n";
}
