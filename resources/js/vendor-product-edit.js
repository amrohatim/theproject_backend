import './bootstrap'
import { createApp } from 'vue'
import VendorProductEditApp from './components/vendor/VendorProductEditApp.vue'

// Global DOM stability helper
function ensureDOMStability() {
  // Pause any running animations or transitions that might interfere
  const style = document.createElement('style');
  style.id = 'vue-mount-stability-style';
  style.textContent = `
    #vendor-product-edit-app * {
      transition: none !important;
      animation: none !important;
    }
  `;
  document.head.appendChild(style);

  // Remove the style after a longer delay for production environments
  setTimeout(() => {
    const existingStyle = document.getElementById('vue-mount-stability-style');
    if (existingStyle) {
      document.head.removeChild(existingStyle);
    }
  }, 1000); // Increased timeout for production
}

// Production environment detection
function isProductionEnvironment() {
  return window.location.hostname !== 'localhost' &&
         window.location.hostname !== '127.0.0.1' &&
         !window.location.hostname.includes('local');
}

// Function to clean up existing Vue app
function cleanupVueApp(container) {
  console.log('🧹 Cleaning up existing Vue app...');

  if (container.__vue_app__) {
    try {
      if (typeof container.__vue_app__.unmount === 'function') {
        container.__vue_app__.unmount();
        console.log('✅ Previous Vue app unmounted');
      }
    } catch (e) {
      console.warn('⚠️ Error unmounting previous Vue app:', e);
    }
    container.__vue_app__ = null;
  }

  // For edit forms, preserve the container content and classes
  // Only reset the Vue app reference
  console.log('🔧 Preserving container content for edit form');

  console.log('✅ Vue app cleanup completed');
}

// Function to safely prepare container for Vue mounting
function prepareContainerForMount(container) {
  console.log('🔧 Preparing container for Vue mounting...');

  // Ensure container is visible and properly positioned in DOM
  if (container.classList.contains('hidden')) {
    container.classList.remove('hidden');
  }

  // Stop any ongoing DOM mutations that might interfere
  const observer = new MutationObserver(() => {});
  observer.observe(container, { childList: true, subtree: true });

  // Clear any pending DOM operations
  if (window.requestAnimationFrame) {
    window.requestAnimationFrame(() => {
      observer.disconnect();
    });
  }

  // Ensure container is stable in the DOM
  const rect = container.getBoundingClientRect();
  if (rect.width === 0 || rect.height === 0) {
    console.warn('⚠️ Container has zero dimensions, adjusting...');
    container.style.minHeight = '100px';
    container.style.display = 'block';
  }

  console.log('✅ Container prepared for mounting');
  return true;
}

// Global flag to prevent multiple initialization attempts
let isVueAppInitializing = false;
let vueAppInitialized = false;

// Enhanced Vue app functionality check
function isVueAppFunctional(container) {
    if (!container || !container.__vue_app__) {
        return false;
    }

    // Check for Vue-specific attributes and content
    const hasVueAttributes = container.hasAttribute('data-v-app') ||
                            container.querySelector('[data-v-]') !== null;

    // Check for meaningful content (more than just loading indicators)
    const hasContent = container.children.length > 0;
    const hasTextContent = container.textContent.trim().length > 100; // More generous threshold

    // Check for specific Vue component elements
    const hasVueComponents = container.querySelector('.vue-content-container') !== null ||
                            container.querySelector('[class*="vue-"]') !== null ||
                            container.querySelector('form') !== null ||
                            container.querySelector('input, select, textarea') !== null;

    console.log('🔍 Vue app functionality check:', {
        hasVueAttributes,
        hasContent,
        hasTextContent,
        hasVueComponents,
        childrenCount: container.children.length,
        textLength: container.textContent.trim().length
    });

    return hasVueAttributes && (hasContent || hasTextContent || hasVueComponents);
}

