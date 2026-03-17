<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorNotification extends Model
{
    use HasFactory;

    public const RECIPIENT_VENDOR = 'vendor';
    public const RECIPIENT_PROVIDER = 'provider';
    public const RECIPIENT_MERCHANT = 'merchant';

    public const TYPE_PRODUCT = 'product';
    public const TYPE_SERVICE = 'service';
    public const TYPE_ORDER = 'order';
    public const TYPE_BOOKING = 'booking';

    public const TYPES = [
        self::TYPE_PRODUCT,
        self::TYPE_SERVICE,
        self::TYPE_ORDER,
        self::TYPE_BOOKING,
    ];

    public const RECIPIENT_TYPES = [
        self::RECIPIENT_VENDOR,
        self::RECIPIENT_PROVIDER,
        self::RECIPIENT_MERCHANT,
    ];

    protected $fillable = [
        'notification_type',
        'sender_name',
        'message',
        'message_arabic',
        'is_opened',
        'recipient_type',
        'recipient_id',
        'product_id',
        'provider_product_id',
        'service_id',
        'order_item_id',
        'booking_id',
    ];

    protected $casts = [
        'is_opened' => 'boolean',
    ];

    public function reads()
    {
        return $this->hasMany(VendorNotificationRead::class);
    }

    public function scopeForVendorCompanyRecipient($query, int $companyId)
    {
        return $query->where('recipient_type', self::RECIPIENT_VENDOR)
            ->where('recipient_id', $companyId);
    }

    public function scopeUnreadByUser($query, int $userId)
    {
        return $query->whereDoesntHave('reads', function ($readQuery) use ($userId) {
            $readQuery->where('user_id', $userId);
        });
    }

    public function scopeVisibleToServiceProviderBranches($query, array $branchIds)
    {
        if (empty($branchIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function ($visibilityQuery) use ($branchIds) {
            // Global company notifications (not tied to a branch-bound entity) are visible to all service providers in the same company.
            $visibilityQuery->where(function ($genericQuery) {
                $genericQuery->whereNull('product_id')
                    ->whereNull('service_id')
                    ->whereNull('order_item_id')
                    ->whereNull('booking_id');
            })
                ->orWhereHas('product', function ($productQuery) use ($branchIds) {
                    $productQuery->whereIn('branch_id', $branchIds);
                })
                ->orWhereHas('service', function ($serviceQuery) use ($branchIds) {
                    $serviceQuery->whereIn('branch_id', $branchIds);
                })
                ->orWhereHas('booking', function ($bookingQuery) use ($branchIds) {
                    $bookingQuery->whereIn('branch_id', $branchIds);
                })
                ->orWhereHas('orderItem.product', function ($orderItemProductQuery) use ($branchIds) {
                    $orderItemProductQuery->whereIn('branch_id', $branchIds);
                });
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'recipient_id');
    }

    public function providerRecipient()
    {
        return $this->belongsTo(Provider::class, 'recipient_id');
    }

    public function merchantRecipient()
    {
        return $this->belongsTo(Merchant::class, 'recipient_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function providerProduct()
    {
        return $this->belongsTo(ProviderProduct::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
