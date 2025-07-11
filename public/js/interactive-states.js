/**
 * Interactive States Manager
 * Handles loading states, empty states, and error states with smooth transitions
 */

class InteractiveStatesManager {
    constructor(container, options = {}) {
        this.container = container;
        this.options = {
            enableAnimations: true,
            loadingMessages: [
                'Searching through your products...',
                'Finding the best matches...',
                'Almost there...',
                'Preparing results...'
            ],
            loadingTips: [
                'Use specific keywords for better results',
                'Try different filter combinations',
                'Check your spelling and try again'
            ],
            ...options
        };

        this.currentState = 'idle';
        this.loadingMessageIndex = 0;
        this.loadingInterval = null;
        this.progressValue = 0;

        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Listen for state change events
        document.addEventListener('searchStateChange', (e) => {
            this.handleStateChange(e.detail);
        });

        // Listen for progress updates
        document.addEventListener('searchProgress', (e) => {
            this.updateProgress(e.detail.progress);
        });
    }

    handleStateChange(detail) {
        const { state, data } = detail;
        
        switch (state) {
            case 'loading':
                this.showLoadingState(data);
                break;
            case 'empty':
                this.showEmptyState(data);
                break;
            case 'error':
                this.showErrorState(data);
                break;
            case 'success':
                this.showSuccessState(data);
                break;
            case 'idle':
                this.hideAllStates();
                break;
        }
    }

    showLoadingState(data = {}) {
        this.currentState = 'loading';
        const { type = 'search', message, showProgress = false, showTips = true } = data;

        const loadingHTML = this.createLoadingHTML(type, message, showProgress, showTips);
        this.updateContainer(loadingHTML);

        if (showProgress) {
            this.startProgressAnimation();
        }

        this.startLoadingMessageRotation();
    }

    createLoadingHTML(type, customMessage, showProgress, showTips) {
        const message = customMessage || this.getRandomLoadingMessage();
        const tip = showTips ? this.getRandomLoadingTip() : null;

        return `
            <div class="interactive-loading-container" data-animate-entrance="fadeInUp">
                ${this.createLoadingSpinner(type)}
                <div class="loading-message">
                    <h3 class="loading-title">${message}</h3>
                    <p class="loading-subtitle">Please wait while we process your request</p>
                </div>
                ${showProgress ? this.createProgressBar() : ''}
                ${tip ? `
                    <div class="loading-tips">
                        <p class="loading-tip">
                            <i class="fas fa-lightbulb"></i>
                            <span>${tip}</span>
                        </p>
                    </div>
                ` : ''}
            </div>
        `;
    }

    createLoadingSpinner(type) {
        switch (type) {
            case 'skeleton':
                return this.createSkeletonLoader();
            case 'dots':
                return `
                    <div class="dots-loader">
                        <div class="dot"></div>
                        <div class="dot"></div>
                        <div class="dot"></div>
                    </div>
                `;
            case 'spinner':
            default:
                return `
                    <div class="advanced-spinner">
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                        <div class="spinner-ring"></div>
                    </div>
                `;
        }
    }

    createSkeletonLoader() {
        return `
            <div class="skeleton-container">
                ${Array.from({ length: 3 }, () => `
                    <div class="skeleton-item">
                        <div class="skeleton-header">
                            <div class="skeleton-avatar"></div>
                            <div class="skeleton-content">
                                <div class="skeleton-line title"></div>
                                <div class="skeleton-line medium"></div>
                                <div class="skeleton-line short"></div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }

    createProgressBar() {
        return `
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
            </div>
        `;
    }

    showEmptyState(data = {}) {
        this.currentState = 'empty';
        const { 
            type = 'no-results', 
            title, 
            description, 
            actions = [],
            illustration = 'search'
        } = data;

        const emptyHTML = this.createEmptyStateHTML(type, title, description, actions, illustration);
        this.updateContainer(emptyHTML);
    }

    createEmptyStateHTML(type, customTitle, customDescription, actions, illustration) {
        const config = this.getEmptyStateConfig(type);
        const title = customTitle || config.title;
        const description = customDescription || config.description;

        return `
            <div class="empty-state-container" data-animate-entrance="fadeInUp">
                <div class="empty-illustration">
                    <div class="illustration-${illustration}">
                        <i class="${config.icon}"></i>
                    </div>
                </div>
                <h3 class="empty-state-title">${title}</h3>
                <p class="empty-state-description">${description}</p>
                <div class="empty-state-actions">
                    ${this.createActionButtons(actions.length > 0 ? actions : config.actions)}
                </div>
            </div>
        `;
    }

