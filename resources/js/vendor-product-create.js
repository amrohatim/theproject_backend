import { createApp } from 'vue';
import VendorProductCreateApp from './components/vendor/VendorProductCreateApp.vue';

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

  // Clear container content
  container.innerHTML = '';

  // Reset container classes
  container.className = 'vue-app-container';

  console.log('✅ Vue app cleanup completed');
}

// Function to initialize Vue app
function initVendorProductCreateApp(forceCleanup = false) {
  console.log('🎯 initVendorProductCreateApp called');
  const container = document.getElementById('vendor-product-create-app');

  console.log('Container found:', !!container);
  console.log('Container classes:', container?.className);
  console.log('Container hidden:', container?.classList.contains('hidden'));

  if (!container) {
    console.warn('❌ Vue container #vendor-product-create-app not found');
    return false;
  }

  if (container.classList.contains('hidden')) {
    console.warn('⚠️ Vue container is hidden, showing it...');
    container.classList.remove('hidden');
  }

  // Force cleanup if requested
  if (forceCleanup && container.__vue_app__) {
    console.log('🔄 Force cleanup requested');
    cleanupVueApp(container);
  }

  // Check if already mounted and functional
  if (container.__vue_app__) {
    console.log('🔍 Existing Vue app found, checking if functional...');

    // Check if the Vue app is actually functional by looking for Vue-rendered content
    const hasVueContent = container.children.length > 0 &&
                         (container.querySelector('[data-v-]') ||
                          container.textContent.trim().length > 50);

    if (hasVueContent) {
      console.log('✅ Vue app already mounted and functional');
      return true;
    } else {
      console.warn('⚠️ Vue app exists but appears non-functional, cleaning up...');
      cleanupVueApp(container);
    }
  }

  console.log('🚀 Initializing vendor product create Vue app');

  // Get props from data attributes
  const userRole = container.dataset.userRole || 'vendor';

  // Create Vue app with props
  const app = createApp(VendorProductCreateApp, {
    backUrl: container.dataset.backUrl,
    createDataUrl: container.dataset.createDataUrl,
    storeUrl: container.dataset.storeUrl,
    sessionStoreUrl: container.dataset.sessionStoreUrl,
    sessionGetUrl: container.dataset.sessionGetUrl,
    sessionClearUrl: container.dataset.sessionClearUrl,
    userRole: userRole
  });

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
    console.log('🎯 Attempting to mount Vue app to #vendor-product-create-app');
    console.log('Container before mount:', container.outerHTML.substring(0, 200));

    // Ensure container is ready for mounting
    if (container.innerHTML.trim()) {
      console.log('🧹 Container has existing content, clearing...');
      container.innerHTML = '';
    }

    const mountedApp = app.mount('#vendor-product-create-app');
    container.__vue_app__ = mountedApp;

    console.log('✅ Vue app mounted successfully');
    console.log('Container after mount:', container.outerHTML.substring(0, 200));

    // Verify the mount was successful
    setTimeout(() => {
      const hasContent = container.children.length > 0 || container.textContent.trim().length > 50;
      if (!hasContent) {
        console.error('❌ Vue app mounted but no content rendered, retrying...');
        cleanupVueApp(container);
        setTimeout(() => initVendorProductCreateApp(true), 500);
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
      const backUrl = container.dataset.backUrl || '/products-manager/products';
      const backLink = container.querySelector('a[href="/vendor/products"], a[href="/vendor/products"]');
      if (backLink) backLink.setAttribute('href', backUrl);
    } catch (e) {
      console.warn('Could not adjust Back to Products link:', e);
    }

    return true;
  } catch (error) {
    console.error('❌ Failed to mount Vue app:', error);
    console.error('Error details:', error.message);
    console.error('Error stack:', error.stack);
    return false;
  }
}

// Global cleanup function
function cleanupVendorProductCreateApp() {
  console.log('🧹 Global cleanup for vendor product create app');
  const container = document.getElementById('vendor-product-create-app');
  if (container) {
    cleanupVueApp(container);
  }
}

// Expose functions globally for debugging and cleanup
window.initVendorProductCreateApp = initVendorProductCreateApp;
window.cleanupVendorProductCreateApp = cleanupVendorProductCreateApp;

console.log('📦 vendor-product-create.js loaded');
console.log('Document ready state:', document.readyState);

// Try to initialize immediately
console.log('🚀 Attempting immediate initialization...');
const immediateResult = initVendorProductCreateApp();
console.log('Immediate initialization result:', immediateResult);

// Also try after DOM is ready (for AJAX contexts)
if (document.readyState === 'loading') {
  console.log('📋 Document still loading, waiting for DOMContentLoaded...');
  document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 DOMContentLoaded fired, initializing Vue app...');
    initVendorProductCreateApp();
  });
} else {
  // DOM is already ready, try again after a short delay
  console.log('📋 Document already ready, trying again after delay...');
  setTimeout(function() {
    console.log('⏰ Delayed initialization attempt...');
    initVendorProductCreateApp();
  }, 100);

  // Also try after a longer delay for AJAX contexts
  setTimeout(function() {
    console.log('⏰ Extended delay initialization attempt...');
    initVendorProductCreateApp();
  }, 1000);
}
