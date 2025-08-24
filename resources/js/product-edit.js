import './bootstrap'
import { createApp } from 'vue'
import ProductEditApp from './components/merchant/ProductEditApp.vue'

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

// Create Vue app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const productEditContainer = document.getElementById('product-edit-app')

    if (productEditContainer) {
        // Prepare container for safe mounting
        if (!prepareContainerForMount(productEditContainer)) {
            console.error('❌ Failed to prepare container for mounting');
            return;
        }

        const app = createApp(ProductEditApp, {
            productId: productEditContainer.dataset.productId,
            backUrl: productEditContainer.dataset.backUrl || '/merchant/products'
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

        // Mount with enhanced error handling
        try {
            console.log('🎯 Attempting to mount Vue app to #product-edit-app');

            // Use a more defensive mounting approach
            let mountedApp;
            try {
                // First, try direct mounting
                mountedApp = app.mount('#product-edit-app');
            } catch (mountError) {
                console.warn('⚠️ Direct mount failed, trying alternative approach:', mountError.message);

                // If direct mount fails, try mounting to the element directly
                mountedApp = app.mount(productEditContainer);
            }

            console.log('✅ Vue app mounted successfully');
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
        }
    }
})