    getEmptyStateConfig(type) {
        const configs = {
            'no-results': {
                title: 'No results found',
                description: 'We couldn\'t find any items matching your search criteria. Try adjusting your filters or search terms.',
                icon: 'fas fa-search',
                actions: [
                    { text: 'Clear Filters', action: 'clearFilters', type: 'primary' },
                    { text: 'Try Different Keywords', action: 'focusSearch', type: 'secondary' }
                ]
            },
            'no-products': {
                title: 'No products yet',
                description: 'You haven\'t added any products to your store yet. Start by adding your first product.',
                icon: 'fas fa-box',
                actions: [
                    { text: 'Add Product', action: 'addProduct', type: 'primary' },
                    { text: 'Import Products', action: 'importProducts', type: 'secondary' }
                ]
            },
            'no-services': {
                title: 'No services available',
                description: 'You haven\'t created any services yet. Add your first service to start offering them to customers.',
                icon: 'fas fa-concierge-bell',
                actions: [
                    { text: 'Add Service', action: 'addService', type: 'primary' },
                    { text: 'Learn More', action: 'learnMore', type: 'secondary' }
                ]
            }
        };

        return configs[type] || configs['no-results'];
    }

    showErrorState(data = {}) {
        this.currentState = 'error';
        const { 
            title = 'Something went wrong',
            description = 'We encountered an error while processing your request. Please try again.',
            actions = [],
            type = 'general'
        } = data;

        const errorHTML = this.createErrorStateHTML(title, description, actions, type);
        this.updateContainer(errorHTML);
    }

    createErrorStateHTML(title, description, actions, type) {
        const defaultActions = [
            { text: 'Try Again', action: 'retry', type: 'primary' },
            { text: 'Go Back', action: 'goBack', type: 'secondary' }
        ];

        return `
            <div class="empty-state-container error-state-container" data-animate-entrance="fadeInUp">
                <div class="empty-illustration">
                    <div class="illustration-error">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <h3 class="empty-state-title error-title">${title}</h3>
                <p class="empty-state-description">${description}</p>
                <div class="empty-state-actions error-actions">
                    ${this.createActionButtons(actions.length > 0 ? actions : defaultActions)}
                </div>
            </div>
        `;
    }

    showSuccessState(data = {}) {
        this.currentState = 'success';
        const { 
            title = 'Success!',
            description = 'Your action was completed successfully.',
            actions = [],
            autoHide = true,
            autoHideDelay = 3000
        } = data;

        const successHTML = this.createSuccessStateHTML(title, description, actions);
        this.updateContainer(successHTML);

        if (autoHide) {
            setTimeout(() => {
                this.hideAllStates();
            }, autoHideDelay);
        }
    }

    createSuccessStateHTML(title, description, actions) {
        return `
            <div class="empty-state-container success-state-container" data-animate-entrance="fadeInUp">
                <div class="empty-illustration">
                    <div class="success-illustration">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
                <h3 class="empty-state-title">${title}</h3>
                <p class="empty-state-description">${description}</p>
                ${actions.length > 0 ? `
                    <div class="empty-state-actions">
                        ${this.createActionButtons(actions)}
                    </div>
                ` : ''}
            </div>
        `;
    }

    createActionButtons(actions) {
        return actions.map(action => `
            <button class="state-action-btn ${action.type || 'primary'}" 
                    data-action="${action.action}" 
                    ${action.href ? `onclick="window.location.href='${action.href}'"` : ''}>
                ${action.icon ? `<i class="${action.icon}"></i>` : ''}
                <span>${action.text}</span>
            </button>
        `).join('');
    }

    updateContainer(html) {
        this.container.innerHTML = html;
        
        // Bind action button events
        this.bindActionButtons();
        
        // Trigger entrance animations if enabled
        if (this.options.enableAnimations) {
            this.triggerEntranceAnimations();
        }
    }

