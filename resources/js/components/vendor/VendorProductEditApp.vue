<template>
  <div class="vendor-product-edit-app">
    <!-- Loading State -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading product editor...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="container mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Edit Product</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">Update product information, colors, and specifications</p>
          </div>
          <div class="flex gap-2">
            <a :href="backUrl" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
              <i class="fas fa-arrow-left mr-2"></i> Back to Products
            </a>
            <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150" @click="saveProduct" :disabled="saving">
              <i class="fas fa-save mr-2"></i>
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Stock Progress Indicator -->
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Stock Allocation Progress</span>
          <span class="text-sm text-blue-600 dark:text-blue-400">
            <span>{{ totalAllocatedStock }}</span> / {{ productData.stock }} units allocated
          </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
               :style="{ width: stockProgressPercentage + '%' }">
          </div>
        </div>
        <div v-if="isStockOverAllocated" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
          <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
              You've allocated more stock than available. Please adjust color stock quantities.
            </p>
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
          <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
            <button
              v-for="tab in tabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="getTabClasses(tab.id)"
              class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            >
              <i :class="tab.icon" class="mr-2"></i>
              {{ tab.label }}
            </button>
          </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">

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
                              required
                              @change="validateCategorySelection">
                        <option value="">Select Category</option>
                        <optgroup v-for="parent in categories" :key="parent.id" :label="parent.name">
                          <option v-for="child in parent.children"
                                  :key="child.id"
                                  :value="child.id"
                                  :disabled="!child.is_selectable"
                                  :class="{ 'text-gray-400': !child.is_selectable }">
                            &nbsp;&nbsp;{{ child.name }}
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
                        <option value="">Select Branch</option>
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

                  <div class="grid grid-cols-2 gap-4">
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

                    <div class="space-y-2">
                      <label for="display_order" class="block vue-text-sm">
                        Display Order
                      </label>
                      <div class="relative">
                        <i class="fas fa-sort-numeric-up absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="number"
                               id="display_order"
                               v-model.number="productData.display_order"
                               min="0"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.display_order }">
                      </div>
                      <p class="text-xs" style="color: var(--gray-500);">Order in which this product appears in listings</p>
                      <div v-if="errors.display_order" class="text-red-500 text-xs mt-1">{{ errors.display_order }}</div>
                    </div>
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
              <VendorColorVariantCard
                v-for="(color, index) in productData.colors"
                :key="color.id || index"
                :color="color"
                :index="index"
                :is-default="color.is_default"
                :product-id="productId"
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

                  <VendorSpecificationItem
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
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
          <i class="fas fa-check text-green-600 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Success!</h3>
        <p class="text-sm text-gray-500 mb-6">Product updated successfully!</p>
        <button @click="closeSuccessModal"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
          Continue
        </button>
      </div>
    </div>
  </div>

  <!-- Error Modal -->
  <div v-if="showErrorModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
          <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Error</h3>
        <p class="text-sm text-gray-500 mb-6">{{ errorMessage }}</p>
        <button @click="closeErrorModal"
                class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import VendorColorVariantCard from './VendorColorVariantCard.vue'
import VendorSpecificationItem from './VendorSpecificationItem.vue'

