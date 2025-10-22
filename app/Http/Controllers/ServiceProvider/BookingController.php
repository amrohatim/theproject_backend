<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
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

        $query = $this->buildAccessibleBookingsQuery($serviceProvider)
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
        $services = $this->getAccessibleServices($serviceProvider);

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
        if (!$this->canAccessBooking($serviceProvider, $booking)) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['service', 'user']);

        return view('service-provider.bookings.show', compact('booking', 'serviceProvider'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        if (!$this->canAccessBooking($serviceProvider, $booking)) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['service', 'user', 'branch']);

        return view('service-provider.bookings.edit', compact('booking', 'serviceProvider'));
    }

    /**
     * Display the booking invoice.
     */
    public function invoice(Booking $booking)
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        if (!$this->canAccessBooking($serviceProvider, $booking)) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['service', 'user', 'branch.company']);

        return view('service-provider.bookings.invoice', compact('booking', 'serviceProvider'));
    }

    /**
     * Display the calendar view of bookings.
     */
    public function calendar()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        $branchIds = $this->normalizeIdArray($serviceProvider->branch_ids ?? []);
        $branches = !empty($branchIds)
            ? Branch::whereIn('id', $branchIds)->orderBy('name')->get()
            : collect();

        $bookings = $this->buildAccessibleBookingsQuery($serviceProvider)
            ->with(['user', 'service', 'branch'])
            ->whereDate('booking_date', '>=', now()->subDays(30)->toDateString())
            ->whereDate('booking_date', '<=', now()->addDays(60)->toDateString())
            ->get();

        $calendarEvents = $bookings->map(function (Booking $booking) {
            $status = $booking->status ?? 'pending';
            $color = match ($status) {
                'completed' => '#10B981',
                'confirmed' => '#3B82F6',
                'in_progress' => '#A855F7',
                'cancelled' => '#EF4444',
                default => '#F59E0B',
            };

            $startDate = optional($booking->booking_date)->format('Y-m-d') ?? now()->format('Y-m-d');
            $startTime = $booking->booking_time ?? '00:00:00';
            $durationMinutes = (int) ($booking->duration ?? 60);
            $endTimestamp = \Carbon\Carbon::parse("{$startDate} {$startTime}")->addMinutes($durationMinutes);

            return [
                'id' => $booking->id,
                'title' => trim(($booking->user->name ?? __('service_provider.unknown_customer')) . ' - ' . ($booking->service->name ?? __('service_provider.unknown_service'))),
                'start' => "{$startDate}T{$startTime}",
                'end' => $endTimestamp->format('Y-m-d\TH:i:s'),
                'url' => route('service-provider.bookings.show', $booking),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'branchId' => $booking->branch_id,
                'status' => $status,
                'extendedProps' => [
                    'branch_id' => $booking->branch_id,
                    'status' => $status,
                ],
            ];
        });

        return view('service-provider.bookings.calendar', compact('calendarEvents', 'branches', 'serviceProvider'));
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
        if (!$this->canAccessBooking($serviceProvider, $booking)) {
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
            'total_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)->count(),
            'pending_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)
                ->where('status', 'pending')->count(),
            'confirmed_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)
                ->where('status', 'confirmed')->count(),
            'completed_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)
                ->where('status', 'completed')->count(),
            'cancelled_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)
                ->where('status', 'cancelled')->count(),
            'today_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)
                ->whereDate('booking_date', today())->count(),
            'this_week_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)
                ->whereBetween('booking_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_bookings' => $this->buildAccessibleBookingsQuery($serviceProvider)
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

        $todayBookings = $this->buildAccessibleBookingsQuery($serviceProvider)
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
        $bookings = $this->buildAccessibleBookingsQuery($serviceProvider)
            ->whereIn('id', $request->booking_ids)
            ->get();

        if ($bookings->count() !== count($request->booking_ids)) {
            return redirect()->back()->with('error', 'Some bookings do not belong to your assigned services or branches.');
        }

        $bookings->each(function ($booking) use ($request) {
            $booking->update(['status' => $request->status]);
        });

        return redirect()->back()->with('success', 'Booking statuses updated successfully.');
    }

    /**
     * Build a query limited to bookings the service provider can access.
     */
    protected function buildAccessibleBookingsQuery($serviceProvider): Builder
    {
        $serviceIds = $this->normalizeIdArray($serviceProvider->service_ids ?? []);
        $branchIds = $this->normalizeIdArray($serviceProvider->branch_ids ?? []);

        if (empty($serviceIds) && empty($branchIds)) {
            return Booking::query()->whereRaw('0 = 1');
        }

        return Booking::query()->where(function ($query) use ($serviceIds, $branchIds) {
            if (!empty($serviceIds)) {
                $query->whereIn('service_id', $serviceIds);
            }

            if (!empty($branchIds)) {
                if (!empty($serviceIds)) {
                    $query->orWhereIn('branch_id', $branchIds);
                } else {
                    $query->whereIn('branch_id', $branchIds);
                }
            }
        });
    }

    /**
     * Determine if the service provider can access the given booking.
     */
    protected function canAccessBooking($serviceProvider, Booking $booking): bool
    {
        $serviceIds = $this->normalizeIdArray($serviceProvider->service_ids ?? []);
        $branchIds = $this->normalizeIdArray($serviceProvider->branch_ids ?? []);

        $serviceId = $booking->service_id;
        if (!is_null($serviceId) && in_array((int) $serviceId, $serviceIds, true)) {
            return true;
        }

        $branchId = $booking->branch_id;
        if (!is_null($branchId) && in_array((int) $branchId, $branchIds, true)) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve the services the service provider can manage.
     */
    protected function getAccessibleServices($serviceProvider)
    {
        $serviceIds = $this->normalizeIdArray($serviceProvider->service_ids ?? []);
        $branchIds = $this->normalizeIdArray($serviceProvider->branch_ids ?? []);

        if (empty($serviceIds) && empty($branchIds)) {
            return collect();
        }

        return Service::query()
            ->where(function ($query) use ($serviceIds, $branchIds) {
                if (!empty($serviceIds)) {
                    $query->whereIn('id', $serviceIds);
                }

                if (!empty($branchIds)) {
                    if (!empty($serviceIds)) {
                        $query->orWhereIn('branch_id', $branchIds);
                    } else {
                        $query->whereIn('branch_id', $branchIds);
                    }
                }
            })
            ->get();
    }

    /**
     * Normalize an array of IDs into unique integers.
     */
    protected function normalizeIdArray($ids): array
    {
        if (!is_array($ids)) {
            return [];
        }

        $normalized = array_map(function ($value) {
            if (is_numeric($value)) {
                return (int) $value;
            }

            if (is_array($value) && isset($value['id']) && is_numeric($value['id'])) {
                return (int) $value['id'];
            }

            return null;
        }, $ids);

        return array_values(array_unique(array_filter($normalized, static function ($value) {
            return !is_null($value);
        })));
    }
}
