<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Company;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProductStatusNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Test Category',
            'type' => 'product',
            'is_active' => true,
        ]);
    }

    private function createVendorOwnedProduct(string $status = 'pending'): Product
    {
        $vendorUser = User::factory()->create([
            'role' => 'vendor',
            'status' => 'active',
        ]);

        $company = Company::create([
            'user_id' => $vendorUser->id,
            'name' => 'Vendor Company',
            'status' => 'active',
        ]);

        $branch = Branch::create([
            'user_id' => $vendorUser->id,
            'company_id' => $company->id,
            'name' => 'Vendor Branch',
            'address' => 'Vendor Address',
            'lat' => 25.2048,
            'lng' => 55.2708,
            'status' => 'active',
        ]);

        return Product::create([
            'branch_id' => $branch->id,
            'category_id' => $this->createCategory()->id,
            'user_id' => $vendorUser->id,
            'name' => 'Vendor Product',
            'product_name_arabic' => 'منتج بائع',
            'price' => 100,
            'stock' => 10,
            'is_available' => true,
            'status' => $status,
        ]);
    }

    private function createMerchantOwnedProduct(string $status = 'pending'): Product
    {
        $merchantUser = User::factory()->create([
            'role' => 'merchant',
            'status' => 'active',
        ]);

        $merchant = Merchant::create([
            'user_id' => $merchantUser->id,
            'business_name' => 'Merchant Business',
            'status' => 'active',
            'license_status' => 'verified',
            'license_verified' => true,
        ]);

        return Product::create([
            'branch_id' => null,
            'merchant_id' => $merchant->id,
            'category_id' => $this->createCategory()->id,
            'user_id' => $merchantUser->id,
            'name' => 'Merchant Product',
            'product_name_arabic' => 'منتج تاجر',
            'price' => 90,
            'stock' => 7,
            'is_available' => true,
            'status' => $status,
            'is_merchant' => true,
        ]);
    }

    public function test_admin_status_update_creates_vendor_notification_for_vendor_owned_product(): void
    {
        $admin = $this->createAdminUser();
        $product = $this->createVendorOwnedProduct('pending');
        $companyId = $product->branch->company_id;

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product->id), [
            'featured' => 1,
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('vendor_notifications', [
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'recipient_type' => VendorNotification::RECIPIENT_VENDOR,
            'recipient_id' => $companyId,
            'product_id' => $product->id,
            'is_opened' => false,
        ]);
    }

    public function test_admin_status_update_creates_merchant_notification_for_merchant_owned_product(): void
    {
        $admin = $this->createAdminUser();
        $product = $this->createMerchantOwnedProduct('pending');

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product->id), [
            'featured' => 0,
            'status' => 'rejected',
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('vendor_notifications', [
            'notification_type' => VendorNotification::TYPE_PRODUCT,
            'sender_name' => 'admin',
            'recipient_type' => VendorNotification::RECIPIENT_MERCHANT,
            'recipient_id' => $product->merchant_id,
            'product_id' => $product->id,
            'is_opened' => false,
        ]);
    }

    public function test_admin_status_update_does_not_create_notification_when_status_is_unchanged(): void
    {
        $admin = $this->createAdminUser();
        $product = $this->createVendorOwnedProduct('approved');

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product->id), [
            'featured' => 1,
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseCount('vendor_notifications', 0);
    }

    public function test_admin_status_update_skips_notification_when_recipient_cannot_be_resolved(): void
    {
        $admin = $this->createAdminUser();
        $owner = User::factory()->create([
            'role' => 'vendor',
            'status' => 'active',
        ]);

        $branchWithoutCompany = Branch::create([
            'user_id' => $owner->id,
            'company_id' => null,
            'name' => 'Orphan Branch',
            'address' => 'No Company Address',
            'lat' => 25.0000,
            'lng' => 55.0000,
            'status' => 'active',
        ]);

        $product = Product::create([
            'branch_id' => $branchWithoutCompany->id,
            'merchant_id' => null,
            'category_id' => $this->createCategory()->id,
            'user_id' => $owner->id,
            'name' => 'Unresolved Product',
            'price' => 55,
            'stock' => 3,
            'is_available' => true,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product->id), [
            'featured' => 0,
            'status' => 'approved',
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseCount('vendor_notifications', 0);
    }
}
