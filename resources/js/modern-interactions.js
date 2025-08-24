// Modern Interactions and UI Enhancements

document.addEventListener('DOMContentLoaded', function() {
    initScrollReveal();
    initSmoothScrolling();
    initFormEnhancements();
    initCounterAnimations();
    initParallaxEffects();
    initThemeToggle();
    initMobileMenu();
    initTooltips();
});

// Scroll Reveal Animation
function initScrollReveal() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all elements with scroll-reveal class
    document.querySelectorAll('.scroll-reveal').forEach(el => {
        observer.observe(el);
    });

    // Add staggered animation delays
    document.querySelectorAll('.stagger-children > *').forEach((child, index) => {
        child.style.animationDelay = `${index * 0.1}s`;
    });
}

// Smooth Scrolling for Anchor Links
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Form Enhancements
function initFormEnhancements() {
    // Floating Labels (if enabled)
    const inputs = document.querySelectorAll('.auth-form-input, .modern-input');
    inputs.forEach(input => {
        // Skip validation for login form inputs to prevent conflicts
        const isAuthForm = input.closest('.auth-form');

        // Add focus/blur effects
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
            this.classList.add('pulse-focus');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
            this.classList.remove('pulse-focus');

            // Only validate if not in auth form
            if (!isAuthForm) {
                validateField(this);
            }
        });

        // Real-time validation - skip for auth forms
        if (!isAuthForm) {
            input.addEventListener('input', function() {
                clearTimeout(this.validationTimeout);
                this.validationTimeout = setTimeout(() => {
                    validateField(this);
                }, 500);
            });
        }
    });

    // Password Toggle - Skip for auth forms to prevent conflicts
    document.querySelectorAll('.auth-password-toggle').forEach(toggle => {
        // Skip if this is in an auth form (handled by inline script)
        if (toggle.closest('.auth-form')) {
            return;
        }

        toggle.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    });

    // Form Submission with Loading State - Skip auth forms to prevent conflicts
    document.querySelectorAll('form:not(.auth-form)').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.btn-primary');
            if (submitBtn && !submitBtn.disabled) {
                showLoadingState(submitBtn);
            }
        });
    });
}

// Field Validation
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    
    // Clear previous states
    field.classList.remove('error', 'success');
    removeFieldMessage(field);
    
    if (required && !value) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    if (value) {
        switch (type) {
            case 'email':
                if (!isValidEmail(value)) {
                    showFieldError(field, 'Please enter a valid email address');
                    return false;
                }
                break;
            case 'password':
                if (value.length < 8) {
                    showFieldError(field, 'Password must be at least 8 characters');
                    return false;
                }
                break;
        }
        
        showFieldSuccess(field);
        return true;
    }
    
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(field, message) {
    field.classList.add('error');
    const errorEl = document.createElement('div');
    errorEl.className = 'auth-error-message';
    errorEl.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    field.parentElement.appendChild(errorEl);
}

function showFieldSuccess(field) {
    field.classList.add('success');
}

function removeFieldMessage(field) {
    const existing = field.parentElement.querySelector('.auth-error-message, .auth-success-message');
    if (existing) {
        existing.remove();
    }
}

// Loading State for Buttons
function showLoadingState(button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <span class="auth-loading">
            <span class="auth-spinner"></span>
            Loading...
        </span>
    `;
    
    // Store original text for restoration
    button.dataset.originalText = originalText;
}

function hideLoadingState(button) {
    button.disabled = false;
    button.innerHTML = button.dataset.originalText || button.innerHTML;
}

// Counter Animations
function initCounterAnimations() {
    const counters = document.querySelectorAll('.stat-number');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
}

function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target')) || 0;
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;

    // Determine if this is a percentage counter
    const isPercentage = element.textContent.includes('%');
    const hasPlus = element.textContent.includes('+');

    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }

        // Format number with commas
        const formatted = Math.floor(current).toLocaleString();

        // Update text content with appropriate suffix
        if (isPercentage) {
            element.textContent = formatted + '%';
        } else if (hasPlus) {
            element.textContent = formatted + '+';
        } else {
            element.textContent = formatted;
        }
    }, 16);
}

// Parallax Effects
function initParallaxEffects() {
    const parallaxElements = document.querySelectorAll('.parallax');
    
    if (parallaxElements.length > 0) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            
            parallaxElements.forEach(element => {
                const rate = scrolled * -0.5;
                element.style.transform = `translateY(${rate}px)`;
            });
        });
    }
}

// Theme Toggle
function initThemeToggle() {
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.documentElement.classList.toggle('dark');
            
            // Save preference
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Update icon
            const icon = this.querySelector('i');
            icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
        });
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.documentElement.classList.add('dark');
            const icon = themeToggle.querySelector('i');
            if (icon) icon.className = 'fas fa-sun';
        }
    }
}

// Mobile Menu
function initMobileMenu() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
            this.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            document.body.classList.toggle('menu-open');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mobileMenu.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }
}

// Tooltips
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            showTooltip(this);
        });
        
        element.addEventListener('mouseleave', function() {
            hideTooltip();
        });
    });
}

function showTooltip(element) {
    const text = element.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    
    document.body.appendChild(tooltip);
    
    // Position tooltip
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
    
    // Animate in
    requestAnimationFrame(() => {
        tooltip.classList.add('visible');
    });
}

function hideTooltip() {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Utility Functions
function debounce(func, wait) {
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

function throttle(func, limit) {
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

// Performance optimizations
const optimizedScrollHandler = throttle(() => {
    // Handle scroll-based animations
    const scrolled = window.pageYOffset;
    const parallaxElements = document.querySelectorAll('.parallax');

    parallaxElements.forEach(element => {
        const rate = scrolled * -0.5;
        element.style.transform = `translate3d(0, ${rate}px, 0)`;
    });
}, 16);

// Add scroll listener only if parallax elements exist
if (document.querySelectorAll('.parallax').length > 0) {
    window.addEventListener('scroll', optimizedScrollHandler, { passive: true });
}

// Preload critical images
function preloadImages() {
    const criticalImages = [
        // Add any critical image URLs here
    ];

    criticalImages.forEach(src => {
        const img = new Image();
        img.src = src;
    });
}

// Initialize preloading
preloadImages();

// Service Worker registration for PWA features (if needed)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // Uncomment when service worker is implemented
        // navigator.serviceWorker.register('/sw.js');
    });
}

// Export functions for external use
window.ModernInteractions = {
    showLoadingState,
    hideLoadingState,
    validateField,
    showFieldError,
    showFieldSuccess,
    removeFieldMessage,
    debounce,
    throttle
};
