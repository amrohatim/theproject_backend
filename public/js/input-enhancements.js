/**
 * Input Enhancements
 * Adds advanced features to input fields
 */

document.addEventListener('DOMContentLoaded', function() {
    // Apply enhancements to all text inputs
    const textInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="search"], input[type="tel"], input[type="url"], input[type="date"], input[type="datetime-local"], input[type="month"], input[type="week"], input[type="time"], input[type="color"]');

    textInputs.forEach(input => {
        // Add focus animation
        addFocusAnimation(input);

        // Add placeholder animation - DISABLED FOR STATIC PLACEHOLDERS
        // addPlaceholderAnimation(input);

        // Add character counter if maxlength is set
        if (input.hasAttribute('maxlength')) {
            addCharacterCounter(input);
        }

        // Add input validation visual feedback
        addValidationFeedback(input);
    });

    // Add modern-label class to all labels that are associated with inputs
    const inputLabels = document.querySelectorAll('label[for]');
    inputLabels.forEach(label => {
        const forAttribute = label.getAttribute('for');
        const associatedInput = document.getElementById(forAttribute);

        if (associatedInput && isTextInput(associatedInput)) {
            label.classList.add('modern-label');
        }
    });
});

/**
 * Check if an element is a text input
 * @param {HTMLElement} element - The element to check
 * @returns {boolean} - True if the element is a text input
 */
function isTextInput(element) {
    if (!element || !element.tagName) return false;

    const inputTypes = ['text', 'email', 'password', 'number', 'search', 'tel', 'url', 'date', 'datetime-local', 'month', 'week', 'time', 'color'];
    return element.tagName === 'INPUT' && inputTypes.includes(element.type);
}

/**
 * Add focus animation to input
 * @param {HTMLInputElement} input - The input element
 */
function addFocusAnimation(input) {
    // Create a wrapper if it doesn't exist
    if (!input.parentElement.classList.contains('input-wrapper')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'input-wrapper relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
    }

    // Add focus and blur event listeners
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');

        // Add a subtle pulse animation
        this.classList.add('pulse-focus');
        setTimeout(() => {
            this.classList.remove('pulse-focus');
        }, 500);
    });

    input.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });

    // Add hover effect
    input.addEventListener('mouseenter', function() {
        if (!this.parentElement.classList.contains('focused')) {
            this.parentElement.classList.add('hovered');
        }
    });

    input.addEventListener('mouseleave', function() {
        this.parentElement.classList.remove('hovered');
    });
}

/**
 * Add placeholder animation to input - DISABLED FOR STATIC PLACEHOLDERS
 * @param {HTMLInputElement} input - The input element
 */
/*
function addPlaceholderAnimation(input) {
    // Skip if the input already has a visible label or is in a special container
    if (input.parentElement.querySelector('label:not(.sr-only)') ||
        input.closest('.input-group') ||
        input.closest('.form-group') ||
        !input.hasAttribute('placeholder')) {
        return;
    }

    const placeholder = input.getAttribute('placeholder');

    // Create floating label
    const label = document.createElement('label');
    label.className = 'floating-label absolute left-3 top-2 text-gray-500 dark:text-gray-400 transition-all duration-300 pointer-events-none text-sm';
    label.textContent = placeholder;

    // Add label to wrapper
    input.parentElement.appendChild(label);

    // Remove original placeholder
    input.removeAttribute('placeholder');

    // Add event listeners
    input.addEventListener('focus', function() {
        label.classList.add('active');
        label.style.transform = 'translateY(-18px) scale(0.85)'; // Adjusted for more reasonable height
        label.style.color = 'rgb(59, 130, 246)'; // Blue color
    });

    input.addEventListener('blur', function() {
        if (this.value === '') {
            label.classList.remove('active');
            label.style.transform = '';
            label.style.color = '';
        }
    });

    // Set initial state
    if (input.value !== '') {
        label.classList.add('active');
        label.style.transform = 'translateY(-18px) scale(0.85)'; // Adjusted for more reasonable height
        label.style.color = 'rgb(59, 130, 246)'; // Blue color
    }
}
*/

