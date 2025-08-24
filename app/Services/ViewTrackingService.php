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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
     * Enhanced with improved logging and error handling.
     *
     * @param string $entityType
     * @param int $entityId
     * @param Request $request
     * @return bool True if view was tracked, false if duplicate/rate limited
     */
    public function trackView(string $entityType, int $entityId, Request $request): bool
    {
        try {
            Log::info("ViewTrackingService: Starting trackView for $entityType:$entityId");

            $userId = Auth::id();
            $sessionId = $request->hasSession() ? $request->session()->getId() : null;
            $deviceFingerprint = $request->header('X-Device-Fingerprint');

            // Get IP address, prioritizing X-Forwarded-For for testing
            $ipAddress = $request->header('X-Forwarded-For') ?: $request->ip();
            $userAgent = $request->userAgent();

            Log::info("ViewTrackingService: User ID: " . ($userId ?? 'null') . ", IP: $ipAddress, Session: " . ($sessionId ?? 'null'));

            Log::info("Processing view tracking request for {$entityType} {$entityId}", [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'has_device_fingerprint' => !empty($deviceFingerprint)
            ]);

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
            $viewTracking = ViewTracking::create([
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'viewed_at' => Carbon::now(),
            ]);

            Log::info("View record created for {$entityType} {$entityId} with ID: {$viewTracking->id}", [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress
            ]);

            // Increment the entity's view count
            $this->incrementEntityViewCount($entityType, $entityId);

            Log::info("View tracking completed successfully for {$entityType} {$entityId}", [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error tracking view for {$entityType} {$entityId}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Try to increment the view count anyway, even if there was an error
            // This ensures that views are still counted even if there's an issue with the tracking system
            try {
                Log::info("Attempting to increment view count despite error for {$entityType} {$entityId}");
                $this->incrementEntityViewCount($entityType, $entityId);
            } catch (\Exception $innerEx) {
                Log::error("Failed to increment view count after error: " . $innerEx->getMessage());
            }
            
            return false;
        }
    }

    /**
     * Check if this view is a duplicate.
     * For authenticated users: Only allow one view per entity (permanent)
     * For anonymous users: Use time-based windows to prevent abuse while allowing legitimate views
     */
    private function isDuplicateView(
        string $entityType,
        int $entityId,
        ?int $userId,
        ?string $sessionId,
        ?string $deviceFingerprint,
        string $ipAddress
    ): bool {
        // For authenticated users: Check if THIS specific user has viewed this entity before
        // This ensures each authenticated user can only increment the view count once per entity
        if ($userId) {
            $isDuplicate = ViewTracking::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('user_id', $userId)
                ->exists();

            Log::info("Checking duplicate view for authenticated user ID: $userId, Entity: $entityType:$entityId, Result: " .
                ($isDuplicate ? 'DUPLICATE (user already viewed this entity)' : 'NEW VIEW'));

            return $isDuplicate;
        }
        
        // For anonymous users, prioritize device fingerprint for better tracking
        if ($deviceFingerprint) {
            // Check if this device fingerprint has viewed this entity before
            // Use permanent tracking for device fingerprints to prevent abuse
            $isDuplicate = ViewTracking::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('device_fingerprint', $deviceFingerprint)
                ->exists();

            Log::info("Checking duplicate view for device fingerprint, Entity: $entityType:$entityId, Result: " .
                ($isDuplicate ? 'DUPLICATE' : 'NEW VIEW'));
            return $isDuplicate;
        }

        // Check by session ID with time window (for anonymous users without device fingerprint)
        if ($sessionId) {
            $since = Carbon::now()->subHours(self::DUPLICATE_WINDOW_HOURS);
            $isDuplicate = ViewTracking::where('entity_type', $entityType)
                ->where('entity_id', $entityId)
                ->where('session_id', $sessionId)
                ->where('viewed_at', '>=', $since)
                ->exists();

            Log::info("Checking duplicate view for session ID (24h window), Entity: $entityType:$entityId, Result: " .
                ($isDuplicate ? 'DUPLICATE' : 'NEW VIEW'));
            return $isDuplicate;
        }

        // Fallback to IP address with time window (least reliable, allows multiple users from same IP)
        // Use a shorter time window for IP-based tracking to allow multiple users from same network
        $since = Carbon::now()->subHours(1); // Only 1 hour window for IP-based tracking
        $isDuplicate = ViewTracking::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('ip_address', $ipAddress)
            ->where('user_id', null) // Only check anonymous users
            ->where('device_fingerprint', null) // Only check users without device fingerprint
            ->where('session_id', null) // Only check users without session
            ->where('viewed_at', '>=', $since)
            ->exists();

        Log::info("Checking duplicate view for IP address (1h window), Entity: $entityType:$entityId, Result: " .
            ($isDuplicate ? 'DUPLICATE' : 'NEW VIEW'));

        return $isDuplicate;
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
     * Enhanced to ensure view counts are properly incremented.
     */
    private function incrementEntityViewCount(string $entityType, int $entityId): void
    {
        try {
            // Use direct SQL update to ensure view_count is incremented
            // This is more reliable than using the model's increment method
            switch ($entityType) {
                case 'vendor':
                case 'company':
                    $table = 'companies';
                    break;
                case 'branch':
                    $table = 'branches';
                    break;
                case 'provider':
                    $table = 'providers';
                    break;
                case 'merchant':
                    $table = 'merchants';
                    break;
                case 'category':
                    $table = 'categories';
                    break;
                default:
                    Log::warning("Unknown entity type for view tracking: {$entityType}");
                    return;
            }
            
            // Check if view_count column exists
            $columns = \Schema::getColumnListing($table);
            if (!in_array('view_count', $columns)) {
                Log::error("view_count column does not exist in {$table} table!");
                return;
            }
            
            // Get current view count for logging
            $currentViewCount = \DB::table($table)
                ->where('id', $entityId)
                ->value('view_count');
            
            Log::info("Current view count for {$entityType} {$entityId}: " . 
                (is_null($currentViewCount) ? 'NULL' : $currentViewCount));
            
            // Initialize view_count if it's NULL
            if (is_null($currentViewCount)) {
                \DB::table($table)
                    ->where('id', $entityId)
                    ->update(['view_count' => 0]);
                
                Log::info("Initialized NULL view_count to 0 for {$entityType} {$entityId}");
                $currentViewCount = 0;
            }
            
            // Update using direct SQL for reliability
            // Use a more explicit update to ensure the increment happens
            $newCount = (int)$currentViewCount + 1;
            $affected = \DB::table($table)
                ->where('id', $entityId)
                ->update([
                    'view_count' => $newCount
                ]);
            
            // Log the result
            if ($affected) {
                // Double-check the new value to ensure it was updated correctly
                $verifiedNewCount = \DB::table($table)
                    ->where('id', $entityId)
                    ->value('view_count');
                
                Log::info("Successfully updated view count for {$entityType} {$entityId}. New count: {$verifiedNewCount} (Expected: {$newCount})");
                
                // If the update didn't work as expected, try one more time with a different approach
                if ($verifiedNewCount != $newCount) {
                    Log::warning("View count mismatch for {$entityType} {$entityId}. Trying alternative update method.");
                    
                    // Try using the raw SQL increment as a fallback
                    $affected = \DB::table($table)
                        ->where('id', $entityId)
                        ->update([
                            'view_count' => \DB::raw('COALESCE(view_count, 0) + 1')
                        ]);
                    
                    if ($affected) {
                        $finalCount = \DB::table($table)
                            ->where('id', $entityId)
                            ->value('view_count');
                        
                        Log::info("Alternative update method for {$entityType} {$entityId}. Final count: {$finalCount}");
                    }
                }
            } else {
                Log::warning("Failed to update view count for {$entityType} {$entityId}. No rows affected.");
                
                // Try an alternative approach if the update failed
                $model = null;
                switch ($entityType) {
                    case 'vendor':
                    case 'company':
                        $model = \App\Models\Company::find($entityId);
                        break;
                    case 'branch':
                        $model = \App\Models\Branch::find($entityId);
                        break;
                    case 'provider':
                        $model = \App\Models\Provider::find($entityId);
                        break;
                    case 'merchant':
                        $model = \App\Models\Merchant::find($entityId);
                        break;
                    case 'category':
                        $model = \App\Models\Category::find($entityId);
                        break;
                }
                
                if ($model) {
                    $model->view_count = ($model->view_count ?? 0) + 1;
                    $model->save();
                    Log::info("Used model approach to update view count for {$entityType} {$entityId}. New count: {$model->view_count}");
                }
            }
        } catch (\Exception $e) {
            Log::error("Error incrementing view count for {$entityType} {$entityId}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
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
