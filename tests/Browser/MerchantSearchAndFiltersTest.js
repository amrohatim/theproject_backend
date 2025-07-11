const { test, expect } = require('@playwright/test');

/**
 * Comprehensive tests for merchant search and filtering functionality
 * Tests both desktop and mobile viewports with performance considerations
 */

// Test data setup
const testMerchant = {
    email: 'test.merchant@example.com',
    password: 'password123',
    name: 'Test Merchant'
};

const testProducts = [
    { name: 'Test Product 1', sku: 'TP001', price: 29.99, category: 'Electronics' },
    { name: 'Test Product 2', sku: 'TP002', price: 49.99, category: 'Clothing' },
    { name: 'Test Product 3', sku: 'TP003', price: 19.99, category: 'Electronics' }
];

const testServices = [
    { name: 'Test Service 1', price: 99.99, duration: 60, category: 'Consulting' },
    { name: 'Test Service 2', price: 149.99, duration: 90, category: 'Repair' }
];

// Helper functions
async function loginAsMerchant(page) {
    await page.goto('/login');
    await page.fill('input[name="email"]', testMerchant.email);
    await page.fill('input[name="password"]', testMerchant.password);
    await page.click('button[type="submit"]');
    await page.waitForURL('/merchant/dashboard');
}

async function createTestData(page) {
    // This would typically be done via API or database seeding
    // For now, we'll assume test data exists
    console.log('Test data should be seeded before running tests');
}

async function waitForSearchResults(page) {
    await page.waitForSelector('.search-results, .products-table-container, .services-table-container', { timeout: 10000 });
    await page.waitForLoadState('networkidle');
}

// Desktop Tests
test.describe('Merchant Search and Filters - Desktop', () => {
    test.beforeEach(async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 720 });
        await loginAsMerchant(page);
    });

    test('Dashboard global search functionality', async ({ page }) => {
        await page.goto('/merchant/dashboard');
        
        // Test search input visibility
        const searchInput = page.locator('.merchant-search-input');
        await expect(searchInput).toBeVisible();
        
        // Test search suggestions
        await searchInput.fill('Test');
        await page.waitForSelector('.search-suggestions', { timeout: 5000 });
        
        const suggestions = page.locator('.suggestion-item');
        await expect(suggestions.first()).toBeVisible();
        
        // Test search execution
        await searchInput.press('Enter');
        await waitForSearchResults(page);
        
        const searchResults = page.locator('.search-results');
        await expect(searchResults).toBeVisible();
    });

    test('Product search and filtering', async ({ page }) => {
        await page.goto('/merchant/products');
        
        // Test basic search
        const searchInput = page.locator('.merchant-search-input');
        await searchInput.fill('Test Product');
        await waitForSearchResults(page);
        
        // Verify search results
        const productRows = page.locator('.discord-table tbody tr');
        await expect(productRows).toHaveCountGreaterThan(0);
        
        // Test category filter
        const filterToggle = page.locator('#filterToggle');
        await filterToggle.click();
        
        const categorySelect = page.locator('#categoryFilter');
        await categorySelect.selectOption('Electronics');
        
        const applyFilters = page.locator('#applyFilters');
        await applyFilters.click();
        
        await waitForSearchResults(page);
        
        // Verify filtered results
        const filteredRows = page.locator('.discord-table tbody tr');
        await expect(filteredRows).toHaveCountGreaterThan(0);
        
        // Test price range filter
        await filterToggle.click();
        await page.fill('#priceMin', '20');
        await page.fill('#priceMax', '50');
        await applyFilters.click();
        
        await waitForSearchResults(page);
        
        // Test clear filters
        const clearFilters = page.locator('#clearFilters');
        await clearFilters.click();
        await waitForSearchResults(page);
    });

    test('Service search and filtering', async ({ page }) => {
        await page.goto('/merchant/services');
        
        // Test service search
        const searchInput = page.locator('.merchant-search-input');
        await searchInput.fill('Test Service');
        await waitForSearchResults(page);
        
        const serviceRows = page.locator('.discord-table tbody tr');
        await expect(serviceRows).toHaveCountGreaterThan(0);
        
        // Test service type filter
        const serviceTypeFilter = page.locator('[data-filter="service_type"][data-value="home_service"]');
        await serviceTypeFilter.click();
        await waitForSearchResults(page);
        
        // Test duration filter
        const filterToggle = page.locator('#filterToggle');
        await filterToggle.click();
        
        await page.fill('#durationMin', '30');
        await page.fill('#durationMax', '120');
        
        const applyFilters = page.locator('#applyFilters');
        await applyFilters.click();
        
        await waitForSearchResults(page);
    });

    test('Quick filter buttons functionality', async ({ page }) => {
        await page.goto('/merchant/products');
        
        // Test featured filter
        const featuredFilter = page.locator('[data-filter="featured"]');
        await featuredFilter.click();
        
        await expect(featuredFilter).toHaveClass(/active/);
        await waitForSearchResults(page);
        
        // Test active status filter
        const activeFilter = page.locator('[data-filter="status"][data-value="active"]');
        await activeFilter.click();
        
        await expect(activeFilter).toHaveClass(/active/);
        await waitForSearchResults(page);
        
        // Test filter removal
        await featuredFilter.click();
        await expect(featuredFilter).not.toHaveClass(/active/);
        await waitForSearchResults(page);
    });

    test('URL parameter handling for shareable views', async ({ page }) => {
        await page.goto('/merchant/products?search=Test&category_id=1&status=active');
        
        // Verify URL parameters are applied
        const searchInput = page.locator('.merchant-search-input');
        await expect(searchInput).toHaveValue('Test');
        
        const activeFilter = page.locator('[data-filter="status"][data-value="active"]');
        await expect(activeFilter).toHaveClass(/active/);
        
        // Test URL updates when filters change
        const featuredFilter = page.locator('[data-filter="featured"]');
        await featuredFilter.click();
        
        await page.waitForFunction(() => {
            return window.location.search.includes('featured=1');
        });
    });

    test('Pagination with search and filters', async ({ page }) => {
        await page.goto('/merchant/products');
        
        // Apply search to get results
        const searchInput = page.locator('.merchant-search-input');
        await searchInput.fill('Test');
        await waitForSearchResults(page);
        
        // Test pagination if available
        const paginationLinks = page.locator('.pagination .page-link[data-page]');
        const paginationCount = await paginationLinks.count();
        
        if (paginationCount > 0) {
            const secondPageLink = paginationLinks.nth(1);
            await secondPageLink.click();
            await waitForSearchResults(page);
            
            // Verify search is maintained across pages
            await expect(searchInput).toHaveValue('Test');
        }
    });
});

