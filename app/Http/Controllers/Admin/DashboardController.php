<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Service;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics
        $totalUsers = User::count();
        $totalVendors = User::where('role', 'vendor')->count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalProducts = Product::count();
        $totalServices = Service::count();
        $totalCompanies = Company::count();
        $totalBranches = Branch::count();
        $totalCategories = Category::count();
        
        // Get recent users
        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get recent products
        $recentProducts = Product::with('branch')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get recent services
        $recentServices = Service::with('branch')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers',
            'totalVendors',
            'totalCustomers',
            'totalProducts',
            'totalServices',
            'totalCompanies',
            'totalBranches',
            'totalCategories',
            'recentUsers',
            'recentProducts',
            'recentServices'
        ));
    }
}
