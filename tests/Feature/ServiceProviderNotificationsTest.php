<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\VendorNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceProviderNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function createBranchForCompany(Company $company, User $owner, string $name): Branch
    {
        return Branch::create([
            'user_id' => $owner->id,
            'company_id' => $company->id,
            'name' => $name,
            'address' => $name . ' Address',
            'lat' => 25.2048,
            'lng' => 55.2708,
            'status' => 'active',
        ]);
    }

    private function createServiceProviderUser(Company $company, array $branchIds, array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'role' => 'service_provider',
            'status' => 'active',
            'registration_step' => 'verified',
        ], $overrides));

        ServiceProvider::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'branch_ids' => $branchIds,
            'service_ids' => [],
            'number_of_services' => 0,
        ]);

        return $user;
    }

    public function test_service_provider_sees_company_notifications_filtered_by_allowed_branches(): void
    {
        $vendorOwner = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $company = Company::create(['user_id' => $vendorOwner->id, 'name' => 'Company A', 'status' => 'active']);
        $allowedBranch = $this->createBranchForCompany($company, $vendorOwner, 'Allowed Branch');
        $blockedBranch = $this->createBranchForCompany($company, $vendorOwner, 'Blocked Branch');

        $serviceProviderUser = $this->createServiceProviderUser($company, [$allowedBranch->id]);

        $category = Category::create([
            'name' => 'Products',
            'type' => 'product',
            'is_active' => true,
        ]);

        $allowedProduct = Product::create([
            'branch_id' => $allowedBranch->id,
            'category_id' => $category->id,
            'user_id' => $vendorOwner->id,
            'name' => 'Allowed Product',
            'price' => 10,
            'stock' => 10,
            'status' => 'approved',
            'is_available' => true,
        ]);

        $blockedProduct = Product::create([
            'branch_id' => $blockedBranch->id,
            'category_id' => $category->id,
            'user_id' => $vendorOwner->id,
            'name' => 'Blocked Product',
            'price' => 11,
            'stock' => 10,
            'status' => 'approved',
            'is_available' => true,
        ]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Allowed branch notification',
            'message_arabic' => 'اشعار فرع مسموح',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
            'product_id' => $allowedProduct->id,
            'is_opened' => false,
        ]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Blocked branch notification',
            'message_arabic' => 'اشعار فرع غير مسموح',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
            'product_id' => $blockedProduct->id,
            'is_opened' => false,
        ]);

        VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_ORDER,
            'sender_name' => 'admin',
            'message' => 'Generic company notification',
            'message_arabic' => 'اشعار عام للشركة',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
            'is_opened' => false,
        ]);

        $response = $this->actingAs($serviceProviderUser)->get(route('service-provider.notifications.index'));

        $response->assertOk();
        $response->assertSee('Allowed branch notification');
        $response->assertSee('Generic company notification');
        $response->assertDontSee('Blocked branch notification');
    }

    public function test_service_provider_read_receipts_are_per_user_and_only_for_visible_notifications(): void
    {
        $vendorOwner = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $company = Company::create(['user_id' => $vendorOwner->id, 'name' => 'Company A', 'status' => 'active']);
        $allowedBranch = $this->createBranchForCompany($company, $vendorOwner, 'Allowed Branch');
        $blockedBranch = $this->createBranchForCompany($company, $vendorOwner, 'Blocked Branch');

        $serviceProviderUser = $this->createServiceProviderUser($company, [$allowedBranch->id]);
        $otherServiceProvider = $this->createServiceProviderUser($company, [$allowedBranch->id], ['email' => 'sp-other@example.com']);

        $category = Category::create([
            'name' => 'Products',
            'type' => 'product',
            'is_active' => true,
        ]);

        $allowedProduct = Product::create([
            'branch_id' => $allowedBranch->id,
            'category_id' => $category->id,
            'user_id' => $vendorOwner->id,
            'name' => 'Allowed Product',
            'price' => 10,
            'stock' => 10,
            'status' => 'approved',
            'is_available' => true,
        ]);

        $blockedProduct = Product::create([
            'branch_id' => $blockedBranch->id,
            'category_id' => $category->id,
            'user_id' => $vendorOwner->id,
            'name' => 'Blocked Product',
            'price' => 11,
            'stock' => 10,
            'status' => 'approved',
            'is_available' => true,
        ]);

        $visibleNotification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Visible to service provider',
            'message_arabic' => 'مرئي لمقدم الخدمة',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
            'product_id' => $allowedProduct->id,
            'is_opened' => false,
        ]);

        $hiddenNotification = VendorNotification::create([
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'message' => 'Hidden from service provider',
            'message_arabic' => 'مخفي عن مقدم الخدمة',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $company->id,
            'product_id' => $blockedProduct->id,
            'is_opened' => false,
        ]);

        $this->actingAs($serviceProviderUser)->get(route('service-provider.notifications.index'))->assertOk();

        $this->assertDatabaseHas('vendor_notification_reads', [
            'vendor_notification_id' => $visibleNotification->id,
            'user_id' => $serviceProviderUser->id,
        ]);

        $this->assertDatabaseMissing('vendor_notification_reads', [
            'vendor_notification_id' => $hiddenNotification->id,
            'user_id' => $serviceProviderUser->id,
        ]);

        $this->assertDatabaseMissing('vendor_notification_reads', [
            'vendor_notification_id' => $visibleNotification->id,
            'user_id' => $otherServiceProvider->id,
        ]);
    }
}
