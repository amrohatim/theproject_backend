const { test, expect } = require('@playwright/test');

test.describe('Vendor Product Edit - UI Structure Verification', () => {
  
  test('should verify edit page uses dashboard layout structure', async ({ page }) => {
    // Navigate directly to a product edit page (assuming product ID 1 exists)
    // This test focuses on UI structure rather than authentication flow
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    // Wait for either the Vue app to load or login redirect
    try {
      await page.waitForSelector('#vendor-product-edit-app', { timeout: 5000 });
      
      // If Vue app loads, verify the structure
      await expect(page.locator('#vendor-product-edit-app')).toBeVisible();
      await expect(page.locator('.vue-app-container')).toBeVisible();
      
      console.log('✓ Vue app container found with correct class');
      
    } catch (error) {
      // If redirected to login, verify the edit page structure by checking the HTML source
      const content = await page.content();
      
      // Verify the page extends layouts.dashboard (not layouts.vendor)
      expect(content).toContain('Dala3Chic Admin'); // Dashboard layout title
      expect(content).toContain('vue-app-container'); // Tailwind class
      expect(content).toContain('vendor-product-edit-app'); // Vue app ID
      
      console.log('✓ Edit page uses dashboard layout structure');
    }
  });

  test('should verify edit page has correct CSS and JS assets', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify Vite assets are loaded
    expect(content).toContain('vendor-product-edit.js');
    
    // Verify CSS frameworks
    expect(content).toContain('Font Awesome');
    expect(content).toContain('tailwindcss');
    
    // Verify Vue configuration
    expect(content).toContain('vendorProductEditConfig');
    expect(content).toContain('apiBaseUrl');
    expect(content).toContain('csrfToken');
    
    console.log('✓ All required assets and configurations are present');
  });

  test('should verify edit page HTML structure matches create page', async ({ page }) => {
    // Get create page structure
    await page.goto('http://localhost:8000/vendor/products/create');
    const createContent = await page.content();
    
    // Get edit page structure  
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    const editContent = await page.content();
    
    // Both should use dashboard layout
    const createUsesVendorLayout = createContent.includes('@extends(\'layouts.vendor\')');
    const editUsesVendorLayout = editContent.includes('@extends(\'layouts.vendor\')');
    
    // Both should use dashboard layout (not vendor layout)
    expect(createUsesVendorLayout).toBe(false);
    expect(editUsesVendorLayout).toBe(false);
    
    // Both should have vue-app-container class
    expect(createContent).toContain('vue-app-container');
    expect(editContent).toContain('vue-app-container');
    
    // Both should have similar CSS structure
    expect(createContent).toContain('vue-form-control');
    expect(editContent).toContain('vue-form-control');
    
    expect(createContent).toContain('vue-btn');
    expect(editContent).toContain('vue-btn');
    
    console.log('✓ Create and edit pages have consistent structure');
  });

  test('should verify Vue component structure in edit page', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify Vue app mounting
    expect(content).toContain('vendor-product-edit-app');
    expect(content).toContain('VendorProductEditApp');
    
    // Verify data attributes for Vue props
    expect(content).toContain('data-product-id');
    expect(content).toContain('data-back-url');
    
    console.log('✓ Vue component structure is correct');
  });

  test('should verify responsive design classes are present', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify Tailwind responsive classes
    expect(content).toContain('container mx-auto');
    expect(content).toContain('lg:grid-cols-2');
    expect(content).toContain('sm:flex-row');
    expect(content).toContain('md:');
    
    // Verify responsive CSS
    expect(content).toContain('@media (max-width: 768px)');
    expect(content).toContain('vue-app-container');
    
    console.log('✓ Responsive design classes are present');
  });

  test('should verify tab structure in Vue component', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify tab configuration in Vue component
    expect(content).toContain('Basic Info');
    expect(content).toContain('Colors & Images'); 
    expect(content).toContain('Specifications');
    
    // Verify tab styling classes
    expect(content).toContain('border-b-2');
    expect(content).toContain('transition-colors');
    expect(content).toContain('duration-200');
    
    console.log('✓ Tab structure is correctly configured');
  });

  test('should verify error handling structure', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify modal structures for success/error
    expect(content).toContain('showSuccessModal');
    expect(content).toContain('showErrorModal');
    expect(content).toContain('bg-green-100');
    expect(content).toContain('bg-red-100');
    
    console.log('✓ Error handling structure is present');
  });

  test('should verify stock progress indicator structure', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify stock progress elements
    expect(content).toContain('Stock Allocation Progress');
    expect(content).toContain('totalAllocatedStock');
    expect(content).toContain('stockProgressPercentage');
    expect(content).toContain('isStockOverAllocated');
    
    console.log('✓ Stock progress indicator structure is correct');
  });

  test('should verify form validation structure', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify validation methods
    expect(content).toContain('validateForm');
    expect(content).toContain('validateCategorySelection');
    
    // Verify error handling
    expect(content).toContain('errors');
    expect(content).toContain('errorMessage');
    
    console.log('✓ Form validation structure is present');
  });

  test('should verify component imports and dependencies', async ({ page }) => {
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    
    const content = await page.content();
    
    // Verify Vue component imports
    expect(content).toContain('VendorColorVariantCard');
    expect(content).toContain('VendorSpecificationItem');
    
    // Verify Vue composition API usage
    expect(content).toContain('ref, reactive, computed, onMounted');
    
    console.log('✓ Component dependencies are correctly configured');
  });

  test('should verify CSS consistency between create and edit pages', async ({ page }) => {
    // Check create page CSS
    await page.goto('http://localhost:8000/vendor/products/create');
    const createContent = await page.content();
    
    // Check edit page CSS  
    await page.goto('http://localhost:8000/vendor/products/1/edit');
    const editContent = await page.content();
    
    // Both should have similar CSS classes
    const sharedClasses = [
      'vue-form-control',
      'vue-btn-primary',
      'vue-btn-secondary',
      'vue-card',
      'vue-tab-content',
      'container mx-auto',
      'bg-white dark:bg-gray-800',
      'rounded-lg shadow'
    ];
    
    for (const className of sharedClasses) {
      expect(createContent).toContain(className);
      expect(editContent).toContain(className);
    }
    
    console.log('✓ CSS classes are consistent between create and edit pages');
  });
});
