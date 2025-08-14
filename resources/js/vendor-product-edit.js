import './bootstrap'
import { createApp } from 'vue'
import VendorProductEditApp from './components/vendor/VendorProductEditApp.vue'

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

// Function to initialize Vue app
function initVendorProductEditApp(forceCleanup = false) {
    const vendorProductEditContainer = document.getElementById('vendor-product-edit-app')

    if (!vendorProductEditContainer) {
        console.warn('Vue container #vendor-product-edit-app not found');
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

    // For edit forms, be more conservative with force cleanup
    if (forceCleanup && vendorProductEditContainer.__vue_app__) {
        console.log('🔄 Force cleanup requested for edit form');
        // Check if the current app is actually broken before cleaning up
        const hasVueContent = vendorProductEditContainer.children.length > 0 &&
                             (vendorProductEditContainer.querySelector('[data-v-]') ||
                              vendorProductEditContainer.textContent.trim().length > 50);

        if (!hasVueContent) {
            console.log('🧹 Edit form appears broken, proceeding with cleanup');
            cleanupVueApp(vendorProductEditContainer);
        } else {
            console.log('✅ Edit form appears functional, skipping force cleanup');
            return true;
        }
    }

    // Check if already mounted and functional
    if (vendorProductEditContainer.__vue_app__) {
        console.log('🔍 Existing Vue app found, checking if functional...');

        // Check if the Vue app is actually functional by looking for Vue-rendered content
        const hasVueContent = vendorProductEditContainer.children.length > 0 &&
                             (vendorProductEditContainer.querySelector('[data-v-]') ||
                              vendorProductEditContainer.textContent.trim().length > 50);

        if (hasVueContent) {
            console.log('✅ Vue app already mounted and functional');
            return true;
        } else {
            console.warn('⚠️ Vue app exists but appears non-functional, cleaning up...');
            cleanupVueApp(vendorProductEditContainer);
        }
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

    // Mount the app
    try {
        console.log('🎯 Attempting to mount Vue app to #vendor-product-edit-app');
        console.log('Container before mount:', vendorProductEditContainer.outerHTML.substring(0, 200));

        // For edit forms, don't clear the container as it may have initial data
        console.log('🔧 Container ready for mounting (preserving existing content for edit form)');

        const mountedApp = app.mount('#vendor-product-edit-app');
        vendorProductEditContainer.__vue_app__ = mountedApp;

        console.log('✅ Vue app mounted successfully');
        console.log('Container after mount:', vendorProductEditContainer.outerHTML.substring(0, 200));

        // Verify the mount was successful (more lenient for edit forms)
        setTimeout(() => {
            const hasContent = vendorProductEditContainer.children.length > 0 || vendorProductEditContainer.textContent.trim().length > 50;
            if (!hasContent) {
                console.warn('⚠️ Vue app mounted but no content rendered for edit form');
                // For edit forms, don't retry as aggressively to avoid mounting conflicts
                console.log('🔧 Edit form will show fallback content');
                return false;
            }
            console.log('✅ Vue app mount verification successful');
        }, 100);

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

// Try to initialize immediately
console.log('🚀 Attempting immediate initialization...');
const immediateResult = initVendorProductEditApp();
console.log('Immediate initialization result:', immediateResult);

// Also try when DOM is ready (for AJAX contexts)
if (document.readyState === 'loading') {
    console.log('📋 Document still loading, waiting for DOMContentLoaded...');
    document.addEventListener('DOMContentLoaded', () => {
        console.log('📋 DOMContentLoaded fired, trying initialization...');
        initVendorProductEditApp();
    });
} else {
    console.log('📋 Document already ready, trying again after delay...');
    // DOM is already ready, try again after a short delay
    setTimeout(() => {
        console.log('⏰ Delayed initialization attempt...');
        initVendorProductEditApp();
    }, 100);

    setTimeout(() => {
        console.log('⏰ Extended delay initialization attempt...');
        initVendorProductEditApp();
    }, 1000);
}
