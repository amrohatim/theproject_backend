<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CompanyBookingsAnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            $customer = User::create([
                'name' => 'Analytics Customer',
                'email' => 'analytics.customer@example.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'status' => 'active',
            ]);
            $customers = collect([$customer]);
        }

        $services = Service::with(['branch.company'])
            ->get()
            ->filter(function ($service) {
                return $service->branch && $service->branch->company_id;
            })
            ->values();

        if ($services->isEmpty()) {
            $this->command->info('No company-linked services found. Skipping analytics bookings seeding.');
            return;
        }

        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
        $paymentMethods = ['credit_card', 'paypal', 'cash', 'bank_transfer'];
        $hasCompanyId = Schema::hasColumn('bookings', 'company_id');

        $hotCount = max(1, (int) ceil($services->count() * 0.25));
        $hotServices = $services->count() <= $hotCount ? $services : $services->random($hotCount);
        $hotServiceIds = $hotServices->pluck('id')->flip();

        $bookingSequence = 1;

        foreach ($services as $service) {
            $bookingCount = $hotServiceIds->has($service->id) ? rand(4, 10) : rand(1, 3);

            for ($i = 0; $i < $bookingCount; $i++) {
                $customer = $customers->random();
                $bookingDate = Carbon::now()->subDays(rand(1, 120));

                $hour = rand(9, 20);
                $minute = [0, 15, 30, 45][array_rand([0, 15, 30, 45])];
                $bookingTime = sprintf('%02d:%02d:00', $hour, $minute);

                $status = $statuses[array_rand($statuses)];
                if ($status === 'completed') {
                    $paymentStatus = 'paid';
                } elseif ($status === 'cancelled' || $status === 'no_show') {
                    $paymentStatus = 'refunded';
                } else {
                    $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
                }

                $booking = Booking::create([
                    'user_id' => $customer->id,
                    'service_id' => $service->id,
                    'branch_id' => $service->branch_id,
                    'booking_number' => $this->makeBookingNumber($bookingSequence++),
                    'booking_date' => $bookingDate->format('Y-m-d'),
                    'booking_time' => $bookingTime,
                    'duration' => $service->duration ?? 60,
                    'price' => $service->price ?? 0,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'notes' => 'Analytics seed booking',
                    'created_at' => $bookingDate->copy()->subDays(rand(0, 7)),
                    'updated_at' => $bookingDate->copy()->addDays(rand(0, 3)),
                ]);

                if ($hasCompanyId) {
                    $booking->company_id = $service->branch->company_id;
                    $booking->save();
                }
            }
        }
    }

    private function makeBookingNumber(int $sequence): string
    {
        return 'BKG-' . strtoupper(Str::random(6)) . '-' . str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }
}
