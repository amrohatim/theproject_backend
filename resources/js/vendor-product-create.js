import { createApp } from 'vue';
import VendorProductCreateApp from './components/vendor/VendorProductCreateApp.vue';

// Create Vue app
const app = createApp(VendorProductCreateApp);

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
app.mount('#vendor-product-create-app');
