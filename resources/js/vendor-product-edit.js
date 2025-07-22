import './bootstrap'
import { createApp } from 'vue'
import VendorProductEditApp from './components/vendor/VendorProductEditApp.vue'

// Create Vue app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const vendorProductEditContainer = document.getElementById('vendor-product-edit-app')
    
    if (vendorProductEditContainer) {
        const app = createApp(VendorProductEditApp, {
            productId: vendorProductEditContainer.dataset.productId,
            backUrl: vendorProductEditContainer.dataset.backUrl || '/vendor/products'
        })
        
        app.mount('#vendor-product-edit-app')
    }
})
