<?php

namespace App\Http\Controllers\ProductsManager;

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
        $productsManager = $user?->productsManager;
        $companyId = $productsManager?->company_id;

        if (!$companyId) {
            return view('products-manager.notifications.index', [
                'notifications' => collect(),
                'hasCompany' => false,
            ]);
        }

        $notificationScope = VendorNotification::forVendorCompanyRecipient($companyId);
        $this->markAsReadForUser((clone $notificationScope), $user->id);

        $notifications = $notificationScope
            ->latest()
            ->paginate(20);

        return view('products-manager.notifications.index', [
            'notifications' => $notifications,
            'hasCompany' => true,
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