// Function to initialize Vue app
function initVendorProductEditApp(forceCleanup = false) {
    // Prevent multiple simultaneous initialization attempts
    if (isVueAppInitializing) {
        console.log('🔄 Vue app initialization already in progress, skipping...');
        return false;
    }

    // If already successfully initialized and not forcing cleanup, skip
    if (vueAppInitialized && !forceCleanup) {
        console.log('✅ Vue app already initialized, skipping...');
        return true;
    }

    isVueAppInitializing = true;

    // Ensure DOM stability before proceeding
    ensureDOMStability();

    const vendorProductEditContainer = document.getElementById('vendor-product-edit-app')

    if (!vendorProductEditContainer) {
        console.warn('Vue container #vendor-product-edit-app not found');
        isVueAppInitializing = false;
        return false;
    }

    console.log('🎯 initVendorProductEditApp called');
    console.log('Container found:', !!vendorProductEditContainer);
    console.log('Container classes:', vendorProductEditContainer.className);
    console.log('Container hidden:', vendorProductEditContainer.classList.contains('hidden'));

    if (vendorProductEditContainer.classList.contains('hidden')) {
        console.warn('⚠️ Vue container is hidden, showing it...');
        vendorProductEditContainer.classList.remove('hidden');
    }

    // Check if already mounted and functional with enhanced verification
    if (vendorProductEditContainer.__vue_app__ && !forceCleanup) {
        console.log('🔍 Existing Vue app found, checking if functional...');

        if (isVueAppFunctional(vendorProductEditContainer)) {
            console.log('✅ Vue app already mounted and functional');
            vueAppInitialized = true;
            isVueAppInitializing = false;
            return true;
        } else {
            console.warn('⚠️ Vue app exists but appears non-functional, will remount...');
            cleanupVueApp(vendorProductEditContainer);
        }
    }

    // Force cleanup if requested and app exists
    if (forceCleanup && vendorProductEditContainer.__vue_app__) {
        console.log('🔄 Force cleanup requested for edit form');
        cleanupVueApp(vendorProductEditContainer);
    }

    console.log('Initializing vendor product edit Vue app');

    const app = createApp(VendorProductEditApp, {
        productId: vendorProductEditContainer.dataset.productId,
        backUrl: vendorProductEditContainer.dataset.backUrl || '/vendor/products'
    })

    // Add global translation method
    app.config.globalProperties.$t = function(key, replacements = {}) {
        // Try multiple translation sources
        let translation = key;

        if (window.appTranslations && window.appTranslations[key]) {
            translation = window.appTranslations[key];
        } else if (window.Laravel && window.Laravel.translations && window.Laravel.translations[key]) {
            translation = window.Laravel.translations[key];
        } else if (window.translations && window.translations[key]) {
            translation = window.translations[key];
        }

        // Handle placeholder replacements
        Object.keys(replacements).forEach(placeholder => {
            translation = translation.replace(`:${placeholder}`, replacements[placeholder]);
        });

        return translation;
    };

    // Add global error handler
    app.config.errorHandler = (err, vm, info) => {
        console.error('Vue error:', err, info);
    };

    // Add global warning handler
    app.config.warnHandler = (msg, vm, trace) => {
        console.warn('Vue warning:', msg, trace);
    };

    // Prepare container for safe mounting
    if (!prepareContainerForMount(vendorProductEditContainer)) {
        console.error('❌ Failed to prepare container for mounting');
        return false;
    }

    // Mount the app with enhanced error handling
    try {
        console.log('🎯 Attempting to mount Vue app to #vendor-product-edit-app');
        console.log('Container before mount:', vendorProductEditContainer.outerHTML.substring(0, 200));

        // For edit forms, don't clear the container as it may have initial data
        console.log('🔧 Container ready for mounting (preserving existing content for edit form)');

        // Use a more defensive mounting approach with production-specific handling
        let mountedApp;

        // In production, add extra stability measures
        if (isProductionEnvironment()) {
            console.log('🏭 Production environment detected, using enhanced mounting...');

            // Force a reflow to ensure DOM is stable
            vendorProductEditContainer.offsetHeight;

            // Clear any existing Vue artifacts that might cause conflicts
            const existingVueElements = vendorProductEditContainer.querySelectorAll('[data-v-]');
            existingVueElements.forEach(el => {
                // Remove Vue-specific attributes that might cause conflicts
                Array.from(el.attributes).forEach(attr => {
                    if (attr.name.startsWith('data-v-')) {
                        el.removeAttribute(attr.name);
                    }
                });
            });
        }

        try {
            // First, try direct mounting
            mountedApp = app.mount('#vendor-product-edit-app');
        } catch (mountError) {
            console.warn('⚠️ Direct mount failed, trying alternative approach:', mountError.message);

            // If direct mount fails, try mounting to the element directly
            try {
                mountedApp = app.mount(vendorProductEditContainer);
            } catch (alternativeError) {
                console.error('❌ Alternative mount also failed:', alternativeError.message);
                throw alternativeError;
            }
        }

        vendorProductEditContainer.__vue_app__ = mountedApp;

        console.log('✅ Vue app mounted successfully');
        console.log('Container after mount:', vendorProductEditContainer.outerHTML.substring(0, 200));

        // Enhanced verification with environment-specific timeout
        const verificationTimeout = isProductionEnvironment() ? 1000 : 500;
        setTimeout(() => {
            if (isVueAppFunctional(vendorProductEditContainer)) {
                console.log('✅ Vue app mount verification successful');
                vueAppInitialized = true;
            } else {
                console.warn('⚠️ Vue app mounted but verification failed');
                // Don't retry automatically to avoid infinite loops
                console.log('🔧 Edit form will show fallback content if available');
            }
            isVueAppInitializing = false;
        }, verificationTimeout);

        // Hide any fallback or loading indicators
        const fallbackEl = document.getElementById('fallback-content');
        if (fallbackEl) fallbackEl.classList.add('hidden');
        const loadingEl1 = document.getElementById('loading-indicator');
        if (loadingEl1) loadingEl1.style.display = 'none';
        const loadingEl2 = document.getElementById('vue-loading-indicator');
        if (loadingEl2) loadingEl2.classList.add('hidden');

        // Correct the Back to Products link inside the Vue header if present
        try {
            const backUrl = vendorProductEditContainer.dataset.backUrl || '/products-manager/products';
            const backLink = vendorProductEditContainer.querySelector('a[href="/vendor/products"], a[href="/vendor/products"]');
            if (backLink) backLink.setAttribute('href', backUrl);
        } catch (e) {
            console.warn('Could not adjust Back to Products link:', e);
        }

        return true;
    } catch (error) {
        console.error('❌ Failed to mount Vue app:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            name: error.name
        });

        // Try to provide more specific error information
        if (error.message && error.message.includes('nextSibling')) {
            console.error('🔍 DOM manipulation conflict detected. This usually happens when other scripts modify the DOM during Vue mounting.');
            console.error('💡 Suggestion: Check for conflicting JavaScript that manipulates the container element.');
        }

        isVueAppInitializing = false;
        return false;
    }
}

