<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the service provider dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return redirect('/')->with('error', 'Service provider profile not found.');
        }

        // Get services that this service provider can manage
        $services = Service::whereIn('id', $serviceProvider->service_ids ?? [])
            ->with(['branch'])
            ->get();

        // Get recent bookings for services this provider manages
        $recentBookings = Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get statistics (with error handling for missing columns)
        try {
            $stats = [
                'total_services' => $serviceProvider->number_of_services,
                'total_branches' => count($serviceProvider->branch_ids ?? []),
                'total_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])->count(),
                'pending_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                    ->where('status', 'pending')
                    ->count(),
                'completed_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                    ->where('status', 'completed')
                    ->count(),
                'total_revenue' => 0, // Default to 0 if column doesn't exist
            ];
        } catch (\Exception $e) {
            // Fallback stats if database queries fail
            $stats = [
                'total_services' => $serviceProvider->number_of_services,
                'total_branches' => count($serviceProvider->branch_ids ?? []),
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'completed_bookings' => 0,
                'total_revenue' => 0,
            ];
        }

        // Get monthly booking statistics for chart (with error handling)
        try {
            $monthlyBookings = Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } catch (\Exception $e) {
            $monthlyBookings = collect();
        }

        // Get deals for services this provider manages
        try {
            $activeDeals = Deal::where('applies_to', 'services')
                ->where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($query) use ($serviceProvider) {
                    // Check if any of the service provider's service IDs are in the deal's service_ids JSON array
                    foreach ($serviceProvider->service_ids ?? [] as $serviceId) {
                        $query->orWhereJsonContains('service_ids', $serviceId);
                    }
                })
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Fallback to empty collection if query fails
            $activeDeals = collect();
        }

        return view('service-provider.dashboard', compact(
            'user',
            'serviceProvider',
            'services',
            'recentBookings',
            'stats',
            'monthlyBookings',
            'activeDeals'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function getStats()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider profile not found'], 404);
        }

        $stats = [
            'total_services' => $serviceProvider->number_of_services,
            'total_branches' => count($serviceProvider->branch_ids ?? []),
            'total_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])->count(),
            'pending_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->where('status', 'pending')
                ->count(),
            'completed_bookings' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->where('status', 'completed')
                ->count(),
            'total_revenue' => Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
                ->where('status', 'completed')
                ->sum('total_amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Get recent activity for AJAX requests.
     */
    public function getRecentActivity()
    {
        $user = Auth::user();
        $serviceProvider = $user->serviceProvider;

        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider profile not found'], 404);
        }

        $recentBookings = Booking::whereIn('service_id', $serviceProvider->service_ids ?? [])
            ->with(['service', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($recentBookings);
    }
}

// Additional controllers will be created separately for specific functionality
