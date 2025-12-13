<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\UserLocation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingHomeServiceBackfillSeeder extends Seeder
{
    /**
     * Seed missing customer_location, is_home_service and service_location
     * for existing bookings.
     */
    public function run(): void
    {
        $this->command?->info('🔄 Backfilling customer_location, is_home_service, and service_location for existing bookings...');

        // Only touch records that are missing one of the fields
        $bookings = Booking::query()
            ->whereNull('customer_location')
            ->orWhereNull('is_home_service')
            ->orWhereNull('service_location')
            ->get();

        $updated = 0;

        foreach ($bookings as $booking) {
            $locationSnapshot = $this->resolveLocationSnapshot($booking);

            // Prefer existing values; only fill when missing
            if (is_null($booking->customer_location) && $locationSnapshot) {
                $booking->customer_location = $locationSnapshot;
            }

            if (is_null($booking->is_home_service)) {
                // Default to false unless we have a customer location, then set true
                $booking->is_home_service = (bool) $booking->customer_location;
            }

            if (is_null($booking->service_location)) {
                $booking->service_location = $booking->customer_location ? 'customer' : 'provider';
            }

            if ($booking->isDirty()) {
                DB::transaction(function () use ($booking) {
                    $booking->save();
                });
                $updated++;
            }
        }

        $this->command?->info("✅ Backfill complete. Updated {$updated} booking(s).");
    }

    /**
     * Build a location snapshot from the user's latest location
     * or fallback to the booking address.
     */
    protected function resolveLocationSnapshot(Booking $booking): ?array
    {
        $userLocation = UserLocation::where('user_id', $booking->user_id)
            ->latest()
            ->first();

        if ($userLocation) {
            return [
                'latitude' => $userLocation->latitude,
                'longitude' => $userLocation->longitude,
                'address' => $userLocation->address ?? $userLocation->name,
                'name' => $userLocation->name,
                'emirate' => $userLocation->emirate,
                'user_location_id' => $userLocation->id,
                'snapshot_source' => 'user_location_seeder',
            ];
        }

        if (!empty($booking->address)) {
            return [
                'address' => $booking->address,
                'snapshot_source' => 'booking_address_seeder',
            ];
        }

        return null;
    }
}
