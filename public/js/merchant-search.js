/**
 * Merchant Search Component
 * Provides global search functionality with autocomplete, recent searches, and filtering
 */
class MerchantSearch {
    constructor(options = {}) {
        this.options = {
            searchInputSelector: '.merchant-search-input',
            suggestionsContainerSelector: '.search-suggestions',
            recentSearchesSelector: '.recent-searches',
            searchResultsSelector: '.search-results',
            minQueryLength: 2,
            debounceDelay: 300,
            maxSuggestions: 10,
            ...options
        };

        this.searchInput = null;
        this.suggestionsContainer = null;
        this.recentSearchesContainer = null;
        this.searchResultsContainer = null;
        this.debounceTimer = null;
        this.currentQuery = '';
        this.isSearching = false;

        this.init();
    }

    init() {
        this.searchInput = document.querySelector(this.options.searchInputSelector);
        this.suggestionsContainer = document.querySelector(this.options.suggestionsContainerSelector);
        this.recentSearchesContainer = document.querySelector(this.options.recentSearchesSelector);
        this.searchResultsContainer = document.querySelector(this.options.searchResultsSelector);

        if (!this.searchInput) {
            console.warn('Search input not found');
            return;
        }

        this.bindEvents();
        this.loadRecentSearches();
    }

    bindEvents() {
        // Search input events
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearchInput(e.target.value);
        });

        this.searchInput.addEventListener('focus', () => {
            this.showSuggestions();
        });

        this.searchInput.addEventListener('keydown', (e) => {
            this.handleKeydown(e);
        });

        // Click outside to hide suggestions
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container')) {
                this.hideSuggestions();
            }
        });

        // Form submission
        const searchForm = this.searchInput.closest('form');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch(this.searchInput.value);
            });
        }
    }

    handleSearchInput(query) {
        this.currentQuery = query.trim();

        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        if (this.currentQuery.length < this.options.minQueryLength) {
            this.showRecentSearches();
            return;
        }

        this.debounceTimer = setTimeout(() => {
            this.getSuggestions(this.currentQuery);
        }, this.options.debounceDelay);
    }

    handleKeydown(e) {
        const suggestions = this.suggestionsContainer?.querySelectorAll('.suggestion-item');
        if (!suggestions || suggestions.length === 0) return;

        const activeItem = this.suggestionsContainer.querySelector('.suggestion-item.active');
        let activeIndex = activeItem ? Array.from(suggestions).indexOf(activeItem) : -1;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                activeIndex = (activeIndex + 1) % suggestions.length;
                this.setActiveSuggestion(suggestions, activeIndex);
                break;
            case 'ArrowUp':
                e.preventDefault();
                activeIndex = activeIndex <= 0 ? suggestions.length - 1 : activeIndex - 1;
                this.setActiveSuggestion(suggestions, activeIndex);
                break;
            case 'Enter':
                e.preventDefault();
                if (activeItem) {
                    this.selectSuggestion(activeItem.textContent.trim());
                } else {
                    this.performSearch(this.currentQuery);
                }
                break;
            case 'Escape':
                this.hideSuggestions();
                this.searchInput.blur();
                break;
        }
    }

    setActiveSuggestion(suggestions, index) {
        suggestions.forEach((item, i) => {
            item.classList.toggle('active', i === index);
        });
    }

    async getSuggestions(query) {
        if (this.isSearching) return;

        try {
            this.isSearching = true;
            this.showLoadingState();

            const response = await fetch(`/merchant/dashboard/search/suggestions?q=${encodeURIComponent(query)}`, {
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
                this.displaySuggestions(data.suggestions);
            } else {
                this.showErrorState('Failed to load suggestions');
            }
        } catch (error) {
            this.handleError(error, 'loading suggestions');
            this.showErrorState('Error loading suggestions');
        } finally {
            this.isSearching = false;
        }
    }

    displaySuggestions(suggestions) {
        if (!this.suggestionsContainer) return;

        if (suggestions.length === 0) {
            this.suggestionsContainer.innerHTML = `
                <div class="no-suggestions">
                    <i class="fas fa-search"></i>
                    <span>No suggestions found</span>
                </div>
            `;
            this.showSuggestions();
            return;
        }

        const suggestionsHtml = suggestions.map(suggestion => `
            <div class="suggestion-item" data-query="${suggestion}">
                <i class="fas fa-search"></i>
                <span>${this.highlightQuery(suggestion, this.currentQuery)}</span>
            </div>
        `).join('');

        this.suggestionsContainer.innerHTML = suggestionsHtml;

        // Bind click events to suggestions
        this.suggestionsContainer.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', () => {
                this.selectSuggestion(item.dataset.query);
            });
        });

        this.showSuggestions();
    }

    highlightQuery(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<strong>$1</strong>');
    }

    showLoadingState() {
        if (!this.suggestionsContainer) return;
        
        this.suggestionsContainer.innerHTML = `
            <div class="loading-suggestions">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Loading suggestions...</span>
            </div>
        `;
        this.showSuggestions();
    }

    showErrorState(message) {
        if (!this.suggestionsContainer) return;
        
        this.suggestionsContainer.innerHTML = `
            <div class="error-suggestions">
                <i class="fas fa-exclamation-triangle"></i>
                <span>${message}</span>
            </div>
        `;
        this.showSuggestions();
    }

    showRecentSearches() {
        if (!this.suggestionsContainer) return;

        const recentSearches = this.getRecentSearches();
        
        if (recentSearches.length === 0) {
            this.hideSuggestions();
            return;
        }

        const recentHtml = `
            <div class="recent-searches-header">
                <span>Recent Searches</span>
                <button type="button" class="clear-recent" onclick="merchantSearch.clearRecentSearches()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            ${recentSearches.map(search => `
                <div class="suggestion-item recent-search-item" data-query="${search}">
                    <i class="fas fa-history"></i>
                    <span>${search}</span>
                </div>
            `).join('')}
        `;

        this.suggestionsContainer.innerHTML = recentHtml;

        // Bind click events
        this.suggestionsContainer.querySelectorAll('.recent-search-item').forEach(item => {
            item.addEventListener('click', () => {
                this.selectSuggestion(item.dataset.query);
            });
        });

        this.showSuggestions();
    }

    selectSuggestion(query) {
        this.searchInput.value = query;
        this.hideSuggestions();
        this.performSearch(query);
    }

    async performSearch(query) {
        if (!query || query.length < this.options.minQueryLength) return;

        // Save search to recent searches
        await this.saveSearch(query);
        
        // Update recent searches in localStorage
        this.addToRecentSearches(query);

        // Trigger search event
        const searchEvent = new CustomEvent('merchantSearch', {
            detail: { query: query }
        });
        document.dispatchEvent(searchEvent);

        // If we have a search results container, perform the search
        if (this.searchResultsContainer) {
            await this.displaySearchResults(query);
        }
    }

    async displaySearchResults(query) {
        try {
            this.showSearchLoading();

            const response = await fetch(`/merchant/dashboard/search?q=${encodeURIComponent(query)}`, {
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
                this.renderSearchResults(data.results, query);
            } else {
                this.showSearchError('Failed to load search results');
            }
        } catch (error) {
            this.handleError(error, 'performing search');
            this.showSearchError('Error loading search results');
        }
    }

    renderSearchResults(results, query) {
        if (!this.searchResultsContainer) return;

        const totalResults = results.products.length + results.services.length;
        
        if (totalResults === 0) {
            this.searchResultsContainer.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No results found for "${query}"</h3>
                    <p>Try adjusting your search terms or browse our categories.</p>
                </div>
            `;
            return;
        }

        let html = `<div class="search-results-header">
            <h3>Search Results for "${query}" (${totalResults} found)</h3>
        </div>`;

        if (results.products.length > 0) {
            html += this.renderProductResults(results.products);
        }

        if (results.services.length > 0) {
            html += this.renderServiceResults(results.services);
        }

        this.searchResultsContainer.innerHTML = html;
    }

    renderProductResults(products) {
        return `
            <div class="results-section">
                <h4><i class="fas fa-box"></i> Products (${products.length})</h4>
                <div class="results-grid">
                    ${products.map(product => `
                        <div class="result-item" onclick="window.location.href='${product.url}'">
                            <div class="result-image">
                                ${product.image ? 
                                    `<img src="${product.image}" alt="${product.name}">` :
                                    `<div class="no-image"><i class="fas fa-box"></i></div>`
                                }
                            </div>
                            <div class="result-content">
                                <h5>${product.name}</h5>
                                ${product.sku ? `<p class="sku">SKU: ${product.sku}</p>` : ''}
                                <p class="category">${product.category}</p>
                                <div class="result-footer">
                                    <span class="price">$${product.price}</span>
                                    <span class="status ${product.status.toLowerCase()}">${product.status}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    renderServiceResults(services) {
        return `
            <div class="results-section">
                <h4><i class="fas fa-concierge-bell"></i> Services (${services.length})</h4>
                <div class="results-grid">
                    ${services.map(service => `
                        <div class="result-item" onclick="window.location.href='${service.url}'">
                            <div class="result-image">
                                ${service.image ? 
                                    `<img src="${service.image}" alt="${service.name}">` :
                                    `<div class="no-image"><i class="fas fa-concierge-bell"></i></div>`
                                }
                            </div>
                            <div class="result-content">
                                <h5>${service.name}</h5>
                                <p class="category">${service.category}</p>
                                <div class="result-footer">
                                    <span class="price">$${service.price}</span>
                                    ${service.duration ? `<span class="duration">${service.duration} min</span>` : ''}
                                    <span class="status ${service.status.toLowerCase()}">${service.status}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    showSearchLoading() {
        if (!this.searchResultsContainer) return;
        
        this.searchResultsContainer.innerHTML = `
            <div class="search-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Searching...</p>
            </div>
        `;
    }

    showSearchError(message) {
        if (!this.searchResultsContainer) return;

        this.searchResultsContainer.innerHTML = `
            <div class="search-error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>${message}</p>
                <button type="button" class="discord-btn-secondary mt-3" onclick="merchantSearch.retryLastSearch()">
                    <i class="fas fa-redo me-1"></i> Try Again
                </button>
            </div>
        `;
    }

    retryLastSearch() {
        if (this.currentQuery) {
            this.displaySearchResults(this.currentQuery);
        }
    }

    // Enhanced error handling with user feedback
    handleError(error, context = 'operation') {
        console.error(`Error during ${context}:`, error);

        // Show user-friendly error message
        this.showUserNotification(`An error occurred during ${context}. Please try again.`, 'error');
    }

    showUserNotification(message, type = 'info') {
        // Create or update notification element
        let notification = document.getElementById('merchant-search-notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.id = 'merchant-search-notification';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                padding: 12px 16px;
                border-radius: 6px;
                color: white;
                font-size: 14px;
                max-width: 300px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
                transform: translateX(100%);
            `;
            document.body.appendChild(notification);
        }

        // Set notification style based on type
        const colors = {
            info: 'var(--discord-primary)',
            success: 'var(--discord-green)',
            warning: 'var(--discord-yellow)',
            error: 'var(--discord-red)'
        };

        notification.style.backgroundColor = colors[type] || colors.info;
        notification.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" style="background: none; border: none; color: white; margin-left: 12px; cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Show notification
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    async saveSearch(query) {
        try {
            await fetch('/merchant/dashboard/search/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ q: query })
            });
        } catch (error) {
            // Silently fail for search saving - not critical
            console.warn('Error saving search:', error);
        }
    }

    addToRecentSearches(query) {
        let recentSearches = this.getRecentSearches();
        
        // Remove if already exists
        recentSearches = recentSearches.filter(search => search !== query);
        
        // Add to beginning
        recentSearches.unshift(query);
        
        // Keep only last 10 searches
        recentSearches = recentSearches.slice(0, 10);
        
        localStorage.setItem('merchant_recent_searches', JSON.stringify(recentSearches));
    }

    getRecentSearches() {
        try {
            return JSON.parse(localStorage.getItem('merchant_recent_searches') || '[]');
        } catch (error) {
            return [];
        }
    }

    clearRecentSearches() {
        localStorage.removeItem('merchant_recent_searches');
        this.hideSuggestions();
    }

    loadRecentSearches() {
        // This method can be called to preload recent searches if needed
        const recentSearches = this.getRecentSearches();
        console.log('Loaded recent searches:', recentSearches);
    }

    showSuggestions() {
        if (this.suggestionsContainer) {
            this.suggestionsContainer.style.display = 'block';
        }
    }

    hideSuggestions() {
        if (this.suggestionsContainer) {
            this.suggestionsContainer.style.display = 'none';
        }
    }

    destroy() {
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }
        // Remove event listeners if needed
    }
}

// Global instance
let merchantSearch;

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    merchantSearch = new MerchantSearch();
});
