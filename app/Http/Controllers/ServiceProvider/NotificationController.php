<?php

namespace App\Http\Controllers\ServiceProvider;

use App\Http\Controllers\Controller;
use App\Models\VendorNotification;
use App\Models\VendorNotificationRead;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $serviceProvider = $user?->serviceProvider;
        $companyId = $serviceProvider?->company_id;
        $branchIds = array_values($serviceProvider?->branch_ids ?? []);

        if (!$companyId) {
            return view('service-provider.notifications.index', [
                'notifications' => collect(),
                'hasCompany' => false,
                'hasAllowedBranches' => false,
            ]);
        }

        if (empty($branchIds)) {
            return view('service-provider.notifications.index', [
                'notifications' => collect(),
                'hasCompany' => true,
                'hasAllowedBranches' => false,
            ]);
        }

        $notificationScope = VendorNotification::forVendorCompanyRecipient($companyId)
            ->visibleToServiceProviderBranches($branchIds);

        $this->markAsReadForUser((clone $notificationScope), $user->id);

        $notifications = $notificationScope
            ->latest()
            ->paginate(20);

        return view('service-provider.notifications.index', [
            'notifications' => $notifications,
            'hasCompany' => true,
            'hasAllowedBranches' => true,
        ]);
    }

    private function markAsReadForUser($notificationScope, int $userId): void
    {
        $ids = $notificationScope
            ->unreadByUser($userId)
            ->pluck('id');

        if ($ids->isEmpty()) {
            return;
        }

        $now = now();
        $payload = $ids->map(static function ($id) use ($userId, $now) {
            return [
                'vendor_notification_id' => $id,
                'user_id' => $userId,
                'read_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->all();

        VendorNotificationRead::insertOrIgnore($payload);
    }
}
