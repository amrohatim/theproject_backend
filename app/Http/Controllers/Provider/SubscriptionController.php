<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderSubscription;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display the provider's subscription information.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get the provider record
        $provider = Provider::where('user_id', $user->id)->first();
        
        if (!$provider) {
            return view('provider.subscription.index', [
                'hasProvider' => false,
                'currentSubscription' => null,
                'subscriptionHistory' => collect([]),
            ]);
        }
        
        // Get current active subscription
        $currentSubscription = ProviderSubscription::with('subscriptionType')
            ->where('provider_id', $provider->id)
            ->where('status', 'active')
            ->orderBy('end_at', 'desc')
            ->first();
        
        // Get subscription history (all subscriptions ordered by most recent)
        $subscriptionHistory = ProviderSubscription::with('subscriptionType')
            ->where('provider_id', $provider->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('provider.subscription.index', [
            'hasProvider' => true,
            'provider' => $provider,
            'currentSubscription' => $currentSubscription,
            'subscriptionHistory' => $subscriptionHistory,
        ]);
    }
}

