<template>
  <div class="min-h-screen" :class="{ 'rtl': isRTL }" style="background-color: var(--gray-50);">
    <!-- Loading State -->
    <div v-if="loading" class="d-flex justify-content-center align-items-center" style="min-height: 50vh;">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">{{ $t('loading') }}</span>
        </div>
        <p class="mt-3 text-muted">{{ $t('loading_product_creation_form') }}</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="vue-text-2xl">{{ $t('create_new_product') }}</h1>
          <p class="text-sm text-gray-600">{{ $t('add_new_product_inventory') }}</p>
        </div>
        <a :href="backUrl" class="vue-btn vue-btn-secondary">
          <i class="fas fa-arrow-left w-4 h-4"></i>
          {{ $t('back_to_products') }}
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
                  {{ $t('product_details') }}
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <!-- Product Name with Language Switch -->
                <div class="space-y-2">
                  <label class="block vue-text-sm">
                    {{ $t('product_name') }} <span class="text-red-500">*</span>
                  </label>

                  <!-- Language Switch for Product Name -->
                  <LanguageSwitch
                    v-model="currentLanguage"
                    @language-changed="handleLanguageChange"
                  />

                  <!-- English Product Name -->
                  <div v-show="currentLanguage === 'en'">
                    <input type="text"
                           id="name"
                           v-model="productData.name"
                           class="vue-form-control"
                           :class="{ 'border-red-500': errors.name }"
                           :placeholder="$t('product_name_english')"
                           required>
                    <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>
                  </div>

                  <!-- Arabic Product Name -->
                  <div v-show="currentLanguage === 'ar'" dir="rtl">
                    <input type="text"
                           id="product_name_arabic"
                           v-model="productData.product_name_arabic"
                           class="vue-form-control"
                           :class="{ 'border-red-500': errors.product_name_arabic }"
                           :placeholder="$t('product_name_arabic')"
                           style="text-align: right; direction: rtl;"
                           required>
                    <p v-if="errors.product_name_arabic" class="text-red-500 text-sm">{{ errors.product_name_arabic }}</p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="category_id" class="block vue-text-sm">
                    {{ $t('category') }} <span class="text-red-500">*</span>
                  </label>
                  <select id="category_id"
                          v-model="productData.category_id"
                          class="vue-form-control text-left"
                          style="text-align: left; direction: ltr;"
                          :class="{ 'border-red-500': errors.category_id }"
                          required
                          @change="validateCategorySelection">
                    <option  value="">{{ $t('select_category') }}</option>
                    <optgroup v-for="parent in categories" :key="parent.id" :label="parent.name">
                      <!-- <option :value="parent.id"
                              disabled
                              class="text-gray-400 font-semibold">
                        {{ parent.name }} (Category Group)
                      </option> -->
                      <option v-for="child in parent.children"
                              :key="child.id"
                              :value="child.id"
                              :disabled="!child.is_selectable"
                              :class="{ 'text-gray-400': !child.is_selectable }">
                        &nbsp;&nbsp;{{ child.name }}
                      </option>
                    </optgroup>
                  </select>
                  <p v-if="errors.category_id" class="text-red-500 text-sm">{{ errors.category_id }}</p>
                </div>

                <!-- Product Description with Language Switch -->
                <div class="space-y-2">
                  <label class="block vue-text-sm">{{ $t('description') }}</label>

                  <!-- Language Switch for Description -->
                  <LanguageSwitch
                    v-model="currentLanguage"
                    @language-changed="handleLanguageChange"
                  />

                  <!-- English Description -->
                  <div v-show="currentLanguage === 'en'">
                    <textarea id="description"
                              v-model="productData.description"
                              rows="4"
                              class="vue-form-control"
                              :placeholder="$t('enter_product_description_english')"></textarea>
                    <p v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</p>
                  </div>

                  <!-- Arabic Description -->
                  <div v-show="currentLanguage === 'ar'" dir="rtl">
                    <textarea id="product_description_arabic"
                              v-model="productData.product_description_arabic"
                              rows="4"
                              class="vue-form-control"
                              :placeholder="$t('enter_product_description_arabic')"
                              style="text-align: right; direction: rtl;"></textarea>
                    <p v-if="errors.product_description_arabic" class="text-red-500 text-sm">{{ errors.product_description_arabic }}</p>
                  </div>
                </div>



                <div class="flex items-center space-x-2">
                  <input type="checkbox" 
                         id="is_available" 
                         v-model="productData.is_available"
                         class="vue-checkbox">
                  <label for="is_available" class="vue-text-sm">{{ $t('product_available_sale') }}</label>
                </div>
              </div>
            </div>

            <!-- Pricing & Stock Card -->
            <div class="vue-card">
              <div class="p-6 border-b" style="border-color: var(--gray-200);">
                <h3 class="flex items-center gap-2 vue-text-lg">
                  <i class="fas fa-dollar-sign w-5 h-5" style="color: var(--gray-600);"></i>
                  {{ $t('pricing_stock') }}
                </h3>
              </div>
              <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                  <div class="space-y-2">
                    <label for="price" class="block vue-text-sm">
                      {{ $t('price') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="price" 
                           v-model="productData.price"
                           class="vue-form-control"
                           :class="{ 'border-red-500': errors.price }"
                           @input="e => productData.price = e.target.value.replace(/[^0-9.]/g, '')"
                           @blur="e => productData.price = parseFloat(e.target.value) || 0"
                           pattern="[0-9]*\.?[0-9]*"
                           inputmode="decimal"
                           required>
                    <p v-if="errors.price" class="text-red-500 text-sm">{{ errors.price }}</p>
                  </div>

                  <div class="space-y-2">
                    <label for="original_price" class="block vue-text-sm">{{ $t('original_price') }}</label>
                    <input type="text" 
                           id="original_price" 
                           v-model="productData.original_price"
                           class="vue-form-control"
                           @input="e => productData.original_price = e.target.value.replace(/[^0-9.]/g, '')"
                           @blur="e => productData.original_price = parseFloat(e.target.value) || 0"
                           pattern="[0-9]*\.?[0-9]*"
                           inputmode="decimal"
                           min="0">
                  </div>
                </div>

                <div class="space-y-2">
                  <label for="stock" class="block vue-text-sm">
                    {{ $t('total_stock') }} <span class="text-red-500">*</span>
                  </label>
                  <input type="text" 
                         id="stock" 
                         v-model="productData.stock"
                         class="vue-form-control"
                         :class="{ 'border-red-500': errors.stock }"
                         @input="e => productData.stock = e.target.value.replace(/[^0-9]/g, '')"
                         @blur="e => productData.stock = parseInt(e.target.value) || 0"
                         pattern="[0-9]*"
                         inputmode="numeric"
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
              <h3 class="vue-text-lg">{{ $t('product_colors') }}</h3>
              <p class="text-sm text-gray-600">{{ $t('add_color_variants_images') }}</p>
            </div>
            <button type="button" @click="addNewColor" class="vue-btn vue-btn-primary">
              <i class="fas fa-plus w-4 h-4"></i>
              {{ $t('add_color') }}
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
              <h3 class="vue-text-lg text-gray-600 mb-2">{{ $t('no_colors_added_yet') }}</h3>
              <p class="text-gray-500 mb-4">{{ $t('add_color_variants_appealing') }}</p>
              <button type="button" @click="addNewColor" class="vue-btn vue-btn-primary">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('add_first_color') }}
              </button>
            </div>
          </div>

          <!-- Stock Progress Indicator -->
          <div v-if="productData.stock > 0" class="vue-card" style="background-color: var(--primary-blue-light); border-color: var(--gray-200);">
            <div class="vue-card-body">
              <div class="flex items-center justify-between mb-2">
                <span class="vue-text-sm" style="color: var(--primary-blue-hover);">{{ $t('stock_allocation_progress') }}</span>
                <span class="vue-text-sm" style="color: var(--primary-blue);">
                  <span>{{ totalAllocatedStock }}</span> / {{ productData.stock }} {{ $t('units_allocated') }}
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
                {{ $t('stock_over_allocated_adjust') }}
              </div>
            </div>
          </div>

          <!-- Stock Summary -->
          <div v-if="productData.colors.length > 0 && productData.stock > 0" class="vue-card">
            <div class="p-6">
              <h4 class="vue-text-lg mb-4">{{ $t('stock_allocation_summary') }}</h4>
              <div class="space-y-3">
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">{{ $t('total_stock') }}:</span>
                  <span class="font-medium">{{ productData.stock }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">{{ $t('allocated_stock') }}:</span>
                  <span class="font-medium" :class="{ 'text-red-600': isStockOverAllocated }">
                    {{ totalAllocatedStock }}
                  </span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">{{ $t('remaining_stock') }}:</span>
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
                  {{ $t('stock_over_allocated_adjust') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Specifications Tab -->
        <div v-show="activeTab === 'specifications'" class="vue-tab-content space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="vue-text-lg">{{ $t('product_specifications') }}</h3>
              <p class="text-sm text-gray-600">{{ $t('add_detailed_specifications') }}</p>
            </div>
            <button type="button" @click="addNewSpecification" class="vue-btn vue-btn-primary">
              <i class="fas fa-plus w-4 h-4"></i>
              {{ $t('add_specification') }}
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
              <h3 class="vue-text-lg text-gray-600 mb-2">{{ $t('no_specifications_added_yet') }}</h3>
              <p class="text-gray-500 mb-4">{{ $t('add_specifications_detailed_info') }}</p>
              <button type="button" @click="addNewSpecification" class="vue-btn vue-btn-primary">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('add_first_specification') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-3 mt-6">
          <a :href="backUrl" class="vue-btn vue-btn-secondary">
            <i class="fas fa-times w-4 h-4"></i>
            {{ $t('cancel') }}
          </a>
          <button type="submit" class="vue-btn vue-btn-primary" :disabled="saving">
            <i class="fas fa-save w-4 h-4"></i>
            <span v-if="saving">{{ $t('creating') }}</span>
            <span v-else>{{ $t('create_product') }}</span>
          </button>
        </div>
      </form>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="modal-overlay" @click="closeSuccessModal">
      <div class="modal-content" @click.stop>
        <div class="text-center">
          <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
          <h3 class="vue-text-lg mb-2">{{ $t('product_created_successfully') }}</h3>
          <p class="text-gray-600 mb-4">{{ $t('product_created_available_inventory') }}</p>
          <div class="flex gap-3 justify-center">
            <a :href="backUrl" class="vue-btn vue-btn-secondary">{{ $t('view_products') }}</a>
            <button @click="createAnother" class="vue-btn vue-btn-primary">{{ $t('create_another') }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Modal -->
    <div v-if="showErrorModal" class="modal-overlay" @click="closeErrorModal">
      <div class="modal-content" @click.stop>
        <div class="text-center">
          <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
          <h3 class="vue-text-lg mb-2">{{ $t('error_creating_product') }}</h3>
          <p class="text-gray-600 mb-4">{{ errorMessage }}</p>
          <button @click="closeErrorModal" class="vue-btn vue-btn-primary">{{ $t('try_again') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import ColorVariantCard from './ColorVariantCard.vue'
import SpecificationItem from './SpecificationItem.vue'
import LanguageSwitch from '../common/LanguageSwitch.vue'

export default {
  name: 'ProductCreateApp',
  components: {
    ColorVariantCard,
    SpecificationItem,
    LanguageSwitch
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
      product_name_arabic: '',
      category_id: '',
      price: 0,
      original_price: null,
      stock: 0,
      description: '',
      product_description_arabic: '',
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

    // Language switching
    const currentLanguage = ref('en')

    const handleLanguageChange = (language) => {
      currentLanguage.value = language
    }

    // Tab configuration
    const tabs = computed(() => [
      { id: 'basic', label: $t('basic_info'), icon: 'fas fa-box' },
      { id: 'colors', label: $t('colors_images'), icon: 'fas fa-palette' },
      { id: 'specifications', label: $t('specifications'), icon: 'fas fa-file-text' }
    ])

    // Computed properties
    const isRTL = computed(() => {
      return document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar'
    })

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

    // Translation method
    const $t = (key, replacements = {}) => {
      const translations = window.Laravel?.translations || {}
      let translation = translations[key] || key
      
      // Handle placeholder replacements
      Object.keys(replacements).forEach(placeholder => {
        const regex = new RegExp(`:${placeholder}`, 'g')
        translation = translation.replace(regex, replacements[placeholder])
      })
      
      return translation
    }

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
        errorMessage.value = $t('merchant.failed_load_form_data_refresh')
        showErrorModal.value = true
        loading.value = false
      }
    }

    const validateForm = () => {
      // Clear previous errors
      Object.keys(errors).forEach(key => delete errors[key])

      let isValid = true

      // Basic validation - Product name required in both languages
      if (!productData.name.trim()) {
        errors.name = $t('merchant.product_name_required')
        isValid = false
      }

      if (!productData.product_name_arabic.trim()) {
        errors.product_name_arabic = $t('merchant.product_name_required')
        isValid = false
      }

      // Description validation - if entered in one language, required in the other
      const hasEnglishDescription = productData.description && productData.description.trim()
      const hasArabicDescription = productData.product_description_arabic && productData.product_description_arabic.trim()

      if (hasEnglishDescription && !hasArabicDescription) {
        errors.product_description_arabic = $t('merchant.description_required_when_english_provided')
        isValid = false
      }

      if (hasArabicDescription && !hasEnglishDescription) {
        errors.description = $t('merchant.description_required_when_arabic_provided')
        isValid = false
      }

      if (!productData.category_id) {
        errors.category_id = $t('merchant.category_required')
        isValid = false
      } else {
        // Validate that selected category is a leaf category
        const selectedCategory = findCategoryById(productData.category_id)
        if (selectedCategory && !selectedCategory.is_selectable) {
          errors.category_id = $t('merchant.select_specific_subcategory_not_group')
          isValid = false
        }
      }

      if (!productData.price || productData.price <= 0) {
        errors.price = $t('merchant.price_must_be_greater_than_zero')
        isValid = false
      }

      if (!productData.stock || productData.stock < 0) {
        errors.stock = $t('merchant.stock_must_be_zero_or_greater')
        isValid = false
      }

      // Colors validation
      if (productData.colors.length === 0) {
        errors.colors = $t('merchant.at_least_one_color_required')
        isValid = false
      }

      // Check if at least one color has an image
      const hasColorWithImage = productData.colors.some(color => color.image || color.imageFile)
      if (!hasColorWithImage) {
        errors.colors = $t('merchant.at_least_one_color_must_have_image')
        isValid = false
      }

      return isValid
    }

    const saveProduct = async () => {
      if (!validateForm()) {
        // Switch to the tab with errors
        if (errors.name || errors.product_name_arabic || errors.description || errors.product_description_arabic || errors.category_id || errors.price || errors.stock) {
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
        formData.append('name', productData.name || '')
        formData.append('product_name_arabic', productData.product_name_arabic || '')
        formData.append('category_id', productData.category_id)
        formData.append('price', productData.price)
        if (productData.original_price) {
          formData.append('original_price', productData.original_price)
        }
        formData.append('stock', productData.stock)
        formData.append('description', productData.description || '')
        formData.append('product_description_arabic', productData.product_description_arabic || '')
        formData.append('is_available', productData.is_available ? '1' : '0')
        formData.append('display_order', productData.display_order || 0)

        // Add branch_id - use 'auto' to trigger auto-assignment in backend
        formData.append('branch_id', 'auto')

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
              formData.append(`colors[${index}][sizes][${sizeIndex}][category]`, size.category || 'clothes')
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
          errorMessage.value = errorData.message || $t('merchant.failed_create_product')
          showErrorModal.value = true
        }
      } catch (error) {
        console.error('Error creating product:', error)
        errorMessage.value = $t('merchant.unexpected_error_try_again')
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
          errors.category_id = $t('merchant.select_specific_subcategory_not_group')
        } else {
          // Clear any previous error
          delete errors.category_id
        }
      }
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
      currentLanguage,

      // Computed
      isRTL,
      totalAllocatedStock,
      stockProgressPercentage,
      isStockOverAllocated,
      showSaleBadge,
      salePercentage,

      // Methods
      $t,
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
      createAnother,
      findCategoryById,
      validateCategorySelection,
      handleLanguageChange
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

/* RTL Support */
.rtl {
  direction: rtl;
}

.rtl .text-left {
  text-align: right;
}

.rtl .text-right {
  text-align: left;
}

.rtl .ml-2 {
  margin-left: 0;
  margin-right: 0.5rem;
}

.rtl .mr-2 {
  margin-right: 0;
  margin-left: 0.5rem;
}

.rtl .ml-4 {
  margin-left: 0;
  margin-right: 1rem;
}

.rtl .mr-4 {
  margin-right: 0;
  margin-left: 1rem;
}

.rtl .pl-4 {
  padding-left: 0;
  padding-right: 1rem;
}

.rtl .pr-4 {
  padding-right: 0;
  padding-left: 1rem;
}

.rtl .pl-6 {
  padding-left: 0;
  padding-right: 1.5rem;
}

.rtl .pr-6 {
  padding-right: 0;
  padding-left: 1.5rem;
}

.rtl .flex-row {
  flex-direction: row-reverse;
}

.rtl input[type="text"],
.rtl input[type="number"],
.rtl input[type="email"],
.rtl textarea,
.rtl select {
  text-align: right;
}

.rtl .grid {
  direction: rtl;
}

.rtl .space-x-2 > * + * {
  margin-left: 0;
  margin-right: 0.5rem;
}

.rtl .gap-2 {
  gap: 0.5rem;
  flex-direction: row-reverse;
}

.rtl .gap-3 {
  gap: 0.75rem;
  flex-direction: row-reverse;
}

.rtl .gap-4 {
  gap: 1rem;
  flex-direction: row-reverse;
}
</style>
