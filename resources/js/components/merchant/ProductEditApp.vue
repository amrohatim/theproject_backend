<template>
  <div class="vue-page-container" :class="{ 'rtl': isRTL }">
    <div class="vue-content-container">
      <!-- Header Section -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
          <a :href="backUrl" class="vue-btn vue-btn-secondary">
            <i class="fas fa-arrow-left w-4 h-4"></i>
            {{ $t('back_to_products') }}
          </a>
          <div>
            <h1 class="vue-text-2xl">{{ $t('edit_product') }}</h1>
            <p class="vue-text-muted mt-1">{{ $t('update_product_info_colors_specs') }}</p>
          </div>
        </div>
        <div class="flex gap-2">
          <button type="button" class="vue-btn vue-btn-secondary" @click="previewProduct">
            {{ $t('preview') }}
          </button>
          <button type="button" class="vue-btn vue-btn-primary" @click="saveProduct" :disabled="saving">
            <i class="fas fa-save w-4 h-4"></i>
            {{ saving ? $t('saving') : $t('save_changes') }}
          </button>
        </div>
      </div>

      <!-- Stock Progress Indicator -->
      <div class="vue-card" style="background-color: var(--primary-blue-light); border-color: var(--gray-200);">
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
          <div v-if="isStockOverAllocated" class="mt-3 p-3 rounded-lg"
               style="background-color: var(--yellow-100); border: 1px solid var(--yellow-600);">
            <div class="flex items-center gap-2">
              <i class="fas fa-exclamation-triangle" style="color: var(--yellow-600);"></i>
              <p style="color: var(--yellow-800); font-size: 0.875rem; margin: 0;">
                {{ $t('stock_over_allocated_adjust_quantities') }}
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
            <span class="hidden sm:inline">{{ $t(tab.label) }}</span>
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
                      <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</div>
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
                      <div v-if="errors.product_name_arabic" class="text-red-500 text-xs mt-1">{{ errors.product_name_arabic }}</div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
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
                        <option value="">{{ $t('select_category') }}</option>
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
                      <div v-if="errors.category_id" class="text-red-500 text-xs mt-1">{{ errors.category_id }}</div>
                    </div>


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
                                :class="{ 'border-red-500': errors.description }"
                                :placeholder="$t('enter_product_description_english')">
                      </textarea>
                      <div v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description }}</div>
                    </div>

                    <!-- Arabic Description -->
                    <div v-show="currentLanguage === 'ar'" dir="rtl">
                      <textarea id="product_description_arabic"
                                v-model="productData.product_description_arabic"
                                rows="4"
                                class="vue-form-control"
                                :class="{ 'border-red-500': errors.product_description_arabic }"
                                :placeholder="$t('enter_product_description_arabic')"
                                style="text-align: right; direction: rtl;">
                      </textarea>
                      <div v-if="errors.product_description_arabic" class="text-red-500 text-xs mt-1">{{ errors.product_description_arabic }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pricing & Inventory Card -->
              <div class="vue-card">
                <div class="p-6 border-b" style="border-color: var(--gray-200);">
                  <h3 class="flex items-center gap-2 vue-text-lg">
                    <i class="fas fa-dollar-sign w-5 h-5" style="color: var(--gray-600);"></i>
                    {{ $t('pricing_inventory') }}
                  </h3>
                </div>
                <div class="p-6 space-y-4">
                  <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="price" class="block vue-text-sm">
                        {{ $t('current_price') }} <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <i class="fas fa-dollar-sign absolute pt-2 left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="text"
                               id="price"
                               v-model="productData.price"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.price }"
                               @input="e => productData.price = e.target.value.replace(/[^0-9.]/g, '')"
                               @blur="e => productData.price = parseFloat(e.target.value) || 0"
                               pattern="[0-9]*\.?[0-9]*"
                               inputmode="decimal"
                               min="0"
                               required>
                      </div>
                      <div v-if="errors.price" class="text-red-500 text-xs mt-1">{{ errors.price }}</div>
                    </div>

                    <div class="space-y-2">
                      <label for="original_price" class="block vue-text-sm">
                        {{ $t('original_price') }}
                      </label>
                      <div class="relative">
                        <i class="fas fa-dollar-sign absolute left-3 top-1/2 pt-2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="text"
                               id="original_price"
                               v-model="productData.original_price"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.original_price }"
                               @input="e => productData.original_price = e.target.value.replace(/[^0-9.]/g, '')"
                               @blur="e => productData.original_price = parseFloat(e.target.value) || 0"
                               pattern="[0-9]*\.?[0-9]*"
                               inputmode="decimal"
                               min="0">
                      </div>
                      <div v-if="errors.original_price" class="text-red-500 text-xs mt-1">{{ errors.original_price }}</div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="stock" class="block vue-text-sm">
                        {{ $t('total_stock') }} <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <i class="fas fa-warehouse absolute pt-2 left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="text"
                               id="stock"
                               v-model="productData.stock"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.stock }"
                               @input="e => productData.stock = e.target.value.replace(/[^0-9]/g, '')"
                               @blur="e => productData.stock = parseInt(e.target.value) || 0"
                               pattern="[0-9]*"
                               inputmode="numeric"
                               min="0"
                               required>
                      </div>
                      <p class="text-xs" style="color: var(--gray-500);">{{ isRTL ? 'إجمالي المخزون المخصص عبر جميع  الألوان' : 'Total inventory to be allocated across color variants' }}</p>
                      <div v-if="errors.stock" class="text-red-500 text-xs mt-1">{{ errors.stock }}</div>
                    </div>

                    <!-- <div class="space-y-2">
                      <label for="display_order" class="block vue-text-sm">
                        {{ $t('display_order') }}
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
                      <p class="text-xs" style="color: var(--gray-500);">{{ isRTL ? 'ترتيب المنتج في قائمة المنتجات' :  'Order in which this product appears in listings' }}</p>
                      <div v-if="errors.display_order" class="text-red-500 text-xs mt-1">{{ errors.display_order }}</div>
                    </div> -->
                  </div>

                  <div class="flex items-center space-x-2">
                    <input id="is_available"
                           v-model="productData.is_available"
                           type="checkbox"
                           class="w-4 h-4 bg-gray-100 border-gray-300 rounded"
                           style="color: var(--primary-blue); --tw-ring-color: var(--primary-blue);">
                    <label for="is_available" class="vue-text-sm">
                      {{ $t('available_for_purchase') }}
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
                <h3 class="vue-text-lg">{{ $t('product_colors') }}</h3>
                <p class="text-sm" style="color: var(--gray-600);">{{ $t('add_color_variants_images') }}</p>
              </div>
              <button type="button" class="vue-btn vue-btn-primary" @click="addNewColor">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('add_color') }}
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
                <h3 class="vue-text-lg">{{ $t('product_specifications') }}</h3>
                <p class="text-sm" style="color: var(--gray-600);">{{ $t('add_detailed_specifications') }}</p>
              </div>
              <button type="button" class="vue-btn vue-btn-primary" @click="addNewSpecification">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('add_specification') }}
              </button>
            </div>

            <div class="vue-card">
              <div class="p-6">
                <div class="space-y-4">
                  <div v-if="productData.specifications.length === 0" class="text-center py-8">
                    <i class="fas fa-file-text w-12 h-12 mx-auto mb-4" style="color: var(--gray-400);"></i>
                    <h3 class="vue-text-lg mb-2">{{ $t('no_specifications_added_yet') }}</h3>
                    <p class="mb-4" style="color: var(--gray-600);">
                      {{ $t('add_specifications_detailed_info') }}
                    </p>
                    <button type="button" class="vue-btn vue-btn-primary" @click="addNewSpecification">
                      <i class="fas fa-plus w-4 h-4"></i>
                      {{ $t('add_first_specification') }}
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

  <!-- Success Modal -->
  <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 shadow-xl">
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
          <i class="fas fa-check text-green-600 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('success') }}</h3>
        <p class="text-sm text-gray-500 mb-6">{{ $t('product_updated_successfully') }}</p>
        <button @click="closeSuccessModal"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
          {{ $t('continue') }}
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
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('error') }}</h3>
        <p class="text-sm text-gray-500 mb-6">{{ errorMessage }}</p>
        <button @click="closeErrorModal"
                class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
          {{ $t('close') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import ColorVariantCard from './ColorVariantCard.vue'
import SpecificationItem from './SpecificationItem.vue'
import LanguageSwitch from '../common/LanguageSwitch.vue'

export default {
  name: 'ProductEditApp',
  components: {
    ColorVariantCard,
    SpecificationItem,
    LanguageSwitch
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

    // RTL support
    const isRTL = computed(() => {
      return document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar'
    })

    // Reactive translations
    const translations = ref(window.Laravel?.translations || {})

    // Watch for translation changes
    const updateTranslations = () => {
      translations.value = window.Laravel?.translations || {}
    }

    // Check for translations periodically until they're loaded
    const checkTranslations = () => {
      if (window.Laravel?.translations && Object.keys(window.Laravel.translations).length > 0) {
        updateTranslations()
      } else {
        setTimeout(checkTranslations, 100)
      }
    }

    onMounted(() => {
      checkTranslations()
    })

    // Translation method
    const $t = (key, params = {}) => {
      const trans = translations.value
      if (trans[key]) {
        let translation = trans[key]
        // Replace placeholders with actual values
        Object.keys(params).forEach(param => {
          translation = translation.replace(`:${param}`, params[param])
        })
        return translation
      }
      return key
    }

    // Tab configuration
    const tabs = computed(() => [
      { id: 'basic', label: $t('basic_info'), icon: 'fas fa-box' },
      { id: 'colors', label: $t('colors_images'), icon: 'fas fa-palette' },
      { id: 'specifications', label: $t('specifications'), icon: 'fas fa-file-text' }
    ])

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

      // Basic validation - Product name required in both languages
      if (!productData.name?.trim()) {
        errors.name = $t('merchant.product_name_required')
        isValid = false
      }

      if (!productData.product_name_arabic?.trim()) {
        errors.product_name_arabic = $t('merchant.product_name_required')
        isValid = false
      }

      // Description validation - if entered in one language, required in the other
      const hasEnglishDescription = productData.description && productData.description.trim()
      const hasArabicDescription = productData.product_description_arabic && productData.product_description_arabic.trim()

      if (hasEnglishDescription && !hasArabicDescription) {
        errors.product_description_arabic = $t('description_required_when_english_provided')
        isValid = false
      }

      if (hasArabicDescription && !hasEnglishDescription) {
        errors.description = $t('description_required_when_arabic_provided')
        isValid = false
      }

      if (!productData.category_id) {
        errors.category_id = $t('merchant.category_required')
        isValid = false
      }

      if (!productData.price || productData.price <= 0) {
        errors.price = $t('merchant.price_must_be_greater_than_zero')
        isValid = false
      }

      if (!productData.stock || productData.stock < 0) {
        errors.stock = $t('merchant.stock_must_be_zero_or_greater')
        isValid = false
      }

      // Color validation
      if (productData.colors.length === 0) {
        errors.colors = $t('merchant.at_least_one_color_variant_required')
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

      try {
        saving.value = true

        // Prepare form data for submission
        const formData = new FormData()

        // Add basic product data
        const basicFields = {
          id: productData.id,
          name: productData.name || '',
          product_name_arabic: productData.product_name_arabic || '',
          category_id: productData.category_id || '',
          price: productData.price || 0,
          original_price: productData.original_price || null,
          stock: productData.stock || 0,
          description: productData.description || '',
          product_description_arabic: productData.product_description_arabic || '',
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

        const response = await window.axios.post(`/merchant/products/${props.productId}`, formData, {
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
      currentLanguage,

      // Computed
      totalAllocatedStock,
      stockProgressPercentage,
      isStockOverAllocated,
      showSaleBadge,
      salePercentage,
      isRTL,

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
      validateCategorySelection,
      handleLanguageChange
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

.rtl .pl-10 {
  padding-left: 0;
  padding-right: 2.5rem;
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

.rtl .absolute.left-3 {
  left: auto;
  right: 0.75rem;
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
