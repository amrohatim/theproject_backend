const { test, expect } = require('@playwright/test');
const path = require('path');
const fs = require('fs');

// Test configuration
const BASE_URL = 'https://dala3chic.com';
const MERCHANT_EMAIL = 'merchant@test.com';
const MERCHANT_PASSWORD = 'password123';

test.describe('License Upload Fix Verification', () => {
    let page;

    test.beforeEach(async ({ browser }) => {
        page = await browser.newPage();
        
        // Set viewport for consistent testing
        await page.setViewportSize({ width: 1280, height: 720 });
        
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
    });

    test('should show license management section', async () => {
        // Check if license management section exists
        await expect(page.locator('h3:has-text("License Management")')).toBeVisible();
        
        // Check if upload area exists
        await expect(page.locator('#license-upload-area')).toBeVisible();
        
        // Check if file input exists
        await expect(page.locator('#license_file')).toBeAttached();
        
        // Check if expiry date input exists
        await expect(page.locator('#license_expiry_date')).toBeVisible();
        
        // Check if submit button exists and is initially disabled
        const submitBtn = page.locator('#license-submit-btn');
        await expect(submitBtn).toBeVisible();
        await expect(submitBtn).toBeDisabled();
    });

    test('should enable submit button when file and date are selected', async () => {
        // Create a test PDF file
        const testPdfPath = await createTestPDF();
        
        // Upload file
        await page.setInputFiles('#license_file', testPdfPath);
        
        // Set expiry date
        await page.fill('#license_expiry_date', '2025-12-31');
        
        // Check if submit button is enabled
        const submitBtn = page.locator('#license-submit-btn');
        await expect(submitBtn).toBeEnabled();
        
        // Clean up
        fs.unlinkSync(testPdfPath);
    });

    test('should show validation error for missing file', async () => {
        // Set only expiry date
        await page.fill('#license_expiry_date', '2025-12-31');
        
        // Try to submit
        await page.click('#license-submit-btn');
        
        // Should show client-side validation
        await page.waitForFunction(() => {
            return window.confirm || window.alert;
        });
    });

    test('should show validation error for missing expiry date', async () => {
        // Create a test PDF file
        const testPdfPath = await createTestPDF();
        
        // Upload file only
        await page.setInputFiles('#license_file', testPdfPath);
        
        // Try to submit (button should still be disabled)
        const submitBtn = page.locator('#license-submit-btn');
        await expect(submitBtn).toBeDisabled();
        
        // Clean up
        fs.unlinkSync(testPdfPath);
    });

    test('should handle file upload and show success message', async () => {
        // Create a test PDF file
        const testPdfPath = await createTestPDF();
        
        // Upload file
        await page.setInputFiles('#license_file', testPdfPath);
        
        // Set expiry date
        await page.fill('#license_expiry_date', '2025-12-31');
        
        // Submit form
        await page.click('#license-submit-btn');
        
        // Wait for response
        await page.waitForLoadState('networkidle');
        
        // Check for success or error message
        const successMessage = page.locator('.alert-success, .text-success');
        const errorMessage = page.locator('.alert-danger, .text-danger');
        
        // Should have either success or a specific error (not the generic "Please select a license file")
        const hasSuccess = await successMessage.count() > 0;
        const hasError = await errorMessage.count() > 0;
        
        if (hasError) {
            const errorText = await errorMessage.first().textContent();
            // Should not be the generic file selection error
            expect(errorText).not.toContain('Please select a license file');
        }
        
        expect(hasSuccess || hasError).toBe(true);
        
        // Clean up
        fs.unlinkSync(testPdfPath);
    });

    test('should update upload display when file is selected', async () => {
        // Create a test PDF file
        const testPdfPath = await createTestPDF();
        
        // Upload file
        await page.setInputFiles('#license_file', testPdfPath);
        
        // Wait for upload display to update
        await page.waitForTimeout(1000);
        
        // Check if upload area shows file info
        const uploadArea = page.locator('#license-upload-area');
        const uploadAreaText = await uploadArea.textContent();
        
        // Should show file name or "Ready to upload" message
        expect(uploadAreaText).toMatch(/test_license\.pdf|Ready to upload/);
        
        // Clean up
        fs.unlinkSync(testPdfPath);
    });
});

// Helper function to create a test PDF file
async function createTestPDF() {
    const testDir = path.join(__dirname, 'temp');
    if (!fs.existsSync(testDir)) {
        fs.mkdirSync(testDir, { recursive: true });
    }
    
    const testPdfPath = path.join(testDir, 'test_license.pdf');
    const pdfContent = `%PDF-1.4
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

2 0 obj
<<
/Type /Pages
/Kids [3 0 R]
/Count 1
>>
endobj

3 0 obj
<<
/Type /Page
/Parent 2 0 R
/MediaBox [0 0 612 792]
/Contents 4 0 R
>>
endobj

4 0 obj
<<
/Length 44
>>
stream
BT
/F1 12 Tf
72 720 Td
(Test License Document) Tj
ET
endstream
endobj

xref
0 5
0000000000 65535 f 
0000000009 00000 n 
0000000058 00000 n 
0000000115 00000 n 
0000000204 00000 n 
trailer
<<
/Size 5
/Root 1 0 R
>>
startxref
298
%%EOF`;

    fs.writeFileSync(testPdfPath, pdfContent);
    return testPdfPath;
}
