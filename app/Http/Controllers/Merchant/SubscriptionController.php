<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\MerchantSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display the merchant's subscription information.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get the merchant record
        $merchant = Merchant::where('user_id', $user->id)->first();
        
        if (!$merchant) {
            return view('merchant.subscription.index', [
                'hasMerchant' => false,
                'currentSubscription' => null,
                'subscriptionHistory' => collect([]),
            ]);
        }
        
        // Get current active subscription
        $currentSubscription = MerchantSubscription::with('subscriptionType')
            ->where('merchant_id', $merchant->id)
            ->where('status', 'active')
            ->orderBy('end_at', 'desc')
            ->first();
        
        // Get subscription history (all subscriptions ordered by most recent)
        $subscriptionHistory = MerchantSubscription::with('subscriptionType')
            ->where('merchant_id', $merchant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('merchant.subscription.index', [
            'hasMerchant' => true,
            'merchant' => $merchant,
            'currentSubscription' => $currentSubscription,
            'subscriptionHistory' => $subscriptionHistory,
        ]);
    }
}

