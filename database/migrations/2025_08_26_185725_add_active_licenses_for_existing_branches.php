<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add active license records for all existing branches
        $branches = DB::table('branches')->get();

        foreach ($branches as $branch) {
            // Check if branch already has a license
            $existingLicense = DB::table('branches_licenses')
                ->where('branch_id', $branch->id)
                ->first();

            if (!$existingLicense) {
                // Create a default active license for existing branches
                DB::table('branches_licenses')->insert([
                    'branch_id' => $branch->id,
                    'license_file_path' => 'branch_licenses/default_license.pdf', // Placeholder path
                    'start_date' => Carbon::now()->subYear()->toDateString(), // 1 year ago
                    'end_date' => Carbon::now()->addYear()->toDateString(), // 1 year from now
                    'status' => 'active',
                    'uploaded_at' => $branch->created_at ?? Carbon::now(),
                    'verified_at' => $branch->created_at ?? Carbon::now(),
                    'created_at' => $branch->created_at ?? Carbon::now(),
                    'updated_at' => $branch->updated_at ?? Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all default licenses (those with placeholder path)
        DB::table('branches_licenses')
            ->where('license_file_path', 'branch_licenses/default_license.pdf')
            ->delete();
    }
};
