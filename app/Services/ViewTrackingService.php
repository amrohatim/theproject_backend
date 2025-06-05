<?php

namespace App\Services;

use App\Models\ViewTracking;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Provider;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ViewTrackingService
{
    /**
     * Time window for considering views as duplicates (in hours).
     */
    const DUPLICATE_WINDOW_HOURS = 24;

    /**
     * Rate limit: maximum views per user per entity per hour.
     */
    const RATE_LIMIT_PER_HOUR = 5;

    /**
     * Track a view for an entity with duplicate prevention.
     *
     * @param string $entityType
     * @param int $entityId
     * @param Request $request
     * @return bool True if view was tracked, false if duplicate/rate limited
     */
    public function trackView(string $entityType, int $entityId, Request $request): bool
    {
        try {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            $deviceFingerprint = $request->header('X-Device-Fingerprint');
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Check if this is a duplicate view within the time window
            if ($this->isDuplicateView($entityType, $entityId, $userId, $sessionId, $deviceFingerprint, $ipAddress)) {
                Log::info("Duplicate view blocked for {$entityType} {$entityId}", [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'ip_address' => $ipAddress
                ]);
                return false;
            }

            // Check rate limiting
            if ($this->isRateLimited($entityType, $entityId, $userId, $sessionId, $deviceFingerprint, $ipAddress)) {
                Log::warning("Rate limit exceeded for {$entityType} {$entityId}", [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'ip_address' => $ipAddress
                ]);
                return false;
            }

            // Record the view
            ViewTracking::create([
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'viewed_at' => Carbon::now(),
            ]);

            // Increment the entity's view count
            $this->incrementEntityViewCount($entityType, $entityId);

            Log::info("View tracked for {$entityType} {$entityId}", [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error tracking view for {$entityType} {$entityId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if this view is a duplicate within the time window.
     */
    private function isDuplicateView(
        string $entityType,
        int $entityId,
        ?int $userId,
        ?string $sessionId,
        ?string $deviceFingerprint,
        string $ipAddress
    ): bool {
        $since = Carbon::now()->subHours(self::DUPLICATE_WINDOW_HOURS);

        $query = ViewTracking::forEntity($entityType, $entityId)
            ->withinTimeWindow($since);

        // Check for authenticated user
        if ($userId) {
            return $query->forUser($userId)->exists();
        }

        // Check for anonymous user by device fingerprint (most reliable)
        if ($deviceFingerprint) {
            return $query->forDevice($deviceFingerprint)->exists();
        }

        // Check by session ID (fallback)
        if ($sessionId) {
            return $query->forSession($sessionId)->exists();
        }

        // Last resort: check by IP address (least reliable)
        return $query->forIp($ipAddress)->exists();
    }

    /**
     * Check if the user/session is rate limited.
     */
    private function isRateLimited(
        string $entityType,
        int $entityId,
        ?int $userId,
        ?string $sessionId,
        ?string $deviceFingerprint,
        string $ipAddress
    ): bool {
        $since = Carbon::now()->subHour();

        $query = ViewTracking::forEntity($entityType, $entityId)
            ->withinTimeWindow($since);

        // Check rate limit for authenticated user
        if ($userId) {
            $viewCount = $query->forUser($userId)->count();
            return $viewCount >= self::RATE_LIMIT_PER_HOUR;
        }

        // Check rate limit for anonymous user by device fingerprint
        if ($deviceFingerprint) {
            $viewCount = $query->forDevice($deviceFingerprint)->count();
            return $viewCount >= self::RATE_LIMIT_PER_HOUR;
        }

        // Check rate limit by session ID
        if ($sessionId) {
            $viewCount = $query->forSession($sessionId)->count();
            return $viewCount >= self::RATE_LIMIT_PER_HOUR;
        }

        // Check rate limit by IP address
        $viewCount = $query->forIp($ipAddress)->count();
        return $viewCount >= self::RATE_LIMIT_PER_HOUR;
    }

    /**
     * Increment the view count for the specific entity.
     */
    private function incrementEntityViewCount(string $entityType, int $entityId): void
    {
        try {
            switch ($entityType) {
                case 'vendor':
                case 'company':
                    $entity = Company::find($entityId);
                    break;
                case 'branch':
                    $entity = Branch::find($entityId);
                    break;
                case 'provider':
                    $entity = Provider::find($entityId);
                    break;
                case 'category':
                    $entity = Category::find($entityId);
                    break;
                default:
                    Log::warning("Unknown entity type for view tracking: {$entityType}");
                    return;
            }

            if ($entity) {
                $entity->increment('view_count');
            }
        } catch (\Exception $e) {
            Log::error("Error incrementing view count for {$entityType} {$entityId}: " . $e->getMessage());
        }
    }

    /**
     * Get unique view count for an entity.
     */
    public function getUniqueViewCount(string $entityType, int $entityId): int
    {
        try {
            $result = ViewTracking::forEntity($entityType, $entityId)
                ->uniqueViews()
                ->first();

            return $result->unique_views ?? 0;
        } catch (\Exception $e) {
            Log::error("Error getting unique view count for {$entityType} {$entityId}: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get view analytics for an entity.
     */
    public function getViewAnalytics(string $entityType, int $entityId): array
    {
        try {
            $totalViews = ViewTracking::forEntity($entityType, $entityId)->count();
            $uniqueViews = $this->getUniqueViewCount($entityType, $entityId);
            $recentViews = ViewTracking::forEntity($entityType, $entityId)->recent()->count();

            return [
                'total_views' => $totalViews,
                'unique_views' => $uniqueViews,
                'recent_views' => $recentViews,
                'engagement_rate' => $totalViews > 0 ? round(($uniqueViews / $totalViews) * 100, 2) : 0,
            ];
        } catch (\Exception $e) {
            Log::error("Error getting view analytics for {$entityType} {$entityId}: " . $e->getMessage());
            return [
                'total_views' => 0,
                'unique_views' => 0,
                'recent_views' => 0,
                'engagement_rate' => 0,
            ];
        }
    }

    /**
     * Clean up old view tracking records (older than 30 days).
     */
    public function cleanupOldRecords(): int
    {
        try {
            $cutoffDate = Carbon::now()->subDays(30);
            $deletedCount = ViewTracking::where('viewed_at', '<', $cutoffDate)->delete();
            
            Log::info("Cleaned up {$deletedCount} old view tracking records");
            return $deletedCount;
        } catch (\Exception $e) {
            Log::error("Error cleaning up old view tracking records: " . $e->getMessage());
            return 0;
        }
    }
}
