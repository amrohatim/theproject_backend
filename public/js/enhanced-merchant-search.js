/**
 * Enhanced Merchant Search Component
 * Modern UI/UX with animations, micro-interactions, and advanced features
 */

class EnhancedMerchantSearch {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            minQueryLength: 2,
            debounceDelay: 300,
            maxSuggestions: 8,
            maxRecentSearches: 5,
            enableAnimations: true,
            enableSounds: false,
            ...options
        };

        this.searchInput = null;
        this.suggestionsContainer = null;
        this.loadingIndicator = null;
        this.clearButton = null;
        this.searchIcon = null;
        
        this.currentQuery = '';
        this.isLoading = false;
        this.selectedIndex = -1;
        this.suggestions = [];
        this.recentSearches = [];
        
        this.debounceTimer = null;
        this.animationFrame = null;

        this.init();
    }

    init() {
        this.findElements();
        this.loadRecentSearches();
        this.bindEvents();
        this.setupKeyboardNavigation();
        this.initializeAnimations();
    }

    findElements() {
        this.searchInput = this.container.querySelector('.enhanced-search-input');
        this.suggestionsContainer = this.container.querySelector('.enhanced-search-suggestions');
        this.loadingIndicator = this.container.querySelector('.enhanced-search-loading');
        this.clearButton = this.container.querySelector('.enhanced-search-clear');
        this.searchIcon = this.container.querySelector('.enhanced-search-icon');
        this.recentSearchesContainer = this.container.querySelector('.recent-searches');
        this.suggestionsListContainer = this.container.querySelector('.suggestions-list');
        this.searchStatsContainer = this.container.querySelector('.search-stats');

        if (!this.searchInput) {
            console.error('Enhanced search input not found');
            return;
        }
    }

    bindEvents() {
        // Input events with debouncing
        this.searchInput.addEventListener('input', (e) => {
            this.handleInput(e.target.value);
        });

        this.searchInput.addEventListener('focus', () => {
            this.handleFocus();
        });

        this.searchInput.addEventListener('blur', (e) => {
            // Delay hiding suggestions to allow for clicks
            setTimeout(() => this.handleBlur(e), 150);
        });

        // Clear button
        if (this.clearButton) {
            this.clearButton.addEventListener('click', () => {
                this.clearSearch();
            });
        }

        // Form submission
        const form = this.container.querySelector('.search-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch(this.searchInput.value);
            });
        }

        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!this.container.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }

    setupKeyboardNavigation() {
        this.searchInput.addEventListener('keydown', (e) => {
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    this.navigateDown();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.navigateUp();
                    break;
                case 'Enter':
                    e.preventDefault();
                    this.selectCurrent();
                    break;
                case 'Escape':
                    this.hideSuggestions();
                    this.searchInput.blur();
                    break;
                case 'Tab':
                    if (this.selectedIndex >= 0) {
                        e.preventDefault();
                        this.selectCurrent();
                    }
                    break;
            }
        });
    }

    initializeAnimations() {
        if (!this.options.enableAnimations) return;

        // Add entrance animation to container
        this.container.style.opacity = '0';
        this.container.style.transform = 'translateY(10px)';
        
        requestAnimationFrame(() => {
            this.container.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            this.container.style.opacity = '1';
            this.container.style.transform = 'translateY(0)';
        });
    }

    handleInput(value) {
        this.currentQuery = value.trim();
        
        // Update clear button visibility
        this.updateClearButton();
        
        // Clear previous debounce timer
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        // Debounce the search
        this.debounceTimer = setTimeout(() => {
            if (this.currentQuery.length >= this.options.minQueryLength) {
                this.fetchSuggestions(this.currentQuery);
            } else if (this.currentQuery.length === 0) {
                this.showRecentSearches();
            } else {
                this.hideSuggestions();
            }
        }, this.options.debounceDelay);
    }

    handleFocus() {
        this.addFocusEffects();
        
        if (this.currentQuery.length >= this.options.minQueryLength) {
            this.showSuggestions();
        } else {
            this.showRecentSearches();
        }
    }

    handleBlur(e) {
        this.removeFocusEffects();
        
        // Don't hide if clicking on suggestions
        if (e.relatedTarget && this.suggestionsContainer.contains(e.relatedTarget)) {
            return;
        }
        
        this.hideSuggestions();
    }

    addFocusEffects() {
        this.container.classList.add('focused');
        
        if (this.options.enableAnimations) {
            this.searchIcon.style.transform = 'translateY(-50%) scale(1.2) rotate(90deg)';
        }
    }

    removeFocusEffects() {
        this.container.classList.remove('focused');
        
        if (this.options.enableAnimations) {
            this.searchIcon.style.transform = 'translateY(-50%) scale(1)';
        }
    }

    updateClearButton() {
        if (!this.clearButton) return;
        
        if (this.currentQuery.length > 0) {
            this.clearButton.classList.add('visible');
        } else {
            this.clearButton.classList.remove('visible');
        }
    }

    async fetchSuggestions(query) {
        if (this.isLoading) return;
        
        this.showLoading();
        
        try {
            const searchType = this.searchInput.dataset.searchType || 'global';
            const endpoint = this.getSearchEndpoint(searchType);
            
            const response = await fetch(`${endpoint}?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            if (data.success) {
                this.suggestions = data.suggestions || [];
                this.renderSuggestions();
                this.showSuggestions();
            }
        } catch (error) {
            console.error('Error fetching suggestions:', error);
            this.showErrorState();
        } finally {
            this.hideLoading();
        }
    }

    getSearchEndpoint(searchType) {
        const endpoints = {
            'global': '/merchant/dashboard/search/suggestions',
            'products': '/merchant/products/search/suggestions',
            'services': '/merchant/services/search/suggestions'
        };
        
        return endpoints[searchType] || endpoints['global'];
    }

    renderSuggestions() {
        if (!this.suggestionsListContainer) return;

        if (this.suggestions.length === 0) {
            this.renderNoResults();
            return;
        }

        const html = this.suggestions.map((suggestion, index) => `
            <div class="enhanced-suggestion-item" data-index="${index}" data-value="${suggestion.value}">
                ${this.renderSuggestionThumbnail(suggestion)}
                <div class="suggestion-content">
                    <div class="suggestion-title">${this.highlightQuery(suggestion.title)}</div>
                    ${this.renderSuggestionMeta(suggestion)}
                </div>
            </div>
        `).join('');

        this.suggestionsListContainer.innerHTML = html;
        this.bindSuggestionEvents();
        this.updateSearchStats();
    }

    renderSuggestionThumbnail(suggestion) {
        if (suggestion.thumbnail) {
            return `<img src="${suggestion.thumbnail}" alt="${suggestion.title}" class="suggestion-thumbnail" loading="lazy">`;
        } else if (suggestion.image) {
            return `<img src="${suggestion.image}" alt="${suggestion.title}" class="suggestion-thumbnail" loading="lazy">`;
        } else if (suggestion.type) {
            const initial = suggestion.title.charAt(0).toUpperCase();
            return `<div class="suggestion-thumbnail-placeholder">${initial}</div>`;
        } else {
            return `
                <div class="suggestion-icon">
                    <i class="${suggestion.icon || 'fas fa-search'}"></i>
                </div>
            `;
        }
    }

    renderSuggestionMeta(suggestion) {
        if (!suggestion.meta && !suggestion.price && !suggestion.category && !suggestion.status) {
            return '';
        }

        let metaHtml = '<div class="suggestion-meta">';

        if (suggestion.price) {
            metaHtml += `<span class="suggestion-price">$${suggestion.price}</span>`;
        }

        if (suggestion.category) {
            metaHtml += `<span class="suggestion-category">${suggestion.category}</span>`;
        }

        if (suggestion.status) {
            metaHtml += `<span class="suggestion-status ${suggestion.status}">${suggestion.status}</span>`;
        }

        if (suggestion.rating) {
            metaHtml += `
                <span class="suggestion-rating">
                    <span class="stars">${this.renderStars(suggestion.rating)}</span>
                    <span class="rating-text">(${suggestion.rating_count || 0})</span>
                </span>
            `;
        }

        if (suggestion.meta) {
            metaHtml += `<span>${suggestion.meta}</span>`;
        }

        metaHtml += '</div>';
        return metaHtml;
    }

    renderStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;
        let starsHtml = '';

        for (let i = 0; i < fullStars; i++) {
            starsHtml += '<i class="fas fa-star"></i>';
        }

        if (hasHalfStar) {
            starsHtml += '<i class="fas fa-star-half-alt"></i>';
        }

        const emptyStars = 5 - Math.ceil(rating);
        for (let i = 0; i < emptyStars; i++) {
            starsHtml += '<i class="far fa-star"></i>';
        }

        return starsHtml;
    }

    renderNoResults() {
        this.suggestionsListContainer.innerHTML = `
            <div class="no-suggestions">
                <div class="suggestion-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="suggestion-content">
                    <div class="suggestion-title">No suggestions found</div>
                    <div class="suggestion-meta">Try a different search term</div>
                </div>
            </div>
        `;
    }

    highlightQuery(text) {
        if (!this.currentQuery) return text;
        
        const regex = new RegExp(`(${this.escapeRegex(this.currentQuery)})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    bindSuggestionEvents() {
        const suggestionItems = this.suggestionsListContainer.querySelectorAll('.enhanced-suggestion-item');
        
        suggestionItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                this.selectSuggestion(index);
            });
            
            item.addEventListener('mouseenter', () => {
                this.setSelectedIndex(index);
            });
        });
    }

    showRecentSearches() {
        if (!this.recentSearchesContainer || this.recentSearches.length === 0) {
            this.hideSuggestions();
            return;
        }
        
        this.renderRecentSearches();
        this.showSuggestions();
    }

    renderRecentSearches() {
        const recentSearchesList = this.recentSearchesContainer.querySelector('.recent-searches-list');
        if (!recentSearchesList) return;

        const html = this.recentSearches.map(search => `
            <div class="recent-search-item" data-value="${search.query}">
                <div class="recent-search-content">
                    <div class="recent-search-query">${search.query}</div>
                    <div class="recent-search-timestamp">${this.formatTimestamp(search.timestamp)}</div>
                    ${search.resultsCount ? `
                        <div class="recent-search-meta">
                            <span class="recent-search-results-count">${search.resultsCount} results</span>
                        </div>
                    ` : ''}
                </div>
                <button class="recent-search-remove" data-query="${search.query}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');

        recentSearchesList.innerHTML = html;
        this.recentSearchesContainer.style.display = 'block';

        // Bind events for recent searches
        this.bindRecentSearchEvents();
    }

    formatTimestamp(timestamp) {
        const now = Date.now();
        const diff = now - timestamp;

        const minutes = Math.floor(diff / (1000 * 60));
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));

        if (minutes < 1) {
            return 'Just now';
        } else if (minutes < 60) {
            return `${minutes}m ago`;
        } else if (hours < 24) {
            return `${hours}h ago`;
        } else if (days < 7) {
            return `${days}d ago`;
        } else {
            return new Date(timestamp).toLocaleDateString();
        }
    }

    bindRecentSearchEvents() {
        const recentItems = this.recentSearchesContainer.querySelectorAll('.recent-search-item');
        const removeButtons = this.recentSearchesContainer.querySelectorAll('.recent-search-remove');
        
        recentItems.forEach(item => {
            item.addEventListener('click', (e) => {
                if (!e.target.closest('.recent-search-remove')) {
                    const query = item.dataset.value;
                    this.searchInput.value = query;
                    this.performSearch(query);
                }
            });
        });
        
        removeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const query = button.dataset.query;
                this.removeRecentSearch(query);
            });
        });
    }

    showLoading() {
        this.isLoading = true;
        if (this.loadingIndicator) {
            this.loadingIndicator.classList.add('active');
        }
    }

    hideLoading() {
        this.isLoading = false;
        if (this.loadingIndicator) {
            this.loadingIndicator.classList.remove('active');
        }
    }

    showSuggestions() {
        if (this.suggestionsContainer) {
            this.suggestionsContainer.classList.add('visible');
        }
    }

    hideSuggestions() {
        if (this.suggestionsContainer) {
            this.suggestionsContainer.classList.remove('visible');
        }
        this.selectedIndex = -1;
        this.updateSelectedItem();
    }

    navigateDown() {
        if (this.selectedIndex < this.suggestions.length - 1) {
            this.setSelectedIndex(this.selectedIndex + 1);
        }
    }

    navigateUp() {
        if (this.selectedIndex > 0) {
            this.setSelectedIndex(this.selectedIndex - 1);
        }
    }

    setSelectedIndex(index) {
        this.selectedIndex = index;
        this.updateSelectedItem();
    }

    updateSelectedItem() {
        const items = this.suggestionsListContainer.querySelectorAll('.enhanced-suggestion-item');
        
        items.forEach((item, index) => {
            if (index === this.selectedIndex) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    selectCurrent() {
        if (this.selectedIndex >= 0 && this.selectedIndex < this.suggestions.length) {
            this.selectSuggestion(this.selectedIndex);
        } else {
            this.performSearch(this.currentQuery);
        }
    }

    selectSuggestion(index) {
        const suggestion = this.suggestions[index];
        if (suggestion) {
            this.searchInput.value = suggestion.value;
            this.performSearch(suggestion.value);
        }
    }

    async performSearch(query) {
        if (!query || query.length < this.options.minQueryLength) return;

        // Show loading state
        this.showLoading();

        try {
            // Get search results for preview
            const searchResults = await this.getSearchResults(query);
            const resultsCount = searchResults ? (searchResults.products?.length || 0) + (searchResults.services?.length || 0) : null;

            // Add to recent searches with results count
            this.addToRecentSearches(query, resultsCount);

            // Hide suggestions
            this.hideSuggestions();

            // Trigger search event with results
            const searchEvent = new CustomEvent('enhancedMerchantSearch', {
                detail: {
                    query: query,
                    searchType: this.searchInput.dataset.searchType || 'global',
                    results: searchResults,
                    resultsCount: resultsCount
                }
            });
            document.dispatchEvent(searchEvent);

            // Save search to backend
            await this.saveSearch(query);

        } catch (error) {
            console.error('Error performing search:', error);

            // Still add to recent searches without results count
            this.addToRecentSearches(query);

            // Trigger search event without results
            const searchEvent = new CustomEvent('enhancedMerchantSearch', {
                detail: {
                    query: query,
                    searchType: this.searchInput.dataset.searchType || 'global'
                }
            });
            document.dispatchEvent(searchEvent);
        } finally {
            this.hideLoading();
        }
    }

    async getSearchResults(query) {
        try {
            const searchType = this.searchInput.dataset.searchType || 'global';
            const endpoint = this.getSearchEndpoint(searchType).replace('/suggestions', '');

            const response = await fetch(`${endpoint}?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            return data.success ? data.results : null;
        } catch (error) {
            console.error('Error fetching search results:', error);
            return null;
        }
    }

    clearSearch() {
        this.searchInput.value = '';
        this.currentQuery = '';
        this.updateClearButton();
        this.hideSuggestions();
        this.searchInput.focus();
        
        // Trigger clear event
        const clearEvent = new CustomEvent('enhancedMerchantSearchClear');
        document.dispatchEvent(clearEvent);
    }

    addToRecentSearches(query, resultsCount = null) {
        // Remove if already exists
        this.recentSearches = this.recentSearches.filter(search => search.query !== query);

        // Add to beginning with enhanced metadata
        this.recentSearches.unshift({
            query: query,
            timestamp: Date.now(),
            resultsCount: resultsCount,
            searchType: this.searchInput.dataset.searchType || 'global'
        });

        // Limit to max recent searches
        this.recentSearches = this.recentSearches.slice(0, this.options.maxRecentSearches);

        // Save to localStorage
        this.saveRecentSearches();
    }

    removeRecentSearch(query) {
        this.recentSearches = this.recentSearches.filter(search => search.query !== query);
        this.saveRecentSearches();
        this.renderRecentSearches();
    }

    loadRecentSearches() {
        try {
            const saved = localStorage.getItem('enhancedMerchantSearchRecent');
            if (saved) {
                this.recentSearches = JSON.parse(saved);
            }
        } catch (error) {
            console.error('Error loading recent searches:', error);
            this.recentSearches = [];
        }
    }

    saveRecentSearches() {
        try {
            localStorage.setItem('enhancedMerchantSearchRecent', JSON.stringify(this.recentSearches));
        } catch (error) {
            console.error('Error saving recent searches:', error);
        }
    }

    async saveSearch(query) {
        try {
            await fetch('/merchant/dashboard/search/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ query: query })
            });
        } catch (error) {
            console.error('Error saving search:', error);
        }
    }

    updateSearchStats() {
        if (!this.searchStatsContainer) return;
        
        const statsText = this.searchStatsContainer.querySelector('.search-stats-text');
        if (statsText) {
            const count = this.suggestions.length;
            statsText.textContent = `${count} suggestion${count !== 1 ? 's' : ''} found`;
            this.searchStatsContainer.style.display = count > 0 ? 'block' : 'none';
        }
    }

    showErrorState() {
        if (this.suggestionsListContainer) {
            this.suggestionsListContainer.innerHTML = `
                <div class="search-error">
                    <div class="suggestion-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="suggestion-content">
                        <div class="suggestion-title">Search temporarily unavailable</div>
                        <div class="suggestion-meta">Please try again in a moment</div>
                    </div>
                </div>
            `;
            this.showSuggestions();
        }
    }

    destroy() {
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }
        
        if (this.animationFrame) {
            cancelAnimationFrame(this.animationFrame);
        }
        
        // Remove event listeners
        // Note: In a real implementation, you'd want to store references to bound functions
        // and remove them properly to prevent memory leaks
    }
}

// Auto-initialize enhanced search components
document.addEventListener('DOMContentLoaded', function() {
    const searchContainers = document.querySelectorAll('.enhanced-search-container');
    
    searchContainers.forEach(container => {
        new EnhancedMerchantSearch(container, {
            enableAnimations: true,
            enableSounds: false
        });
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedMerchantSearch;
}
