<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'vendor') {
            abort(403);
        }

        $company = $user?->company;

        if (!$company) {
            return view('vendor.notifications.index', [
                'notifications' => collect(),
                'hasCompany' => false,
            ]);
        }

        $notificationScope = VendorNotification::where('recipient_type', VendorNotification::RECIPIENT_VENDOR)
            ->where('recipient_id', $company->id);

        (clone $notificationScope)
            ->where('is_opened', false)
            ->update(['is_opened' => true]);

        $notifications = $notificationScope->latest()->paginate(20);

        return view('vendor.notifications.index', [
            'notifications' => $notifications,
            'hasCompany' => true,
        ]);
    }
}
