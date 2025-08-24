/**
 * Textarea Enhancements
 * Adds advanced features to textareas
 */

document.addEventListener('DOMContentLoaded', function() {
    // Apply enhancements to all textareas
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(textarea => {
        // Add auto-expanding functionality
        makeExpandable(textarea);
        
        // Add character counter if maxlength is set
        if (textarea.hasAttribute('maxlength')) {
            addCharacterCounter(textarea);
        }
        
        // Add focus animation
        addFocusAnimation(textarea);
        
        // Add placeholder animation
        addPlaceholderAnimation(textarea);
        
        // Add syntax highlighting for code textareas
        if (textarea.classList.contains('code-textarea')) {
            addSyntaxHighlighting(textarea);
        }
    });
});

/**
 * Make textarea auto-expand based on content
 * @param {HTMLTextAreaElement} textarea - The textarea element
 */
function makeExpandable(textarea) {
    // Set initial height
    adjustHeight(textarea);
    
    // Add event listeners
    textarea.addEventListener('input', function() {
        adjustHeight(this);
    });
    
    textarea.addEventListener('focus', function() {
        adjustHeight(this);
    });
}

/**
 * Adjust the height of a textarea based on its content
 * @param {HTMLTextAreaElement} textarea - The textarea element
 */
function adjustHeight(textarea) {
    // Reset height to auto to get the correct scrollHeight
    textarea.style.height = 'auto';
    
    // Set the height to scrollHeight + border width
    const borderHeight = textarea.offsetHeight - textarea.clientHeight;
    textarea.style.height = (textarea.scrollHeight + borderHeight) + 'px';
}

/**
 * Add character counter to textarea
 * @param {HTMLTextAreaElement} textarea - The textarea element
 */
function addCharacterCounter(textarea) {
    const maxLength = parseInt(textarea.getAttribute('maxlength'));
    
    // Create counter element
    const counter = document.createElement('div');
    counter.className = 'char-counter';
    updateCharCounter(textarea, counter, maxLength);
    
    // Insert counter after textarea
    textarea.parentNode.insertBefore(counter, textarea.nextSibling);
    
    // Update counter on input
    textarea.addEventListener('input', function() {
        updateCharCounter(this, counter, maxLength);
    });
}

/**
 * Update character counter
 * @param {HTMLTextAreaElement} textarea - The textarea element
 * @param {HTMLElement} counter - The counter element
 * @param {number} maxLength - Maximum length allowed
 */
function updateCharCounter(textarea, counter, maxLength) {
    const currentLength = textarea.value.length;
    const remaining = maxLength - currentLength;
    
    counter.textContent = `${currentLength}/${maxLength} characters`;
    
    // Add warning classes when approaching limit
    counter.classList.remove('limit-near', 'limit-reached');
    
    if (remaining <= 0) {
        counter.classList.add('limit-reached');
    } else if (remaining <= maxLength * 0.1) { // Warning at 10% remaining
        counter.classList.add('limit-near');
    }
}

/**
 * Add focus animation to textarea
 * @param {HTMLTextAreaElement} textarea - The textarea element
 */
function addFocusAnimation(textarea) {
    // Create a wrapper if it doesn't exist
    if (!textarea.parentElement.classList.contains('textarea-wrapper')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'textarea-wrapper relative';
        textarea.parentNode.insertBefore(wrapper, textarea);
        wrapper.appendChild(textarea);
    }
    
    // Add focus and blur event listeners
    textarea.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    
    textarea.addEventListener('blur', function() {
        this.parentElement.classList.remove('focused');
    });
}

/**
 * Add placeholder animation to textarea
 * @param {HTMLTextAreaElement} textarea - The textarea element
 */
function addPlaceholderAnimation(textarea) {
    if (textarea.hasAttribute('placeholder')) {
        const placeholder = textarea.getAttribute('placeholder');
        
        // Create floating label
        const label = document.createElement('label');
        label.className = 'floating-label absolute left-3 top-3 text-gray-500 dark:text-gray-400 transition-all duration-300 pointer-events-none';
        label.textContent = placeholder;
        
        // Add label to wrapper
        textarea.parentElement.appendChild(label);
        
        // Remove original placeholder
        textarea.removeAttribute('placeholder');
        
        // Add event listeners
        textarea.addEventListener('focus', function() {
            label.classList.add('active');
        });
        
        textarea.addEventListener('blur', function() {
            if (this.value === '') {
                label.classList.remove('active');
            }
        });
        
        // Set initial state
        if (textarea.value !== '') {
            label.classList.add('active');
        }
    }
}

/**
 * Add syntax highlighting for code textareas
 * @param {HTMLTextAreaElement} textarea - The textarea element
 */
function addSyntaxHighlighting(textarea) {
    // This is a simplified version - in a real implementation, 
    // you would use a library like Prism.js or highlight.js
    textarea.classList.add('font-mono');
}
