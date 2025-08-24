<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings for services managed by the service provider.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        $query = Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
            ->with(['service', 'user']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        // Search by customer name or service name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('service', function ($serviceQuery) use ($search) {
                    $serviceQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->paginate(15);

        // Get services for filter dropdown
        $services = Service::whereIn('id', $serviceProvider->service_ids ?? [])->get();

        return view('service-provider.bookings.index', compact('bookings', 'services', 'serviceProvider'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this booking
        if (!in_array($booking->service_id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['service', 'user']);

        return view('service-provider.bookings.show', compact('booking', 'serviceProvider'));
    }

    /**
     * Update the booking status.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Check if the service provider can access this booking
        if (!in_array($booking->service_id, $serviceProvider->service_ids ?? [])) {
            abort(403, 'You do not have access to this booking.');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }

    /**
     * Get booking statistics for AJAX requests.
     */
    public function getStats()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider profile not found'], 404);
        }

        $stats = [
            'total_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])->count(),
            'pending_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->where('status', 'confirmed')->count(),
            'completed_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->where('status', 'completed')->count(),
            'cancelled_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->where('status', 'cancelled')->count(),
            'today_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->whereDate('booking_date', today())->count(),
            'this_week_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->whereMonth('booking_date', now()->month)
                ->whereYear('booking_date', now()->year)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get upcoming bookings for today.
     */
    public function getTodayBookings()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider profile not found'], 404);
        }

        $todayBookings = Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
            ->whereDate('booking_date', today())
            ->with(['service', 'user'])
            ->orderBy('booking_time')
            ->get();

        return response()->json($todayBookings);
    }

    /**
     * Bulk update booking statuses.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id',
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
        ]);

        // Verify all bookings belong to services this provider manages
        $bookings = Booking::whereIn('id', $request->booking_ids)
            ->whereIn('service_id', $serviceProvider->service_ids ?? [])
            ->get();

        if ($bookings->count() !== count($request->booking_ids)) {
            return redirect()->back()->with('error', 'Some bookings do not belong to your services.');
        }

        $bookings->each(function ($booking) use ($request) {
            $booking->update(['status' => $request->status]);
        });

        return redirect()->back()->with('success', 'Booking statuses updated successfully.');
    }
}