    bindActionButtons() {
        const actionButtons = this.container.querySelectorAll('[data-action]');
        actionButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.target.closest('[data-action]').dataset.action;
                this.handleAction(action, e);
            });
        });
    }

    handleAction(action, event) {
        // Trigger action event
        const actionEvent = new CustomEvent('stateActionClick', {
            detail: { 
                action: action,
                button: event.target,
                currentState: this.currentState
            }
        });
        document.dispatchEvent(actionEvent);

        // Handle common actions
        switch (action) {
            case 'retry':
                this.triggerRetry();
                break;
            case 'clearFilters':
                this.triggerClearFilters();
                break;
            case 'focusSearch':
                this.focusSearchInput();
                break;
            case 'goBack':
                window.history.back();
                break;
        }
    }

    triggerRetry() {
        const retryEvent = new CustomEvent('stateRetry', {
            detail: { previousState: this.currentState }
        });
        document.dispatchEvent(retryEvent);
    }

    triggerClearFilters() {
        const clearEvent = new CustomEvent('stateClearFilters');
        document.dispatchEvent(clearEvent);
    }

    focusSearchInput() {
        const searchInput = document.querySelector('.enhanced-search-input, .merchant-search-input');
        if (searchInput) {
            searchInput.focus();
        }
    }

    triggerEntranceAnimations() {
        const animatedElements = this.container.querySelectorAll('[data-animate-entrance]');
        animatedElements.forEach((element, index) => {
            element.style.animationDelay = `${index * 100}ms`;
        });
    }

    startLoadingMessageRotation() {
        if (this.loadingInterval) {
            clearInterval(this.loadingInterval);
        }

        this.loadingInterval = setInterval(() => {
            const titleElement = this.container.querySelector('.loading-title');
            if (titleElement && this.currentState === 'loading') {
                this.loadingMessageIndex = (this.loadingMessageIndex + 1) % this.options.loadingMessages.length;
                titleElement.textContent = this.options.loadingMessages[this.loadingMessageIndex];
            }
        }, 2000);
    }

    startProgressAnimation() {
        const progressFill = this.container.querySelector('.progress-fill');
        if (!progressFill) return;

        let progress = 0;
        const progressInterval = setInterval(() => {
            if (this.currentState !== 'loading') {
                clearInterval(progressInterval);
                return;
            }

            progress += Math.random() * 15;
            if (progress > 90) progress = 90;

            progressFill.style.width = `${progress}%`;
        }, 500);
    }

    updateProgress(value) {
        const progressFill = this.container.querySelector('.progress-fill');
        if (progressFill) {
            progressFill.style.width = `${Math.min(100, Math.max(0, value))}%`;
        }
    }

    hideAllStates() {
        this.currentState = 'idle';
        
        if (this.loadingInterval) {
            clearInterval(this.loadingInterval);
            this.loadingInterval = null;
        }

        if (this.options.enableAnimations) {
            const currentElement = this.container.firstElementChild;
            if (currentElement) {
                currentElement.style.animation = 'fadeOut 0.3s ease-out forwards';
                setTimeout(() => {
                    this.container.innerHTML = '';
                }, 300);
            } else {
                this.container.innerHTML = '';
            }
        } else {
            this.container.innerHTML = '';
        }
    }

    getRandomLoadingMessage() {
        return this.options.loadingMessages[Math.floor(Math.random() * this.options.loadingMessages.length)];
    }

    getRandomLoadingTip() {
        return this.options.loadingTips[Math.floor(Math.random() * this.options.loadingTips.length)];
    }

    getCurrentState() {
        return this.currentState;
    }

    destroy() {
        if (this.loadingInterval) {
            clearInterval(this.loadingInterval);
        }
    }
}

// Auto-initialize interactive states managers
document.addEventListener('DOMContentLoaded', function() {
    const stateContainers = document.querySelectorAll('#searchResults, .search-results');
    
    stateContainers.forEach(container => {
        new InteractiveStatesManager(container, {
            enableAnimations: true
        });
    });
});

// CSS for fadeOut animation
const fadeOutCSS = `
@keyframes fadeOut {
    from { opacity: 1; transform: translateY(0); }
    to { opacity: 0; transform: translateY(-10px); }
}
`;

// Only add styles if they haven't been added already
if (!document.getElementById('interactive-states-fadeout-styles')) {
    const style = document.createElement('style');
    style.id = 'interactive-states-fadeout-styles';
    style.textContent = fadeOutCSS;
    document.head.appendChild(style);
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = InteractiveStatesManager;
}
