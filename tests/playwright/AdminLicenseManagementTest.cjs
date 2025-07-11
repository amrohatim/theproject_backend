const { test, expect } = require('@playwright/test');

/**
 * Comprehensive Playwright tests for Admin License Management functionality
 * Tests cover: navigation, listing, filtering, PDF viewing, approval/rejection workflow, and mobile responsiveness
 */

// Test configuration
const BASE_URL = 'https://dala3chic.com';
const ADMIN_EMAIL = 'admin@example.com';
const ADMIN_PASSWORD = 'password';

// Helper function to login as admin
async function loginAsAdmin(page) {
    await page.goto(`${BASE_URL}/admin/dashboard`);
    
    // Check if already logged in
    const isLoggedIn = await page.locator('text=Welcome, Admin User!').isVisible().catch(() => false);
    if (isLoggedIn) {
        return;
    }
    
    // Fill login form
    await page.fill('input[type="email"]', ADMIN_EMAIL);
    await page.fill('input[type="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    
    // Wait for dashboard to load
    await page.waitForSelector('text=Welcome, Admin User!');
}

// Helper function to create a test merchant with license
async function createTestMerchant(page) {
    // This would typically involve API calls or database seeding
    // For now, we'll assume test data exists
    console.log('Using existing test merchant data');
}

test.describe('Admin License Management', () => {
    
    test.beforeEach(async ({ page }) => {
        await loginAsAdmin(page);
    });

    test('should navigate to merchant license management from sidebar', async ({ page }) => {
        // Click on Merchant Licenses in sidebar
        await page.click('a[href*="merchant-licenses"]');
        
        // Verify we're on the correct page
        await expect(page).toHaveURL(/.*merchant-licenses/);
        await expect(page.locator('h1')).toContainText('Merchant License Management');
        await expect(page.locator('text=Review and approve merchant license uploads')).toBeVisible();
    });

    test('should display license statistics correctly', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses`);
        
        // Check statistics cards are visible
        await expect(page.locator('text=Pending Review')).toBeVisible();
        await expect(page.locator('text=Approved')).toBeVisible();
        await expect(page.locator('text=Rejected')).toBeVisible();
        await expect(page.locator('text=Expired')).toBeVisible();
        
        // Verify statistics have numbers
        const pendingCount = await page.locator('text=Pending Review').locator('..').locator('p.text-2xl').textContent();
        expect(pendingCount).toMatch(/^\d+$/);
    });

    test('should filter licenses by status', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses`);
        
        // Test filtering by different statuses
        const statuses = ['all', 'checking', 'verified', 'rejected', 'expired'];
        
        for (const status of statuses) {
            await page.selectOption('select[name="status"]', status);
            await page.click('button:has-text("Filter")');
            
            // Verify URL contains the status parameter
            await expect(page).toHaveURL(new RegExp(`status=${status}`));
        }
    });

    test('should display license list with correct information', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=all`);
        
        // Check if table headers are present
        await expect(page.locator('th:has-text("Merchant")')).toBeVisible();
        await expect(page.locator('th:has-text("Business Name")')).toBeVisible();
        await expect(page.locator('th:has-text("Status")')).toBeVisible();
        await expect(page.locator('th:has-text("Expiry Date")')).toBeVisible();
        await expect(page.locator('th:has-text("Uploaded")')).toBeVisible();
        await expect(page.locator('th:has-text("Actions")')).toBeVisible();
    });

    test('should navigate to license details page', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=all`);
        
        // Click on first "View" link if available
        const viewLink = page.locator('td a:has-text("View")').first();
        if (await viewLink.isVisible()) {
            await viewLink.click();
            
            // Verify we're on the license details page
            await expect(page.locator('h1:has-text("License Details")')).toBeVisible();
            await expect(page.locator('text=Back to List')).toBeVisible();
        }
    });

    test('should display license details correctly', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=all`);
        
        const viewLink = page.locator('td a:has-text("View")').first();
        if (await viewLink.isVisible()) {
            await viewLink.click();
            
            // Check license information section
            await expect(page.locator('text=License Information')).toBeVisible();
            await expect(page.locator('text=Current Status')).toBeVisible();
            await expect(page.locator('text=Expiry Date')).toBeVisible();
            await expect(page.locator('text=Uploaded Date')).toBeVisible();
            
            // Check merchant information section
            await expect(page.locator('text=Merchant Information')).toBeVisible();
            await expect(page.locator('text=Merchant Name')).toBeVisible();
            await expect(page.locator('text=Email')).toBeVisible();
            await expect(page.locator('text=Business Name')).toBeVisible();
        }
    });

    test('should display PDF viewer controls', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=all`);
        
        const viewLink = page.locator('td a:has-text("View")').first();
        if (await viewLink.isVisible()) {
            await viewLink.click();
            
            // Check PDF document section
            await expect(page.locator('text=License Document')).toBeVisible();
            await expect(page.locator('button:has-text("View Inline")')).toBeVisible();
            await expect(page.locator('a:has-text("Open in New Tab")')).toBeVisible();
            await expect(page.locator('a:has-text("Download")')).toBeVisible();
        }
    });

    test('should toggle inline PDF viewer', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=all`);
        
        const viewLink = page.locator('td a:has-text("View")').first();
        if (await viewLink.isVisible()) {
            await viewLink.click();
            
            // Test PDF viewer toggle
            const toggleButton = page.locator('button:has-text("View Inline")');
            if (await toggleButton.isVisible()) {
                // Click to show PDF viewer
                await toggleButton.click();
                await expect(page.locator('text=Hide Inline')).toBeVisible();
                await expect(page.locator('#pdf-viewer-container')).toBeVisible();
                
                // Click to hide PDF viewer
                await page.click('button:has-text("Hide Inline")');
                await expect(page.locator('text=View Inline')).toBeVisible();
            }
        }
    });

    test('should handle license approval workflow', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=checking`);
        
        const viewLink = page.locator('td a:has-text("View")').first();
        if (await viewLink.isVisible()) {
            await viewLink.click();
            
            // Check if approve button is available for pending licenses
            const approveButton = page.locator('button:has-text("Approve License")');
            if (await approveButton.isVisible()) {
                // Override confirm dialog
                await page.evaluate(() => {
                    window.confirm = () => true;
                });
                
                await approveButton.click();
                
                // Should redirect to license list with success message
                await expect(page).toHaveURL(/.*merchant-licenses/);
                await expect(page.locator('text=approved successfully')).toBeVisible();
            }
        }
    });

    test('should handle license rejection workflow', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=checking`);
        
        const viewLink = page.locator('td a:has-text("View")').first();
        if (await viewLink.isVisible()) {
            await viewLink.click();
            
            // Check if reject button is available for pending licenses
            const rejectButton = page.locator('button:has-text("Reject License")');
            if (await rejectButton.isVisible()) {
                await rejectButton.click();
                
                // Fill rejection reason
                await page.fill('textarea[name="rejection_reason"]', 'Test rejection reason for automated testing');
                
                // Submit rejection
                await page.click('button[type="submit"]:has-text("Reject License")');
                
                // Should redirect to license list with success message
                await expect(page).toHaveURL(/.*merchant-licenses/);
                await expect(page.locator('text=rejected')).toBeVisible();
            }
        }
    });

    test('should handle bulk approval functionality', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=checking`);
        
        // Check if bulk approval section is visible
        const selectAllCheckbox = page.locator('#select-all');
        if (await selectAllCheckbox.isVisible()) {
            // Select all licenses
            await selectAllCheckbox.click();
            
            // Verify bulk approve button is enabled
            const bulkApproveButton = page.locator('#bulk-approve-btn');
            await expect(bulkApproveButton).not.toBeDisabled();
            
            // Note: We won't actually submit to avoid affecting test data
            console.log('Bulk approval UI is functional');
        }
    });

    test('should be mobile responsive', async ({ page }) => {
        // Set mobile viewport
        await page.setViewportSize({ width: 375, height: 667 });
        
        await page.goto(`${BASE_URL}/admin/merchant-licenses`);
        
        // Check if page loads correctly on mobile
        await expect(page.locator('h1:has-text("Merchant License Management")')).toBeVisible();
        
        // Check if statistics cards stack properly on mobile
        const statsCards = page.locator('.grid.grid-cols-1.md\\:grid-cols-4');
        await expect(statsCards).toBeVisible();
        
        // Test navigation on mobile
        const viewLink = page.locator('td a:has-text("View")').first();
        if (await viewLink.isVisible()) {
            await viewLink.click();
            await expect(page.locator('h1:has-text("License Details")')).toBeVisible();
        }
    });

    test('should handle pagination if present', async ({ page }) => {
        await page.goto(`${BASE_URL}/admin/merchant-licenses?status=all`);
        
        // Check if pagination exists
        const paginationLinks = page.locator('.pagination, nav[role="navigation"]');
        if (await paginationLinks.isVisible()) {
            // Test pagination navigation
            const nextLink = page.locator('a:has-text("Next"), a[rel="next"]');
            if (await nextLink.isVisible()) {
                await nextLink.click();
                // Verify page changed
                await expect(page).toHaveURL(/page=2/);
            }
        }
    });

    test('should maintain proper authorization', async ({ page }) => {
        // Test that non-admin users cannot access the page
        await page.goto(`${BASE_URL}/logout`);
        await page.goto(`${BASE_URL}/admin/merchant-licenses`);
        
        // Should redirect to login or show access denied
        const currentUrl = page.url();
        expect(currentUrl).not.toContain('/admin/merchant-licenses');
    });
});
