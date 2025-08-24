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
        // Update the default value for the status column in licenses table
        DB::statement("ALTER TABLE licenses MODIFY COLUMN status ENUM('active', 'expired', 'pending', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the default value back to 'active'
        DB::statement("ALTER TABLE licenses MODIFY COLUMN status ENUM('active', 'expired', 'pending', 'rejected') DEFAULT 'active'");
    }
};
