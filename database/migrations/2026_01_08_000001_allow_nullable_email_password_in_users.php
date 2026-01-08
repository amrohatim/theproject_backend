<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Allow phone-only or social registrations without email/password.
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NULL');
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous constraints (may fail if NULL data exists).
        DB::statement('ALTER TABLE users MODIFY email VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
    }
};