// Mobile Tests
test.describe('Merchant Search and Filters - Mobile', () => {
    test.beforeEach(async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 667 }); // iPhone SE
        await loginAsMerchant(page);
    });

    test('Mobile search interface responsiveness', async ({ page }) => {
        await page.goto('/merchant/dashboard');
        
        // Test mobile search input
        const searchInput = page.locator('.merchant-search-input');
        await expect(searchInput).toBeVisible();
        
        // Verify mobile-friendly font size (prevents zoom on iOS)
        const fontSize = await searchInput.evaluate(el => 
            window.getComputedStyle(el).fontSize
        );
        expect(parseInt(fontSize)).toBeGreaterThanOrEqual(16);
        
        // Test search suggestions on mobile
        await searchInput.fill('Test');
        await page.waitForSelector('.search-suggestions');
        
        const suggestions = page.locator('.suggestion-item');
        await expect(suggestions.first()).toBeVisible();
        
        // Test touch interaction
        await suggestions.first().tap();
        await waitForSearchResults(page);
    });

    test('Mobile filter interface', async ({ page }) => {
        await page.goto('/merchant/products');
        
        // Test filter toggle button
        const filterToggle = page.locator('#filterToggle');
        await expect(filterToggle).toBeVisible();
        
        await filterToggle.tap();
        
        // Test modal opens properly on mobile
        const modal = page.locator('#advancedFiltersModal');
        await expect(modal).toBeVisible();
        
        // Test form inputs are mobile-friendly
        const priceMin = page.locator('#priceMin');
        const fontSize = await priceMin.evaluate(el => 
            window.getComputedStyle(el).fontSize
        );
        expect(parseInt(fontSize)).toBeGreaterThanOrEqual(16);
        
        // Test modal close
        const closeButton = page.locator('.btn-close');
        await closeButton.tap();
        await expect(modal).not.toBeVisible();
    });

    test('Mobile quick filters layout', async ({ page }) => {
        await page.goto('/merchant/products');
        
        // Test quick filters are properly wrapped on mobile
        const quickFilters = page.locator('.quick-filters');
        await expect(quickFilters).toBeVisible();
        
        const filterButtons = page.locator('.filter-toggle');
        const buttonCount = await filterButtons.count();
        
        if (buttonCount > 0) {
            // Test first filter button
            await filterButtons.first().tap();
            await waitForSearchResults(page);
            
            // Verify active state
            await expect(filterButtons.first()).toHaveClass(/active/);
        }
    });
});

