<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Branch;
use App\Models\User;
use App\Services\ViewTrackingService;

class ViewTrackingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that branch views are properly tracked and incremented.
     *
     * @return void
     */
    public function test_branch_view_tracking()
    {
        // Create a test branch
        $branch = Branch::factory()->create([
            'view_count' => 0
        ]);

        // Create test users
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // First view from user1
        $response = $this->actingAs($user1)
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => true,
            ]);

        // Refresh the branch from database
        $branch->refresh();
        $this->assertGreaterThan(0, $branch->view_count, 'Branch view count should be incremented after first view');
        $initialCount = $branch->view_count;

        // Second view from user2 (different user)
        $response = $this->actingAs($user2)
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => true,
            ]);

        // Refresh the branch again
        $branch->refresh();
        $this->assertGreaterThan($initialCount, $branch->view_count, 'Branch view count should be incremented after second view from different user');
        $secondCount = $branch->view_count;

        // Third view from user1 again (should be blocked as duplicate)
        $response = $this->actingAs($user1)
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => false, // Not tracked because it's a duplicate
            ]);

        // Refresh the branch again
        $branch->refresh();
        $this->assertEquals($secondCount, $branch->view_count, 'Branch view count should not change for duplicate views from same user');

        // Fourth view from user1 after a long time (should still be blocked as duplicate)
        // This simulates a user coming back after the time window has passed
        $this->travel(25)->hours(); // Travel 25 hours into the future (beyond the 24-hour window)
        
        $response = $this->actingAs($user1)
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => false, // Still not tracked because authenticated users can only view once ever
            ]);

        // Refresh the branch again
        $branch->refresh();
        $this->assertEquals($secondCount, $branch->view_count, 'Branch view count should not change for duplicate views from same user, even after time window');
    }

    /**
     * Test that IP-based view tracking works correctly.
     *
     * @return void
     */
    public function test_ip_based_view_tracking()
    {
        // Create a test branch
        $branch = Branch::factory()->create([
            'view_count' => 0
        ]);

        // First anonymous view
        $response = $this->withHeaders([
                'X-Forwarded-For' => '192.168.1.1',
            ])
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => true,
            ]);

        // Refresh the branch from database
        $branch->refresh();
        $this->assertGreaterThan(0, $branch->view_count, 'Branch view count should be incremented after first anonymous view');

        // Second anonymous view from different IP
        $response = $this->withHeaders([
                'X-Forwarded-For' => '192.168.1.2',
            ])
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => true,
            ]);

        // Refresh the branch again
        $branch->refresh();
        $this->assertGreaterThan(1, $branch->view_count, 'Branch view count should be incremented after second view from different IP');

        // Third anonymous view from same IP (should be blocked as duplicate)
        $response = $this->withHeaders([
                'X-Forwarded-For' => '192.168.1.1',
            ])
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => false, // Not tracked because it's a duplicate
            ]);

        // Refresh the branch again
        $previousCount = $branch->view_count;
        $branch->refresh();
        $this->assertEquals($previousCount, $branch->view_count, 'Branch view count should not change for duplicate views from same IP');
        
        // Fourth anonymous view from same IP after a long time (should still be blocked as duplicate)
        // This simulates a user coming back after the time window has passed
        $this->travel(25)->hours(); // Travel 25 hours into the future (beyond the previous 1-hour window)
        
        $response = $this->withHeaders([
                'X-Forwarded-For' => '192.168.1.1',
            ])
            ->post("/api/branches/{$branch->id}/track-view");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'tracked' => false, // Still not tracked because IP-based tracking is now permanent
            ]);

        // Refresh the branch again
        $branch->refresh();
        $this->assertEquals($previousCount, $branch->view_count, 'Branch view count should not change for duplicate views from same IP, even after time window');
    }
}
