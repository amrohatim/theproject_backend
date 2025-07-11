<template>
  <div class="vue-page-container">
    <div class="vue-content-container">
      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
          <a :href="backUrl" class="vue-btn vue-btn-secondary">
            <i class="fas fa-arrow-left w-4 h-4"></i>
            Back to Products
          </a>
          <div>
            <h1 class="vue-text-2xl">Edit Product</h1>
            <p class="vue-text-muted mt-1">Update product information, colors, and specifications</p>
          </div>
        </div>
        <div class="flex gap-2">
          <button type="button" class="vue-btn vue-btn-secondary" @click="previewProduct">
            Preview
          </button>
          <button type="button" class="vue-btn vue-btn-primary" @click="saveProduct" :disabled="saving">
            <i class="fas fa-save w-4 h-4"></i>
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>

      <!-- Stock Progress Indicator -->
      <div class="vue-card" style="background-color: var(--primary-blue-light); border-color: var(--gray-200);">
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
          <div v-if="isStockOverAllocated" class="mt-3 p-3 rounded-lg"
               style="background-color: var(--yellow-100); border: 1px solid var(--yellow-600);">
            <div class="flex items-center gap-2">
              <i class="fas fa-exclamation-triangle" style="color: var(--yellow-600);"></i>
              <p style="color: var(--yellow-800); font-size: 0.875rem; margin: 0;">
                You've allocated more stock than available. Please adjust color stock quantities.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="space-y-6">
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
                    <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="category_id" class="block vue-text-sm">
                        Category <span class="text-red-500">*</span>
                      </label>
                      <select id="category_id" 
                              v-model="productData.category_id" 
                              class="vue-form-control"
                              :class="{ 'border-red-500': errors.category_id }"
                              required>
                        <option value="">Select category</option>
                        <optgroup v-for="parentCategory in categories" 
                                  :key="parentCategory.id" 
                                  :label="parentCategory.name">
                          <option v-for="childCategory in parentCategory.children" 
                                  :key="childCategory.id" 
                                  :value="childCategory.id">
                            {{ childCategory.name }}
                          </option>
                        </optgroup>
                      </select>
                      <div v-if="errors.category_id" class="text-red-500 text-xs mt-1">{{ errors.category_id }}</div>
                    </div>

                    <div class="space-y-2">
                      <label for="branch_id" class="block vue-text-sm">
                        Branch <span class="text-red-500">*</span>
                      </label>
                      <select id="branch_id" 
                              v-model="productData.branch_id" 
                              class="vue-form-control"
                              :class="{ 'border-red-500': errors.branch_id }"
                              required>
                        <option value="">Select branch</option>
                        <option v-for="branch in branches" 
                                :key="branch.id" 
                                :value="branch.id">
                          {{ branch.name }}
                        </option>
                      </select>
                      <div v-if="errors.branch_id" class="text-red-500 text-xs mt-1">{{ errors.branch_id }}</div>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label for="description" class="block vue-text-sm">
                      Description
                    </label>
                    <textarea id="description" 
                              v-model="productData.description" 
                              rows="4"
                              class="vue-form-control"
                              :class="{ 'border-red-500': errors.description }">
                    </textarea>
                    <div v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description }}</div>
                  </div>
                </div>
              </div>

              <!-- Pricing & Inventory Card -->
              <div class="vue-card">
                <div class="p-6 border-b" style="border-color: var(--gray-200);">
                  <h3 class="flex items-center gap-2 vue-text-lg">
                    <i class="fas fa-dollar-sign w-5 h-5" style="color: var(--gray-600);"></i>
                    Pricing & Inventory
                  </h3>
                </div>
                <div class="p-6 space-y-4">
                  <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="price" class="block vue-text-sm">
                        Current Price <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="number" 
                               id="price" 
                               v-model.number="productData.price"
                               min="0" 
                               step="0.01"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.price }"
                               required>
                      </div>
                      <div v-if="errors.price" class="text-red-500 text-xs mt-1">{{ errors.price }}</div>
                    </div>

                    <div class="space-y-2">
                      <label for="original_price" class="block vue-text-sm">
                        Original Price
                      </label>
                      <div class="relative">
                        <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="number" 
                               id="original_price" 
                               v-model.number="productData.original_price"
                               min="0" 
                               step="0.01"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.original_price }">
                      </div>
                      <div v-if="errors.original_price" class="text-red-500 text-xs mt-1">{{ errors.original_price }}</div>
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label for="stock" class="block vue-text-sm">
                      Total Stock <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <i class="fas fa-warehouse absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                      <input type="number" 
                             id="stock" 
                             v-model.number="productData.stock"
                             min="0"
                             class="vue-form-control pl-10"
                             :class="{ 'border-red-500': errors.stock }"
                             required>
                    </div>
                    <p class="text-xs" style="color: var(--gray-500);">Total inventory to be allocated across color variants</p>
                    <div v-if="errors.stock" class="text-red-500 text-xs mt-1">{{ errors.stock }}</div>
                  </div>

                  <div class="flex items-center space-x-2">
                    <input id="is_available"
                           v-model="productData.is_available"
                           type="checkbox"
                           class="w-4 h-4 bg-gray-100 border-gray-300 rounded"
                           style="color: var(--primary-blue); --tw-ring-color: var(--primary-blue);">
                    <label for="is_available" class="vue-text-sm">
                      Available for purchase
                    </label>
                  </div>

                  <!-- Sale Badge -->
                  <div v-if="showSaleBadge" class="p-3 rounded-lg"
                       style="background-color: var(--primary-blue-light); border: 1px solid var(--gray-200);">
                    <div class="flex items-center gap-2">
                      <span class="px-2 py-1 text-xs font-medium rounded"
                            style="background-color: var(--gray-100); color: var(--primary-blue-hover);">
                        Sale
                      </span>
                      <span class="text-sm" style="color: var(--primary-blue);">
                        {{ salePercentage }}% off
                      </span>
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
                <p class="text-sm" style="color: var(--gray-600);">Add color variants with images and size options</p>
              </div>
              <button type="button" class="vue-btn vue-btn-primary" @click="addNewColor">
                <i class="fas fa-plus w-4 h-4"></i>
                Add Color
              </button>
            </div>

            <!-- Empty State for Colors -->
            <div v-if="productData.colors.length === 0" class="vue-card" style="border: 2px dashed var(--gray-300);">
              <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-palette w-12 h-12 mb-4" style="color: var(--gray-400);"></i>
                <h3 class="vue-text-lg mb-2">No colors added</h3>
                <p class="text-center mb-4" style="color: var(--gray-600);">
                  Add at least one color variant with an image to continue
                </p>
                <button type="button" class="vue-btn vue-btn-primary" @click="addNewColor">
                  <i class="fas fa-plus w-4 h-4"></i>
                  Add Your First Color
                </button>
              </div>
            </div>

            <!-- Color Cards Container -->
            <div class="grid gap-6">
              <ColorVariantCard
                v-for="(color, index) in productData.colors"
                :key="color.id || index"
                :color="color"
                :index="index"
                :is-default="color.is_default"
                :product-id="productId"
                @update="updateColor"
                @remove="removeColor"
                @set-default="setDefaultColor"
                @image-upload="handleImageUpload"
                @sizes-updated="handleColorSizesUpdated"
              />
            </div>
          </div>

          <!-- Specifications Tab -->
          <div v-show="activeTab === 'specifications'" class="vue-tab-content space-y-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="vue-text-lg">Product Specifications</h3>
                <p class="text-sm" style="color: var(--gray-600);">Add technical details and product features</p>
              </div>
              <button type="button" class="vue-btn vue-btn-primary" @click="addNewSpecification">
                <i class="fas fa-plus w-4 h-4"></i>
                Add Specification
              </button>
            </div>

            <div class="vue-card">
              <div class="p-6">
                <div class="space-y-4">
                  <div v-if="productData.specifications.length === 0" class="text-center py-8">
                    <i class="fas fa-file-text w-12 h-12 mx-auto mb-4" style="color: var(--gray-400);"></i>
                    <h3 class="vue-text-lg mb-2">No specifications added</h3>
                    <p class="mb-4" style="color: var(--gray-600);">
                      Add product specifications to provide detailed information to customers
                    </p>
                    <button type="button" class="vue-btn vue-btn-primary" @click="addNewSpecification">
                      <i class="fas fa-plus w-4 h-4"></i>
                      Add First Specification
                    </button>
                  </div>

                  <SpecificationItem
                    v-for="(spec, index) in productData.specifications"
                    :key="spec.id || index"
                    :specification="spec"
                    :index="index"
                    @update="updateSpecification"
                    @remove="removeSpecification"
                  />
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import ColorVariantCard from './ColorVariantCard.vue'
import SpecificationItem from './SpecificationItem.vue'

