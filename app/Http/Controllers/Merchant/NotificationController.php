<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\VendorNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'merchant') {
            abort(403);
        }

        $merchant = $user->merchantRecord;

        if (!$merchant) {
            return view('merchant.notifications.index', [
                'notifications' => null,
                'hasMerchant' => false,
            ]);
        }

        $notificationScope = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_MERCHANT)
            ->where('recipient_id', $merchant->id);

        (clone $notificationScope)
            ->where('is_opened', false)
            ->update(['is_opened' => true]);

        $notifications = $notificationScope->latest()->paginate(20);

        return view('merchant.notifications.index', [
            'notifications' => $notifications,
            'hasMerchant' => true,
        ]);
    }
}
