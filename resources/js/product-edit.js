import './bootstrap'
import { createApp } from 'vue'
import ProductEditApp from './components/merchant/ProductEditApp.vue'

// Create Vue app when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const productEditContainer = document.getElementById('product-edit-app')
    
    if (productEditContainer) {
        const app = createApp(ProductEditApp, {
            productId: productEditContainer.dataset.productId,
            backUrl: productEditContainer.dataset.backUrl || '/merchant/products'
        })
        
        app.mount('#product-edit-app')
    }
})
