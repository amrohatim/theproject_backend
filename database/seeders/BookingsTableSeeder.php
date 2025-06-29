<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Branch;
use App\Models\Service;
use Carbon\Carbon;

class BookingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get customer users
        $customers = User::where('role', 'customer')->get();

        // Get all branches
        $branches = Branch::all();

        // Get all services
        $services = Service::all();

        // Booking statuses
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

        // Payment statuses
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        // Payment methods
        $paymentMethods = ['credit_card', 'paypal', 'cash'];

        // Create bookings for each customer
        foreach ($customers as $customer) {
            // Create 1-4 bookings per customer
            $numBookings = rand(1, 4);

            for ($i = 0; $i < $numBookings; $i++) {
                // Select a random service
                $service = $services->random();

                // Get the branch associated with the service
                $branch = $branches->firstWhere('id', $service->branch_id);

                // If no branch found, select a random branch
                if (!$branch) {
                    $branch = $branches->random();
                }

                // Generate a random booking date within the next 30 days
                $bookingDate = Carbon::now()->addDays(rand(1, 30))->format('Y-m-d');

                // Generate a random booking time between 9 AM and 5 PM
                $hour = rand(9, 17);
                $minute = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
                $bookingTime = sprintf('%02d:%02d:00', $hour, $minute);

                // Generate a unique booking number
                $bookingNumber = 'BKG-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Ymd');

                // Select a random status
                $status = $statuses[array_rand($statuses)];

                // If booking is completed, payment status is paid
                // If booking is cancelled, payment status is refunded
                // Otherwise, randomly select a payment status
                if ($status === 'completed') {
                    $paymentStatus = 'paid';
                } elseif ($status === 'cancelled') {
                    $paymentStatus = 'refunded';
                } else {
                    $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                }

                // Select a random payment method
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // Create the booking
                Booking::create([
                    'user_id' => $customer->id,
                    'service_id' => $service->id,
                    'branch_id' => $branch->id,
                    'booking_number' => $bookingNumber,
                    'booking_date' => $bookingDate,
                    'booking_time' => $bookingTime,
                    'duration' => $service->duration,
                    'price' => $service->price,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'notes' => $this->getRandomBookingNote(),
                    'created_at' => Carbon::now()->subDays(rand(1, 14)),
                    'updated_at' => Carbon::now()->subDays(rand(0, 1)),
                ]);
            }

            // Create 1-2 past bookings per customer
            $numPastBookings = rand(1, 2);

            for ($i = 0; $i < $numPastBookings; $i++) {
                // Select a random service
                $service = $services->random();

                // Get the branch associated with the service
                $branch = $branches->firstWhere('id', $service->branch_id);

                // If no branch found, select a random branch
                if (!$branch) {
                    $branch = $branches->random();
                }

                // Generate a random booking date within the past 30 days
                $bookingDate = Carbon::now()->subDays(rand(1, 30))->format('Y-m-d');

                // Generate a random booking time between 9 AM and 5 PM
                $hour = rand(9, 17);
                $minute = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
                $bookingTime = sprintf('%02d:%02d:00', $hour, $minute);

                // Generate a unique booking number
                $bookingNumber = 'BKG-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Ymd');

                // Past bookings are either completed or cancelled
                $status = ['completed', 'cancelled'][array_rand(['completed', 'cancelled'])];

                // If booking is completed, payment status is paid
                // If booking is cancelled or no_show, payment status is refunded
                $paymentStatus = ($status === 'completed') ? 'paid' : 'refunded';

                // Select a random payment method
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                // Create the booking
                Booking::create([
                    'user_id' => $customer->id,
                    'service_id' => $service->id,
                    'branch_id' => $branch->id,
                    'booking_number' => $bookingNumber,
                    'booking_date' => $bookingDate,
                    'booking_time' => $bookingTime,
                    'duration' => $service->duration,
                    'price' => $service->price,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'notes' => $this->getRandomBookingNote(),
                    'created_at' => Carbon::parse($bookingDate)->subDays(rand(1, 14)),
                    'updated_at' => Carbon::parse($bookingDate)->addDays(1),
                ]);
            }
        }
    }

    /**
     * Get a random booking note.
     */
    private function getRandomBookingNote(): string
    {
        $notes = [
            'Please call 15 minutes before the appointment.',
            'I might be a few minutes late.',
            'I have a preference for a female service provider.',
            'I have a preference for a male service provider.',
            'I have mobility issues and need assistance.',
            'This is my first time using this service.',
            'I have allergies to certain products.',
            '',
            '',
            '',
        ];

        return $notes[array_rand($notes)];
    }
}
