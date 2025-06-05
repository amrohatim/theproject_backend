<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

        $bookings = $query->get();

        // Transform the bookings to match the frontend model
        $transformedBookings = $bookings->map(function ($booking) {
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
                'service_name' => $booking->service->name,
                'branch_name' => $booking->branch->name,
                'booking_number' => $booking->booking_number,
                'payment_status' => $booking->payment_status,
                'payment_method' => $booking->payment_method,
                'duration' => $booking->duration,
                'created_at' => $booking->created_at->toDateTimeString(),
                'updated_at' => $booking->updated_at->toDateTimeString(),
            ];
        });

        return response()->json([
            'success' => true,
            'bookings' => $transformedBookings,
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
        ]);

        // Load relationships
        $booking->load(['service', 'branch']);

        // Transform the booking to match the frontend model
        $transformedBooking = [
            'id' => $booking->id,
            'user_id' => $booking->user_id,
            'branch_id' => $booking->branch_id,
            'service_id' => $booking->service_id,
            'date' => $booking->booking_date->format('Y-m-d'),
            'time' => Carbon::parse($booking->booking_time)->format('h:i A'),
            'status' => $booking->status,
            'amount' => (float) $booking->price,
            'notes' => $booking->notes,
            'service_name' => $booking->service->name,
            'branch_name' => $booking->branch->name,
            'booking_number' => $booking->booking_number,
            'payment_status' => $booking->payment_status,
            'payment_method' => $booking->payment_method,
            'duration' => $booking->duration,
            'created_at' => $booking->created_at->toDateTimeString(),
            'updated_at' => $booking->updated_at->toDateTimeString(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking' => $transformedBooking,
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

        // Transform the booking to match the frontend model
        $transformedBooking = [
            'id' => $booking->id,
            'user_id' => $booking->user_id,
            'branch_id' => $booking->branch_id,
            'service_id' => $booking->service_id,
            'date' => $booking->booking_date->format('Y-m-d'),
            'time' => Carbon::parse($booking->booking_time)->format('h:i A'),
            'status' => $booking->status,
            'amount' => (float) $booking->price,
            'notes' => $booking->notes,
            'service_name' => $booking->service->name,
            'branch_name' => $booking->branch->name,
            'booking_number' => $booking->booking_number,
            'payment_status' => $booking->payment_status,
            'payment_method' => $booking->payment_method,
            'duration' => $booking->duration,
            'created_at' => $booking->created_at->toDateTimeString(),
            'updated_at' => $booking->updated_at->toDateTimeString(),
        ];

        return response()->json([
            'success' => true,
            'booking' => $transformedBooking,
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

        // Load relationships
        $booking->load(['service', 'branch']);

        // Transform the booking to match the frontend model
        $transformedBooking = [
            'id' => $booking->id,
            'user_id' => $booking->user_id,
            'branch_id' => $booking->branch_id,
            'service_id' => $booking->service_id,
            'date' => $booking->booking_date->format('Y-m-d'),
            'time' => Carbon::parse($booking->booking_time)->format('h:i A'),
            'status' => $booking->status,
            'amount' => (float) $booking->price,
            'notes' => $booking->notes,
            'service_name' => $booking->service->name,
            'branch_name' => $booking->branch->name,
            'booking_number' => $booking->booking_number,
            'payment_status' => $booking->payment_status,
            'payment_method' => $booking->payment_method,
            'duration' => $booking->duration,
            'created_at' => $booking->created_at->toDateTimeString(),
            'updated_at' => $booking->updated_at->toDateTimeString(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'booking' => $transformedBooking,
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

        // Load relationships
        $booking->load(['service', 'branch']);

        // Transform the booking to match the frontend model
        $transformedBooking = [
            'id' => $booking->id,
            'user_id' => $booking->user_id,
            'branch_id' => $booking->branch_id,
            'service_id' => $booking->service_id,
            'date' => $booking->booking_date->format('Y-m-d'),
            'time' => Carbon::parse($booking->booking_time)->format('h:i A'),
            'status' => $booking->status,
            'amount' => (float) $booking->price,
            'notes' => $booking->notes,
            'service_name' => $booking->service->name,
            'branch_name' => $booking->branch->name,
            'booking_number' => $booking->booking_number,
            'payment_status' => $booking->payment_status,
            'payment_method' => $booking->payment_method,
            'duration' => $booking->duration,
            'created_at' => $booking->created_at->toDateTimeString(),
            'updated_at' => $booking->updated_at->toDateTimeString(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
            'booking' => $transformedBooking,
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

        // Get the service
        $service = Service::findOrFail($request->service_id);

        // Get the branch
        $branch = Branch::findOrFail($service->branch_id);

        // Get the date
        $date = Carbon::parse($request->date);

        // Get branch opening hours for the day of the week
        $dayOfWeek = strtolower($date->format('l'));
        $openingHours = json_decode($branch->opening_hours, true);

        // Check if the branch is open on this day
        if (!isset($openingHours[$dayOfWeek]) || !$openingHours[$dayOfWeek]['is_open']) {
            return response()->json([
                'success' => true,
                'available_times' => [],
                'message' => 'The branch is closed on this day',
            ]);
        }

        // Get opening and closing times
        $openTime = Carbon::parse($openingHours[$dayOfWeek]['open']);
        $closeTime = Carbon::parse($openingHours[$dayOfWeek]['close']);

        // Generate time slots based on service duration
        $timeSlots = [];
        $currentTime = clone $openTime;

        while ($currentTime->addMinutes($service->duration)->lte($closeTime)) {
            $timeSlots[] = $currentTime->copy()->subMinutes($service->duration)->format('h:i A');
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
}