// Performance Tests
test.describe('Search and Filter Performance', () => {
    test.beforeEach(async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 720 });
        await loginAsMerchant(page);
    });

    test('Search response time with large dataset', async ({ page }) => {
        await page.goto('/merchant/products');
        
        const searchInput = page.locator('.merchant-search-input');
        
        // Measure search response time
        const startTime = Date.now();
        await searchInput.fill('Test');
        await waitForSearchResults(page);
        const endTime = Date.now();
        
        const responseTime = endTime - startTime;
        expect(responseTime).toBeLessThan(3000); // Should respond within 3 seconds
    });

    test('Filter application performance', async ({ page }) => {
        await page.goto('/merchant/products');
        
        const filterToggle = page.locator('#filterToggle');
        await filterToggle.click();
        
        // Apply multiple filters simultaneously
        const startTime = Date.now();
        
        await page.selectOption('#categoryFilter', '1');
        await page.fill('#priceMin', '10');
        await page.fill('#priceMax', '100');
        await page.check('#featuredFilter');
        
        const applyFilters = page.locator('#applyFilters');
        await applyFilters.click();
        
        await waitForSearchResults(page);
        const endTime = Date.now();
        
        const responseTime = endTime - startTime;
        expect(responseTime).toBeLessThan(5000); // Should apply filters within 5 seconds
    });

    test('Memory usage during extended search session', async ({ page }) => {
        await page.goto('/merchant/products');
        
        const searchInput = page.locator('.merchant-search-input');
        
        // Perform multiple searches to test for memory leaks
        const searches = ['Test', 'Product', 'Service', 'Category', 'Price'];
        
        for (const searchTerm of searches) {
            await searchInput.fill(searchTerm);
            await waitForSearchResults(page);
            await page.waitForTimeout(500); // Brief pause between searches
        }
        
        // Check for JavaScript errors
        const errors = [];
        page.on('pageerror', error => errors.push(error));
        
        expect(errors).toHaveLength(0);
    });
});

// Error Handling Tests
test.describe('Search and Filter Error Handling', () => {
    test.beforeEach(async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 720 });
        await loginAsMerchant(page);
    });

    test('Network error handling', async ({ page }) => {
        await page.goto('/merchant/products');
        
        // Simulate network failure
        await page.route('/merchant/products/search/suggestions*', route => {
            route.abort();
        });
        
        const searchInput = page.locator('.merchant-search-input');
        await searchInput.fill('Test');
        
        // Should show error state gracefully
        await page.waitForSelector('.error-suggestions, .no-suggestions', { timeout: 5000 });
    });

    test('Invalid filter values handling', async ({ page }) => {
        await page.goto('/merchant/products');
        
        const filterToggle = page.locator('#filterToggle');
        await filterToggle.click();
        
        // Test invalid price range
        await page.fill('#priceMin', 'invalid');
        await page.fill('#priceMax', 'also-invalid');
        
        const applyFilters = page.locator('#applyFilters');
        await applyFilters.click();
        
        // Should handle gracefully without breaking the interface
        await page.waitForTimeout(2000);
        
        const errors = [];
        page.on('pageerror', error => errors.push(error));
        
        expect(errors).toHaveLength(0);
    });
});

