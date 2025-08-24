/**
 * Enhanced Password Validation System
 * Provides real-time password strength validation with visual feedback
 */

class PasswordValidator {
    constructor(passwordFieldId, confirmFieldId = null, options = {}) {
        this.passwordField = document.getElementById(passwordFieldId);
        this.confirmField = confirmFieldId ? document.getElementById(confirmFieldId) : null;
        this.options = {
            minLength: 8,
            requireUppercase: true,
            requireLowercase: true,
            requireNumbers: true,
            requireSpecialChars: true,
            showStrengthBar: true,
            showRequirements: true,
            realTimeValidation: true,
            ...options
        };
        
        this.requirements = [
            {
                id: 'length',
                text: `At least ${this.options.minLength} characters`,
                test: (password) => password.length >= this.options.minLength
            },
            {
                id: 'uppercase',
                text: 'One uppercase letter',
                test: (password) => /[A-Z]/.test(password)
            },
            {
                id: 'lowercase',
                text: 'One lowercase letter',
                test: (password) => /[a-z]/.test(password)
            },
            {
                id: 'number',
                text: 'One number',
                test: (password) => /\d/.test(password)
            },
            {
                id: 'special',
                text: 'One special character',
                test: (password) => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
            }
        ];
        
        this.init();
    }
    
    init() {
        if (!this.passwordField) {
            console.error('Password field not found');
            return;
        }
        
        this.createPasswordContainer();
        this.attachEventListeners();
        
        // Initial validation
        if (this.passwordField.value) {
            this.validatePassword(this.passwordField.value);
        }
    }
    
    createPasswordContainer() {
        // Create password strength container
        const container = document.createElement('div');
        container.className = 'password-strength-container';
        container.id = `${this.passwordField.id}-strength`;
        
        let html = '';
        
        if (this.options.showStrengthBar) {
            html += `
                <div class="password-strength-bar">
                    <div class="password-strength-fill" style="width: 0%"></div>
                </div>
            `;
        }
        
        if (this.options.showRequirements) {
            html += '<div class="password-requirements">';
            this.requirements.forEach(req => {
                html += `
                    <div class="password-requirement" data-requirement="${req.id}">
                        <div class="password-requirement-icon">
                            <i class="fas fa-times"></i>
                        </div>
                        <span>${req.text}</span>
                    </div>
                `;
            });
            html += '</div>';
        }
        
        container.innerHTML = html;
        
        // Insert after password field
        this.passwordField.parentNode.insertBefore(container, this.passwordField.nextSibling);
        this.strengthContainer = container;
    }
    
    attachEventListeners() {
        if (this.options.realTimeValidation) {
            this.passwordField.addEventListener('input', (e) => {
                this.validatePassword(e.target.value);
            });
            
            this.passwordField.addEventListener('focus', () => {
                this.strengthContainer.style.display = 'block';
            });
        }
        
        if (this.confirmField) {
            this.confirmField.addEventListener('input', (e) => {
                this.validatePasswordConfirmation(e.target.value);
            });
        }
        
        // Form submission validation
        const form = this.passwordField.closest('form');
        if (form) {
            form.addEventListener('submit', (e) => {
                if (!this.isPasswordValid(this.passwordField.value)) {
                    e.preventDefault();
                    this.showError('Please ensure your password meets all requirements');
                    return false;
                }
                
                if (this.confirmField && !this.isPasswordConfirmationValid()) {
                    e.preventDefault();
                    this.showError('Passwords do not match');
                    return false;
                }
            });
        }
    }
    
    validatePassword(password) {
        const strength = this.calculatePasswordStrength(password);
        this.updateStrengthBar(strength);
        this.updateRequirements(password);
        this.updateFieldState(password);
        
        return this.isPasswordValid(password);
    }
    
    calculatePasswordStrength(password) {
        if (!password) return 0;
        
        let score = 0;
        const maxScore = this.requirements.length;
        
        this.requirements.forEach(req => {
            if (req.test(password)) {
                score++;
            }
        });
        
        return (score / maxScore) * 100;
    }
    
    updateStrengthBar(strength) {
        if (!this.options.showStrengthBar) return;
        
        const strengthFill = this.strengthContainer.querySelector('.password-strength-fill');
        if (strengthFill) {
            strengthFill.style.width = `${strength}%`;
            
            // Update color based on strength
            if (strength < 40) {
                strengthFill.style.background = '#ef4444'; // Red
            } else if (strength < 80) {
                strengthFill.style.background = '#f59e0b'; // Orange
            } else {
                strengthFill.style.background = '#10b981'; // Green
            }
        }
    }
    
    updateRequirements(password) {
        if (!this.options.showRequirements) return;
        
        this.requirements.forEach(req => {
            const element = this.strengthContainer.querySelector(`[data-requirement="${req.id}"]`);
            if (element) {
                const isMet = req.test(password);
                const icon = element.querySelector('i');
                
                if (isMet) {
                    element.classList.add('met');
                    icon.className = 'fas fa-check';
                } else {
                    element.classList.remove('met');
                    icon.className = 'fas fa-times';
                }
            }
        });
    }
    
