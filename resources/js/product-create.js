import './bootstrap'
import { createApp } from 'vue'
import ProductCreateApp from './components/merchant/ProductCreateApp.vue'

// Create Vue app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const productCreateContainer = document.getElementById('product-create-app')
    
    if (productCreateContainer) {
        const app = createApp(ProductCreateApp, {
            backUrl: productCreateContainer.dataset.backUrl || '/merchant/products'
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
        
        app.mount('#product-create-app')
    }
})
