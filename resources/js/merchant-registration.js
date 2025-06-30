import { createApp } from 'vue';
import MerchantRegistrationApp from './components/MerchantRegistrationApp.vue';

// Create and mount the Vue app
const app = createApp(MerchantRegistrationApp);

// Mount the app to the DOM element
app.mount('#merchant-registration-app');

// Add global error handler
app.config.errorHandler = (err, vm, info) => {
  console.error('Vue error:', err, info);
};

// Add global warning handler
app.config.warnHandler = (msg, vm, trace) => {
  console.warn('Vue warning:', msg, trace);
};
