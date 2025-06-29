<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ViewTracking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'user_id',
        'session_id',
        'device_fingerprint',
        'ip_address',
        'user_agent',
        'viewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'view_tracking';

    /**
     * Get the user that made the view.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by entity type and ID.
     */
    public function scopeForEntity($query, string $entityType, int $entityId)
    {
        return $query->where('entity_type', $entityType)
                    ->where('entity_id', $entityId);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by session.
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope to filter by device fingerprint.
     */
    public function scopeForDevice($query, string $deviceFingerprint)
    {
        return $query->where('device_fingerprint', $deviceFingerprint);
    }

    /**
     * Scope to filter by IP address.
     */
    public function scopeForIp($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope to filter views within a time window.
     */
    public function scopeWithinTimeWindow($query, Carbon $since)
    {
        return $query->where('viewed_at', '>=', $since);
    }

    /**
     * Scope to get recent views (within last 24 hours).
     */
    public function scopeRecent($query)
    {
        return $query->where('viewed_at', '>=', Carbon::now()->subDay());
    }

    /**
     * Scope to get unique views by counting distinct identifiers.
     */
    public function scopeUniqueViews($query)
    {
        return $query->selectRaw('
            COUNT(DISTINCT CASE 
                WHEN user_id IS NOT NULL THEN user_id 
                WHEN device_fingerprint IS NOT NULL THEN device_fingerprint 
                WHEN session_id IS NOT NULL THEN session_id 
                ELSE ip_address 
            END) as unique_views
        ');
    }
}
