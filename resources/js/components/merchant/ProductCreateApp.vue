<template>
  <div class="min-h-screen" style="background-color: var(--gray-50);">
    <!-- Loading State -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading product creation form...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="vue-text-2xl">Create New Product</h1>
          <p class="text-sm text-gray-600">Add a new product to your inventory</p>
        </div>
        <a :href="backUrl" class="vue-btn vue-btn-secondary">
          <i class="fas fa-arrow-left w-4 h-4"></i>
          Back to Products
        </a>
      </div>

      <!-- Tab Navigation -->
      <div class="flex border-b bg-white rounded-t-lg" style="border-color: var(--gray-200);">
        <button type="button"
                v-for="tab in tabs"
                :key="tab.id"
                class="vue-tab-button flex items-center gap-2 px-6 py-3 font-medium text-sm transition-colors"
                :class="getTabClasses(tab.id)"
                @click="activeTab = tab.id">
          <i :class="tab.icon + ' w-4 h-4'"></i>
          <span class="hidden sm:inline">{{ tab.label }}</span>
        </button>
      </div>

      <!-- Form Container -->
      <form @submit.prevent="saveProduct">
        <!-- Basic Information Tab -->
        <div v-show="activeTab === 'basic'" class="vue-tab-content space-y-6">
          <div class="grid lg:grid-cols-2 gap-6">
            <!-- Product Details Card -->
            <div class="vue-card">
              <div class="p-6 border-b" style="border-color: var(--gray-200);">
                <h3 class="flex items-center gap-2 vue-text-lg">
                  <i class="fas fa-box w-5 h-5" style="color: var(--gray-600);"></i>
                  Product Details
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="space-y-2">
                  <label for="name" class="block vue-text-sm">
                    Product Name <span class="text-red-500">*</span>
                  </label>
                  <input type="text" 
                         id="name" 
                         v-model="productData.name"
                         class="vue-form-control"
                         :class="{ 'border-red-500': errors.name }"
                         required>
                  <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>
                </div>

                <div class="space-y-2">
                  <label for="category_id" class="block vue-text-sm">
                    Category <span class="text-red-500">*</span>
                  </label>
                  <select id="category_id" 
                          v-model="productData.category_id"
                          class="vue-form-control"
                          :class="{ 'border-red-500': errors.category_id }"
                          required>
                    <option value="">Select Category</option>
                    <optgroup v-for="parent in categories" :key="parent.id" :label="parent.name">
                      <option :value="parent.id">{{ parent.name }}</option>
                      <option v-for="child in parent.children" :key="child.id" :value="child.id">
                        {{ child.name }}
                      </option>
                    </optgroup>
                  </select>
                  <p v-if="errors.category_id" class="text-red-500 text-sm">{{ errors.category_id }}</p>
                </div>

                <div class="space-y-2">
                  <label for="description" class="block vue-text-sm">Description</label>
                  <textarea id="description" 
                            v-model="productData.description"
                            rows="4"
                            class="vue-form-control"
                            placeholder="Enter product description..."></textarea>
                </div>



                <div class="flex items-center space-x-2">
                  <input type="checkbox" 
                         id="is_available" 
                         v-model="productData.is_available"
                         class="vue-checkbox">
                  <label for="is_available" class="vue-text-sm">Product is available for sale</label>
                </div>
              </div>
            </div>

            <!-- Pricing & Stock Card -->
            <div class="vue-card">
              <div class="p-6 border-b" style="border-color: var(--gray-200);">
                <h3 class="flex items-center gap-2 vue-text-lg">
                  <i class="fas fa-dollar-sign w-5 h-5" style="color: var(--gray-600);"></i>
                  Pricing & Stock
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label for="price" class="block vue-text-sm">
                      Price <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           id="price" 
                           v-model.number="productData.price"
                           class="vue-form-control"
                           :class="{ 'border-red-500': errors.price }"
                           step="0.01"
                           min="0"
                           required>
                    <p v-if="errors.price" class="text-red-500 text-sm">{{ errors.price }}</p>
                  </div>

                  <div class="space-y-2">
                    <label for="original_price" class="block vue-text-sm">Original Price</label>
                    <input type="number" 
                           id="original_price" 
                           v-model.number="productData.original_price"
                           class="vue-form-control"
                           step="0.01"
                           min="0">
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="stock" class="block vue-text-sm">
                    Total Stock <span class="text-red-500">*</span>
                  </label>
                  <input type="number" 
                         id="stock" 
                         v-model.number="productData.stock"
                         class="vue-form-control"
                         :class="{ 'border-red-500': errors.stock }"
                         min="0"
                         required>
                  <p v-if="errors.stock" class="text-red-500 text-sm">{{ errors.stock }}</p>
                </div>

                <!-- Sale Badge Preview -->
                <div v-if="showSaleBadge" class="p-3 rounded-lg" style="background-color: var(--green-50); border: 1px solid var(--green-200);">
                  <div class="flex items-center gap-2">
                    <i class="fas fa-tag text-green-600"></i>
                    <span class="text-green-700 font-medium">{{ salePercentage }}% OFF</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Colors & Images Tab -->
        <div v-show="activeTab === 'colors'" class="vue-tab-content space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="vue-text-lg">Product Colors</h3>
              <p class="text-sm text-gray-600">Add color variants with images and size options</p>
            </div>
            <button type="button" @click="addNewColor" class="vue-btn vue-btn-primary">
              <i class="fas fa-plus w-4 h-4"></i>
              Add Color
            </button>
          </div>

          <!-- Colors List -->
          <div v-if="productData.colors.length > 0" class="space-y-4">
            <ColorVariantCard
              v-for="(color, index) in productData.colors"
              :key="index"
              :color="color"
              :index="index"
              :is-default="color.is_default"
              :product-id="'new'"
              :general-stock="productData.stock"
              :all-colors="productData.colors"
              @update="updateColor"
              @remove="removeColor"
              @set-default="setDefaultColor"
              @image-upload="handleImageUpload"
              @sizes-updated="handleColorSizesUpdated"
              @stock-corrected="handleStockCorrected"
            />
          </div>

          <!-- Empty State -->
          <div v-else class="vue-card" style="border: 2px dashed var(--gray-300);">
            <div class="flex flex-col items-center justify-center py-12">
              <i class="fas fa-palette w-12 h-12 text-gray-400 mb-4"></i>
              <h3 class="vue-text-lg text-gray-600 mb-2">No colors added yet</h3>
              <p class="text-gray-500 mb-4">Add color variants to make your product more appealing</p>
              <button type="button" @click="addNewColor" class="vue-btn vue-btn-primary">
                <i class="fas fa-plus w-4 h-4"></i>
                Add First Color
              </button>
            </div>
          </div>

          <!-- Stock Progress Indicator -->
          <div v-if="productData.stock > 0" class="vue-card" style="background-color: var(--primary-blue-light); border-color: var(--gray-200);">
            <div class="vue-card-body">
              <div class="flex items-center justify-between mb-2">
                <span class="vue-text-sm" style="color: var(--primary-blue-hover);">Stock Allocation Progress</span>
                <span class="vue-text-sm" style="color: var(--primary-blue);">
                  <span>{{ totalAllocatedStock }}</span> / {{ productData.stock }} units allocated
                </span>
              </div>
              <div class="w-full rounded-full h-2" style="background-color: var(--gray-200);">
                <div class="h-2 rounded-full transition-all duration-300"
                     style="background-color: var(--primary-blue);"
                     :style="{ width: stockProgressPercentage + '%' }">
                </div>
              </div>
              <div v-if="isStockOverAllocated" class="mt-2 text-red-600 text-sm">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Stock over-allocated! Please adjust color stock values.
              </div>
            </div>
          </div>

          <!-- Stock Summary -->
          <div v-if="productData.colors.length > 0 && productData.stock > 0" class="vue-card">
            <div class="p-6">
              <h4 class="vue-text-lg mb-4">Stock Allocation Summary</h4>
              <div class="space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">Total Stock:</span>
                  <span class="font-medium">{{ productData.stock }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">Allocated Stock:</span>
                  <span class="font-medium" :class="{ 'text-red-600': isStockOverAllocated }">
                    {{ totalAllocatedStock }}
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">Remaining Stock:</span>
                  <span class="font-medium" :class="{ 'text-red-600': isStockOverAllocated }">
                    {{ productData.stock - totalAllocatedStock }}
                  </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div class="h-2 rounded-full transition-all duration-300"
                       :class="isStockOverAllocated ? 'bg-red-500' : 'bg-blue-500'"
                       :style="{ width: stockProgressPercentage + '%' }"></div>
                </div>
                <p v-if="isStockOverAllocated" class="text-red-600 text-sm">
                  <i class="fas fa-exclamation-triangle mr-1"></i>
                  Stock over-allocated! Please adjust color stock values.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Specifications Tab -->
        <div v-show="activeTab === 'specifications'" class="vue-tab-content space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="vue-text-lg">Product Specifications</h3>
              <p class="text-sm text-gray-600">Add detailed specifications for your product</p>
            </div>
            <button type="button" @click="addNewSpecification" class="vue-btn vue-btn-primary">
              <i class="fas fa-plus w-4 h-4"></i>
              Add Specification
            </button>
          </div>

          <!-- Specifications List -->
          <div v-if="productData.specifications.length > 0" class="space-y-4">
            <SpecificationItem
              v-for="(spec, index) in productData.specifications"
              :key="index"
              :specification="spec"
              :index="index"
              @update="updateSpecification"
              @remove="removeSpecification"
            />
          </div>

          <!-- Empty State -->
          <div v-else class="vue-card" style="border: 2px dashed var(--gray-300);">
            <div class="flex flex-col items-center justify-center py-12">
              <i class="fas fa-file-text w-12 h-12 text-gray-400 mb-4"></i>
              <h3 class="vue-text-lg text-gray-600 mb-2">No specifications added yet</h3>
              <p class="text-gray-500 mb-4">Add specifications to provide detailed product information</p>
              <button type="button" @click="addNewSpecification" class="vue-btn vue-btn-primary">
                <i class="fas fa-plus w-4 h-4"></i>
                Add First Specification
              </button>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 mt-6">
          <a :href="backUrl" class="vue-btn vue-btn-secondary">
            <i class="fas fa-times w-4 h-4"></i>
            Cancel
          </a>
          <button type="submit" class="vue-btn vue-btn-primary" :disabled="saving">
            <i class="fas fa-save w-4 h-4"></i>
            <span v-if="saving">Creating...</span>
            <span v-else>Create Product</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
      <div class="modal-content" @click.stop>
        <div class="text-center">
          <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
          <h3 class="vue-text-lg mb-2">Product Created Successfully!</h3>
          <p class="text-gray-600 mb-4">Your product has been created and is now available in your inventory.</p>
          <div class="flex gap-3 justify-center">
            <a :href="backUrl" class="vue-btn vue-btn-secondary">View Products</a>
            <button @click="createAnother" class="vue-btn vue-btn-primary">Create Another</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Modal -->
    <div v-if="showErrorModal" class="modal-overlay" @click="closeErrorModal">
      <div class="modal-content" @click.stop>
        <div class="text-center">
          <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
          <h3 class="vue-text-lg mb-2">Error Creating Product</h3>
          <p class="text-gray-600 mb-4">{{ errorMessage }}</p>
          <button @click="closeErrorModal" class="vue-btn vue-btn-primary">Try Again</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import ColorVariantCard from './ColorVariantCard.vue'
import SpecificationItem from './SpecificationItem.vue'

export default {
  name: 'ProductCreateApp',
  components: {
    ColorVariantCard,
    SpecificationItem
  },
  props: {
    backUrl: {
      type: String,
      default: '/merchant/products'
    }
  },
  setup(props) {
    // Reactive data
    const activeTab = ref('basic')
    const saving = ref(false)
    const loading = ref(true)
    const errors = reactive({})

    const productData = reactive({
      name: '',
      category_id: '',
      price: 0,
      original_price: null,
      stock: 0,
      description: '',
      is_available: true,
      display_order: 0,
      colors: [],
      specifications: []
    })

    const categories = ref([])
    const branches = ref([])

    // Modal states
    const showSuccessModal = ref(false)
    const showErrorModal = ref(false)
    const errorMessage = ref('')

    // Tab configuration
    const tabs = [
      { id: 'basic', label: 'Basic Info', icon: 'fas fa-box' },
      { id: 'colors', label: 'Colors & Images', icon: 'fas fa-palette' },
      { id: 'specifications', label: 'Specifications', icon: 'fas fa-file-text' }
    ]

    // Computed properties
    const totalAllocatedStock = computed(() => {
      return productData.colors.reduce((total, color) => {
        return total + (parseInt(color.stock) || 0)
      }, 0)
    })

    const stockProgressPercentage = computed(() => {
      if (productData.stock === 0) return 0
      return Math.min((totalAllocatedStock.value / productData.stock) * 100, 100)
    })

    const isStockOverAllocated = computed(() => {
      return totalAllocatedStock.value > productData.stock
    })

    const showSaleBadge = computed(() => {
      return productData.original_price && productData.price && productData.original_price > productData.price
    })

    const salePercentage = computed(() => {
      if (!showSaleBadge.value) return 0
      return Math.round(((productData.original_price - productData.price) / productData.original_price) * 100)
    })

    // Methods
    const getTabClasses = (tabId) => {
      const baseClasses = 'vue-tab-button flex items-center gap-2 px-6 py-3 font-medium text-sm transition-colors'
      if (activeTab.value === tabId) {
        return baseClasses + ' active'
      }
      return baseClasses + ' text-gray-600 hover:text-gray-900 hover:bg-gray-50'
    }

    const fetchInitialData = async () => {
      try {
        const response = await fetch('/merchant/products/create-data')
        const data = await response.json()

        categories.value = data.categories || []
        branches.value = data.branches || []

        loading.value = false
      } catch (error) {
        console.error('Error fetching initial data:', error)
        errorMessage.value = 'Failed to load form data. Please refresh the page.'
        showErrorModal.value = true
        loading.value = false
      }
    }

    const validateForm = () => {
      // Clear previous errors
      Object.keys(errors).forEach(key => delete errors[key])

      let isValid = true

      // Basic validation
      if (!productData.name.trim()) {
        errors.name = 'Product name is required'
        isValid = false
      }

      if (!productData.category_id) {
        errors.category_id = 'Category is required'
        isValid = false
      }

      if (!productData.price || productData.price <= 0) {
        errors.price = 'Price must be greater than 0'
        isValid = false
      }

      if (!productData.stock || productData.stock < 0) {
        errors.stock = 'Stock must be 0 or greater'
        isValid = false
      }

      // Colors validation
      if (productData.colors.length === 0) {
        errors.colors = 'At least one color is required'
        isValid = false
      }

      // Check if at least one color has an image
      const hasColorWithImage = productData.colors.some(color => color.image || color.imageFile)
      if (!hasColorWithImage) {
        errors.colors = 'At least one color must have an image'
        isValid = false
      }

      return isValid
    }

    const saveProduct = async () => {
      if (!validateForm()) {
        // Switch to the tab with errors
        if (errors.name || errors.category_id || errors.price || errors.stock) {
          activeTab.value = 'basic'
        } else if (errors.colors) {
          activeTab.value = 'colors'
        }
        return
      }

      saving.value = true

      try {
        const formData = new FormData()

        // Add basic product data
        formData.append('name', productData.name)
        formData.append('category_id', productData.category_id)
        formData.append('price', productData.price)
        if (productData.original_price) {
          formData.append('original_price', productData.original_price)
        }
        formData.append('stock', productData.stock)
        if (productData.description) {
          formData.append('description', productData.description)
        }
        formData.append('is_available', productData.is_available ? '1' : '0')
        formData.append('display_order', productData.display_order || 0)

        // Add colors data
        productData.colors.forEach((color, index) => {
          formData.append(`colors[${index}][name]`, color.name)
          formData.append(`colors[${index}][color_code]`, color.color_code || '')
          formData.append(`colors[${index}][price_adjustment]`, color.price_adjustment || 0)
          formData.append(`colors[${index}][stock]`, color.stock || 0)
          formData.append(`colors[${index}][display_order]`, color.display_order || index)
          formData.append(`colors[${index}][is_default]`, color.is_default ? '1' : '0')

          if (color.imageFile) {
            formData.append(`colors[${index}][image]`, color.imageFile)
          }

          // Add sizes data if available
          if (color.sizes && color.sizes.length > 0) {
            color.sizes.forEach((size, sizeIndex) => {
              formData.append(`colors[${index}][sizes][${sizeIndex}][name]`, size.name)
              formData.append(`colors[${index}][sizes][${sizeIndex}][value]`, size.value || '')
              formData.append(`colors[${index}][sizes][${sizeIndex}][stock]`, size.stock || 0)
              formData.append(`colors[${index}][sizes][${sizeIndex}][price_adjustment]`, size.price_adjustment || 0)
              formData.append(`colors[${index}][sizes][${sizeIndex}][display_order]`, size.display_order || sizeIndex)
            })
          }
        })

        // Add specifications data
        productData.specifications.forEach((spec, index) => {
          if (spec.key && spec.value) {
            formData.append(`specifications[${index}][key]`, spec.key)
            formData.append(`specifications[${index}][value]`, spec.value)
            formData.append(`specifications[${index}][display_order]`, spec.display_order || index)
          }
        })

        const response = await fetch('/merchant/products', {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        if (response.ok) {
          showSuccessModal.value = true
        } else {
          const errorData = await response.json()
          errorMessage.value = errorData.message || 'Failed to create product'
          showErrorModal.value = true
        }
      } catch (error) {
        console.error('Error creating product:', error)
        errorMessage.value = 'An unexpected error occurred. Please try again.'
        showErrorModal.value = true
      } finally {
        saving.value = false
      }
    }

    const addNewColor = () => {
      const newColor = {
        name: '',
        color_code: '',
        price_adjustment: 0,
        stock: 0,
        display_order: productData.colors.length,
        is_default: productData.colors.length === 0, // First color is default
        image: null,
        imageFile: null,
        sizes: []
      }
      productData.colors.push(newColor)
    }

    const updateColor = (index, updatedColor) => {
      // Force Vue reactivity by updating properties individually
      Object.assign(productData.colors[index], updatedColor)
    }

    const removeColor = (index) => {
      const removedColor = productData.colors[index]
      productData.colors.splice(index, 1)

      // If we removed the default color, make the first remaining color default
      if (removedColor.is_default && productData.colors.length > 0) {
        productData.colors[0].is_default = true
      }
    }

    const setDefaultColor = (index) => {
      productData.colors.forEach((color, i) => {
        color.is_default = i === index
      })
    }

    const handleImageUpload = (index, file) => {
      if (productData.colors[index]) {
        productData.colors[index].imageFile = file

        // Create preview URL
        const reader = new FileReader()
        reader.onload = (e) => {
          productData.colors[index].image = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }

    const handleColorSizesUpdated = (colorIndex, sizes) => {
      if (productData.colors[colorIndex]) {
        productData.colors[colorIndex].sizes = sizes
      }
    }

    const handleStockCorrected = (data) => {
      // Handle stock correction feedback if needed
      console.log('Stock corrected:', data)
    }

    const addNewSpecification = () => {
      const newSpec = {
        key: '',
        value: '',
        display_order: productData.specifications.length
      }
      productData.specifications.push(newSpec)
    }

    const updateSpecification = (index, updatedSpec) => {
      productData.specifications[index] = updatedSpec
    }

    const removeSpecification = (index) => {
      productData.specifications.splice(index, 1)
    }

    const closeSuccessModal = () => {
      showSuccessModal.value = false
    }

    const closeErrorModal = () => {
      showErrorModal.value = false
    }

    const createAnother = () => {
      // Reset form data
      productData.name = ''
      productData.category_id = ''
      productData.price = 0
      productData.original_price = null
      productData.stock = 0
      productData.description = ''
      productData.is_available = true
      productData.display_order = 0
      productData.colors = []
      productData.specifications = []

      // Clear errors
      Object.keys(errors).forEach(key => delete errors[key])

      // Reset to first tab
      activeTab.value = 'basic'

      // Close modal
      showSuccessModal.value = false
    }

    // Lifecycle
    onMounted(() => {
      fetchInitialData()
    })

    return {
      // Reactive data
      activeTab,
      saving,
      loading,
      errors,
      productData,
      categories,
      branches,
      tabs,
      showSuccessModal,
      showErrorModal,
      errorMessage,

      // Computed
      totalAllocatedStock,
      stockProgressPercentage,
      isStockOverAllocated,
      showSaleBadge,
      salePercentage,

      // Methods
      getTabClasses,
      fetchInitialData,
      validateForm,
      saveProduct,
      addNewColor,
      updateColor,
      removeColor,
      setDefaultColor,
      handleImageUpload,
      handleColorSizesUpdated,
      handleStockCorrected,
      addNewSpecification,
      updateSpecification,
      removeSpecification,
      closeSuccessModal,
      closeErrorModal,
      createAnother
    }
  }
}
</script>

<style scoped>
/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 2rem;
  border-radius: 0.5rem;
  max-width: 500px;
  width: 90%;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Tab styles */
.vue-tab-button.active {
  color: var(--primary-blue);
  border-bottom: 2px solid var(--primary-blue);
  background-color: var(--primary-blue-light);
}

.vue-tab-content {
  background: white;
  border-radius: 0 0 0.5rem 0.5rem;
  padding: 1.5rem;
}
</style>
