/**
 * Comprehensive test for Product Edit page fixes
 * Tests both image display fix and button cleanup
 */

const { test, expect } = require('@playwright/test');

// Test configuration
const BASE_URL = 'https://dala3chic.com';
const MERCHANT_EMAIL = 'merchant@test.com';
const MERCHANT_PASSWORD = 'password123';
const PRODUCT_EDIT_URL = `${BASE_URL}/merchant/products/12/edit`;

test.describe('Product Edit Page Fixes', () => {
    test.beforeEach(async ({ page }) => {
        // Set viewport for consistent testing
        await page.setViewportSize({ width: 1280, height: 720 });
        
        // Login as merchant
        await page.goto(`${BASE_URL}/login`);
        await page.fill('input[name="email"]', MERCHANT_EMAIL);
        await page.fill('input[name="password"]', MERCHANT_PASSWORD);
        await page.click('button[type="submit"]');
        await page.waitForURL(BASE_URL);
        
        // Navigate to product edit page
        await page.goto(PRODUCT_EDIT_URL);
        await page.waitForLoadState('networkidle');
    });

    test('should display product color images correctly (not as black screens)', async ({ page }) => {
        // Click on Colors & Images tab
        await page.click('button[data-tab="colors"]');
        await page.waitForTimeout(1000); // Wait for tab content to load
        
        // Check that image preview elements exist
        const imageElements = await page.locator('.image-preview').count();
        expect(imageElements).toBeGreaterThan(0);
        
        // Verify images are loaded and visible
        const imageData = await page.evaluate(() => {
            const images = document.querySelectorAll('.image-preview');
            return Array.from(images).map(img => ({
                src: img.src,
                complete: img.complete,
                naturalWidth: img.naturalWidth,
                naturalHeight: img.naturalHeight,
                visible: img.style.display !== 'none'
            }));
        });
        
        // All images should be loaded and visible
        for (const image of imageData) {
            expect(image.complete).toBe(true);
            expect(image.naturalWidth).toBeGreaterThan(0);
            expect(image.naturalHeight).toBeGreaterThan(0);
            expect(image.visible).toBe(true);
            expect(image.src).toContain('dala3chic.com');
        }
        
        // Verify overlays are transparent (not black)
        const overlayData = await page.evaluate(() => {
            const overlays = document.querySelectorAll('.absolute.inset-0.bg-transparent');
            return Array.from(overlays).map(overlay => {
                const style = window.getComputedStyle(overlay);
                return {
                    backgroundColor: style.backgroundColor,
                    opacity: style.opacity
                };
            });
        });
        
        // All overlays should be transparent
        for (const overlay of overlayData) {
            expect(overlay.backgroundColor).toBe('rgba(0, 0, 0, 0)');
            expect(overlay.opacity).toBe('1');
        }
    });

    test('should have only "Save Changes" button, no old "Update Product" buttons', async ({ page }) => {
        // Check for any "Update Product" buttons (should be none)
        const updateButtons = await page.evaluate(() => {
            const buttons = document.querySelectorAll('button');
            return Array.from(buttons)
                .filter(button => button.textContent.includes('Update Product'))
                .map(button => button.textContent.trim());
        });
        
        expect(updateButtons).toHaveLength(0);
        
        // Check for "Save Changes" button (should exist)
        const saveButtons = await page.evaluate(() => {
            const buttons = document.querySelectorAll('button');
            return Array.from(buttons)
                .filter(button => button.textContent.includes('Save Changes'))
                .map(button => ({
                    text: button.textContent.trim(),
                    type: button.type,
                    form: button.getAttribute('form')
                }));
        });
        
        expect(saveButtons).toHaveLength(1);
        expect(saveButtons[0].text).toBe('Save Changes');
        expect(saveButtons[0].type).toBe('submit');
        expect(saveButtons[0].form).toBe('product-edit-form');
    });

    test('should have properly configured form for submission', async ({ page }) => {
        // Verify form configuration
        const formData = await page.evaluate(() => {
            const form = document.getElementById('product-edit-form');
            if (form) {
                return {
                    action: form.action,
                    method: form.method,
                    enctype: form.enctype
                };
            }
            return null;
        });
        
        expect(formData).not.toBeNull();
        expect(formData.action).toContain('/merchant/products/12');
        expect(formData.method).toBe('post');
        expect(formData.enctype).toBe('multipart/form-data');
    });

    test('should display images correctly on hover interaction', async ({ page }) => {
        // Click on Colors & Images tab
        await page.click('button[data-tab="colors"]');
        await page.waitForTimeout(1000);
        
        // Hover over first image container
        await page.hover('.image-preview-container:first-child');
        
        // Verify hover overlay appears with correct styling
        const hoverOverlayData = await page.evaluate(() => {
            const overlay = document.querySelector('.absolute.inset-0.bg-transparent');
            const style = window.getComputedStyle(overlay);
            return {
                backgroundColor: style.backgroundColor,
                opacity: style.opacity
            };
        });
        
        // On hover, overlay should still be transparent (hover effects handled by CSS)
        expect(hoverOverlayData.backgroundColor).toBe('rgba(0, 0, 0, 0)');
        expect(hoverOverlayData.opacity).toBe('1');
        
        // Verify "Change Image" button appears on hover
        const changeButton = await page.locator('.trigger-image-upload').first();
        await expect(changeButton).toBeVisible();
    });

    test('should work correctly on mobile viewport', async ({ page }) => {
        // Set mobile viewport
        await page.setViewportSize({ width: 375, height: 667 });
        
        // Click on Colors & Images tab
        await page.click('button[data-tab="colors"]');
        await page.waitForTimeout(1000);
        
        // Verify images are still visible on mobile
        const mobileImageData = await page.evaluate(() => {
            const images = document.querySelectorAll('.image-preview');
            return Array.from(images).map(img => ({
                complete: img.complete,
                naturalWidth: img.naturalWidth,
                naturalHeight: img.naturalHeight,
                visible: img.style.display !== 'none'
            }));
        });
        
        // All images should still be loaded and visible on mobile
        for (const image of mobileImageData) {
            expect(image.complete).toBe(true);
            expect(image.naturalWidth).toBeGreaterThan(0);
            expect(image.naturalHeight).toBeGreaterThan(0);
            expect(image.visible).toBe(true);
        }
        
        // Verify "Save Changes" button is still accessible on mobile
        const saveButton = await page.locator('button:has-text("Save Changes")');
        await expect(saveButton).toBeVisible();
    });

    test('should maintain Discord-themed styling after fixes', async ({ page }) => {
        // Verify the page maintains the Discord-themed color scheme
        const stylingData = await page.evaluate(() => {
            const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-500');
            const saveButton = document.querySelector('button:has-text("Save Changes")');
            const buttonClasses = saveButton ? saveButton.className : '';
            
            return {
                primaryColor: primaryColor.trim(),
                buttonClasses: buttonClasses,
                hasDiscordTheme: buttonClasses.includes('vue-btn-primary')
            };
        });
        
        expect(stylingData.primaryColor).toBe('#1E5EFF'); // Discord blue
        expect(stylingData.hasDiscordTheme).toBe(true);
        expect(stylingData.buttonClasses).toContain('vue-btn vue-btn-primary');
    });
});