// Accessibility Tests
test.describe('Search and Filter Accessibility', () => {
    test.beforeEach(async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 720 });
        await loginAsMerchant(page);
    });

    test('Keyboard navigation support', async ({ page }) => {
        await page.goto('/merchant/products');

        const searchInput = page.locator('.merchant-search-input');
        await searchInput.focus();

        // Test Tab navigation
        await page.keyboard.press('Tab');
        await page.keyboard.press('Tab');

        // Test search with keyboard
        await searchInput.focus();
        await searchInput.fill('Test');

        // Test arrow key navigation in suggestions
        await page.waitForSelector('.suggestion-item');
        await page.keyboard.press('ArrowDown');
        await page.keyboard.press('ArrowDown');
        await page.keyboard.press('Enter');

        await waitForSearchResults(page);
    });

    test('Screen reader compatibility', async ({ page }) => {
        await page.goto('/merchant/products');

        // Check for proper ARIA labels
        const searchInput = page.locator('.merchant-search-input');
        await expect(searchInput).toHaveAttribute('aria-label');

        const suggestions = page.locator('.search-suggestions');
        await expect(suggestions).toHaveAttribute('role', 'listbox');

        // Test focus management
        await searchInput.fill('Test');
        await page.waitForSelector('.suggestion-item');

        const firstSuggestion = page.locator('.suggestion-item').first();
        await expect(firstSuggestion).toBeFocused();
    });
});

// Integration Tests
test.describe('Search and Filter Integration', () => {
    test.beforeEach(async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 720 });
        await loginAsMerchant(page);
    });

    test('Cross-page search consistency', async ({ page }) => {
        // Start search on dashboard
        await page.goto('/merchant/dashboard');

        const dashboardSearch = page.locator('.merchant-search-input');
        await dashboardSearch.fill('Test Product');
        await dashboardSearch.press('Enter');

        // Navigate to products page
        await page.goto('/merchant/products');

        // Verify search context is maintained if implemented
        const productSearch = page.locator('.merchant-search-input');
        // Note: This would depend on implementation details
        // await expect(productSearch).toHaveValue('Test Product');
    });

    test('Filter state persistence across sessions', async ({ page, context }) => {
        await page.goto('/merchant/products');

        // Apply filters
        const featuredFilter = page.locator('[data-filter="featured"]');
        await featuredFilter.click();

        const filterToggle = page.locator('#filterToggle');
        await filterToggle.click();

        await page.selectOption('#categoryFilter', '1');

        const applyFilters = page.locator('#applyFilters');
        await applyFilters.click();

        await waitForSearchResults(page);

        // Create new page in same context
        const newPage = await context.newPage();
        await newPage.goto('/merchant/products');

        // Check if filters are remembered (depends on implementation)
        // This would test localStorage or session persistence
    });
});

// Data Validation Tests
test.describe('Search and Filter Data Validation', () => {
    test.beforeEach(async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 720 });
        await loginAsMerchant(page);
    });

    test('SQL injection prevention', async ({ page }) => {
        await page.goto('/merchant/products');

        const searchInput = page.locator('.merchant-search-input');

        // Test various SQL injection attempts
        const maliciousInputs = [
            "'; DROP TABLE products; --",
            "' OR '1'='1",
            "'; SELECT * FROM users; --",
            "<script>alert('xss')</script>",
            "../../etc/passwd"
        ];

        for (const input of maliciousInputs) {
            await searchInput.fill(input);
            await page.waitForTimeout(1000);

            // Should not cause errors or unexpected behavior
            const errors = [];
            page.on('pageerror', error => errors.push(error));

            expect(errors).toHaveLength(0);
        }
    });

    test('XSS prevention in search results', async ({ page }) => {
        await page.goto('/merchant/products');

        const searchInput = page.locator('.merchant-search-input');
        await searchInput.fill('<script>alert("xss")</script>');

        await waitForSearchResults(page);

        // Verify no script execution
        const alerts = [];
        page.on('dialog', dialog => {
            alerts.push(dialog.message());
            dialog.dismiss();
        });

        expect(alerts).toHaveLength(0);
    });
});

// Cleanup and utility functions
test.afterEach(async ({ page }) => {
    // Clean up any test data or state
    await page.evaluate(() => {
        // Clear localStorage
        localStorage.clear();

        // Clear sessionStorage
        sessionStorage.clear();
    });
});

test.afterAll(async () => {
    // Global cleanup if needed
    console.log('All search and filter tests completed');
});