/**
 * Add character counter to input
 * @param {HTMLInputElement} input - The input element
 */
function addCharacterCounter(input) {
    const maxLength = parseInt(input.getAttribute('maxlength'));

    // Create counter element
    const counter = document.createElement('div');
    counter.className = 'char-counter text-xs text-gray-500 dark:text-gray-400 mt-1 text-right transition-colors duration-200';
    updateCharCounter(input, counter, maxLength);

    // Insert counter after input
    input.parentNode.insertBefore(counter, input.nextSibling);

    // Update counter on input
    input.addEventListener('input', function() {
        updateCharCounter(this, counter, maxLength);
    });
}

/**
 * Update character counter
 * @param {HTMLInputElement} input - The input element
 * @param {HTMLElement} counter - The counter element
 * @param {number} maxLength - Maximum length allowed
 */
function updateCharCounter(input, counter, maxLength) {
    const currentLength = input.value.length;
    const remaining = maxLength - currentLength;

    counter.textContent = `${currentLength}/${maxLength}`;

    // Add warning classes when approaching limit
    counter.classList.remove('text-yellow-600', 'text-red-600', 'dark:text-yellow-400', 'dark:text-red-400');

    if (remaining <= 0) {
        counter.classList.add('text-red-600', 'dark:text-red-400');
    } else if (remaining <= maxLength * 0.1) { // Warning at 10% remaining
        counter.classList.add('text-yellow-600', 'dark:text-yellow-400');
    }
}

/**
 * Add validation feedback to input
 * @param {HTMLInputElement} input - The input element
 */
function addValidationFeedback(input) {
    // Skip if the input is not required or doesn't have validation attributes
    if (!input.hasAttribute('required') &&
        !input.hasAttribute('pattern') &&
        !input.hasAttribute('minlength') &&
        !input.hasAttribute('maxlength') &&
        input.type !== 'email' &&
        input.type !== 'url' &&
        input.type !== 'number') {
        return;
    }

    // Create validation icon container
    const iconContainer = document.createElement('div');
    iconContainer.className = 'validation-icon absolute right-3 top-1/2 transform -translate-y-1/2 hidden';

    // Create success icon
    const successIcon = document.createElement('div');
    successIcon.className = 'success-icon text-green-500 dark:text-green-400 hidden';
    successIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';

    // Create error icon
    const errorIcon = document.createElement('div');
    errorIcon.className = 'error-icon text-red-500 dark:text-red-400 hidden';
    errorIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>';

    // Add icons to container
    iconContainer.appendChild(successIcon);
    iconContainer.appendChild(errorIcon);

    // Add container to input wrapper
    input.parentElement.appendChild(iconContainer);

    // Add validation event listeners
    input.addEventListener('blur', function() {
        validateInput(this, iconContainer, successIcon, errorIcon);
    });

    input.addEventListener('input', function() {
        if (this.classList.contains('error') || successIcon.classList.contains('block')) {
            validateInput(this, iconContainer, successIcon, errorIcon);
        }
    });
}

/**
 * Validate input and show appropriate feedback
 * @param {HTMLInputElement} input - The input element
 * @param {HTMLElement} iconContainer - The icon container
 * @param {HTMLElement} successIcon - The success icon
 * @param {HTMLElement} errorIcon - The error icon
 */
function validateInput(input, iconContainer, successIcon, errorIcon) {
    // Skip validation if input is empty and not required
    if (input.value === '' && !input.hasAttribute('required')) {
        iconContainer.classList.add('hidden');
        successIcon.classList.add('hidden');
        errorIcon.classList.add('hidden');
        input.classList.remove('error', 'success');
        return;
    }

    const isValid = input.checkValidity();

    iconContainer.classList.remove('hidden');
    successIcon.classList.toggle('hidden', !isValid);
    errorIcon.classList.toggle('hidden', isValid);

    input.classList.toggle('error', !isValid);
    input.classList.toggle('success', isValid);
}