    updateFieldState(password) {
        const isValid = this.isPasswordValid(password);
        
        if (password.length > 0) {
            if (isValid) {
                this.passwordField.classList.remove('error');
                this.passwordField.classList.add('success');
            } else {
                this.passwordField.classList.remove('success');
                this.passwordField.classList.add('error');
            }
        } else {
            this.passwordField.classList.remove('error', 'success');
        }
    }
    
    validatePasswordConfirmation(confirmPassword) {
        if (!this.confirmField) return true;
        
        const isValid = this.isPasswordConfirmationValid(confirmPassword);
        
        if (confirmPassword.length > 0) {
            if (isValid) {
                this.confirmField.classList.remove('error');
                this.confirmField.classList.add('success');
                this.hideConfirmError();
            } else {
                this.confirmField.classList.remove('success');
                this.confirmField.classList.add('error');
                this.showConfirmError('Passwords do not match');
            }
        } else {
            this.confirmField.classList.remove('error', 'success');
            this.hideConfirmError();
        }
        
        return isValid;
    }
    
    isPasswordValid(password = null) {
        const pwd = password || this.passwordField.value;
        return this.requirements.every(req => req.test(pwd));
    }
    
    isPasswordConfirmationValid(confirmPassword = null) {
        if (!this.confirmField) return true;
        
        const confirm = confirmPassword || this.confirmField.value;
        return confirm === this.passwordField.value;
    }
    
    showError(message) {
        const errorElement = document.getElementById(`${this.passwordField.id}-error`);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    hideError() {
        const errorElement = document.getElementById(`${this.passwordField.id}-error`);
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
    
    showConfirmError(message) {
        if (!this.confirmField) return;
        
        const errorElement = document.getElementById(`${this.confirmField.id}-error`);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    hideConfirmError() {
        if (!this.confirmField) return;
        
        const errorElement = document.getElementById(`${this.confirmField.id}-error`);
        if (errorElement) {
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    }
    
    getValidationResult() {
        return {
            isValid: this.isPasswordValid(),
            isConfirmationValid: this.isPasswordConfirmationValid(),
            strength: this.calculatePasswordStrength(this.passwordField.value),
            metRequirements: this.requirements.filter(req => req.test(this.passwordField.value))
        };
    }
}

// Enhanced Password Toggle Functionality
class PasswordToggle {
    constructor(passwordFieldId, toggleButtonId = null) {
        this.passwordField = document.getElementById(passwordFieldId);
        this.toggleButton = toggleButtonId ? document.getElementById(toggleButtonId) : null;
        
        if (!this.toggleButton) {
            this.createToggleButton();
        }
        
        this.init();
    }
    
    createToggleButton() {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'enhanced-password-toggle';
        button.innerHTML = '<i class="fas fa-eye"></i>';
        
        // Insert button into password field container
        const container = this.passwordField.parentNode;
        if (container.classList.contains('enhanced-password-group') || container.classList.contains('relative')) {
            container.appendChild(button);
        } else {
            // Wrap password field in container
            const wrapper = document.createElement('div');
            wrapper.className = 'enhanced-password-group relative';
            this.passwordField.parentNode.insertBefore(wrapper, this.passwordField);
            wrapper.appendChild(this.passwordField);
            wrapper.appendChild(button);
        }
        
        this.toggleButton = button;
    }
    
    init() {
        if (!this.passwordField || !this.toggleButton) {
            console.error('Password field or toggle button not found');
            return;
        }
        
        this.toggleButton.addEventListener('click', () => {
            this.toggleVisibility();
        });
    }
    
    toggleVisibility() {
        const icon = this.toggleButton.querySelector('i');
        
        if (this.passwordField.type === 'password') {
            this.passwordField.type = 'text';
            icon.className = 'fas fa-eye-slash';
            this.toggleButton.setAttribute('aria-label', 'Hide password');
        } else {
            this.passwordField.type = 'password';
            icon.className = 'fas fa-eye';
            this.toggleButton.setAttribute('aria-label', 'Show password');
        }
    }
}

// Auto-initialize password validation and toggles
document.addEventListener('DOMContentLoaded', function() {
    // Initialize password validation for common field IDs
    const passwordFields = ['password', 'new_password', 'user_password'];
    const confirmFields = ['password_confirmation', 'confirm_password', 'password_confirm'];
    
    passwordFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // Find corresponding confirmation field
            let confirmFieldId = null;
            confirmFields.forEach(confirmId => {
                if (document.getElementById(confirmId)) {
                    confirmFieldId = confirmId;
                }
            });
            
            // Initialize validator
            new PasswordValidator(fieldId, confirmFieldId);
            
            // Initialize toggle
            new PasswordToggle(fieldId);
        }
    });
});

// Export for manual initialization
window.PasswordValidator = PasswordValidator;
window.PasswordToggle = PasswordToggle;
