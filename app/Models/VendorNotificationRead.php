<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorNotificationRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_notification_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(VendorNotification::class, 'vendor_notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
