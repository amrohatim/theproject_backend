<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Provider;
use App\Models\ProviderSubscription;
use App\Models\SubscriptionType;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProviderSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected $provider;
    protected $subscriptionType;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a provider for testing
        $this->provider = Provider::factory()->create();

        // Create a subscription type
        $this->subscriptionType = SubscriptionType::create([
            'type' => 'provider',
            'period' => 'monthly',
            'charge' => 199.99,
            'title' => 'Provider Premium Plan',
            'description' => 'Premium monthly subscription for providers',
            'alert_message' => 'Enjoy unlimited access!',
        ]);
    }

    /** @test */
    public function it_can_create_a_provider_subscription()
    {
        $subscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertDatabaseHas('provider_subscriptions', [
            'provider_id' => $this->provider->id,
            'status' => 'active',
        ]);

        $this->assertEquals('active', $subscription->status);
    }

    /** @test */
    public function it_belongs_to_a_subscription_type()
    {
        $subscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertInstanceOf(SubscriptionType::class, $subscription->subscriptionType);
        $this->assertEquals($this->subscriptionType->id, $subscription->subscriptionType->id);
    }

    /** @test */
    public function it_belongs_to_a_provider()
    {
        $subscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertInstanceOf(Provider::class, $subscription->provider);
        $this->assertEquals($this->provider->id, $subscription->provider->id);
    }

    /** @test */
    public function it_formats_dates_correctly()
    {
        $startDate = Carbon::create(2025, 1, 15);
        $endDate = Carbon::create(2025, 2, 15);

        $subscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => $startDate,
            'end_at' => $endDate,
        ]);

        $this->assertEquals('15-01-2025', $subscription->formatted_start_date);
        $this->assertEquals('15-02-2025', $subscription->formatted_end_date);
    }

    /** @test */
    public function it_calculates_days_remaining_correctly()
    {
        $subscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now()->subDays(10),
            'end_at' => Carbon::now()->addDays(20),
        ]);

        $this->assertEquals(20, $subscription->days_remaining);
    }

    /** @test */
    public function it_detects_expired_subscriptions()
    {
        $expiredSubscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now()->subMonths(2),
            'end_at' => Carbon::now()->subMonth(),
        ]);

        $this->assertTrue($expiredSubscription->isExpired());

        $activeSubscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertFalse($activeSubscription->isExpired());
    }

    /** @test */
    public function it_detects_expiring_soon_subscriptions()
    {
        $expiringSoon = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now()->subDays(20),
            'end_at' => Carbon::now()->addDays(5),
        ]);

        $this->assertTrue($expiringSoon->isExpiringSoon());

        $notExpiringSoon = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addDays(30),
        ]);

        $this->assertFalse($notExpiringSoon->isExpiringSoon());
    }

    /** @test */
    public function it_can_scope_active_subscriptions()
    {
        ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'inactive',
            'start_at' => Carbon::now()->subMonths(2),
            'end_at' => Carbon::now()->subMonth(),
        ]);

        $activeSubscriptions = ProviderSubscription::active()->get();
        $this->assertCount(1, $activeSubscriptions);
        $this->assertEquals('active', $activeSubscriptions->first()->status);
    }

    /** @test */
    public function it_can_scope_inactive_subscriptions()
    {
        ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'inactive',
            'start_at' => Carbon::now()->subMonths(2),
            'end_at' => Carbon::now()->subMonth(),
        ]);

        $inactiveSubscriptions = ProviderSubscription::inactive()->get();
        $this->assertCount(1, $inactiveSubscriptions);
        $this->assertEquals('inactive', $inactiveSubscriptions->first()->status);
    }

    /** @test */
    public function it_can_scope_cancelled_subscriptions()
    {
        ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'cancelled',
            'start_at' => Carbon::now()->subMonths(2),
            'end_at' => Carbon::now()->subMonth(),
        ]);

        $cancelledSubscriptions = ProviderSubscription::cancelled()->get();
        $this->assertCount(1, $cancelledSubscriptions);
        $this->assertEquals('cancelled', $cancelledSubscriptions->first()->status);
    }

    /** @test */
    public function it_returns_correct_status_color()
    {
        $activeSubscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $this->assertEquals('green', $activeSubscription->status_color);

        $inactiveSubscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'inactive',
            'start_at' => Carbon::now()->subMonths(2),
            'end_at' => Carbon::now()->subMonth(),
        ]);

        $this->assertEquals('yellow', $inactiveSubscription->status_color);
    }

    /** @test */
    public function it_cascades_delete_when_provider_is_deleted()
    {
        $subscription = ProviderSubscription::create([
            'subscription_type_id' => $this->subscriptionType->id,
            'provider_id' => $this->provider->id,
            'status' => 'active',
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->addMonth(),
        ]);

        $subscriptionId = $subscription->id;

        $this->provider->delete();

        $this->assertDatabaseMissing('provider_subscriptions', [
            'id' => $subscriptionId,
        ]);
    }
}

