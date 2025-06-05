/**
 * Modern Forms JavaScript
 * Enhances the functionality of modern form elements
 */

document.addEventListener('DOMContentLoaded', function() {
    // Apply modern styling to all textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        // Add modern-textarea class to all textareas
        textarea.classList.add('modern-textarea');

        // Add auto-resize functionality
        textarea.addEventListener('input', autoResizeTextarea);

        // Initialize auto-resize
        autoResizeTextarea.call(textarea);

        // Add character counter if maxlength is set
        if (textarea.hasAttribute('maxlength')) {
            addCharacterCounter(textarea);
        }

        // Add focus animation
        textarea.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        textarea.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });

    // Apply modern styling to all text inputs
    const textInputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="search"], input[type="tel"], input[type="url"], input[type="date"], input[type="datetime-local"], input[type="month"], input[type="week"], input[type="time"], input[type="color"]');
    textInputs.forEach(input => {
        // Add modern-input class to all text inputs
        input.classList.add('modern-input');

        // Ensure the input is wrapped in a div for focus effects
        if (!input.parentElement.classList.contains('input-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'input-wrapper relative';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
        }

        // Add focus and blur event listeners
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });

        // Add character counter if maxlength is set
        if (input.hasAttribute('maxlength')) {
            addCharacterCounter(input);
        }
    });

    // Add modern-label class to all labels that are associated with form elements
    const formLabels = document.querySelectorAll('label[for]');
    formLabels.forEach(label => {
        const forAttribute = label.getAttribute('for');
        const associatedElement = document.getElementById(forAttribute);

        if (associatedElement) {
            if (associatedElement.tagName === 'TEXTAREA' ||
                (associatedElement.tagName === 'INPUT' &&
                 ['text', 'email', 'password', 'number', 'search', 'tel', 'url', 'date', 'datetime-local', 'month', 'week', 'time', 'color'].includes(associatedElement.type))) {
                label.classList.add('modern-label');
            }
        }
    });
});

/**
 * Auto-resize textarea based on content
 */
function autoResizeTextarea() {
    // Reset height to auto to get the correct scrollHeight
    this.style.height = 'auto';

    // Set the height to scrollHeight + border width
    const borderHeight = this.offsetHeight - this.clientHeight;
    this.style.height = (this.scrollHeight + borderHeight) + 'px';
}

/**
 * Add character counter to form element
 * @param {HTMLElement} element - The form element (textarea or input)
 */
function addCharacterCounter(element) {
    const maxLength = parseInt(element.getAttribute('maxlength'));

    // Create counter element
    const counter = document.createElement('div');
    counter.className = 'char-counter';
    updateCharCounter(element, counter, maxLength);

    // Insert counter after element
    element.parentNode.insertBefore(counter, element.nextSibling);

    // Update counter on input
    element.addEventListener('input', function() {
        updateCharCounter(this, counter, maxLength);
    });
}

/**
 * Update character counter
 * @param {HTMLElement} element - The form element (textarea or input)
 * @param {HTMLElement} counter - The counter element
 * @param {number} maxLength - Maximum length allowed
 */
function updateCharCounter(element, counter, maxLength) {
    const currentLength = element.value.length;
    const remaining = maxLength - currentLength;

    // Different text for textarea vs input
    const counterText = element.tagName === 'TEXTAREA'
        ? `${currentLength}/${maxLength} characters`
        : `${currentLength}/${maxLength}`;

    counter.textContent = counterText;

    // Add warning classes when approaching limit
    counter.classList.remove('limit-near', 'limit-reached');

    if (remaining <= 0) {
        counter.classList.add('limit-reached');
    } else if (remaining <= maxLength * 0.1) { // Warning at 10% remaining
        counter.classList.add('limit-near');
    }
}
