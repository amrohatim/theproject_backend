<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with role 'customer'
        $customers = User::where('role', 'customer')->get();
        
        // If no customers, create one
        if ($customers->isEmpty()) {
            $customer = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'status' => 'active',
            ]);
            $customers = collect([$customer]);
        }
        
        // Get branches
        $branches = Branch::all();
        
        // If no branches, skip seeding
        if ($branches->isEmpty()) {
            $this->command->info('No branches found. Skipping booking seeding.');
            return;
        }
        
        // Get services
        $services = Service::all();
        
        // If no services, skip seeding
        if ($services->isEmpty()) {
            $this->command->info('No services found. Skipping booking seeding.');
            return;
        }
        
        // Create 10 bookings
        for ($i = 0; $i < 10; $i++) {
            $customer = $customers->random();
            $branch = $branches->random();
            $service = $services->random();
            
            // Generate a random date within the next 30 days
            $bookingDate = Carbon::now()->addDays(rand(-10, 30))->format('Y-m-d');
            
            // Generate a random time between 9 AM and 5 PM
            $hour = rand(9, 17);
            $minute = [0, 15, 30, 45][rand(0, 3)];
            $bookingTime = sprintf('%02d:%02d:00', $hour, $minute);
            
            Booking::create([
                'user_id' => $customer->id,
                'service_id' => $service->id,
                'branch_id' => $branch->id,
                'booking_number' => 'BKG-' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime,
                'duration' => $service->duration ?? 60,
                'price' => $service->price ?? 0,
                'status' => ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'][rand(0, 4)],
                'payment_status' => ['pending', 'paid', 'failed', 'refunded'][rand(0, 3)],
                'payment_method' => ['credit_card', 'paypal', 'bank_transfer', 'cash'][rand(0, 3)],
                'notes' => 'Test booking ' . ($i + 1),
            ]);
        }
    }
}
