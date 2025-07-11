/**
 * Test helpers for merchant search and filtering functionality
 */

const { expect } = require('@playwright/test');

class SearchTestHelpers {
    constructor(page) {
        this.page = page;
    }

    /**
     * Login as test merchant
     */
    async loginAsMerchant(credentials = {}) {
        const defaultCredentials = {
            email: 'test.merchant@example.com',
            password: 'password123'
        };
        
        const creds = { ...defaultCredentials, ...credentials };
        
        await this.page.goto('/login');
        await this.page.fill('input[name="email"]', creds.email);
        await this.page.fill('input[name="password"]', creds.password);
        await this.page.click('button[type="submit"]');
        await this.page.waitForURL('/merchant/dashboard');
    }

    /**
     * Wait for search results to load
     */
    async waitForSearchResults(timeout = 10000) {
        await this.page.waitForSelector(
            '.search-results, .products-table-container, .services-table-container', 
            { timeout }
        );
        await this.page.waitForLoadState('networkidle');
    }

    /**
     * Perform a search and wait for results
     */
    async performSearch(query, waitForResults = true) {
        const searchInput = this.page.locator('.merchant-search-input');
        await searchInput.fill(query);
        await searchInput.press('Enter');
        
        if (waitForResults) {
            await this.waitForSearchResults();
        }
    }

    /**
     * Apply a quick filter
     */
    async applyQuickFilter(filterType, value) {
        const filterButton = this.page.locator(`[data-filter="${filterType}"][data-value="${value}"]`);
        await filterButton.click();
        await this.waitForSearchResults();
        return filterButton;
    }

    /**
     * Open advanced filters modal
     */
    async openAdvancedFilters() {
        const filterToggle = this.page.locator('#filterToggle');
        await filterToggle.click();
        
        const modal = this.page.locator('#advancedFiltersModal');
        await expect(modal).toBeVisible();
        return modal;
    }

    /**
     * Apply advanced filters
     */
    async applyAdvancedFilters(filters = {}) {
        await this.openAdvancedFilters();
        
        // Apply category filter
        if (filters.category) {
            await this.page.selectOption('#categoryFilter', filters.category);
        }
        
        // Apply status filter
        if (filters.status) {
            await this.page.selectOption('#statusFilter', filters.status);
        }
        
        // Apply price range
        if (filters.priceMin) {
            await this.page.fill('#priceMin', filters.priceMin.toString());
        }
        if (filters.priceMax) {
            await this.page.fill('#priceMax', filters.priceMax.toString());
        }
        
        // Apply date range
        if (filters.dateFrom) {
            await this.page.fill('#dateFrom', filters.dateFrom);
        }
        if (filters.dateTo) {
            await this.page.fill('#dateTo', filters.dateTo);
        }
        
        // Apply featured filter
        if (filters.featured) {
            await this.page.check('#featuredFilter');
        }
        
        // Apply stock status (for products)
        if (filters.stockStatus) {
            await this.page.selectOption('#stockStatusFilter', filters.stockStatus);
        }
        
        // Apply service type (for services)
        if (filters.serviceType) {
            await this.page.selectOption('#serviceTypeFilter', filters.serviceType);
        }
        
        // Apply duration range (for services)
        if (filters.durationMin) {
            await this.page.fill('#durationMin', filters.durationMin.toString());
        }
        if (filters.durationMax) {
            await this.page.fill('#durationMax', filters.durationMax.toString());
        }
        
        // Apply sorting
        if (filters.sortBy) {
            await this.page.selectOption('#sortBy', filters.sortBy);
        }
        if (filters.sortOrder) {
            await this.page.selectOption('#sortOrder', filters.sortOrder);
        }
        
        // Apply filters
        const applyButton = this.page.locator('#applyFilters');
        await applyButton.click();
        
        await this.waitForSearchResults();
    }

    /**
     * Clear all filters
     */
    async clearAllFilters() {
        const clearButton = this.page.locator('#clearFilters');
        if (await clearButton.isVisible()) {
            await clearButton.click();
        } else {
            // Try global clear function
            await this.page.evaluate(() => {
                if (window.clearSearchAndFilters) {
                    window.clearSearchAndFilters();
                }
            });
        }
        await this.waitForSearchResults();
    }

