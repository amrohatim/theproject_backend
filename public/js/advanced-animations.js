/**
 * Advanced Animation Controller
 * Manages performance-optimized animations and micro-interactions
 */

class AdvancedAnimationController {
    constructor(options = {}) {
        this.options = {
            enableAnimations: true,
            respectReducedMotion: true,
            enableGPUAcceleration: true,
            staggerDelay: 50,
            ...options
        };

        this.animationQueue = [];
        this.isAnimating = false;
        this.observers = new Map();
        this.reducedMotion = false;

        this.init();
    }

    init() {
        this.checkReducedMotionPreference();
        this.setupIntersectionObserver();
        this.bindEvents();
        this.initializeExistingElements();
    }

    checkReducedMotionPreference() {
        if (this.options.respectReducedMotion) {
            this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            
            // Listen for changes
            window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
                this.reducedMotion = e.matches;
                this.updateAnimationState();
            });
        }
    }

    updateAnimationState() {
        document.documentElement.classList.toggle('reduced-motion', this.reducedMotion);
        
        if (this.reducedMotion) {
            this.disableAnimations();
        } else {
            this.enableAnimations();
        }
    }

    setupIntersectionObserver() {
        // Observer for entrance animations
        this.entranceObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.triggerEntranceAnimation(entry.target);
                    this.entranceObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });

        // Observer for scroll-triggered animations
        this.scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.triggerScrollAnimation(entry.target);
                }
            });
        }, {
            threshold: 0.2,
            rootMargin: '20px'
        });
    }

    bindEvents() {
        // Button ripple effects
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.btn-ripple');
            if (button && !this.reducedMotion) {
                this.createRippleEffect(button, e);
            }
        });

        // Hover effects with browser compatibility
        document.addEventListener('mouseenter', (e) => {
            // Use matches with fallback for older browsers
            const matchesSelector = e.target.matches || e.target.webkitMatchesSelector || e.target.mozMatchesSelector || e.target.msMatchesSelector;
            if (matchesSelector && matchesSelector.call(e.target, '.hover-lift, .hover-lift-strong, .hover-scale, .hover-scale-small')) {
                this.addGPUAcceleration(e.target);
            }
        }, true);

        // Focus effects with browser compatibility
        document.addEventListener('focusin', (e) => {
            const matchesSelector = e.target.matches || e.target.webkitMatchesSelector || e.target.mozMatchesSelector || e.target.msMatchesSelector;
            if (matchesSelector && matchesSelector.call(e.target, '.search-input-focus')) {
                this.animateSearchFocus(e.target);
            }
        });

        document.addEventListener('focusout', (e) => {
            const matchesSelector = e.target.matches || e.target.webkitMatchesSelector || e.target.mozMatchesSelector || e.target.msMatchesSelector;
            if (matchesSelector && matchesSelector.call(e.target, '.search-input-focus')) {
                this.animateSearchBlur(e.target);
            }
        });
    }

    initializeExistingElements() {
        // Initialize entrance animations
        const entranceElements = document.querySelectorAll('[data-animate-entrance]');
        entranceElements.forEach(el => {
            this.prepareEntranceAnimation(el);
            this.entranceObserver.observe(el);
        });

        // Initialize scroll animations
        const scrollElements = document.querySelectorAll('[data-animate-scroll]');
        scrollElements.forEach(el => {
            this.scrollObserver.observe(el);
        });

        // Initialize staggered animations
        const staggerContainers = document.querySelectorAll('[data-animate-stagger]');
        staggerContainers.forEach(container => {
            this.setupStaggeredAnimation(container);
        });
    }

    // Animation Methods
    animateElement(element, animation, options = {}) {
        if (this.reducedMotion && !options.forceAnimation) {
            return Promise.resolve();
        }

        return new Promise((resolve) => {
            const {
                duration = 300,
                delay = 0,
                easing = 'ease-out',
                cleanup = true
            } = options;

            if (this.options.enableGPUAcceleration) {
                this.addGPUAcceleration(element);
            }

            // Apply animation
            element.style.animation = `${animation} ${duration}ms ${easing} ${delay}ms both`;

            const handleAnimationEnd = () => {
                if (cleanup) {
                    element.style.animation = '';
                    this.removeGPUAcceleration(element);
                }
                element.removeEventListener('animationend', handleAnimationEnd);
                resolve();
            };

            element.addEventListener('animationend', handleAnimationEnd);

            // Fallback timeout
            setTimeout(() => {
                handleAnimationEnd();
            }, duration + delay + 100);
        });
    }

    staggerAnimation(elements, animation, options = {}) {
        const {
            staggerDelay = this.options.staggerDelay,
            ...animationOptions
        } = options;

        const promises = Array.from(elements).map((element, index) => {
            const delay = (animationOptions.delay || 0) + (index * staggerDelay);
            return this.animateElement(element, animation, {
                ...animationOptions,
                delay
            });
        });

        return Promise.all(promises);
    }

    // Specific Animation Implementations
    triggerEntranceAnimation(element) {
        const animationType = element.dataset.animateEntrance || 'fadeInUp';
        const delay = parseInt(element.dataset.animateDelay) || 0;
        
        this.animateElement(element, animationType, { delay });
    }

    triggerScrollAnimation(element) {
        const animationType = element.dataset.animateScroll || 'fadeInUp';
        this.animateElement(element, animationType);
    }

    prepareEntranceAnimation(element) {
        if (this.reducedMotion) return;

        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
    }

    setupStaggeredAnimation(container) {
        const children = container.children;
        const animationType = container.dataset.animateStagger || 'fadeInUp';
        const staggerDelay = parseInt(container.dataset.staggerDelay) || this.options.staggerDelay;

        Array.from(children).forEach((child, index) => {
            child.style.setProperty('--stagger-index', index);
            child.style.setProperty('--stagger-delay', `${staggerDelay}ms`);
            
            if (!this.reducedMotion) {
                child.style.opacity = '0';
                child.style.transform = 'translateY(20px)';
            }
        });

        // Trigger when container comes into view
        this.entranceObserver.observe(container);
    }

    animateSearchFocus(input) {
        if (this.reducedMotion) return;

        input.classList.add('search-input-focus');
        
        // Animate search icon
        const icon = input.parentElement.querySelector('.enhanced-search-icon');
        if (icon) {
            this.animateElement(icon, 'zoomIn', { duration: 200 });
        }
    }

    animateSearchBlur(input) {
        if (this.reducedMotion) return;

        input.classList.remove('search-input-focus');
    }

    createRippleEffect(button, event) {
        const rect = button.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        const ripple = document.createElement('div');
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 600ms ease-out;
            left: ${x}px;
            top: ${y}px;
            width: ${size}px;
            height: ${size}px;
            pointer-events: none;
        `;

        button.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Search-specific animations
    animateSearchSuggestions(container, show = true) {
        if (this.reducedMotion) {
            container.style.display = show ? 'block' : 'none';
            return Promise.resolve();
        }

        if (show) {
            container.style.display = 'block';
            return this.animateElement(container, 'slideInDown', { duration: 200 });
        } else {
            return this.animateElement(container, 'slideInUp', { duration: 150 })
                .then(() => {
                    container.style.display = 'none';
                });
        }
    }

    animateFilterPanel(panel, show = true) {
        if (this.reducedMotion) {
            panel.style.display = show ? 'block' : 'none';
            return Promise.resolve();
        }

        if (show) {
            panel.style.display = 'block';
            return this.animateElement(panel, 'fadeInUp', { duration: 250 });
        } else {
            return this.animateElement(panel, 'fadeInUp', { duration: 150 })
                .then(() => {
                    panel.style.display = 'none';
                });
        }
    }

    animateFilterTag(tag, action = 'add') {
        if (this.reducedMotion) return Promise.resolve();

        if (action === 'add') {
            return this.animateElement(tag, 'zoomIn', { duration: 200 });
        } else {
            return this.animateElement(tag, 'zoomOut', { duration: 150 });
        }
    }

    animateResultsGrid(grid, results) {
        if (this.reducedMotion) return Promise.resolve();

        const cards = grid.querySelectorAll('.enhanced-result-card');
        return this.staggerAnimation(cards, 'fadeInUp', {
            duration: 300,
            staggerDelay: 50
        });
    }

    animateLoadingState(element, show = true) {
        if (this.reducedMotion) {
            element.style.display = show ? 'block' : 'none';
            return Promise.resolve();
        }

        if (show) {
            element.style.display = 'block';
            return this.animateElement(element, 'fadeIn', { duration: 200 });
        } else {
            return this.animateElement(element, 'fadeOut', { duration: 150 })
                .then(() => {
                    element.style.display = 'none';
                });
        }
    }

    // Performance optimization methods
    addGPUAcceleration(element) {
        if (!this.options.enableGPUAcceleration) return;
        
        element.style.transform = element.style.transform || 'translateZ(0)';
        element.style.willChange = 'transform, opacity';
    }

    removeGPUAcceleration(element) {
        if (!this.options.enableGPUAcceleration) return;
        
        element.style.willChange = 'auto';
    }

    // Queue management
    queueAnimation(animationFn) {
        this.animationQueue.push(animationFn);
        this.processQueue();
    }

    async processQueue() {
        if (this.isAnimating || this.animationQueue.length === 0) return;

        this.isAnimating = true;

        while (this.animationQueue.length > 0) {
            const animationFn = this.animationQueue.shift();
            try {
                await animationFn();
            } catch (error) {
                console.error('Animation error:', error);
            }
        }

        this.isAnimating = false;
    }

    // Control methods
    enableAnimations() {
        this.options.enableAnimations = true;
        document.documentElement.classList.remove('animations-disabled');
    }

    disableAnimations() {
        this.options.enableAnimations = false;
        document.documentElement.classList.add('animations-disabled');
    }

    // Utility methods
    isElementVisible(element) {
        const rect = element.getBoundingClientRect();
        return rect.top < window.innerHeight && rect.bottom > 0;
    }

    waitForAnimation(element) {
        return new Promise((resolve) => {
            const handleAnimationEnd = () => {
                element.removeEventListener('animationend', handleAnimationEnd);
                element.removeEventListener('transitionend', handleAnimationEnd);
                resolve();
            };

            element.addEventListener('animationend', handleAnimationEnd);
            element.addEventListener('transitionend', handleAnimationEnd);

            // Fallback timeout
            setTimeout(resolve, 1000);
        });
    }

    // Cleanup
    destroy() {
        if (this.entranceObserver) {
            this.entranceObserver.disconnect();
        }
        
        if (this.scrollObserver) {
            this.scrollObserver.disconnect();
        }

        this.observers.forEach(observer => observer.disconnect());
        this.observers.clear();
        
        this.animationQueue = [];
    }
}

// Global animation controller instance
let globalAnimationController;

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    globalAnimationController = new AdvancedAnimationController({
        enableAnimations: true,
        respectReducedMotion: true,
        enableGPUAcceleration: true
    });

    // Make it globally accessible
    window.AnimationController = globalAnimationController;
});

// CSS injection for ripple effect
const rippleCSS = `
@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
`;

const style = document.createElement('style');
style.textContent = rippleCSS;
document.head.appendChild(style);

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdvancedAnimationController;
}
