<?php

namespace Tests\Feature;

use App\Http\Middleware\MerchantMiddleware;
use App\Models\Merchant;
use App\Models\User;
use App\Models\VendorNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchantNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedMerchantUser(bool $withMerchantRecord = true, array $overrides = []): array
    {
        $user = User::factory()->create(array_merge([
            'role' => 'merchant',
            'status' => 'active',
            'registration_step' => 'verified',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
        ], $overrides));

        $merchant = null;
        if ($withMerchantRecord) {
            $merchant = Merchant::create([
                'user_id' => $user->id,
                'business_name' => 'Merchant ' . $user->id,
                'status' => 'active',
                'license_status' => 'verified',
                'license_verified' => true,
            ]);
        }

        return [$user, $merchant];
    }

    public function test_merchant_full_list_shows_only_its_notifications(): void
    {
        [$merchantUser, $merchant] = $this->createVerifiedMerchantUser();
        [$otherUser, $otherMerchant] = $this->createVerifiedMerchantUser(true, ['email' => 'other-merchant@example.com']);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_ORDER,
            'sender_name' => 'admin',
            'message' => 'Merchant visible notification',
            'message_arabic' => 'اشعار مرئي للتاجر',
            'recipient_type' => VendorNotification::RECIPIENT_MERCHANT,
            'recipient_id' => $merchant->id,
            'is_opened' => false,
        ]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_ORDER,
            'sender_name' => 'admin',
            'message' => 'Other merchant hidden notification',
            'message_arabic' => 'اشعار مخفي لتاجر اخر',
            'recipient_type' => VendorNotification::RECIPIENT_MERCHANT,
            'recipient_id' => $otherMerchant->id,
            'is_opened' => false,
        ]);

        $response = $this->actingAs($merchantUser)->get(route('merchant.notifications.index'));

        $response->assertOk();
        $response->assertSee('Merchant visible notification');
        $response->assertDontSee('Other merchant hidden notification');

        $this->assertNotNull($otherUser);
    }

    public function test_opening_merchant_full_list_marks_only_merchant_notifications_as_opened(): void
    {
        [$merchantUser, $merchant] = $this->createVerifiedMerchantUser();
        [, $otherMerchant] = $this->createVerifiedMerchantUser(true, ['email' => 'another-merchant@example.com']);

        $targetNotification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Merchant unread notification',
            'message_arabic' => 'اشعار غير مقروء للتاجر',
            'recipient_type' => VendorNotification::RECIPIENT_MERCHANT,
            'recipient_id' => $merchant->id,
            'is_opened' => false,
        ]);

        $otherNotification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Other merchant unread notification',
            'message_arabic' => 'اشعار غير مقروء لتاجر اخر',
            'recipient_type' => VendorNotification::RECIPIENT_MERCHANT,
            'recipient_id' => $otherMerchant->id,
            'is_opened' => false,
        ]);

        $this->actingAs($merchantUser)->get(route('merchant.notifications.index'))->assertOk();

        $this->assertDatabaseHas('vendor_notifications', [
            'id' => $targetNotification->id,
            'is_opened' => true,
        ]);

        $this->assertDatabaseHas('vendor_notifications', [
            'id' => $otherNotification->id,
            'is_opened' => false,
        ]);
    }

    public function test_merchant_without_profile_gets_safe_empty_state_in_controller(): void
    {
        [$merchantUser] = $this->createVerifiedMerchantUser(false);

        $response = $this->withoutMiddleware(MerchantMiddleware::class)
            ->actingAs($merchantUser)
            ->get(route('merchant.notifications.index'));

        $response->assertOk();
        $response->assertSee(__('merchant.account_information_subtitle'));
        $response->assertDontSee('fa-bell-slash');
    }

    public function test_merchant_layout_renders_bell_and_limits_preview_to_ten_latest(): void
    {
        [$merchantUser, $merchant] = $this->createVerifiedMerchantUser();

        for ($i = 1; $i <= 11; $i++) {
            $message = $i === 1 ? 'Merchant preview oldest unique' : 'Merchant preview ' . $i;

            VendorNotification::create([
                'notification_type' => VendorNotification::TYPE_ORDER,
                'sender_name' => 'admin',
                'message' => $message,
                'message_arabic' => 'معاينة التاجر ' . $i,
                'recipient_type' => VendorNotification::RECIPIENT_MERCHANT,
                'recipient_id' => $merchant->id,
                'is_opened' => false,
                'created_at' => now()->addSeconds($i),
                'updated_at' => now()->addSeconds($i),
            ]);
        }

        $response = $this->actingAs($merchantUser)->get(route('merchant.dashboard'));

        $response->assertOk();
        $response->assertSee('fa-bell', false);
        $response->assertSee('Merchant preview 11');
        $response->assertDontSee('Merchant preview oldest unique');
    }
}
