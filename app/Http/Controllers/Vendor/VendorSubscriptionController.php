<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorSubscription;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorSubscriptionController extends Controller
{
    /**
     * Display the vendor's subscription information.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get the vendor's company
        $company = Company::where('user_id', $user->id)->first();
        
        if (!$company) {
            return view('vendor.subscription.index', [
                'hasCompany' => false,
                'currentSubscription' => null,
                'subscriptionHistory' => collect([]),
            ]);
        }
        
        // Get current active subscription
        $currentSubscription = VendorSubscription::with('subscriptionType')
            ->where('company_id', $company->id)
            ->where('status', 'active')
            ->orderBy('end_at', 'desc')
            ->first();
        
        // Get subscription history (all subscriptions ordered by most recent)
        $subscriptionHistory = VendorSubscription::with('subscriptionType')
            ->where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('vendor.subscription.index', [
            'hasCompany' => true,
            'company' => $company,
            'currentSubscription' => $currentSubscription,
            'subscriptionHistory' => $subscriptionHistory,
        ]);
    }
}

