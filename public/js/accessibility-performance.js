/**
 * Accessibility and Performance Enhancement Manager
 * WCAG 2.1 AA compliant features and performance optimizations
 */

class AccessibilityPerformanceManager {
    constructor(options = {}) {
        this.options = {
            enableKeyboardNavigation: true,
            enableScreenReaderSupport: true,
            enablePerformanceOptimizations: true,
            enableReducedMotion: true,
            announceChanges: true,
            ...options
        };

        this.keyboardNavigationActive = false;
        this.reducedMotionPreferred = false;
        this.highContrastMode = false;
        this.liveRegion = null;
        this.performanceObserver = null;
        this.intersectionObserver = null;

        this.init();
    }

    init() {
        this.detectAccessibilityPreferences();
        this.setupKeyboardNavigation();
        this.setupScreenReaderSupport();
        this.setupPerformanceOptimizations();
        this.setupFocusManagement();
        this.bindEvents();
    }

    detectAccessibilityPreferences() {
        // Detect reduced motion preference
        this.reducedMotionPreferred = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        // Detect high contrast preference
        this.highContrastMode = window.matchMedia('(prefers-contrast: high)').matches;
        
        // Listen for changes
        window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
            this.reducedMotionPreferred = e.matches;
            this.updateMotionPreferences();
        });

        window.matchMedia('(prefers-contrast: high)').addEventListener('change', (e) => {
            this.highContrastMode = e.matches;
            this.updateContrastMode();
        });

        this.updateMotionPreferences();
        this.updateContrastMode();
    }

    updateMotionPreferences() {
        document.documentElement.classList.toggle('reduced-motion', this.reducedMotionPreferred);
        
        if (this.reducedMotionPreferred) {
            this.disableAnimations();
        } else {
            this.enableAnimations();
        }
    }

    updateContrastMode() {
        document.documentElement.classList.toggle('high-contrast', this.highContrastMode);
    }

    setupKeyboardNavigation() {
        if (!this.options.enableKeyboardNavigation) return;

        // Detect keyboard usage
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                this.keyboardNavigationActive = true;
                document.body.classList.add('keyboard-navigation-active');
            }
        });

        document.addEventListener('mousedown', () => {
            this.keyboardNavigationActive = false;
            document.body.classList.remove('keyboard-navigation-active');
        });

        // Enhanced keyboard navigation for search suggestions
        this.setupSearchKeyboardNavigation();
        
        // Enhanced keyboard navigation for filters
        this.setupFilterKeyboardNavigation();
        
        // Enhanced keyboard navigation for results
        this.setupResultsKeyboardNavigation();
    }

    setupSearchKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            const searchInput = document.querySelector('.enhanced-search-input:focus');
            if (!searchInput) return;

            const suggestions = document.querySelector('.enhanced-search-suggestions.visible');
            if (!suggestions) return;

            const suggestionItems = suggestions.querySelectorAll('.enhanced-suggestion-item, .recent-search-item');
            const currentFocus = suggestions.querySelector('.suggestion-focused');
            let currentIndex = currentFocus ? Array.from(suggestionItems).indexOf(currentFocus) : -1;

            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    currentIndex = Math.min(currentIndex + 1, suggestionItems.length - 1);
                    this.focusSuggestion(suggestionItems, currentIndex);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    currentIndex = Math.max(currentIndex - 1, -1);
                    if (currentIndex === -1) {
                        searchInput.focus();
                        this.clearSuggestionFocus(suggestionItems);
                    } else {
                        this.focusSuggestion(suggestionItems, currentIndex);
                    }
                    break;
                case 'Enter':
                    if (currentFocus) {
                        e.preventDefault();
                        currentFocus.click();
                    }
                    break;
                case 'Escape':
                    this.hideSuggestions();
                    break;
            }
        });
    }

    focusSuggestion(items, index) {
        this.clearSuggestionFocus(items);
        if (items[index]) {
            items[index].classList.add('suggestion-focused');
            items[index].setAttribute('aria-selected', 'true');
            items[index].scrollIntoView({ block: 'nearest' });
            
            // Announce to screen readers
            this.announceToScreenReader(`Suggestion ${index + 1} of ${items.length}: ${items[index].textContent.trim()}`);
        }
    }

    clearSuggestionFocus(items) {
        items.forEach(item => {
            item.classList.remove('suggestion-focused');
            item.removeAttribute('aria-selected');
        });
    }

    setupFilterKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            const filterPanel = document.querySelector('.enhanced-filter-panel.visible');
            if (!filterPanel) return;

            if (e.key === 'Escape') {
                const filterToggle = document.querySelector('.enhanced-filter-toggle');
                if (filterToggle) {
                    filterToggle.click();
                    filterToggle.focus();
                }
            }
        });
    }

    setupResultsKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.target.closest('.enhanced-results-grid')) {
                const cards = document.querySelectorAll('.enhanced-result-card');
                const currentCard = document.activeElement.closest('.enhanced-result-card');
                
                if (!currentCard) return;
                
                const currentIndex = Array.from(cards).indexOf(currentCard);
                let newIndex = currentIndex;

                switch (e.key) {
                    case 'ArrowRight':
                        e.preventDefault();
                        newIndex = Math.min(currentIndex + 1, cards.length - 1);
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        newIndex = Math.max(currentIndex - 1, 0);
                        break;
                    case 'ArrowDown':
                        e.preventDefault();
                        // Move to next row (approximate)
                        newIndex = Math.min(currentIndex + 3, cards.length - 1);
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        // Move to previous row (approximate)
                        newIndex = Math.max(currentIndex - 3, 0);
                        break;
                }

                if (newIndex !== currentIndex && cards[newIndex]) {
                    cards[newIndex].focus();
                }
            }
        });
    }

    setupScreenReaderSupport() {
        if (!this.options.enableScreenReaderSupport) return;

        // Create live region for announcements
        this.createLiveRegion();
        
        // Setup ARIA labels and descriptions
        this.setupAriaLabels();
        
        // Setup role attributes
        this.setupRoleAttributes();
        
        // Setup state announcements
        this.setupStateAnnouncements();
    }

    createLiveRegion() {
        this.liveRegion = document.createElement('div');
        this.liveRegion.setAttribute('aria-live', 'polite');
        this.liveRegion.setAttribute('aria-atomic', 'true');
        this.liveRegion.className = 'live-region sr-only';
        document.body.appendChild(this.liveRegion);

        // Create assertive live region for urgent announcements
        this.assertiveLiveRegion = document.createElement('div');
        this.assertiveLiveRegion.setAttribute('aria-live', 'assertive');
        this.assertiveLiveRegion.setAttribute('aria-atomic', 'true');
        this.assertiveLiveRegion.className = 'live-region sr-only';
        document.body.appendChild(this.assertiveLiveRegion);
    }

    setupAriaLabels() {
        // Search input
        const searchInput = document.querySelector('.enhanced-search-input');
        if (searchInput) {
            searchInput.setAttribute('aria-label', 'Search products and services');
            searchInput.setAttribute('aria-describedby', 'search-help');
            
            // Add search help text
            const helpText = document.createElement('div');
            helpText.id = 'search-help';
            helpText.className = 'sr-only';
            helpText.textContent = 'Type to search. Use arrow keys to navigate suggestions. Press Enter to select.';
            searchInput.parentNode.appendChild(helpText);
        }

        // Filter toggle
        const filterToggle = document.querySelector('.enhanced-filter-toggle');
        if (filterToggle) {
            filterToggle.setAttribute('aria-label', 'Open filters panel');
            filterToggle.setAttribute('aria-expanded', 'false');
        }

        // Results grid
        const resultsGrid = document.querySelector('.enhanced-results-grid');
        if (resultsGrid) {
            resultsGrid.setAttribute('role', 'grid');
            resultsGrid.setAttribute('aria-label', 'Search results');
        }
    }

    setupRoleAttributes() {
        // Search suggestions
        const suggestions = document.querySelector('.enhanced-search-suggestions');
        if (suggestions) {
            suggestions.setAttribute('role', 'listbox');
            suggestions.setAttribute('aria-label', 'Search suggestions');
        }

        // Filter panel
        const filterPanel = document.querySelector('.enhanced-filter-panel');
        if (filterPanel) {
            filterPanel.setAttribute('role', 'dialog');
            filterPanel.setAttribute('aria-label', 'Filters');
            filterPanel.setAttribute('aria-modal', 'false');
        }

        // Result cards
        const resultCards = document.querySelectorAll('.enhanced-result-card');
        resultCards.forEach((card, index) => {
            card.setAttribute('role', 'gridcell');
            card.setAttribute('tabindex', '0');
            card.setAttribute('aria-label', `Result ${index + 1}`);
        });
    }

    setupStateAnnouncements() {
        // Listen for search state changes
        document.addEventListener('enhancedMerchantSearch', (e) => {
            const { query, resultsCount } = e.detail;
            if (resultsCount !== undefined) {
                this.announceToScreenReader(`Search completed. Found ${resultsCount} results for "${query}"`);
            } else {
                this.announceToScreenReader(`Searching for "${query}"`);
            }
        });

        // Listen for filter changes
        document.addEventListener('enhancedFiltersApplied', (e) => {
            const { filterCount } = e.detail;
            this.announceToScreenReader(`Filters applied. ${filterCount} filters active.`);
        });

        // Listen for loading states
        document.addEventListener('searchStateChange', (e) => {
            const { state } = e.detail;
            switch (state) {
                case 'loading':
                    this.announceToScreenReader('Loading search results');
                    break;
                case 'empty':
                    this.announceToScreenReader('No results found');
                    break;
                case 'error':
                    this.announceToScreenReader('An error occurred while searching', true);
                    break;
            }
        });
    }

    announceToScreenReader(message, urgent = false) {
        if (!this.options.announceChanges) return;

        const region = urgent ? this.assertiveLiveRegion : this.liveRegion;
        if (region) {
            region.textContent = message;
            
            // Clear after announcement
            setTimeout(() => {
                region.textContent = '';
            }, 1000);
        }
    }

    setupFocusManagement() {
        // Focus trap for modal dialogs
        this.setupFocusTrap();
        
        // Focus restoration
        this.setupFocusRestoration();
        
        // Skip links
        this.setupSkipLinks();
    }

    setupFocusTrap() {
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab') return;

            const modal = document.querySelector('.enhanced-filter-panel.visible, .enhanced-search-suggestions.visible');
            if (!modal) return;

            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    }

    setupFocusRestoration() {
        let lastFocusedElement = null;

        // Store focus before opening modals
        document.addEventListener('click', (e) => {
            if (e.target.matches('.enhanced-filter-toggle, .enhanced-search-input')) {
                lastFocusedElement = e.target;
            }
        });

        // Restore focus when closing modals
        document.addEventListener('click', (e) => {
            if (e.target.closest('.enhanced-filter-panel, .enhanced-search-suggestions')) return;
            
            const openModal = document.querySelector('.enhanced-filter-panel.visible, .enhanced-search-suggestions.visible');
            if (openModal && lastFocusedElement) {
                setTimeout(() => {
                    lastFocusedElement.focus();
                    lastFocusedElement = null;
                }, 100);
            }
        });
    }

    setupSkipLinks() {
        // Add skip to content link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'skip-to-content';
        skipLink.textContent = 'Skip to main content';
        document.body.insertBefore(skipLink, document.body.firstChild);

        // Ensure main content has ID
        const mainContent = document.querySelector('main, .main-content, #searchResults');
        if (mainContent && !mainContent.id) {
            mainContent.id = 'main-content';
        }
    }

    setupPerformanceOptimizations() {
        if (!this.options.enablePerformanceOptimizations) return;

        this.setupIntersectionObserver();
        this.setupPerformanceObserver();
        this.optimizeAnimations();
        this.setupLazyLoading();
    }

    setupIntersectionObserver() {
        this.intersectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-viewport');
                    
                    // Trigger lazy loading
                    const lazyImages = entry.target.querySelectorAll('img[data-src]');
                    lazyImages.forEach(img => {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    });
                } else {
                    entry.target.classList.remove('in-viewport');
                }
            });
        }, {
            rootMargin: '50px'
        });

        // Observe result cards
        const resultCards = document.querySelectorAll('.enhanced-result-card');
        resultCards.forEach(card => {
            this.intersectionObserver.observe(card);
        });
    }

    setupPerformanceObserver() {
        if ('PerformanceObserver' in window) {
            this.performanceObserver = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (entry.entryType === 'layout-shift' && entry.value > 0.1) {
                        console.warn('Layout shift detected:', entry.value);
                    }
                });
            });

            try {
                this.performanceObserver.observe({ entryTypes: ['layout-shift'] });
            } catch (e) {
                // Layout shift observation not supported
            }
        }
    }

    optimizeAnimations() {
        // Disable animations on low-end devices
        if (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4) {
            document.documentElement.classList.add('low-performance-device');
        }

        // Optimize animations based on battery status
        if ('getBattery' in navigator) {
            navigator.getBattery().then(battery => {
                if (battery.level < 0.2 || !battery.charging) {
                    document.documentElement.classList.add('battery-saver');
                }
            });
        }
    }

    setupLazyLoading() {
        // Use native lazy loading when supported
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[data-src]');
            images.forEach(img => {
                img.loading = 'lazy';
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            });
        }
    }

    disableAnimations() {
        document.documentElement.classList.add('animations-disabled');
    }

    enableAnimations() {
        document.documentElement.classList.remove('animations-disabled');
    }

    hideSuggestions() {
        const suggestions = document.querySelector('.enhanced-search-suggestions');
        if (suggestions) {
            suggestions.classList.remove('visible');
            
            const filterToggle = document.querySelector('.enhanced-filter-toggle');
            if (filterToggle) {
                filterToggle.setAttribute('aria-expanded', 'false');
            }
        }
    }

    // Utility methods for performance monitoring
    measurePerformance(name, fn) {
        const start = performance.now();
        const result = fn();
        const end = performance.now();
        
        if (end - start > 16) { // More than one frame
            console.warn(`Performance warning: ${name} took ${end - start}ms`);
        }
        
        return result;
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    bindEvents() {
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            this.destroy();
        });
    }

    destroy() {
        if (this.intersectionObserver) {
            this.intersectionObserver.disconnect();
        }
        
        if (this.performanceObserver) {
            this.performanceObserver.disconnect();
        }
        
        if (this.liveRegion) {
            this.liveRegion.remove();
        }
        
        if (this.assertiveLiveRegion) {
            this.assertiveLiveRegion.remove();
        }
    }
}

// Auto-initialize accessibility and performance manager
document.addEventListener('DOMContentLoaded', function() {
    window.AccessibilityManager = new AccessibilityPerformanceManager({
        enableKeyboardNavigation: true,
        enableScreenReaderSupport: true,
        enablePerformanceOptimizations: true,
        announceChanges: true
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AccessibilityPerformanceManager;
}
