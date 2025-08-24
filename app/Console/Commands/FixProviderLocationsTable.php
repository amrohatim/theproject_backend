<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class FixProviderLocationsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:provider-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the provider_locations table issue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking provider_locations table...');

        if (!Schema::hasTable('provider_locations')) {
            $this->info('provider_locations table does not exist. Creating it now...');

            try {
                Schema::create('provider_locations', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('provider_id')->constrained('provider_profiles')->onDelete('cascade');
                    $table->string('label')->nullable();
                    $table->string('emirate');
                    $table->decimal('latitude', 10, 8);
                    $table->decimal('longitude', 11, 8);
                    $table->timestamps();
                });

                // Add the migration record to the migrations table
                DB::table('migrations')->insert([
                    'migration' => '2024_07_01_000001_create_provider_locations_table',
                    'batch' => DB::table('migrations')->max('batch') + 1
                ]);

                $this->info('provider_locations table created successfully!');
            } catch (\Exception $e) {
                $this->error('Error creating provider_locations table: ' . $e->getMessage());
            }
        } else {
            $this->info('provider_locations table already exists.');
        }

        return 0;
    }
}