export default {
  name: 'VendorProductEditApp',
  components: {
    VendorColorVariantCard,
    VendorSpecificationItem
  },
  props: {
    productId: {
      type: [String, Number],
      required: true
    },
    backUrl: {
      type: String,
      default: '/vendor/products'
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
        // Sum up stock from all sizes within this color
        const colorSizesStock = (color.sizes || []).reduce((colorTotal, size) => {
          return colorTotal + (parseInt(size.stock) || 0)
        }, 0)
        return total + colorSizesStock
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
      if (activeTab.value === tabId) {
        return 'text-indigo-600 border-indigo-500'
      }
      return 'text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300'
    }

    const fetchProductData = async () => {
      try {
        loading.value = true
        const response = await window.axios.get(`/vendor/products/${props.productId}/edit-data`)

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

        // Ensure only one color is marked as default
        const defaultColors = productData.colors.filter(color => color.is_default)
        if (defaultColors.length > 1) {
          // Multiple defaults found - keep only the first one
          productData.colors.forEach((color, index) => {
            color.is_default = index === 0 // Set first color as default, others as false
          })
        } else if (defaultColors.length === 0 && productData.colors.length > 0) {
          // No default found - set first color as default
          productData.colors[0].is_default = true
        }

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

    // Save product method
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
        const basicFields = {
          id: productData.id,
          name: productData.name || '',
          category_id: productData.category_id || '',
          branch_id: productData.branch_id || '',
          price: productData.price || 0,
          original_price: productData.original_price || null,
          stock: productData.stock || 0,
          description: productData.description || '',
          is_available: productData.is_available ? 1 : 0,
          display_order: productData.display_order || 0
        }

        Object.keys(basicFields).forEach(key => {
          if (basicFields[key] !== null && basicFields[key] !== undefined) {
            formData.append(key, basicFields[key])
          }
        })

        // Add colors data
        productData.colors.forEach((color, index) => {
          // Only include essential color fields and handle null/undefined values
          const colorFields = {
            id: color.id || null,
            name: color.name || '',
            color_code: color.color_code || '#000000',
            price_adjustment: color.price_adjustment || 0,
            stock: color.stock || 0,
            display_order: color.display_order || index,
            is_default: color.is_default ? 1 : 0
          }

          // Include existing image path if no new image file is being uploaded
          if (!color.imageFile && color.image) {
            colorFields.image = color.image
          }

          Object.keys(colorFields).forEach(key => {
            if (colorFields[key] !== null && colorFields[key] !== undefined) {
              formData.append(`colors[${index}][${key}]`, colorFields[key])
            }
          })

          // Handle image file separately (for new uploads)
          if (color.imageFile) {
            formData.append(`color_images[${index}]`, color.imageFile)
          }

          // Handle sizes data if present
          if (color.sizes && Array.isArray(color.sizes) && color.sizes.length > 0) {
            color.sizes.forEach((size, sizeIndex) => {
              const sizeFields = {
                id: size.id || null,
                name: size.name || '',
                value: size.value || '',
                category: size.category || 'clothes', // Default category
                additional_info: size.additional_info || '',
                stock: size.stock || 0,
                price_adjustment: size.price_adjustment || 0,
                display_order: size.display_order || sizeIndex,
                is_default: size.is_default ? 1 : 0
              }

              Object.keys(sizeFields).forEach(sizeKey => {
                if (sizeFields[sizeKey] !== null && sizeFields[sizeKey] !== undefined) {
                  formData.append(`colors[${index}][sizes][${sizeIndex}][${sizeKey}]`, sizeFields[sizeKey])
                }
              })
            })
          }
        })

        // Add specifications data
        productData.specifications.forEach((spec, index) => {
          // Only include specifications with both key and value
          if (spec.key && spec.key.trim() && spec.value && spec.value.trim()) {
            const specFields = {
              id: spec.id || null,
              key: spec.key.trim(),
              value: spec.value.trim(),
              display_order: spec.display_order || index
            }

            Object.keys(specFields).forEach(key => {
              if (specFields[key] !== null && specFields[key] !== undefined) {
                formData.append(`specifications[${index}][${key}]`, specFields[key])
              }
            })
          }
        })

        // Add method override for PUT request
        formData.append('_method', 'PUT')

        const response = await window.axios.post(`/vendor/products/${props.productId}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        // Handle success
        if (response.data.success || response.status === 200) {
          // Show success modal
          showSuccessModal.value = true
        }

      } catch (error) {
        console.error('Error saving product:', error)

        // Handle validation errors
        if (error.response?.data?.errors) {
          Object.assign(errors, error.response.data.errors)
          errorMessage.value = 'Please fix the validation errors and try again.'
        } else {
          errorMessage.value = error.response?.data?.message || 'An error occurred while saving the product. Please try again.'
        }
        showErrorModal.value = true
      } finally {
        saving.value = false
      }
    }

    const previewProduct = () => {
      // Open product preview in new tab
      window.open(`/vendor/products/${props.productId}`, '_blank')
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

    const updateColor = (index, field, value) => {
      console.log('updateColor called:', { index, field, value })
      if (index >= 0 && index < productData.colors.length) {
        console.log('Before update:', productData.colors[index])
        // Handle field-specific updates
        if (typeof field === 'string') {
          productData.colors[index][field] = value
          console.log('After field update:', productData.colors[index])
        } else {
          // Handle object updates (backward compatibility)
          Object.assign(productData.colors[index], field)
          console.log('After object update:', productData.colors[index])
        }
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

        console.log(`Sizes updated for color ${colorIndex}:`, sizes)
      }
    }

    const handleStockCorrected = (correctionData) => {
      console.log('Stock corrected:', correctionData)
      // You can add additional logic here if needed, such as:
      // - Logging the correction for analytics
      // - Showing a global notification
      // - Updating other related data
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

    const updateSpecification = (index, field, value) => {
      if (index >= 0 && index < productData.specifications.length) {
        productData.specifications[index][field] = value
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

    // Modal methods
    const closeSuccessModal = () => {
      showSuccessModal.value = false
      // Redirect to products listing after closing success modal
      window.location.href = props.backUrl
    }

    const closeErrorModal = () => {
      showErrorModal.value = false
    }

    // Category validation helpers
    const findCategoryById = (categoryId) => {
      for (const parent of categories.value) {
        if (parent.id == categoryId) {
          return parent
        }
        for (const child of parent.children) {
          if (child.id == categoryId) {
            return child
          }
        }
      }
      return null
    }

    const validateCategorySelection = () => {
      if (productData.category_id) {
        const selectedCategory = findCategoryById(productData.category_id)
        if (selectedCategory && !selectedCategory.is_selectable) {
          // Clear the invalid selection
          productData.category_id = ''
          errors.category_id = 'Please select a specific subcategory, not a category group'
        } else {
          // Clear any previous error
          delete errors.category_id
        }
      }
    }

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
      handleStockCorrected,
      addNewSpecification,
      updateSpecification,
      removeSpecification,
      closeSuccessModal,
      closeErrorModal,
      findCategoryById,
      validateCategorySelection
    }
  }
}
</script>

<style scoped>
/* Vue component specific styles */
.vendor-product-edit-app {
  min-height: 100vh;
}

.vue-tab-content {
  min-height: 400px;
}

.vue-text-lg {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1f2937;
}

.vue-text-sm {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.vue-form-control {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: #ffffff;
  color: #1f2937;
  font-size: 0.875rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.vue-form-control:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.vue-form-control::placeholder {
  color: #9ca3af;
}

/* Modern buttons */
.vue-btn {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.15s ease-in-out;
  cursor: pointer;
  border: 1px solid transparent;
}

.vue-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.vue-btn-primary {
  background-color: #6366f1;
  color: #ffffff;
  border-color: #6366f1;
}

.vue-btn-primary:hover:not(:disabled) {
  background-color: #5b21b6;
  border-color: #5b21b6;
}

.vue-btn-secondary {
  background-color: #6b7280;
  color: #ffffff;
  border-color: #6b7280;
}

.vue-btn-secondary:hover:not(:disabled) {
  background-color: #4b5563;
  border-color: #4b5563;
}

.vue-btn-success {
  background-color: #10b981;
  color: #ffffff;
  border-color: #10b981;
}

.vue-btn-success:hover:not(:disabled) {
  background-color: #059669;
  border-color: #059669;
}

.vue-card {
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .vue-text-lg {
    color: #f9fafb;
  }

  .vue-text-sm {
    color: #d1d5db;
  }

  .vue-form-control {
    background-color: #374151;
    border-color: #4b5563;
    color: #f9fafb;
  }

  .vue-form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  }

  .vue-card {
    background-color: #1f2937;
    border-color: #374151;
  }
}

/* Animation classes */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Tab transition */
.tab-transition-enter-active, .tab-transition-leave-active {
  transition: all 0.3s ease;
}

.tab-transition-enter-from {
  opacity: 0;
  transform: translateX(10px);
}

.tab-transition-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .vue-app-container {
    padding: 1rem;
  }
}



</style>
