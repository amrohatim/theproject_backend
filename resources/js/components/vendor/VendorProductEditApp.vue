<template>
  <div class="vendor-product-edit-app" :class="{ 'rtl': isRTL }">
    <!-- Loading State -->
    <div v-if="loading" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600 text-lg">{{ $t('vendor.loading') }}</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="container mx-auto">
      <!-- Header -->
      <div class="mb-6">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $t('vendor.edit_product') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $t('vendor.update_product_information') }}</p>
          </div>
          <div class="flex gap-2">
            <a :href="backUrl" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
              <i class="fas fa-arrow-left" :class="isRTL ? 'ml-2' : 'mr-2'"></i> {{ $t('vendor.back_to_products') }}
            </a>
            <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150" @click="saveProduct" :disabled="saving">
              <i class="fas fa-save" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
              {{ saving ? $t('vendor.saving') : $t('vendor.save_changes') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Stock Progress Indicator -->
      <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-orange-700 dark:text-orange-300">{{ $t('vendor.stock_allocation_progress') }}</span>
          <span class="text-sm text-orange-600 dark:text-orange-400">
            <span>{{ totalAllocatedStock }}</span> / {{ productData.stock }} {{ $t('vendor.units_allocated') }}
          </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
          <div class="bg-orange-600 h-2 rounded-full transition-all duration-300"
               :style="{ width: stockProgressPercentage + '%' }">
          </div>
        </div>
        <div v-if="isStockOverAllocated" class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
          <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
              {{ $t('vendor.stock_over_allocated_message') }}
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
              <i :class="[tab.icon, isRTL ? 'ml-2' : 'mr-2']"></i>
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
                    {{ $t('vendor.product_details') }}
                  </h3>
                </div>
                <div class="p-6 space-y-4">
                  <div class="space-y-2">
                    <label for="name" class="block vue-text-sm">
                      {{ $t('vendor.product_name') }} <span class="text-red-500">*</span>
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
                             :placeholder="$t('enter_product_name_english')"
                             required>
                      <div v-if="errors.name" class="text-red-500 text-xs mt-1">{{ errors.name }}</div>
                    </div>

                    <!-- Arabic Product Name -->
                    <div v-show="currentLanguage === 'ar'" :dir="isRTL ? 'rtl' : 'ltr'">
                      <input type="text"
                             id="product_name_arabic"
                             v-model="productData.product_name_arabic"
                             class="vue-form-control"
                             :class="{ 'border-red-500': errors.product_name_arabic, 'text-right': isRTL }"
                             :placeholder="$t('enter_product_name_arabic')"
                             required>
                      <div v-if="errors.product_name_arabic" class="text-red-500 text-xs mt-1">{{ errors.product_name_arabic }}</div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="category_id" class="block vue-text-sm">
                        {{ $t('vendor.category') }} <span class="text-red-500">*</span>
                      </label>
                      <select id="category_id"
                              v-model="productData.category_id"
                              class="vue-form-control"
                              :class="{ 'border-red-500': errors.category_id }"
                              required
                              @change="validateCategorySelection">
                        <option value="">{{ $t('vendor.select_category') }}</option>
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
                        {{ $t('vendor.branch') }} <span class="text-red-500">*</span>
                      </label>
                      <select id="branch_id"
                              v-model="productData.branch_id"
                              class="vue-form-control"
                              :class="{ 'border-red-500': errors.branch_id }"
                              required>
                        <option value="">{{ $t('vendor.select_branch') }}</option>
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
                      {{ $t('vendor.description') }}
                    </label>

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
                    <div v-show="currentLanguage === 'ar'" :dir="isRTL ? 'rtl' : 'ltr'">
                      <textarea id="product_description_arabic"
                                v-model="productData.product_description_arabic"
                                rows="4"
                                class="vue-form-control"
                                :class="{ 'border-red-500': errors.product_description_arabic, 'text-right': isRTL }"
                                :placeholder="$t('enter_product_description_arabic')">
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
                    {{ $t('vendor.pricing_and_inventory') }}
                  </h3>
                </div>
                <div class="p-6 space-y-4">
                  <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="price" class="block vue-text-sm">
                        {{ $t('vendor.current_price') }} <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="text"
                               id="price"
                               v-model="productData.price"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.price }"
                               required
                               @input="e => productData.price = e.target.value.replace(/[^0-9.]/g, '')"
                               @blur="e => productData.price = parseFloat(e.target.value) || 0"
                               pattern="[0-9]*\.?[0-9]*"
                               inputmode="decimal">
                      </div>
                      <div v-if="errors.price" class="text-red-500 text-xs mt-1">{{ errors.price }}</div>
                    </div>

                    <div class="space-y-2">
                      <label for="original_price" class="block vue-text-sm">
                        {{ $t('vendor.original_price') }}
                      </label>
                      <div class="relative">
                        <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                        <input type="text"
                               id="original_price"
                               v-model="productData.original_price"
                               class="vue-form-control pl-10"
                               :class="{ 'border-red-500': errors.original_price }"
                               @input="e => productData.original_price = e.target.value.replace(/[^0-9.]/g, '')"
                               @blur="e => productData.original_price = parseFloat(e.target.value) || 0"
                               pattern="[0-9]*\.?[0-9]*"
                               inputmode="decimal">
                      </div>
                      <div v-if="errors.original_price" class="text-red-500 text-xs mt-1">{{ errors.original_price }}</div>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                      <label for="stock" class="block vue-text-sm">
                        {{ $t('vendor.total_stock') }} <span class="text-red-500">*</span>
                      </label>
                      <div class="relative">
                        <i class="fas fa-warehouse absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
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
                      <p class="text-xs" style="color: var(--gray-500);">{{ $t('vendor.total_inventory_allocation_note') }}</p>
                      <div v-if="errors.stock" class="text-red-500 text-xs mt-1">{{ errors.stock }}</div>
                    </div>

                    <!-- <div class="space-y-2">
                      <label for="display_order" class="block vue-text-sm">
                        {{ $t('vendor.display_order') }}
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
                      <p class="text-xs" style="color: var(--gray-500);">{{ $t('vendor.display_order_note') }}</p>
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
                      {{ $t('vendor.available_for_purchase') }}
                    </label>
                  </div>

                  <!-- Sale Badge -->
                  <div v-if="showSaleBadge" class="p-3 rounded-lg"
                       style="background-color: var(--primary-blue-light); border: 1px solid var(--gray-200);">
                    <div class="flex items-center gap-2">
                      <span class="px-2 py-1 text-xs font-medium rounded"
                            style="background-color: var(--gray-100); color: var(--primary-blue-hover);">
                        {{ $t('vendor.sale') }}
                      </span>
                      <span class="text-sm" style="color: var(--primary-blue);">
                        {{ salePercentage }}% {{ $t('vendor.off') }}
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
                <h3 class="vue-text-lg">{{ $t('vendor.product_colors') }}</h3>
                <p class="text-sm" style="color: var(--gray-600);">{{ $t('vendor.add_color_variants_with_images') }}</p>
              </div>
              <button type="button" class="vue-btn vue-btn-primary" @click="addNewColor">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('vendor.add_color') }}
              </button>
            </div>

            <!-- Empty State for Colors -->
            <div v-if="productData.colors.length === 0" class="vue-card" style="border: 2px dashed var(--gray-300);">
              <div class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-palette w-12 h-12 mb-4" style="color: var(--gray-400);"></i>
                <h3 class="vue-text-lg mb-2">{{ $t('vendor.no_colors_added_yet') }}</h3>
                <p class="text-center mb-4" style="color: var(--gray-600);">
                  {{ $t('vendor.add_at_least_one_color_variant') }}
                </p>
                <button type="button" class="vue-btn vue-btn-primary" @click="addNewColor">
                  <i class="fas fa-plus w-4 h-4"></i>
                  {{ $t('vendor.add_first_color') }}
                </button>
              </div>
            </div>

            <!-- Color Cards Container -->
            <div class="grid gap-6">
              <VendorColorVariantCard
                v-for="(color, index) in productData.colors"
                :ref="el => colorVariantRefs[index] = el"
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
                @save-color-first="handleSaveColorFirst"
              />
            </div>
          </div>

          <!-- Specifications Tab -->
          <div v-show="activeTab === 'specifications'" class="vue-tab-content space-y-6">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="vue-text-lg">{{ $t('vendor.product_specifications') }}</h3>
                <p class="text-sm" style="color: var(--gray-600);">{{ $t('vendor.add_detailed_specifications') }}</p>
              </div>
              <button type="button" class="vue-btn vue-btn-primary" @click="addNewSpecification">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('vendor.add_specification') }}
              </button>
            </div>

            <div class="vue-card">
              <div class="p-6">
                <div class="space-y-4">
                  <div v-if="productData.specifications.length === 0" class="text-center py-8">
                    <i class="fas fa-file-text w-12 h-12 mx-auto mb-4" style="color: var(--gray-400);"></i>
                    <h3 class="vue-text-lg mb-2">{{ $t('vendor.no_specifications_added_yet') }}</h3>
                    <p class="mb-4" style="color: var(--gray-600);">
                      {{ $t('vendor.add_technical_specifications') }}
                    </p>
                    <button type="button" class="vue-btn vue-btn-primary" @click="addNewSpecification">
                      <i class="fas fa-plus w-4 h-4"></i>
                      {{ $t('vendor.add_first_specification') }}
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
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('vendor.success') }}!</h3>
        <p class="text-sm text-gray-500 mb-6">{{ $t('vendor.product_updated_successfully') }}!</p>
        <button @click="closeSuccessModal"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
          {{ $t('vendor.continue') }}
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
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('vendor.error') }}</h3>
        <p class="text-sm text-gray-500 mb-6">{{ errorMessage }}</p>
        <button @click="closeErrorModal"
                class="w-full bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
          {{ $t('vendor.close') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch, nextTick } from 'vue'
import VendorColorVariantCard from './VendorColorVariantCard.vue'
import VendorSpecificationItem from './VendorSpecificationItem.vue'
import LanguageSwitch from '../common/LanguageSwitch.vue'

export default {
  name: 'VendorProductEditApp',
  components: {
    VendorColorVariantCard,
    VendorSpecificationItem,
    LanguageSwitch
  },
  props: {
    productId: {
      type: [String, Number],
      required: true
    },
    backUrl: {
      type: String,
      default: '/vendor/products'
    },
    editDataUrl: {
      type: String,
      default: null
    }
  },
  setup(props) {
    // Translation method
    const translate = (key, replacements = {}) => {
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
      branch_id: '',
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

    // Component refs for accessing child components
    const colorVariantRefs = ref([])

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
    const tabs = [
      { id: 'basic', label: translate('vendor.product_details'), icon: 'fas fa-box' },
      { id: 'colors', label: translate('vendor.colors_and_images'), icon: 'fas fa-palette' },
      { id: 'specifications', label: translate('vendor.specifications'), icon: 'fas fa-file-text' }
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
        // Use editDataUrl prop if provided, otherwise fall back to vendor URL
        const url = props.editDataUrl || `/vendor/products/${props.productId}/edit-data`
        const response = await window.axios.get(url)

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
        errors.name = translate('vendor.product_name_english_required') || 'Product name in English is required'
        isValid = false
      }

      if (!productData.product_name_arabic?.trim()) {
        errors.product_name_arabic = translate('vendor.product_name_arabic_required') || 'Product name in Arabic is required'
        isValid = false
      }

      // Description validation - if one is filled, both are required
      if (productData.description?.trim() && !productData.product_description_arabic?.trim()) {
        errors.product_description_arabic = translate('vendor.arabic_description_required_when_english_provided') || 'Arabic description is required when English description is provided'
        isValid = false
      }

      if (productData.product_description_arabic?.trim() && !productData.description?.trim()) {
        errors.description = translate('vendor.english_description_required_when_arabic_provided') || 'English description is required when Arabic description is provided'
        isValid = false
      }

      if (!productData.category_id) {
        errors.category_id = translate('vendor.category_selection_required') || 'Please select a product category'
        isValid = false
      }

      if (!productData.branch_id) {
        errors.branch_id = translate('vendor.branch_selection_required') || 'Please select a branch for this product'
        isValid = false
      }

      if (!productData.price || productData.price <= 0) {
        errors.price = translate('vendor.price_must_be_greater_than_zero') || 'Product price must be greater than 0 AED'
        isValid = false
      }

      if (!productData.stock || productData.stock < 0) {
        errors.stock = translate('vendor.stock_must_be_zero_or_greater') || 'Stock quantity must be 0 or greater'
        isValid = false
      }

      // Color validation
      if (productData.colors.length === 0) {
        errors.colors = translate('vendor.at_least_one_color_required') || 'At least one color variant is required for this product'
        isValid = false
      }

      // Check if at least one color has an image
      const hasColorWithImage = productData.colors.some(color => color.image || color.imageFile)
      if (!hasColorWithImage) {
        errors.colors = translate('vendor.at_least_one_color_must_have_image') || 'At least one color must have an image'
        isValid = false
      }

      return isValid
    }

    // Save product method
    const saveProduct = async () => {
      if (!validateForm()) {
        // Create a more specific error message based on the errors
        const errorFields = Object.keys(errors)
        let specificMessage = translate('vendor.please_fix_validation_errors') || 'Please fix the following validation errors:'

        if (errorFields.includes('name') || errorFields.includes('product_name_arabic')) {
          specificMessage += '\n• ' + (translate('vendor.product_name_both_languages_required') || 'Product name is required in both English and Arabic')
        }
        if (errorFields.includes('category_id')) {
          specificMessage += '\n• ' + (translate('vendor.category_selection_required') || 'Please select a product category')
        }
        if (errorFields.includes('branch_id')) {
          specificMessage += '\n• ' + (translate('vendor.branch_selection_required') || 'Please select a branch')
        }
        if (errorFields.includes('price')) {
          specificMessage += '\n• ' + (translate('vendor.price_must_be_greater_than_zero') || 'Price must be greater than 0')
        }
        if (errorFields.includes('colors')) {
          specificMessage += '\n• ' + (translate('vendor.color_variants_required') || 'Color variants and images are required')
        }

        errorMessage.value = specificMessage
        showErrorModal.value = true

        // Switch to the tab with errors
        if (errors.name || errors.product_name_arabic || errors.category_id || errors.branch_id || errors.price || errors.stock) {
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
          branch_id: productData.branch_id || '',
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

    // Store pending size creation data
    const pendingSizeCreation = ref(null)

    const handleSaveColorFirst = async (colorIndex, pendingSizeData = null) => {
      console.log('🎨 Save color first requested for color index:', colorIndex, 'with pending size data:', pendingSizeData)

      if (colorIndex < 0 || colorIndex >= productData.colors.length) {
        console.error('Invalid color index:', colorIndex)
        return
      }

      const color = productData.colors[colorIndex]

      // Validate that the color has required fields
      if (!color.name || !color.color_code) {
        console.error('Color missing required fields (name or color_code)')
        // You could show an error message to the user here
        return
      }

      // Store pending size data if provided
      if (pendingSizeData) {
        pendingSizeCreation.value = {
          colorIndex,
          sizeData: pendingSizeData
        }
        console.log('📝 Stored pending size creation data:', pendingSizeCreation.value)
      }

      try {
        console.log('💾 Saving color to backend before adding size...')

        // Prepare color data for saving
        const colorData = {
          name: color.name,
          color_code: color.color_code,
          price_adjustment: color.price_adjustment || 0,
          stock: color.stock || 0,
          display_order: color.display_order || colorIndex,
          is_default: color.is_default || false
        }

        // Save the color to the backend
        const response = await window.axios.post('/vendor/api/colors/create', {
          product_id: productData.id,
          ...colorData
        })

        if (response.data.success && response.data.color) {
          console.log('✅ Color saved successfully:', response.data.color)

          // Update the color object with the returned ID and data
          Object.assign(productData.colors[colorIndex], {
            id: response.data.color.id,
            ...response.data.color
          })

          console.log('🔄 Color updated with ID:', response.data.color.id)

          // Resume size creation if there's pending data
          if (pendingSizeCreation.value && pendingSizeCreation.value.colorIndex === colorIndex) {
            console.log('🔄 Resuming size creation after color save...')
            await nextTick() // Wait for DOM updates

            // Get the color variant component ref for this color index
            const colorVariantComponent = colorVariantRefs.value[colorIndex]

            if (colorVariantComponent && colorVariantComponent.resumeSizeCreation) {
              colorVariantComponent.resumeSizeCreation(pendingSizeCreation.value.sizeData)

              // Clear pending data
              pendingSizeCreation.value = null
            } else {
              console.error('Could not find color variant component to resume size creation for index:', colorIndex)
            }
          }
        } else {
          console.error('Failed to save color:', response.data)
        }
      } catch (error) {
        console.error('Error saving color:', error)
        // You could show an error message to the user here
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
          errors.category_id = translate('vendor.select_specific_subcategory')
        } else {
          // Clear any previous error
          delete errors.category_id
        }
      }
    }

    // Check if RTL is enabled
    const isRTL = computed(() => {
      return document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar'
    })

    // Lifecycle
    onMounted(() => {
      fetchProductData()
    })

    return {
      // Translation function
      $t: translate,

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

      colorVariantRefs,

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
      handleSaveColorFirst,
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

/* RTL Support */
.rtl {
  direction: rtl;
}

.rtl .vue-btn i {
  margin-left: 0.5rem;
  margin-right: 0;
}

.rtl .vue-btn i:first-child {
  margin-left: 0;
  margin-right: 0.5rem;
}

.rtl .flex {
  flex-direction: row-reverse;
}

.rtl .space-x-8 > :not([hidden]) ~ :not([hidden]) {
  --tw-space-x-reverse: 1;
}

.rtl .text-left {
  text-align: right;
}

.rtl .text-right {
  text-align: left;
}

</style>