    /**
     * Get search suggestions
     */
    async getSearchSuggestions(query) {
        const searchInput = this.page.locator('.merchant-search-input');
        await searchInput.fill(query);
        
        await this.page.waitForSelector('.search-suggestions', { timeout: 5000 });
        
        const suggestions = await this.page.locator('.suggestion-item').allTextContents();
        return suggestions;
    }

    /**
     * Verify search results contain expected items
     */
    async verifySearchResults(expectedItems = []) {
        const resultItems = this.page.locator('.result-item, .discord-table tbody tr');
        const count = await resultItems.count();
        
        expect(count).toBeGreaterThan(0);
        
        if (expectedItems.length > 0) {
            for (const expectedItem of expectedItems) {
                const itemLocator = this.page.locator(`text=${expectedItem}`);
                await expect(itemLocator).toBeVisible();
            }
        }
        
        return count;
    }

    /**
     * Verify filter is active
     */
    async verifyFilterActive(filterType, value) {
        const filterButton = this.page.locator(`[data-filter="${filterType}"][data-value="${value}"]`);
        await expect(filterButton).toHaveClass(/active/);
    }

    /**
     * Verify URL contains expected parameters
     */
    async verifyUrlParameters(expectedParams = {}) {
        const url = new URL(this.page.url());
        
        for (const [key, value] of Object.entries(expectedParams)) {
            expect(url.searchParams.get(key)).toBe(value.toString());
        }
    }

    /**
     * Test mobile responsiveness
     */
    async testMobileResponsiveness() {
        // Set mobile viewport
        await this.page.setViewportSize({ width: 375, height: 667 });
        
        // Verify search input is visible and properly sized
        const searchInput = this.page.locator('.merchant-search-input');
        await expect(searchInput).toBeVisible();
        
        // Check font size (should be >= 16px to prevent zoom on iOS)
        const fontSize = await searchInput.evaluate(el => 
            window.getComputedStyle(el).fontSize
        );
        expect(parseInt(fontSize)).toBeGreaterThanOrEqual(16);
        
        // Test filter toggle button
        const filterToggle = this.page.locator('#filterToggle');
        if (await filterToggle.isVisible()) {
            await filterToggle.tap();
            
            // Verify modal opens properly
            const modal = this.page.locator('#advancedFiltersModal');
            await expect(modal).toBeVisible();
            
            // Close modal
            const closeButton = this.page.locator('.btn-close');
            await closeButton.tap();
            await expect(modal).not.toBeVisible();
        }
    }

    /**
     * Test keyboard navigation
     */
    async testKeyboardNavigation() {
        const searchInput = this.page.locator('.merchant-search-input');
        await searchInput.focus();
        
        // Test search with keyboard
        await searchInput.fill('Test');
        
        // Wait for suggestions
        await this.page.waitForSelector('.suggestion-item', { timeout: 5000 });
        
        // Test arrow key navigation
        await this.page.keyboard.press('ArrowDown');
        await this.page.keyboard.press('ArrowDown');
        
        // Test Enter key selection
        await this.page.keyboard.press('Enter');
        
        await this.waitForSearchResults();
    }

    /**
     * Measure performance
     */
    async measureSearchPerformance(query) {
        const startTime = Date.now();
        
        await this.performSearch(query);
        
        const endTime = Date.now();
        const responseTime = endTime - startTime;
        
        console.log(`Search performance for "${query}": ${responseTime}ms`);
        
        return responseTime;
    }

    /**
     * Test error handling
     */
    async testErrorHandling() {
        // Test with malicious input
        const maliciousInputs = [
            "'; DROP TABLE products; --",
            "<script>alert('xss')</script>",
            "' OR '1'='1"
        ];
        
        const errors = [];
        this.page.on('pageerror', error => errors.push(error));
        
        for (const input of maliciousInputs) {
            await this.performSearch(input, false);
            await this.page.waitForTimeout(1000);
        }
        
        expect(errors).toHaveLength(0);
    }

    /**
     * Create test data
     */
    async createTestData() {
        // This would typically involve API calls to create test products and services
        console.log('Creating test data...');
        
        // Example API calls (would need to be implemented)
        // await this.page.request.post('/api/test/products', { data: testProducts });
        // await this.page.request.post('/api/test/services', { data: testServices });
    }

    /**
     * Clean up test data
     */
    async cleanupTestData() {
        // This would typically involve API calls to clean up test data
        console.log('Cleaning up test data...');
        
        // Example API calls (would need to be implemented)
        // await this.page.request.delete('/api/test/cleanup');
    }
}

module.exports = { SearchTestHelpers };
