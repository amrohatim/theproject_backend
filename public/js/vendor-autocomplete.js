/**
 * Vendor Dashboard Auto-completion Component
 * Provides search suggestions for vendor dashboard search fields
 */
class VendorAutoComplete {
    constructor(inputElement, options = {}) {
        console.log('VendorAutoComplete constructor called', inputElement, options);
        this.input = inputElement;
        this.options = {
            apiUrl: options.apiUrl || '',
            minLength: options.minLength || 2,
            debounceDelay: options.debounceDelay || 300,
            maxResults: options.maxResults || 10,
            placeholder: options.placeholder || 'Search...',
            onSelect: options.onSelect || null,
            ...options
        };

        this.cache = new Map();
        this.debounceTimer = null;
        this.isVisible = false;
        this.selectedIndex = -1;
        this.suggestions = [];

        this.init();
    }

    init() {
        console.log('VendorAutoComplete init called');
        this.createDropdown();
        this.bindEvents();
        this.input.setAttribute('autocomplete', 'off');
        this.input.setAttribute('role', 'combobox');
        this.input.setAttribute('aria-expanded', 'false');
        this.input.setAttribute('aria-haspopup', 'listbox');
        console.log('VendorAutoComplete initialization complete');
    }

    createDropdown() {
        this.dropdown = document.createElement('div');
        this.dropdown.className = 'vendor-autocomplete-dropdown';
        this.dropdown.setAttribute('role', 'listbox');
        this.dropdown.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--bs-dark, #212529);
            border: 1px solid var(--bs-border-color, #495057);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
        `;

        // Create loading indicator
        this.loadingIndicator = document.createElement('div');
        this.loadingIndicator.className = 'vendor-autocomplete-loading';
        this.loadingIndicator.innerHTML = `
            <div class="d-flex align-items-center justify-content-center p-3">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="text-muted">Searching...</span>
            </div>
        `;
        this.loadingIndicator.style.display = 'none';

        // Position dropdown relative to input
        const inputRect = this.input.getBoundingClientRect();
        const inputParent = this.input.parentElement;
        
        // Make sure parent has relative positioning
        if (getComputedStyle(inputParent).position === 'static') {
            inputParent.style.position = 'relative';
        }

        inputParent.appendChild(this.dropdown);
        this.dropdown.appendChild(this.loadingIndicator);
    }

    bindEvents() {
        // Input events
        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
        this.input.addEventListener('focus', (e) => this.handleFocus(e));
        this.input.addEventListener('blur', (e) => this.handleBlur(e));

        // Dropdown events
        this.dropdown.addEventListener('mousedown', (e) => e.preventDefault());
        this.dropdown.addEventListener('click', (e) => this.handleDropdownClick(e));

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.hideDropdown();
            }
        });
    }

    handleInput(e) {
        const query = e.target.value.trim();
        console.log('handleInput called with query:', query);

        if (query.length < this.options.minLength) {
            console.log('Query too short, hiding dropdown');
            this.hideDropdown();
            return;
        }

        // Clear previous timer
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        console.log('Setting debounce timer for search');
        // Debounce the search
        this.debounceTimer = setTimeout(() => {
            console.log('Debounce timer fired, calling search');
            this.search(query);
        }, this.options.debounceDelay);
    }

    handleKeydown(e) {
        if (!this.isVisible) return;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.selectNext();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.selectPrevious();
                break;
            case 'Enter':
                e.preventDefault();
                if (this.selectedIndex >= 0) {
                    this.selectSuggestion(this.suggestions[this.selectedIndex]);
                }
                break;
            case 'Escape':
                e.preventDefault();
                this.hideDropdown();
                this.input.blur();
                break;
        }
    }

    handleFocus(e) {
        const query = e.target.value.trim();
        if (query.length >= this.options.minLength) {
            this.search(query);
        }
    }

    handleBlur(e) {
        // Delay hiding to allow for dropdown clicks
        setTimeout(() => {
            if (!this.dropdown.matches(':hover')) {
                this.hideDropdown();
            }
        }, 150);
    }

    handleDropdownClick(e) {
        const item = e.target.closest('.vendor-autocomplete-item');
        if (item) {
            const index = parseInt(item.dataset.index);
            this.selectSuggestion(this.suggestions[index]);
        }
    }

    async search(query) {
        console.log('search method called with query:', query);
        console.log('API URL:', this.options.apiUrl);

        // Check cache first
        if (this.cache.has(query)) {
            console.log('Found cached results for query:', query);
            this.displaySuggestions(this.cache.get(query), query);
            return;
        }

        console.log('Showing loading indicator');
        this.showLoading();

        try {
            const url = `${this.options.apiUrl}?q=${encodeURIComponent(query)}`;
            console.log('Making fetch request to:', url);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const suggestions = await response.json();
            console.log('Received suggestions:', suggestions);

            // Cache the results
            this.cache.set(query, suggestions);

            this.displaySuggestions(suggestions, query);
        } catch (error) {
            console.error('Auto-complete search error:', error);
            this.hideLoading();
            this.showError('Search failed. Please try again.');
        }
    }

    showLoading() {
        this.hideDropdown();
        this.loadingIndicator.style.display = 'block';
        this.dropdown.style.display = 'block';
        this.isVisible = true;
        this.input.setAttribute('aria-expanded', 'true');
    }

    hideLoading() {
        this.loadingIndicator.style.display = 'none';
    }

    displaySuggestions(suggestions, query) {
        console.log('displaySuggestions called with:', suggestions.length, 'suggestions');
        this.hideLoading();
        this.suggestions = suggestions;
        this.selectedIndex = -1;

        if (suggestions.length === 0) {
            console.log('No suggestions, showing no results');
            this.showNoResults();
            return;
        }

        console.log('Building HTML for suggestions');
        const html = suggestions.map((suggestion, index) => `
            <div class="vendor-autocomplete-item" data-index="${index}" role="option" style="cursor: pointer; border-bottom: 1px solid #374151; transition: background-color 0.15s ease-in-out;">
                <div style="display: flex; align-items: center; padding: 12px;">
                    <i class="${suggestion.icon}" style="margin-right: 12px; color: #6366f1;"></i>
                    <div style="flex-grow: 1;">
                        <div style="font-weight: 500; color: #f9fafb;">${suggestion.highlight || suggestion.text}</div>
                        ${suggestion.subtitle ? `<small style="color: #9ca3af;">${suggestion.subtitle}</small>` : ''}
                    </div>
                    <small style="background-color: #374151; color: #d1d5db; padding: 2px 8px; border-radius: 4px; margin-left: 8px;">${suggestion.type}</small>
                </div>
            </div>
        `).join('');

        console.log('Setting dropdown innerHTML and showing dropdown');
        this.dropdown.innerHTML = html;
        this.showDropdown();
    }

    showNoResults() {
        this.dropdown.innerHTML = `
            <div class="vendor-autocomplete-no-results" style="padding: 12px; text-align: center; color: #9ca3af;">
                <i class="fas fa-search" style="margin-right: 8px;"></i>
                No results found
            </div>
        `;
        this.showDropdown();
    }

    showError(message) {
        this.dropdown.innerHTML = `
            <div class="vendor-autocomplete-error" style="padding: 12px; text-align: center; color: #ef4444;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                ${message}
            </div>
        `;
        this.showDropdown();
    }

    showDropdown() {
        console.log('showDropdown called');
        console.log('Dropdown element:', this.dropdown);
        console.log('Dropdown parent:', this.dropdown.parentElement);
        this.dropdown.style.display = 'block';
        this.isVisible = true;
        this.input.setAttribute('aria-expanded', 'true');
        console.log('Dropdown should now be visible');
    }

    hideDropdown() {
        this.dropdown.style.display = 'none';
        this.isVisible = false;
        this.selectedIndex = -1;
        this.input.setAttribute('aria-expanded', 'false');
        this.updateSelection();
    }

    selectNext() {
        if (this.selectedIndex < this.suggestions.length - 1) {
            this.selectedIndex++;
            this.updateSelection();
        }
    }

    selectPrevious() {
        if (this.selectedIndex > 0) {
            this.selectedIndex--;
            this.updateSelection();
        }
    }

    updateSelection() {
        const items = this.dropdown.querySelectorAll('.vendor-autocomplete-item');
        items.forEach((item, index) => {
            if (index === this.selectedIndex) {
                item.style.backgroundColor = '#6366f1';
                item.setAttribute('aria-selected', 'true');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.style.backgroundColor = 'transparent';
                item.setAttribute('aria-selected', 'false');
            }
        });
    }

    selectSuggestion(suggestion) {
        this.input.value = suggestion.text;
        this.hideDropdown();

        if (this.options.onSelect) {
            this.options.onSelect(suggestion);
        }

        // Trigger form submission if configured
        if (this.options.submitOnSelect) {
            const form = this.input.closest('form');
            if (form) {
                form.submit();
            }
        }
    }

    destroy() {
        if (this.debounceTimer) {
            clearTimeout(this.debounceTimer);
        }

        if (this.dropdown && this.dropdown.parentElement) {
            this.dropdown.parentElement.removeChild(this.dropdown);
        }

        this.cache.clear();
    }
}

// Add CSS for spinner animation
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .vendor-autocomplete-item:hover {
        background-color: #6366f1 !important;
    }

    .vendor-autocomplete-item mark {
        background-color: #fbbf24;
        color: #1f2937;
        padding: 0.1em 0.2em;
        border-radius: 0.2em;
    }
`;
document.head.appendChild(style);

// CSS Styles for auto-completion
const autoCompleteStyles = `
    .vendor-autocomplete-item {
        cursor: pointer;
        border-bottom: 1px solid var(--bs-border-color, #495057);
        transition: background-color 0.15s ease-in-out;
    }

    .vendor-autocomplete-item:last-child {
        border-bottom: none;
    }

    .vendor-autocomplete-item:hover,
    .vendor-autocomplete-item.selected {
        background-color: var(--bs-primary, #0d6efd);
    }

    .vendor-autocomplete-item:hover .text-light,
    .vendor-autocomplete-item.selected .text-light {
        color: white !important;
    }

    .vendor-autocomplete-item mark {
        background-color: var(--bs-warning, #ffc107);
        color: var(--bs-dark, #212529);
        padding: 0.1em 0.2em;
        border-radius: 0.2em;
    }
`;

// Inject styles
if (!document.getElementById('vendor-autocomplete-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'vendor-autocomplete-styles';
    styleSheet.textContent = autoCompleteStyles;
    document.head.appendChild(styleSheet);
}

// Export for use
window.VendorAutoComplete = VendorAutoComplete;