export default {
  name: 'ProductEditApp',
  components: {
    ColorVariantCard,
    SpecificationItem
  },
  props: {
    productId: {
      type: [String, Number],
      required: true
    },
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
      id: null,
      name: '',
      category_id: '',
      branch_id: '',
      price: 0,
      original_price: null,
      stock: 0,
      description: '',
      is_available: true,
      colors: [],
      specifications: []
    })

    const categories = ref([])
    const branches = ref([])

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
      if (!productData.stock || productData.stock === 0) return 0
      return Math.min((totalAllocatedStock.value / productData.stock) * 100, 100)
    })

    const isStockOverAllocated = computed(() => {
      return totalAllocatedStock.value > productData.stock
    })

    const showSaleBadge = computed(() => {
      return productData.original_price &&
             productData.original_price > productData.price &&
             productData.price > 0
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

    const fetchProductData = async () => {
      try {
        loading.value = true
        const response = await window.axios.get(`/merchant/products/${props.productId}/edit-data`)

        // Populate product data
        Object.assign(productData, response.data.product)
        categories.value = response.data.parentCategories || []
        branches.value = response.data.branches || []

        // Ensure colors and specifications arrays exist
        if (!productData.colors) productData.colors = []
        if (!productData.specifications) productData.specifications = []

        // Process colors to ensure they have the right structure for Vue
        productData.colors = productData.colors.map(color => ({
          ...color,
          imageFile: null,
          imagePreview: null,
          sizes: color.sizes || [] // Ensure sizes array exists
        }))

      } catch (error) {
        console.error('Error fetching product data:', error)
        alert('Failed to load product data. Please refresh the page.')
      } finally {
        loading.value = false
      }
    }

    const validateForm = () => {
      // Clear previous errors
      Object.keys(errors).forEach(key => delete errors[key])

      let isValid = true

      // Basic validation
      if (!productData.name?.trim()) {
        errors.name = 'Product name is required'
        isValid = false
      }

      if (!productData.category_id) {
        errors.category_id = 'Category is required'
        isValid = false
      }

      if (!productData.branch_id) {
        errors.branch_id = 'Branch is required'
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

      // Color validation
      if (productData.colors.length === 0) {
        errors.colors = 'At least one color variant is required'
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
        if (errors.name || errors.category_id || errors.branch_id || errors.price || errors.stock) {
          activeTab.value = 'basic'
        } else if (errors.colors) {
          activeTab.value = 'colors'
        }
        return
      }

      try {
        saving.value = true

        // Prepare form data for submission
        const formData = new FormData()

        // Add basic product data
        Object.keys(productData).forEach(key => {
          if (key !== 'colors' && key !== 'specifications' && productData[key] !== null) {
            formData.append(key, productData[key])
          }
        })

        // Add colors data
        productData.colors.forEach((color, index) => {
          Object.keys(color).forEach(key => {
            if (key === 'imageFile' && color[key]) {
              formData.append(`color_images[${index}]`, color[key])
            } else if (key !== 'imageFile' && color[key] !== null) {
              formData.append(`colors[${index}][${key}]`, color[key])
            }
          })
        })

        // Add specifications data
        productData.specifications.forEach((spec, index) => {
          Object.keys(spec).forEach(key => {
            if (spec[key] !== null) {
              formData.append(`specifications[${index}][${key}]`, spec[key])
            }
          })
        })

        // Add method override for PUT request
        formData.append('_method', 'PUT')

        const response = await window.axios.post(`/merchant/products/${props.productId}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        // Handle success
        if (response.data.success || response.status === 200) {
          // Show success message
          alert('Product updated successfully!')
          // Optionally redirect or refresh data
          window.location.href = props.backUrl
        }

      } catch (error) {
        console.error('Error saving product:', error)

        // Handle validation errors
        if (error.response?.data?.errors) {
          Object.assign(errors, error.response.data.errors)
        } else {
          alert('An error occurred while saving the product. Please try again.')
        }
      } finally {
        saving.value = false
      }
    }

    const previewProduct = () => {
      // Open product preview in new tab
      window.open(`/merchant/products/${props.productId}`, '_blank')
    }

    // Color management methods
    const addNewColor = () => {
      const newColor = {
        id: null,
        name: '',
        color_code: '#000000',
        price_adjustment: 0,
        stock: 0,
        display_order: productData.colors.length,
        is_default: productData.colors.length === 0,
        image: null,
        imageFile: null,
        sizes: [] // Initialize empty sizes array
      }
      productData.colors.push(newColor)
    }

    const updateColor = (index, updatedColor) => {
      if (index >= 0 && index < productData.colors.length) {
        Object.assign(productData.colors[index], updatedColor)
      }
    }

    const removeColor = (index) => {
      if (index >= 0 && index < productData.colors.length) {
        if (productData.colors.length === 1) {
          alert('At least one color variant is required.')
          return
        }

        const removedColor = productData.colors[index]
        productData.colors.splice(index, 1)

        // If removed color was default, set first color as default
        if (removedColor.is_default && productData.colors.length > 0) {
          productData.colors[0].is_default = true
        }
      }
    }

    const setDefaultColor = (index) => {
      if (index >= 0 && index < productData.colors.length) {
        // Remove default from all colors
        productData.colors.forEach(color => {
          color.is_default = false
        })
        // Set new default
        productData.colors[index].is_default = true
      }
    }

    const handleImageUpload = (index, file) => {
      if (index >= 0 && index < productData.colors.length) {
        productData.colors[index].imageFile = file

        // Create preview URL
        if (file) {
          const reader = new FileReader()
          reader.onload = (e) => {
            productData.colors[index].imagePreview = e.target.result
          }
          reader.readAsDataURL(file)
        }
      }
    }

    const handleColorSizesUpdated = (colorIndex, sizes) => {
      if (colorIndex >= 0 && colorIndex < productData.colors.length) {
        // Store sizes data in the color object
        productData.colors[colorIndex].sizes = sizes

        // Optionally, you could trigger a save or validation here
        console.log(`Sizes updated for color ${colorIndex}:`, sizes)
      }
    }

    // Specification management methods
    const addNewSpecification = () => {
      const newSpec = {
        id: null,
        key: '',
        value: '',
        display_order: productData.specifications.length
      }
      productData.specifications.push(newSpec)
    }

    const updateSpecification = (index, updatedSpec) => {
      if (index >= 0 && index < productData.specifications.length) {
        Object.assign(productData.specifications[index], updatedSpec)
      }
    }

    const removeSpecification = (index) => {
      if (index >= 0 && index < productData.specifications.length) {
        productData.specifications.splice(index, 1)
      }
    }

    // Watchers
    watch(() => productData.stock, (newStock) => {
      // Validate stock allocation when total stock changes
      if (totalAllocatedStock.value > newStock) {
        console.warn('Stock over-allocated')
      }
    })

    // Lifecycle
    onMounted(() => {
      fetchProductData()
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

      // Computed
      totalAllocatedStock,
      stockProgressPercentage,
      isStockOverAllocated,
      showSaleBadge,
      salePercentage,

      // Methods
      getTabClasses,
      fetchProductData,
      validateForm,
      saveProduct,
      previewProduct,
      addNewColor,
      updateColor,
      removeColor,
      setDefaultColor,
      handleImageUpload,
      handleColorSizesUpdated,
      addNewSpecification,
      updateSpecification,
      removeSpecification
    }
  }
}
</script>

<style scoped>
/* Use merchant dashboard color variables for consistency */

/* Main page background with gradient */
.vue-page-container {
  min-height: 100vh;
  background: linear-gradient(to bottom right, var(--gray-50), var(--gray-100));
  padding: 1rem;
}

@media (min-width: 768px) {
  .vue-page-container {
    padding: 1.5rem;
  }
}

/* Main content container */
.vue-content-container {
  max-width: 80rem; /* max-w-7xl */
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 1.5rem; /* space-y-6 */
}

/* Modern card styling - use merchant dashboard card styles */
.vue-card {
  background-color: white;
  border: 1px solid var(--gray-200);
  border-radius: 0.5rem; /* rounded-lg */
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  overflow: hidden;
}

.vue-card-body {
  padding: 1.5rem; /* p-6 */
}

/* Modern form controls - use merchant dashboard variables */
.vue-form-control {
  width: 100%;
  padding: 0.5rem 0.75rem; /* px-3 py-2 */
  border: 1px solid var(--gray-300);
  border-radius: 0.5rem; /* rounded-lg */
  background-color: white;
  color: var(--gray-900);
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.vue-form-control:focus {
  outline: none;
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 2px var(--primary-blue-light);
}

.vue-form-control::placeholder {
  color: var(--gray-400);
}

/* Modern buttons - use merchant dashboard button styles */
.vue-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 8px 16px;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.5rem; /* rounded-lg */
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.vue-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.vue-btn-primary {
  background-color: var(--primary-blue);
  color: white;
}

.vue-btn-primary:hover:not(:disabled) {
  background-color: var(--primary-blue-hover);
  color: white;
}

.vue-btn-secondary {
  background-color: var(--gray-300);
  color: var(--gray-700);
  border: 1px solid var(--gray-300);
}

.vue-btn-secondary:hover {
  background-color: var(--gray-100);
  color: var(--gray-700);
}

/* Tab Navigation Styling - use merchant dashboard colors */
.vue-tab-button {
  border: none;
  background: none;
  cursor: pointer;
  outline: none;
}

.vue-tab-button.active {
  color: var(--primary-blue) !important;
  border-bottom: 2px solid var(--primary-blue) !important;
  background-color: var(--primary-blue-light) !important;
}

.vue-tab-button:not(.active):hover {
  color: var(--gray-900) !important;
  background-color: var(--gray-50) !important;
}

/* Typography - use merchant dashboard colors */
.vue-text-2xl {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--gray-900);
}

@media (min-width: 768px) {
  .vue-text-2xl {
    font-size: 1.875rem; /* text-3xl on md+ */
  }
}

.vue-text-lg {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--gray-900);
}

.vue-text-sm {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-700);
}

.vue-text-muted {
  color: var(--gray-600);
}

/* Utility classes for layout */
.flex { display: flex; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.gap-2 { gap: 0.5rem; }
.gap-4 { gap: 1rem; }
.gap-6 { gap: 1.5rem; }
.space-y-6 > * + * { margin-top: 1.5rem; }
.space-y-4 > * + * { margin-top: 1rem; }
.space-y-2 > * + * { margin-top: 0.5rem; }
.space-x-2 > * + * { margin-left: 0.5rem; }
.w-full { width: 100%; }
.h-2 { height: 0.5rem; }
.w-4 { width: 1rem; }
.h-4 { height: 1rem; }
.w-5 { width: 1.25rem; }
.h-5 { height: 1.25rem; }
.w-12 { width: 3rem; }
.h-12 { height: 3rem; }
.rounded-full { border-radius: 9999px; }
.rounded-t-lg { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; }
.border-b { border-bottom-width: 1px; }
.border-slate-200 { border-color: var(--gray-200); }
.px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
.py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
.p-6 { padding: 1.5rem; }
.p-3 { padding: 0.75rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-4 { margin-bottom: 1rem; }
.mt-1 { margin-top: 0.25rem; }
.mt-3 { margin-top: 0.75rem; }
.mx-auto { margin-left: auto; margin-right: auto; }
.transition-all { transition: all 0.2s ease; }
.duration-300 { transition-duration: 300ms; }
.grid { display: grid; }
.grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.text-center { text-align: center; }
.text-slate-400 { color: var(--gray-400); }
.text-slate-500 { color: var(--gray-500); }
.text-slate-600 { color: var(--gray-600); }
.text-slate-700 { color: var(--gray-700); }
.text-slate-900 { color: var(--gray-900); }
.text-red-500 { color: var(--red-600); }
.text-red-600 { color: var(--red-600); }
.text-primary-600 { color: var(--primary-blue); }
.text-primary-700 { color: var(--primary-blue-hover); }
.text-primary-800 { color: var(--primary-blue-hover); }
.text-xs { font-size: 0.75rem; }
.text-sm { font-size: 0.875rem; }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.relative { position: relative; }
.absolute { position: absolute; }
.left-3 { left: 0.75rem; }
.top-1\/2 { top: 50%; }
.transform { transform: translateX(var(--tw-translate-x)) translateY(var(--tw-translate-y)); }
.-translate-y-1\/2 { --tw-translate-y: -50%; }
.pl-10 { padding-left: 2.5rem; }
.hidden { display: none; }
.block { display: block; }

@media (min-width: 640px) {
  .sm\:flex-row { flex-direction: row; }
  .sm\:items-center { align-items: center; }
  .sm\:justify-between { justify-content: space-between; }
  .sm\:inline { display: inline; }
}

@media (min-width: 1024px) {
  .lg\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
</style>
