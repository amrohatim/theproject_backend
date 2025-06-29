<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users with role 'customer'
        $customers = User::where('role', 'customer')->get();
        
        if ($customers->isEmpty()) {
            $this->command->info('No customers found. Creating a customer user...');
            $customer = User::factory()->create([
                'name' => 'Test Customer',
                'email' => 'customer@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
            ]);
            $customers = collect([$customer]);
        }
        
        // Get services
        $services = Service::all();
        
        if ($services->isEmpty()) {
            $this->command->info('No services found. Please run ServiceSeeder first.');
            return;
        }
        
        // Create bookings
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        $paymentMethods = ['credit_card', 'paypal', 'cash', null];
        
        $this->command->info('Creating bookings...');
        
        // Create 20 bookings
        for ($i = 0; $i < 20; $i++) {
            $service = $services->random();
            $branch = Branch::find($service->branch_id);
            $customer = $customers->random();
            
            // Generate random booking date (past, present, or future)
            $daysOffset = rand(-30, 30); // Between 30 days ago and 30 days from now
            $bookingDate = Carbon::now()->addDays($daysOffset)->format('Y-m-d');
            
            // Generate random booking time
            $hour = rand(9, 17);
            $minute = rand(0, 1) * 30; // Either 0 or 30
            $bookingTime = sprintf('%02d:%02d:00', $hour, $minute);
            
            // Determine status based on date
            $status = $daysOffset < 0 ? $statuses[array_rand(array_slice($statuses, 2, 3))] : $statuses[array_rand(array_slice($statuses, 0, 2))];
            
            // Determine payment status based on booking status
            $paymentStatus = $status === 'completed' ? 'paid' : $paymentStatuses[array_rand($paymentStatuses)];
            
            // Create booking
            Booking::create([
                'user_id' => $customer->id,
                'service_id' => $service->id,
                'branch_id' => $branch->id,
                'booking_number' => 'BKG-' . strtoupper(Str::random(8)),
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime,
                'duration' => $service->duration ?? 60,
                'price' => $service->price,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'notes' => rand(0, 1) ? 'Please call me 15 minutes before the appointment.' : null,
                'created_at' => Carbon::now()->subDays(rand(1, 60)),
                'updated_at' => Carbon::now(),
            ]);
        }
        
        $this->command->info('Bookings created successfully!');
    }
}
