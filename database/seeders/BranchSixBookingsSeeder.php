<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BranchSixBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $bookings = [
            [
                'booking_number' => 'BS6-001',
                'branch_id' => 6,
                'user_id' => 92,
                'service_id' => 13,
                'booking_date' => $now->copy()->addDays(1)->toDateString(),
                'booking_time' => '10:00:00',
                'duration' => 60,
                'price' => 150,
                'status' => 'confirmed',
                'notes' => 'Initial booking for user 92',
            ],
            [
                'booking_number' => 'BS6-002',
                'branch_id' => 6,
                'user_id' => 89,
                'service_id' => 17,
                'booking_date' => $now->copy()->addDays(3)->toDateString(),
                'booking_time' => '14:30:00',
                'duration' => 90,
                'price' => 225,
                'status' => 'pending',
                'notes' => 'Follow-up service booking',
            ],
            [
                'booking_number' => 'BS6-003',
                'branch_id' => 6,
                'user_id' => 90,
                'service_id' => 18,
                'booking_date' => $now->copy()->subDays(2)->toDateString(),
                'booking_time' => '12:15:00',
                'duration' => 45,
                'price' => 110,
                'status' => 'completed',
                'notes' => 'Completed service with great feedback',
            ],
        ];

        foreach ($bookings as $attributes) {
            Booking::updateOrCreate(
                [
                    'booking_number' => $attributes['booking_number'],
                ],
                array_merge($attributes, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
