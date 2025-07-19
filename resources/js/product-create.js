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
        
        app.mount('#product-create-app')
    }
})
