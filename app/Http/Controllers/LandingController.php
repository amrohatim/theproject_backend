<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Service;

class LandingController extends Controller
{
    /**
     * Display the landing page with real statistics.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get real statistics from the database
        $totalProducts = Product::count();
        $totalVendors = User::where('role', 'vendor')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Calculate satisfaction rate
        // For now, we'll use a default of 99% since we don't have comprehensive ratings yet
        // TODO: In the future, calculate this from actual ratings/reviews:
        // $satisfactionRate = round(
        //     DB::table('ratings')
        //       ->where('rating', '>=', 4)
        //       ->count() / DB::table('ratings')->count() * 100
        // );
        $satisfactionRate = 99;

        // Check authentication status and user role for conditional button behavior
        $isAuthenticated = Auth::check();
        $userRole = $isAuthenticated ? Auth::user()->role : null;

        // Determine the appropriate redirect URL for the "Get Started" button
        $getStartedUrl = $this->getGetStartedUrl($isAuthenticated, $userRole);

        return view('landing', compact(
            'totalProducts',
            'totalVendors',
            'totalCustomers',
            'satisfactionRate',
            'isAuthenticated',
            'userRole',
            'getStartedUrl'
        ));
    }

    /**
     * Determine the appropriate URL for the "Get Started" button based on user authentication and role.
     *
     * @param bool $isAuthenticated
     * @param string|null $userRole
     * @return string
     */
    private function getGetStartedUrl($isAuthenticated, $userRole)
    {
        if (!$isAuthenticated) {
            // Non-authenticated users go to login
            return route('login');
        }

        // Authenticated users go to their appropriate dashboard
        switch ($userRole) {
            case 'admin':
                return route('admin.dashboard');
            case 'vendor':
                return route('vendor.dashboard');
            case 'provider':
                return route('provider.dashboard');
            default:
                // Regular customers go to home page (could be changed to a customer dashboard if available)
                return url('/');
        }
    }
}
