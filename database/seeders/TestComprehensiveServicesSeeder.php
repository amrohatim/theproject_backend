<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestComprehensiveServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Running Test Comprehensive Services Seeder...');
        $this->command->info('This will run only Part 1 to test the functionality.');
        
        // Run Part 1 only for testing
        $this->call(ComprehensiveServicesSeeder::class);
        
        $this->command->info('Test seeding completed successfully!');
        $this->command->info('If this works correctly, you can run the full MasterComprehensiveServicesSeeder.');
    }
}
