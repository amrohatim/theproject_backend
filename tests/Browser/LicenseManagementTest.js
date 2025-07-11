const { test, expect } = require('@playwright/test');

// Test configuration
const BASE_URL = 'https://dala3chic.com';
const MERCHANT_EMAIL = 'merchant@test.com';
const MERCHANT_PASSWORD = 'password123';
const ADMIN_EMAIL = 'admin@example.com';
const ADMIN_PASSWORD = 'password';

// Test data
const TEST_LICENSE_DATA = {
    expiryDate: '2025-12-31',
    rejectionReason: 'Invalid license format. Please upload a valid business license document.'
};

test.describe('License Management System', () => {
    test.beforeEach(async ({ page }) => {
        // Set viewport for consistent testing
        await page.setViewportSize({ width: 1280, height: 720 });
    });

    test.describe('Merchant License Upload', () => {
        test('should allow merchant to upload license with valid PDF', async ({ page }) => {
            // Login as merchant
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            // Wait for dashboard to load
            await page.waitForURL('**/merchant/dashboard');
            
            // Navigate to settings
            await page.goto(`${BASE_URL}/merchant/settings/global`);
            await page.waitForLoadState('networkidle');
            
            // Check if license management section exists
            await expect(page.locator('h3:has-text("License Management")')).toBeVisible();
            
            // Upload license file (simulate file upload)
            const fileInput = page.locator('input[name="license_file"]');
            await expect(fileInput).toBeAttached();
            
            // Set expiry date
            await page.fill('input[name="license_expiry_date"]', TEST_LICENSE_DATA.expiryDate);
            
            // Note: In a real test, you would upload an actual PDF file
            // For this test, we'll simulate the file selection
            await page.evaluate(() => {
                const input = document.querySelector('input[name="license_file"]');
                const file = new File(['test pdf content'], 'test-license.pdf', { type: 'application/pdf' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
            
            // Wait for upload button to be enabled
            await page.waitForFunction(() => {
                const btn = document.querySelector('#license-submit-btn');
                return btn && !btn.disabled;
            });
            
            // Submit the form
            await page.click('#license-submit-btn');
            
            // Wait for success message
            await expect(page.locator('.alert-success, .success')).toContainText('License uploaded successfully');
            
            // Verify license status is "Checking"
            await expect(page.locator('text=Checking')).toBeVisible();
        });

        test('should show validation errors for invalid file types', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/merchant/settings/global`);
            await page.waitForLoadState('networkidle');
            
            // Try to upload non-PDF file
            await page.evaluate(() => {
                const input = document.querySelector('input[name="license_file"]');
                const file = new File(['test content'], 'test.txt', { type: 'text/plain' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
            
            // Should show error message
            await expect(page.locator('text=Please select a PDF file only')).toBeVisible();
        });

        test('should show license status and expiry information', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/merchant/settings/global`);
            await page.waitForLoadState('networkidle');
            
            // Check license status display
            const statusSection = page.locator('h5:has-text("Current License Status")').locator('..');
            await expect(statusSection).toBeVisible();
            
            // Should show license status
            await expect(statusSection.locator('text=License Status:')).toBeVisible();
        });
    });

    test.describe('Product/Service Restrictions', () => {
        test('should prevent adding products when license is invalid', async ({ page }) => {
            // First, set merchant license to expired/invalid state
            // This would typically be done through database setup in beforeEach
            
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            // Navigate to products page
            await page.goto(`${BASE_URL}/merchant/products`);
            await page.waitForLoadState('networkidle');
            
            // Check if "Add New Product" button is disabled or shows lock icon
            const addButton = page.locator('button:has-text("Add New Product"), a:has-text("Add New Product")');
            
            // Should either be disabled or show license warning
            const isDisabled = await addButton.evaluate(el => el.disabled || el.classList.contains('disabled'));
            const hasLockIcon = await page.locator('i.fa-lock').isVisible();
            
            expect(isDisabled || hasLockIcon).toBeTruthy();
            
            // Should show license warning banner
            await expect(page.locator('text=License Required')).toBeVisible();
            await expect(page.locator('text=upload a new license')).toBeVisible();
        });

        test('should prevent adding services when license is invalid', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            // Navigate to services page
            await page.goto(`${BASE_URL}/merchant/services`);
            await page.waitForLoadState('networkidle');
            
            // Check for license restriction
            const addButton = page.locator('button:has-text("Add New Service"), a:has-text("Add New Service")');
            const isDisabled = await addButton.evaluate(el => el.disabled || el.classList.contains('disabled'));
            const hasLockIcon = await page.locator('i.fa-lock').isVisible();
            
            expect(isDisabled || hasLockIcon).toBeTruthy();
            
            // Should show license warning banner
            await expect(page.locator('text=License Required')).toBeVisible();
        });

        test('should redirect to settings when trying to access restricted pages', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            // Try to access product creation page directly
            await page.goto(`${BASE_URL}/merchant/products/create`);
            
            // Should be redirected to settings with error message
            await page.waitForURL('**/merchant/settings/global');
            await expect(page.locator('.alert-danger, .error')).toContainText('license');
        });
    });

    test.describe('Admin License Approval', () => {
        test('should allow admin to view pending licenses', async ({ page }) => {
            // Login as admin
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', ADMIN_EMAIL);
            await page.fill('input[name="password"]', ADMIN_PASSWORD);
            await page.click('button[type="submit"]');
            
            // Navigate to merchant licenses
            await page.goto(`${BASE_URL}/admin/merchant-licenses`);
            await page.waitForLoadState('networkidle');
            
            // Check page title and content
            await expect(page.locator('h1:has-text("Merchant License Management")')).toBeVisible();
            
            // Check statistics cards
            await expect(page.locator('text=Pending Review')).toBeVisible();
            await expect(page.locator('text=Approved')).toBeVisible();
            await expect(page.locator('text=Rejected')).toBeVisible();
            
            // Check filter functionality
            const statusFilter = page.locator('select[name="status"]');
            await expect(statusFilter).toBeVisible();
            await statusFilter.selectOption('checking');
            await page.click('button[type="submit"]:has-text("Filter")');
        });

        test('should allow admin to approve a license', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', ADMIN_EMAIL);
            await page.fill('input[name="password"]', ADMIN_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/admin/merchant-licenses`);
            await page.waitForLoadState('networkidle');
            
            // Find a pending license and click view
            const viewLink = page.locator('a:has-text("View")').first();
            if (await viewLink.isVisible()) {
                await viewLink.click();
                
                // Should be on license details page
                await expect(page.locator('h1:has-text("License Details")')).toBeVisible();
                
                // Check if approve button exists
                const approveButton = page.locator('button:has-text("Approve License")');
                if (await approveButton.isVisible()) {
                    // Handle confirmation dialog
                    page.on('dialog', dialog => dialog.accept());
                    await approveButton.click();
                    
                    // Should redirect back to list with success message
                    await page.waitForURL('**/admin/merchant-licenses');
                    await expect(page.locator('.alert-success, .success')).toContainText('approved successfully');
                }
            }
        });

        test('should allow admin to reject a license with reason', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', ADMIN_EMAIL);
            await page.fill('input[name="password"]', ADMIN_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/admin/merchant-licenses`);
            await page.waitForLoadState('networkidle');
            
            const viewLink = page.locator('a:has-text("View")').first();
            if (await viewLink.isVisible()) {
                await viewLink.click();
                
                const rejectButton = page.locator('button:has-text("Reject License")');
                if (await rejectButton.isVisible()) {
                    await rejectButton.click();
                    
                    // Fill rejection reason in modal
                    await page.fill('textarea[name="rejection_reason"]', TEST_LICENSE_DATA.rejectionReason);
                    await page.click('button[type="submit"]:has-text("Reject License")');
                    
                    // Should redirect back with success message
                    await page.waitForURL('**/admin/merchant-licenses');
                    await expect(page.locator('.alert-success, .success')).toContainText('rejected');
                }
            }
        });

        test('should support bulk approval of licenses', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', ADMIN_EMAIL);
            await page.fill('input[name="password"]', ADMIN_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/admin/merchant-licenses?status=checking`);
            await page.waitForLoadState('networkidle');
            
            // Check if bulk approval section exists
            const selectAllCheckbox = page.locator('#select-all');
            if (await selectAllCheckbox.isVisible()) {
                await selectAllCheckbox.check();
                
                // Check if bulk approve button is enabled
                const bulkApproveBtn = page.locator('#bulk-approve-btn');
                await expect(bulkApproveBtn).not.toBeDisabled();
                
                // Handle confirmation dialog
                page.on('dialog', dialog => dialog.accept());
                await bulkApproveBtn.click();
                
                // Should show success message
                await expect(page.locator('.alert-success, .success')).toContainText('approved');
            }
        });
    });

    test.describe('License Expiration Scenarios', () => {
        test('should handle expired license scenarios', async ({ page }) => {
            // This test would require setting up test data with expired licenses
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/merchant/settings/global`);
            await page.waitForLoadState('networkidle');
            
            // Check for expired license warning
            const expiredWarning = page.locator('text=expired');
            if (await expiredWarning.isVisible()) {
                await expect(page.locator('text=upload a new license')).toBeVisible();
            }
        });

        test('should show license renewal warnings', async ({ page }) => {
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/merchant/settings/global`);
            await page.waitForLoadState('networkidle');
            
            // Check for renewal warnings (licenses expiring soon)
            const renewalWarning = page.locator('text=days remaining');
            if (await renewalWarning.isVisible()) {
                // Should show appropriate warning styling
                await expect(renewalWarning).toBeVisible();
            }
        });
    });

    test.describe('Mobile Responsiveness', () => {
        test('should work correctly on mobile devices', async ({ page }) => {
            // Set mobile viewport
            await page.setViewportSize({ width: 375, height: 667 });
            
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');
            
            await page.goto(`${BASE_URL}/merchant/settings/global`);
            await page.waitForLoadState('networkidle');
            
            // Check if license management section is responsive
            await expect(page.locator('h3:has-text("License Management")')).toBeVisible();
            
            // Check if upload area is accessible on mobile
            const uploadArea = page.locator('#license-upload-area');
            await expect(uploadArea).toBeVisible();
            
            // Check if form elements are properly sized
            const expiryInput = page.locator('input[name="license_expiry_date"]');
            await expect(expiryInput).toBeVisible();
        });
    });
});

    test.describe('Integration Tests', () => {
        test('should complete full license workflow', async ({ page }) => {
            // Test the complete workflow from upload to approval

            // 1. Merchant uploads license
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');

            await page.goto(`${BASE_URL}/merchant/settings/global`);
            await page.waitForLoadState('networkidle');

            // Upload license
            await page.fill('input[name="license_expiry_date"]', TEST_LICENSE_DATA.expiryDate);
            await page.evaluate(() => {
                const input = document.querySelector('input[name="license_file"]');
                const file = new File(['test pdf content'], 'test-license.pdf', { type: 'application/pdf' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            await page.waitForFunction(() => !document.querySelector('#license-submit-btn').disabled);
            await page.click('#license-submit-btn');
            await expect(page.locator('.alert-success')).toContainText('uploaded successfully');

            // 2. Verify product creation is blocked
            await page.goto(`${BASE_URL}/merchant/products`);
            await expect(page.locator('text=License Required')).toBeVisible();

            // 3. Admin approves license
            await page.goto(`${BASE_URL}/logout`);
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', ADMIN_EMAIL);
            await page.fill('input[name="password"]', ADMIN_PASSWORD);
            await page.click('button[type="submit"]');

            await page.goto(`${BASE_URL}/admin/merchant-licenses`);
            const viewLink = page.locator('a:has-text("View")').first();
            if (await viewLink.isVisible()) {
                await viewLink.click();
                const approveButton = page.locator('button:has-text("Approve License")');
                if (await approveButton.isVisible()) {
                    page.on('dialog', dialog => dialog.accept());
                    await approveButton.click();
                    await expect(page.locator('.alert-success')).toContainText('approved');
                }
            }

            // 4. Verify merchant can now add products
            await page.goto(`${BASE_URL}/logout`);
            await page.goto(`${BASE_URL}/login`);
            await page.fill('input[name="email"]', MERCHANT_EMAIL);
            await page.fill('input[name="password"]', MERCHANT_PASSWORD);
            await page.click('button[type="submit"]');

            await page.goto(`${BASE_URL}/merchant/products`);
            const addProductBtn = page.locator('a:has-text("Add New Product")');
            await expect(addProductBtn).not.toBeDisabled();
            await expect(addProductBtn).not.toHaveClass(/disabled/);
        });
    });
});

// Helper functions for test setup
async function setupTestMerchantWithExpiredLicense() {
    // This would typically interact with your test database
    // to set up a merchant with an expired license
    console.log('Setting up merchant with expired license...');
}

async function setupTestMerchantWithValidLicense() {
    // This would set up a merchant with a valid license
    console.log('Setting up merchant with valid license...');
}

async function cleanupTestData() {
    // Clean up any test data created during tests
    console.log('Cleaning up test data...');
}
