<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\VendorNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function createVerifiedVendor(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'vendor',
            'status' => 'active',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'registration_step' => 'verified',
        ], $overrides));
    }

    public function test_vendor_sees_only_its_company_notifications_on_full_list(): void
    {
        $vendor = $this->createVerifiedVendor();
        $company = Company::factory()->create(['user_id' => $vendor->id]);

        $otherVendor = $this->createVerifiedVendor(['email' => 'other-vendor@example.com']);
        $otherCompany = Company::factory()->create(['user_id' => $otherVendor->id]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_ORDER,
            'sender_name' => 'admin',
            'message' => 'Visible notification',
            'message_arabic' => 'اشعار مرئي',
            'is_opened' => false,
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
        ]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_BOOKING,
            'sender_name' => 'admin',
            'message' => 'Hidden notification',
            'message_arabic' => 'اشعار مخفي',
            'is_opened' => false,
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $otherCompany->id,
        ]);

        $response = $this->actingAs($vendor)->get(route('vendor.notifications.index'));

        $response->assertOk();
        $response->assertSee('Visible notification');
        $response->assertDontSee('Hidden notification');
    }

    public function test_opening_full_list_marks_vendor_notifications_as_opened(): void
    {
        $vendor = $this->createVerifiedVendor();
        $company = Company::factory()->create(['user_id' => $vendor->id]);

        $notification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_ORDER,
            'sender_name' => 'admin',
            'message' => 'Mark me read',
            'message_arabic' => 'اقراني',
            'is_opened' => false,
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
        ]);

        $this->actingAs($vendor)->get(route('vendor.notifications.index'))->assertOk();

        $this->assertDatabaseHas('vendor_notifications', [
            'id' => $notification->id,
            'is_opened' => true,
        ]);
    }

    public function test_vendor_without_company_cannot_see_other_vendor_notifications(): void
    {
        $vendorWithoutCompany = $this->createVerifiedVendor();

        $otherVendor = $this->createVerifiedVendor(['email' => 'with-company@example.com']);
        $otherCompany = Company::factory()->create(['user_id' => $otherVendor->id]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_SERVICE,
            'sender_name' => 'admin',
            'message' => 'Other company notification',
            'message_arabic' => 'اشعار شركة اخرى',
            'is_opened' => false,
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $otherCompany->id,
        ]);

        $response = $this->actingAs($vendorWithoutCompany)->get(route('vendor.notifications.index'));

        $response->assertOk();
        $response->assertDontSee('Other company notification');
    }

    public function test_dashboard_shows_vendor_notification_bell_and_preview_is_limited_to_ten(): void
    {
        $vendor = $this->createVerifiedVendor();
        $company = Company::factory()->create(['user_id' => $vendor->id]);

        // Create 11 notifications; preview should show only latest 10.
        for ($i = 1; $i <= 11; $i++) {
            $message = $i === 1 ? 'Preview oldest unique' : 'Preview notification ' . $i;

            VendorNotification::create([
                'notification_type' => VendorNotification::TYPE_PRODUCT,
                'sender_name' => 'admin',
                'message' => $message,
                'message_arabic' => 'معاينة ' . $i,
                'is_opened' => false,
                'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
                'recipient_id' => $company->id,
                'created_at' => now()->addSeconds($i),
                'updated_at' => now()->addSeconds($i),
            ]);
        }

        $response = $this->actingAs($vendor)->get(route('vendor.dashboard'));

        $response->assertOk();
        $response->assertSee('fa-bell', false);

        // Latest (11) appears in preview.
        $response->assertSee('Preview notification 11');
        // Oldest (1) should not appear because preview is capped at 10 latest.
        $response->assertDontSee('Preview oldest unique');
    }
}
