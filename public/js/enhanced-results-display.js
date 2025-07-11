/**
 * Enhanced Results Display Component
 * Modern card-based layout with animations and interactive features
 */

class EnhancedResultsDisplay {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            enableAnimations: true,
            defaultView: 'grid', // 'grid', 'list', 'compact'
            enableLazyLoading: true,
            itemsPerPage: 12,
            enableInfiniteScroll: false,
            ...options
        };

        this.currentView = this.options.defaultView;
        this.currentResults = [];
        this.isLoading = false;
        this.currentPage = 1;
        this.totalResults = 0;

        this.resultsContainer = null;
        this.resultsHeader = null;
        this.resultsGrid = null;
        this.viewToggle = null;

        this.init();
    }

    init() {
        this.createStructure();
        this.bindEvents();
        this.setupIntersectionObserver();
    }

    createStructure() {
        this.container.innerHTML = `
            <div class="enhanced-results-container">
                <div class="enhanced-results-header">
                    <div class="results-title">
                        <i class="fas fa-search"></i>
                        <span class="results-title-text">Search Results</span>
                        <span class="results-count">0</span>
                    </div>
                    <div class="results-actions">
                        <div class="results-view-toggle">
                            <button class="view-toggle-btn ${this.currentView === 'grid' ? 'active' : ''}" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-toggle-btn ${this.currentView === 'list' ? 'active' : ''}" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                            <button class="view-toggle-btn ${this.currentView === 'compact' ? 'active' : ''}" data-view="compact">
                                <i class="fas fa-th-large"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="enhanced-results-grid ${this.currentView}-view" id="resultsGrid">
                    <!-- Results will be populated here -->
                </div>
            </div>
        `;

        this.findElements();
    }

    findElements() {
        this.resultsContainer = this.container.querySelector('.enhanced-results-container');
        this.resultsHeader = this.container.querySelector('.enhanced-results-header');
        this.resultsGrid = this.container.querySelector('.enhanced-results-grid');
        this.viewToggle = this.container.querySelector('.results-view-toggle');
        this.resultsCount = this.container.querySelector('.results-count');
        this.resultsTitleText = this.container.querySelector('.results-title-text');
    }

    bindEvents() {
        // View toggle buttons
        if (this.viewToggle) {
            this.viewToggle.addEventListener('click', (e) => {
                const button = e.target.closest('.view-toggle-btn');
                if (button) {
                    this.changeView(button.dataset.view);
                }
            });
        }

        // Card click events
        this.resultsGrid.addEventListener('click', (e) => {
            const card = e.target.closest('.enhanced-result-card');
            if (card && !e.target.closest('.result-action-btn')) {
                this.handleCardClick(card);
            }
        });

        // Action button events
        this.resultsGrid.addEventListener('click', (e) => {
            const actionBtn = e.target.closest('.result-action-btn');
            if (actionBtn) {
                this.handleActionClick(actionBtn, e);
            }
        });

        // Infinite scroll
        if (this.options.enableInfiniteScroll) {
            window.addEventListener('scroll', this.throttle(() => {
                this.handleScroll();
            }, 100));
        }
    }

    setupIntersectionObserver() {
        if (!this.options.enableLazyLoading) return;

        this.imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        this.imageObserver.unobserve(img);
                    }
                }
            });
        }, {
            rootMargin: '50px'
        });
    }

    changeView(view) {
        if (view === this.currentView) return;

        this.currentView = view;

        // Update toggle buttons
        const buttons = this.viewToggle.querySelectorAll('.view-toggle-btn');
        buttons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        // Update grid class
        this.resultsGrid.className = `enhanced-results-grid ${view}-view`;

        // Trigger view change event
        const viewChangeEvent = new CustomEvent('resultsViewChanged', {
            detail: { view: view }
        });
        this.container.dispatchEvent(viewChangeEvent);

        // Re-render if we have results
        if (this.currentResults.length > 0) {
            this.renderResults(this.currentResults, false);
        }
    }

    displayResults(results, query = '', append = false) {
        this.currentResults = append ? [...this.currentResults, ...results] : results;
        this.totalResults = this.currentResults.length;

        // Update header
        this.updateHeader(query, this.totalResults);

        // Show container
        this.showContainer();

        // Render results
        this.renderResults(this.currentResults, append);
    }

    updateHeader(query, count) {
        if (this.resultsTitleText) {
            this.resultsTitleText.textContent = query ? `Results for "${query}"` : 'Search Results';
        }

        if (this.resultsCount) {
            this.resultsCount.textContent = count;
            
            if (this.options.enableAnimations) {
                this.resultsCount.style.animation = 'countPulse 0.3s ease-out';
            }
        }
    }

    renderResults(results, append = false) {
        if (!append) {
            this.resultsGrid.innerHTML = '';
        }

        if (results.length === 0) {
            this.renderEmptyState();
            return;
        }

        const fragment = document.createDocumentFragment();

        results.forEach((result, index) => {
            const card = this.createResultCard(result, index);
            fragment.appendChild(card);
        });

        this.resultsGrid.appendChild(fragment);

        // Setup lazy loading for images
        if (this.options.enableLazyLoading) {
            const images = this.resultsGrid.querySelectorAll('img[data-src]');
            images.forEach(img => this.imageObserver.observe(img));
        }

        // Animate cards entrance
        if (this.options.enableAnimations && !append) {
            this.animateCardsEntrance();
        }
    }

    createResultCard(result, index) {
        const card = document.createElement('div');
        card.className = 'enhanced-result-card';
        card.dataset.resultId = result.id;
        card.dataset.resultType = result.type;

        card.innerHTML = `
            <div class="result-card-header">
                <div class="result-image-container">
                    ${this.renderResultImage(result)}
                    ${this.renderStatusBadge(result)}
                </div>
                <div class="result-card-content">
                    <h3 class="result-title">${this.escapeHtml(result.title || result.name)}</h3>
                    ${result.description ? `<p class="result-description">${this.escapeHtml(result.description)}</p>` : ''}
                    <div class="result-meta">
                        ${this.renderResultMeta(result)}
                    </div>
                </div>
            </div>
            <div class="result-card-footer">
                <div class="result-actions">
                    ${this.renderResultActions(result)}
                </div>
                <div class="result-timestamp">
                    ${this.formatDate(result.created_at || result.updated_at)}
                </div>
            </div>
        `;

        // Add entrance animation delay
        if (this.options.enableAnimations) {
            card.style.animationDelay = `${index * 50}ms`;
        }

        return card;
    }

    renderResultImage(result) {
        const imageUrl = result.image || result.thumbnail || result.featured_image;
        
        if (imageUrl) {
            const src = this.options.enableLazyLoading ? '' : imageUrl;
            const dataSrc = this.options.enableLazyLoading ? imageUrl : '';
            
            return `<img src="${src}" ${dataSrc ? `data-src="${dataSrc}"` : ''} alt="${this.escapeHtml(result.title || result.name)}" class="result-image" loading="lazy">`;
        } else {
            const initial = (result.title || result.name || '?').charAt(0).toUpperCase();
            return `<div class="result-image-placeholder">${initial}</div>`;
        }
    }

    renderStatusBadge(result) {
        if (!result.status) return '';
        
        const statusClass = result.status === 'active' ? 'active' : 
                           result.status === 'inactive' ? 'inactive' : 
                           result.featured ? 'featured' : '';
        
        return statusClass ? `<div class="result-status-badge ${statusClass}"></div>` : '';
    }

    renderResultMeta(result) {
        const metaItems = [];

        if (result.price) {
            metaItems.push(`
                <div class="result-meta-item price">
                    <i class="fas fa-dollar-sign"></i>
                    <span>$${result.price}</span>
                </div>
            `);
        }

        if (result.category) {
            metaItems.push(`
                <div class="result-meta-item category">
                    <i class="fas fa-tag"></i>
                    <span>${this.escapeHtml(result.category)}</span>
                </div>
            `);
        }

        if (result.rating) {
            metaItems.push(`
                <div class="result-meta-item rating">
                    <i class="fas fa-star"></i>
                    <span>${result.rating} (${result.rating_count || 0})</span>
                </div>
            `);
        }

        if (result.stock !== undefined && result.type === 'product') {
            const stockIcon = result.stock > 10 ? 'fa-check-circle' : 
                             result.stock > 0 ? 'fa-exclamation-triangle' : 'fa-times-circle';
            const stockText = result.stock > 10 ? 'In Stock' : 
                             result.stock > 0 ? 'Low Stock' : 'Out of Stock';
            
            metaItems.push(`
                <div class="result-meta-item stock">
                    <i class="fas ${stockIcon}"></i>
                    <span>${stockText}</span>
                </div>
            `);
        }

        return metaItems.join('');
    }

    renderResultActions(result) {
        const actions = [];

        // View/Edit action
        actions.push(`
            <button class="result-action-btn primary" data-action="view" data-id="${result.id}">
                <i class="fas fa-eye"></i>
                <span>View</span>
            </button>
        `);

        // Edit action
        actions.push(`
            <button class="result-action-btn secondary" data-action="edit" data-id="${result.id}">
                <i class="fas fa-edit"></i>
                <span>Edit</span>
            </button>
        `);

        return actions.join('');
    }

    renderEmptyState() {
        this.resultsGrid.innerHTML = `
            <div class="enhanced-results-empty">
                <div class="empty-state-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="empty-state-title">No results found</h3>
                <p class="empty-state-description">
                    Try adjusting your search terms or filters to find what you're looking for.
                </p>
                <div class="empty-state-actions">
                    <button class="result-action-btn primary" onclick="document.querySelector('.enhanced-search-input').focus()">
                        <i class="fas fa-search"></i>
                        <span>Try Another Search</span>
                    </button>
                </div>
            </div>
        `;
    }

    showLoading() {
        this.isLoading = true;
        
        if (this.currentResults.length === 0) {
            // Show skeleton loading
            this.renderSkeletonLoading();
        }
        
        this.showContainer();
    }

    hideLoading() {
        this.isLoading = false;
    }

    renderSkeletonLoading() {
        const skeletonCount = this.currentView === 'list' ? 6 : 8;
        const skeletons = Array.from({ length: skeletonCount }, () => `
            <div class="result-skeleton">
                <div class="skeleton-header">
                    <div class="skeleton-image"></div>
                    <div class="skeleton-content">
                        <div class="skeleton-line"></div>
                        <div class="skeleton-line medium"></div>
                        <div class="skeleton-line short"></div>
                    </div>
                </div>
            </div>
        `).join('');

        this.resultsGrid.innerHTML = skeletons;
    }

    showContainer() {
        if (this.resultsContainer) {
            this.resultsContainer.classList.add('visible');
        }
    }

    hideContainer() {
        if (this.resultsContainer) {
            this.resultsContainer.classList.remove('visible');
        }
    }

    animateCardsEntrance() {
        const cards = this.resultsGrid.querySelectorAll('.enhanced-result-card');
        
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }

    handleCardClick(card) {
        const resultId = card.dataset.resultId;
        const resultType = card.dataset.resultType;
        
        // Trigger card click event
        const cardClickEvent = new CustomEvent('resultCardClick', {
            detail: { 
                id: resultId, 
                type: resultType,
                element: card
            }
        });
        this.container.dispatchEvent(cardClickEvent);
    }

    handleActionClick(button, event) {
        event.stopPropagation();
        
        const action = button.dataset.action;
        const id = button.dataset.id;
        
        // Trigger action event
        const actionEvent = new CustomEvent('resultActionClick', {
            detail: { 
                action: action, 
                id: id,
                button: button
            }
        });
        this.container.dispatchEvent(actionEvent);
    }

    handleScroll() {
        if (!this.options.enableInfiniteScroll || this.isLoading) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        if (scrollTop + windowHeight >= documentHeight - 1000) {
            this.loadMoreResults();
        }
    }

    loadMoreResults() {
        if (this.isLoading) return;

        this.currentPage++;
        
        // Trigger load more event
        const loadMoreEvent = new CustomEvent('loadMoreResults', {
            detail: { 
                page: this.currentPage,
                currentCount: this.currentResults.length
            }
        });
        this.container.dispatchEvent(loadMoreEvent);
    }

    clear() {
        this.currentResults = [];
        this.totalResults = 0;
        this.currentPage = 1;
        this.resultsGrid.innerHTML = '';
        this.hideContainer();
    }

    // Utility methods
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        const minutes = Math.floor(diff / (1000 * 60));
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        
        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        if (hours < 24) return `${hours}h ago`;
        if (days < 7) return `${days}d ago`;
        
        return date.toLocaleDateString();
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }

    destroy() {
        if (this.imageObserver) {
            this.imageObserver.disconnect();
        }
        
        window.removeEventListener('scroll', this.handleScroll);
    }
}

// Auto-initialize enhanced results display
document.addEventListener('DOMContentLoaded', function() {
    const resultsContainers = document.querySelectorAll('#searchResults');
    
    resultsContainers.forEach(container => {
        new EnhancedResultsDisplay(container, {
            enableAnimations: true,
            enableLazyLoading: true,
            defaultView: 'grid'
        });
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = EnhancedResultsDisplay;
}
