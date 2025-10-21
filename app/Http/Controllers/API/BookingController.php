<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Branch;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Booking::with(['service', 'branch'])
            ->where('user_id', $user->id)
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query
            ->get()
            ->map(fn (Booking $booking) => $this->transformBooking($booking));

        return response()->json([
            'success' => true,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Store a newly created booking in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'branch_id' => 'required|exists:branches,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|string',
            'notes' => 'nullable|string',
            'is_home_service' => 'nullable|boolean',
            'service_location' => 'nullable|in:provider,customer',
            'address' => 'nullable|string',
            'customer_location' => 'nullable',
        ]);

        // Get the service
        $service = Service::findOrFail($request->service_id);

        // Check if service is available
        if (!$service->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'This service is currently unavailable.',
            ], 400);
        }

        // Check if the branch and service match
        if ($service->branch_id != $request->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'The service does not belong to the selected branch.',
            ], 400);
        }

        // Parse the booking time
        $bookingTime = Carbon::parse($request->time);
        $bookingDate = Carbon::parse($request->date);

        // Check if the booking time is valid (e.g., within business hours)
        // This would require additional logic based on branch opening hours

        // Generate a unique booking number
        $bookingNumber = 'BKG-' . strtoupper(Str::random(8));

        $isHomeService = $request->has('is_home_service')
            ? $request->boolean('is_home_service')
            : null;

        $customerLocation = $this->resolveCustomerLocationSnapshot($request, Auth::id());

        if ($isHomeService === true &&
            !$customerLocation &&
            !$request->filled('address')) {
            return response()->json([
                'success' => false,
                'message' => 'A customer location or address is required for home service bookings.',
            ], 422);
        }

        // Create the booking
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $request->service_id,
            'branch_id' => $request->branch_id,
            'booking_number' => $bookingNumber,
            'booking_date' => $bookingDate->format('Y-m-d'),
            'booking_time' => $bookingTime->format('H:i:s'),
            'duration' => $service->duration,
            'price' => $service->price,
            'status' => 'pending',
            'payment_status' => 'pending',
            'notes' => $request->notes,
            'is_home_service' => $isHomeService,
            'service_location' => $request->input('service_location'),
            'customer_location' => $customerLocation,
            'address' => $request->input('address'),
        ]);

        $booking->load(['service', 'branch']);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking' => $this->transformBooking($booking),
        ], 201);
    }

    /**
     * Display the specified booking.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $booking = Booking::with(['service', 'branch'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'booking' => $this->transformBooking($booking),
        ]);
    }

    /**
     * Update the specified booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $booking = Booking::where('user_id', $user->id)->findOrFail($id);

        // Only allow updates for pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update a booking that is ' . $booking->status,
            ], 400);
        }

        $request->validate([
            'date' => 'sometimes|required|date|after_or_equal:today',
            'time' => 'sometimes|required|string',
            'notes' => 'nullable|string',
        ]);

        // Update booking details
        if ($request->has('date')) {
            $booking->booking_date = Carbon::parse($request->date)->format('Y-m-d');
        }

        if ($request->has('time')) {
            $booking->booking_time = Carbon::parse($request->time)->format('H:i:s');
        }

        if ($request->has('notes')) {
            $booking->notes = $request->notes;
        }

        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'booking' => $this->transformBooking($booking->load(['service', 'branch'])),
        ]);
    }

    /**
     * Cancel the specified booking.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $booking = Booking::where('user_id', $user->id)->findOrFail($id);

        // Only allow cancellation for pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a booking that is ' . $booking->status,
            ], 400);
        }

        // Update booking status
        $booking->status = 'cancelled';
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'booking' => $this->transformBooking($booking->load(['service', 'branch'])),
        ]);
    }

    /**
     * Check availability for a service on a specific date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->date);

        // Respect service availability days (stored as 0=Sunday, 6=Saturday)
        $availableDays = collect($service->available_days ?? [])->map(fn ($day) => (int) $day);
        if ($availableDays->isNotEmpty()) {
            $dayIndex = (int) $date->format('w'); // 0 (Sun) - 6 (Sat)
            if (!$availableDays->contains($dayIndex)) {
                return response()->json([
                    'success' => true,
                    'available_times' => [],
                    'message' => 'Service not offered on this day',
                ]);
            }
        }

        $windowStart = null;
        $windowEnd = null;

        if (!empty($service->start_time)) {
            try {
                $serviceStart = Carbon::parse($service->start_time);
                $windowStart = $serviceStart;
            } catch (\Throwable $e) {
                // Ignore parsing error and keep existing window start
            }
        }

        if (!empty($service->end_time)) {
            try {
                $serviceEnd = Carbon::parse($service->end_time);
                $windowEnd = $serviceEnd;
            } catch (\Throwable $e) {
                // Ignore parsing error and keep existing window end
            }
        }

        if (!$windowStart || !$windowEnd) {
            // Fallback to default working hours if none were determined
            $windowStart = Carbon::createFromTime(9, 0);
            $windowEnd = Carbon::createFromTime(17, 0);
        }

        if (!$windowEnd->greaterThan($windowStart)) {
            return response()->json([
                'success' => true,
                'available_times' => [],
                'message' => 'Service has no working window on this day',
            ]);
        }

        // Align window times with the requested date
        $windowStart = $date->copy()->setTime(
            (int) $windowStart->format('H'),
            (int) $windowStart->format('i'),
            (int) $windowStart->format('s')
        );
        $windowEnd = $date->copy()->setTime(
            (int) $windowEnd->format('H'),
            (int) $windowEnd->format('i'),
            (int) $windowEnd->format('s')
        );

        // Generate time slots based on service duration
        $timeSlots = [];
        $currentStart = $windowStart->copy();

        while ($currentStart->lt($windowEnd)) {
            $slotEnd = $currentStart->copy()->addMinutes($service->duration);
            if ($slotEnd->gt($windowEnd)) {
                break;
            }
            $timeSlots[] = $currentStart->format('h:i A');
            $currentStart = $slotEnd;
        }

        // Get existing bookings for this service on this date
        $existingBookings = Booking::where('service_id', $service->id)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();

        // Remove booked time slots
        foreach ($existingBookings as $booking) {
            $bookingTime = Carbon::parse($booking->booking_time)->format('h:i A');
            $key = array_search($bookingTime, $timeSlots);
            if ($key !== false) {
                unset($timeSlots[$key]);
            }
        }

        // Reindex array
        $timeSlots = array_values($timeSlots);

        return response()->json([
            'success' => true,
            'available_times' => $timeSlots,
        ]);
    }

    /**
     * Create a standard API response shape for a booking.
     */
    protected function transformBooking(Booking $booking): array
    {
        $booking->loadMissing(['service', 'branch']);

        $location = $this->formatCustomerLocation($booking->customer_location);

        return [
            'id' => $booking->id,
            'user_id' => $booking->user_id,
            'branch_id' => $booking->branch_id,
            'service_id' => $booking->service_id,
            'date' => $booking->booking_date->format('Y-m-d'),
            'time' => Carbon::parse($booking->booking_time)->format('h:i A'),
            'status' => $booking->status,
            'amount' => (float) $booking->price,
            'notes' => $booking->notes,
            'service_name' => optional($booking->service)->name,
            'branch_name' => optional($booking->branch)->name,
            'booking_number' => $booking->booking_number,
            'payment_status' => $booking->payment_status,
            'payment_method' => $booking->payment_method,
            'duration' => $booking->duration,
            'created_at' => optional($booking->created_at)->toDateTimeString(),
            'updated_at' => optional($booking->updated_at)->toDateTimeString(),
            'is_home_service' => (bool) $booking->is_home_service,
            'service_location' => $booking->service_location,
            'customer_location' => $location,
            'address' => $booking->address,
        ];
    }

    /**
     * Format the stored customer location snapshot for API responses.
     */
    protected function formatCustomerLocation($location): ?array
    {
        if (empty($location)) {
            return null;
        }

        if (is_object($location) && method_exists($location, 'toArray')) {
            $location = $location->toArray();
        }

        if (!is_array($location) || empty($location)) {
            return null;
        }

        return [
            'latitude' => isset($location['latitude']) ? (float) $location['latitude'] : null,
            'longitude' => isset($location['longitude']) ? (float) $location['longitude'] : null,
            'address' => $location['address'] ?? ($location['name'] ?? null),
            'name' => $location['name'] ?? null,
            'emirate' => $location['emirate'] ?? null,
            'user_location_id' => $location['user_location_id'] ?? null,
            'snapshot_source' => $location['snapshot_source'] ?? null,
        ];
    }

    /**
     * Resolve customer location input into a snapshot suitable for persistence.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function resolveCustomerLocationSnapshot(Request $request, ?int $userId = null): ?array
    {
        $locationInput = $request->input('customer_location');

        if (is_null($locationInput) && $request->filled('customer_location_id')) {
            $locationInput = $request->input('customer_location_id');
        }

        if (is_string($locationInput)) {
            $decoded = json_decode($locationInput, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $locationInput = $decoded;
            }
        }

        if (is_array($locationInput)) {
            $validator = Validator::make($locationInput, [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'address' => 'nullable|string',
                'name' => 'nullable|string',
                'emirate' => 'nullable|string',
                'user_location_id' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'customer_location' => $validator->errors()->first(),
                ]);
            }

            return [
                'latitude' => (float) $locationInput['latitude'],
                'longitude' => (float) $locationInput['longitude'],
                'address' => $locationInput['address'] ?? ($locationInput['name'] ?? null),
                'name' => $locationInput['name'] ?? null,
                'emirate' => $locationInput['emirate'] ?? null,
                'user_location_id' => $locationInput['user_location_id'] ?? null,
                'snapshot_source' => $locationInput['snapshot_source'] ?? 'direct_input',
            ];
        }

        if (filled($locationInput)) {
            $location = UserLocation::query()
                ->where('id', $locationInput)
                ->when($userId, fn ($query) => $query->where('user_id', $userId))
                ->first();

            if (!$location) {
                throw ValidationException::withMessages([
                    'customer_location' => 'The selected customer location is invalid.',
                ]);
            }

            return [
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'address' => $location->name,
                'name' => $location->name,
                'emirate' => $location->emirate,
                'user_location_id' => $location->id,
                'snapshot_source' => 'user_location',
            ];
        }

        return null;
    }
}