// Global cleanup function
function cleanupVendorProductEditApp() {
  console.log('🧹 Global cleanup for vendor product edit app');
  const container = document.getElementById('vendor-product-edit-app');
  if (container) {
    cleanupVueApp(container);
  }
}

// Expose functions globally for debugging and cleanup
window.initVendorProductEditApp = initVendorProductEditApp;
window.cleanupVendorProductEditApp = cleanupVendorProductEditApp;

console.log('📦 vendor-product-edit.js loaded');
console.log('Document ready state:', document.readyState);

// Smart initialization strategy to prevent multiple attempts
function smartInitialization() {
    console.log('🚀 Smart initialization starting...');

    // Try immediate initialization
    const immediateResult = initVendorProductEditApp();
    console.log('Immediate initialization result:', immediateResult);

    // If immediate initialization succeeded, we're done
    if (immediateResult) {
        console.log('✅ Immediate initialization successful, no further attempts needed');
        return;
    }

    // Only set up additional attempts if immediate initialization failed
    if (document.readyState === 'loading') {
        console.log('📋 Document still loading, waiting for DOMContentLoaded...');
        document.addEventListener('DOMContentLoaded', () => {
            if (!vueAppInitialized) {
                console.log('📋 DOMContentLoaded fired, trying initialization...');
                initVendorProductEditApp();
            }
        });
    } else {
        // DOM is already ready, try once more after a delay for AJAX contexts
        // Use longer delay for production environments
        const delay = isProductionEnvironment() ? 500 : 250;
        setTimeout(() => {
            if (!vueAppInitialized && !isVueAppInitializing) {
                console.log('⏰ Delayed initialization attempt for AJAX context...');
                initVendorProductEditApp();
            }
        }, delay);
    }
}

// Start smart initialization
smartInitialization();
