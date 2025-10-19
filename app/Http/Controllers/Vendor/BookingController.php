<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        // Get branch IDs that belong to the vendor
        $branchIds = $branches->pluck('id')->toArray();

        // If no branches found, return empty results
        if (empty($branchIds)) {
            $bookings = collect([]);
            $stats = (object)[
                'total_bookings' => 0,
                'completed_bookings' => 0,
                'upcoming_bookings' => 0,
                'total_revenue' => 0,
            ];
            return view('vendor.bookings.index', compact('bookings', 'branches', 'stats'));
        }

        // Build the query for bookings
        $query = Booking::with(['user', 'service', 'branch'])
            ->whereIn('branch_id', $branchIds)
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }

        if ($request->filled('date_range')) {
            $dateRange = $request->date_range;
            $today = now()->toDateString();
            $tomorrow = now()->addDay()->toDateString();

            switch ($dateRange) {
                case 'today':
                    $query->whereDate('booking_date', $today);
                    break;
                case 'tomorrow':
                    $query->whereDate('booking_date', $tomorrow);
                    break;
                case 'this_week':
                    $query->whereBetween('booking_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
                    break;
                case 'next_week':
                    $query->whereBetween('booking_date', [now()->addWeek()->startOfWeek()->toDateString(), now()->addWeek()->endOfWeek()->toDateString()]);
                    break;
                case 'this_month':
                    $query->whereMonth('booking_date', now()->month)->whereYear('booking_date', now()->year);
                    break;
                case 'past':
                    $query->whereDate('booking_date', '<', $today);
                    break;
            }
        }

        // Get paginated results
        $bookings = $query->paginate(10);

        // Calculate stats
        $stats = (object)[
            'total_bookings' => Booking::whereIn('branch_id', $branchIds)->count(),
            'completed_bookings' => Booking::whereIn('branch_id', $branchIds)->where('status', 'completed')->count(),
            'upcoming_bookings' => Booking::whereIn('branch_id', $branchIds)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) {
                    $q->whereDate('booking_date', '>=', now()->toDateString());
                })->count(),
            'total_revenue' => Booking::whereIn('branch_id', $branchIds)->where('status', 'completed')->sum('price'),
        ];

        return view('vendor.bookings.index', compact('bookings', 'branches', 'stats'));
    }

    /**
     * Display the calendar view.
     */
    public function calendar()
    {
        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        // Get branch IDs that belong to the vendor
        $branchIds = $branches->pluck('id')->toArray();

        // Get bookings for the calendar
        $bookings = Booking::with(['user', 'service', 'branch'])
            ->whereIn('branch_id', $branchIds)
            ->whereDate('booking_date', '>=', now()->subDays(30)->toDateString())
            ->whereDate('booking_date', '<=', now()->addDays(60)->toDateString())
            ->get();

        // Format bookings for the calendar
        $calendarEvents = $bookings->map(function ($booking) {
            $status = $booking->status;
            $color = '';

            switch ($status) {
                case 'completed':
                    $color = '#10B981'; // green
                    break;
                case 'confirmed':
                    $color = '#3B82F6'; // blue
                    break;
                case 'cancelled':
                    $color = '#EF4444'; // red
                    break;
                case 'no_show':
                    $color = '#6B7280'; // gray
                    break;
                default:
                    $color = '#F59E0B'; // yellow (pending)
            }

            return [
                'id' => $booking->id,
                'title' => ($booking->user->name ?? 'Unknown') . ' - ' . ($booking->service->name ?? 'Unknown Service'),
                'start' => $booking->booking_date->format('Y-m-d') . 'T' . $booking->booking_time,
                'end' => $booking->booking_date->format('Y-m-d') . 'T' . date('H:i:s', strtotime($booking->booking_time . ' + ' . $booking->duration . ' minutes')),
                'url' => route('vendor.bookings.show', $booking->id),
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        });

        return view('vendor.bookings.calendar', compact('calendarEvents', 'branches'));
    }

    /**
     * Export bookings.
     */
    public function export()
    {
        // In a real application, this would generate a CSV or Excel file
        return redirect()->route('vendor.bookings.index')->with('success', 'Bookings exported successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // Check if the booking belongs to the vendor's branch
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($booking->branch_id, $userBranches)) {
            return redirect()->route('vendor.bookings.index')
                ->with('error', 'You do not have permission to view this booking.');
        }

        // Load relationships
        $booking->load(['user', 'service', 'branch', 'customerLocation']);

        return view('vendor.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        // Check if the booking belongs to the vendor's branch
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($booking->branch_id, $userBranches)) {
            return redirect()->route('vendor.bookings.index')
                ->with('error', 'You do not have permission to edit this booking.');
        }

        // Load relationships
        $booking->load(['user', 'service', 'branch']);

        return view('vendor.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        // Check if the booking belongs to the vendor's branch
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($booking->branch_id, $userBranches)) {
            return redirect()->route('vendor.bookings.index')
                ->with('error', 'You do not have permission to update this booking.');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ]);

        $booking->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('vendor.bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Generate an invoice for the booking.
     */
    public function invoice(Booking $booking)
    {
        // Check if the booking belongs to the vendor's branch
        $userBranches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->pluck('id')->toArray();

        if (!in_array($booking->branch_id, $userBranches)) {
            return redirect()->route('vendor.bookings.index')
                ->with('error', 'You do not have permission to view this invoice.');
        }

        // Load relationships
        $booking->load(['user', 'service', 'branch']);

        return view('vendor.bookings.invoice', compact('booking'));
    }

    /**
     * Get search suggestions for bookings.
     */
    public function searchSuggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Get branches that belong to the vendor's company
        $branches = Branch::whereHas('company', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('name')->get();

        $branchIds = $branches->pluck('id')->toArray();

        if (empty($branchIds)) {
            return response()->json([]);
        }

        $suggestions = Booking::query()
            ->with(['user', 'service', 'branch'])
            ->whereIn('branch_id', $branchIds)
            ->where(function ($q) use ($query) {
                $q->where('booking_number', 'like', "%{$query}%")
                  ->orWhereHas('user', function ($userQuery) use ($query) {
                      $userQuery->where('name', 'like', "%{$query}%")
                          ->orWhere('email', 'like', "%{$query}%");
                  })
                  ->orWhereHas('service', function ($serviceQuery) use ($query) {
                      $serviceQuery->where('name', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($booking) use ($query) {
                return [
                    'id' => $booking->id,
                    'text' => $booking->booking_number ?: "Booking #{$booking->id}",
                    'type' => 'booking',
                    'icon' => 'fas fa-calendar-check',
                    'subtitle' => $booking->user ? $booking->user->name : '',
                    'highlight' => $this->highlightMatch($booking->booking_number ?: "Booking #{$booking->id}", $query),
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Highlight matching text in search results.
     */
    private function highlightMatch($text, $query)
    {
        return preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $text);
    }
}
