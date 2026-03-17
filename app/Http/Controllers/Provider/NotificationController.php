<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\VendorNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'provider') {
            abort(403);
        }

        $provider = $user->providerRecord;

        if (!$provider) {
            return view('provider.notifications.index', [
                'notifications' => null,
                'hasProvider' => false,
            ]);
        }

        $notificationScope = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_PROVIDER)
            ->where('recipient_id', $provider->id);

        (clone $notificationScope)
            ->where('is_opened', false)
            ->update(['is_opened' => true]);

        $notifications = $notificationScope->latest()->paginate(20);

        return view('provider.notifications.index', [
            'notifications' => $notifications,
            'hasProvider' => true,
        ]);
    }
}
