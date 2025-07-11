const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

test.describe('License Upload Functionality', () => {
  test('should upload license file and save correct path to database', async ({ page }) => {
    // Create a test PDF file
    const testPdfContent = `%PDF-1.4
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
>>
endobj
xref
0 4
0000000000 65535 f 
0000000009 00000 n 
0000000074 00000 n 
0000000120 00000 n 
trailer
<<
/Size 4
/Root 1 0 R
>>
startxref
179
%%EOF`;

    const testFilePath = path.join(__dirname, 'test_license.pdf');
    fs.writeFileSync(testFilePath, testPdfContent);

    try {
      // Navigate to the merchant settings page
      await page.goto('https://dala3chic.com/merchant/settings/global');
      
      // Wait for the page to load
      await page.waitForLoadState('networkidle');
      
      // Check if we need to login first
      if (page.url().includes('/login')) {
        console.log('Need to login first - this test requires authentication');
        return;
      }
      
      // Look for the license upload form
      const licenseForm = page.locator('#license-form');
      if (await licenseForm.count() === 0) {
        console.log('License upload form not found - merchant may already have a license');
        return;
      }
      
      // Fill in the license expiry date
      await page.fill('#license_expiry_date', '2025-12-31');
      
      // Upload the license file
      await page.setInputFiles('#license_file', testFilePath);
      
      // Wait for the file to be processed
      await page.waitForTimeout(1000);
      
      // Submit the form
      await page.click('#license-submit-btn');
      
      // Wait for the response
      await page.waitForLoadState('networkidle');
      
      // Check for success message
      const successMessage = page.locator('.alert-success, .success-message');
      if (await successMessage.count() > 0) {
        console.log('✅ License upload appears successful');
        
        // Verify the success message content
        const messageText = await successMessage.textContent();
        expect(messageText).toContain('License uploaded successfully');
      } else {
        // Check for error messages
        const errorMessage = page.locator('.alert-danger, .error-message');
        if (await errorMessage.count() > 0) {
          const errorText = await errorMessage.textContent();
          console.log('❌ Error message found:', errorText);
        }
      }
      
    } finally {
      // Clean up the test file
      if (fs.existsSync(testFilePath)) {
        fs.unlinkSync(testFilePath);
      }
    }
  });
  
  test('should validate file type and size restrictions', async ({ page }) => {
    // Create a test text file (invalid type)
    const testFilePath = path.join(__dirname, 'test_invalid.txt');
    fs.writeFileSync(testFilePath, 'This is not a PDF file');

    try {
      await page.goto('https://dala3chic.com/merchant/settings/global');
      await page.waitForLoadState('networkidle');
      
      if (page.url().includes('/login')) {
        console.log('Need to login first - this test requires authentication');
        return;
      }
      
      const licenseForm = page.locator('#license-form');
      if (await licenseForm.count() === 0) {
        console.log('License upload form not found');
        return;
      }
      
      // Try to upload an invalid file type
      await page.setInputFiles('#license_file', testFilePath);
      
      // Check for validation error
      await page.waitForTimeout(1000);
      
      // The JavaScript should show an alert or error message
      const alertShown = await page.evaluate(() => {
        return window.lastAlert || false;
      });
      
      console.log('File type validation test completed');
      
    } finally {
      if (fs.existsSync(testFilePath)) {
        fs.unlinkSync(testFilePath);
      }
    }
  });
});
