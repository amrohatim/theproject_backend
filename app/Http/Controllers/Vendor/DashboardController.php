<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Booking;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the vendor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // Check if the vendor has a company
        $company = Company::where('user_id', $user->id)->first();
        $hasCompany = !is_null($company);

        // Get statistics
        if ($hasCompany) {
            // Get branches for this vendor's company
            $totalBranches = Branch::join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', $user->id)
                ->count();

            // Get products for this vendor's branches
            $totalProducts = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', $user->id)
                ->count();

            // Get services for this vendor's branches
            $totalServices = Service::join('branches', 'services.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', $user->id)
                ->count();

            // Get recent products
            $recentProducts = Product::join('branches', 'products.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', $user->id)
                ->select('products.*')
                ->with('branch')
                ->orderBy('products.created_at', 'desc')
                ->take(5)
                ->get();

            // Get recent services
            $recentServices = Service::join('branches', 'services.branch_id', '=', 'branches.id')
                ->join('companies', 'branches.company_id', '=', 'companies.id')
                ->where('companies.user_id', $user->id)
                ->select('services.*')
                ->with('branch')
                ->orderBy('services.created_at', 'desc')
                ->take(5)
                ->get();
        } else {
            // If no company, set all counts to 0
            $totalBranches = 0;
            $totalProducts = 0;
            $totalServices = 0;
            $recentProducts = collect();
            $recentServices = collect();
        }

        // For now, we don't have orders and bookings models, so we'll set them to 0
        $totalOrders = 0;
        $totalBookings = 0;

        return view('vendor.dashboard', compact(
            'hasCompany',
            'totalBranches',
            'totalProducts',
            'totalServices',
            'totalOrders',
            'totalBookings',
            'recentProducts',
            'recentServices'
        ));
    }
}
