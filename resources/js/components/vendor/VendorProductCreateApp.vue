<template>
  <div class="vendor-product-create-app" :class="{ 'rtl': isRTL }" :style="{
    '--theme-primary': themeColors.primary,
    '--theme-primary-hover': themeColors.primaryHover,
    '--theme-primary-light': themeColors.primaryLight,
    '--theme-primary-dark': themeColors.primaryDark,
    '--theme-gradient': themeColors.gradient,
    '--theme-shadow': themeColors.shadow,
    '--theme-ring': themeColors.ring
  }">
    <!-- Loading State -->
    <div v-if="loading" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2" :style="{ borderColor: themeColors.primary }"></div>
        <p class="mt-4 text-gray-600 text-lg">{{ $t('vendor.loading_product_creation_form') }}</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="w-full px-0 sm:container sm:mx-auto sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-6 px-4 sm:px-0">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="text-center sm:text-left">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ $t('vendor.create_new_product') }}</h2>
            <p class="mt-1 text-sm sm:text-base text-gray-600 dark:text-gray-400">{{ $t('vendor.add_new_product_inventory') }}</p>
          </div>
          <div class="w-full sm:w-auto">
            <a :href="backUrl" class="inline-flex w-full items-center justify-center px-4 py-3 sm:py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
              <i class="fas fa-arrow-left" :class="isRTL ? 'ml-2' : 'mr-2'"></i> {{ $t('vendor.back_to_products') }}
            </a>
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
          <nav class="-mb-px flex flex-nowrap gap-4 overflow-x-auto px-4 sm:gap-8 sm:px-6" aria-label="Tabs">
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
        <div class="p-4 sm:p-6">
          <!-- Basic Information Tab -->
          <div v-show="activeTab === 'basic'" class="vue-tab-content space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h3 class="vue-text-lg">{{ $t('vendor.product_details') }}</h3>
            <p class="text-sm text-gray-600">{{ $t('vendor.enter_basic_details') }}</p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Product Name with Language Switch -->
              <div>
                <label for="name" class="block vue-text-sm mb-2">
                {{ $t('vendor.product_name') }} <span class="text-red-500">*</span>
              </label>

                <!-- Language Switch for Product Name -->
                <LanguageSwitch
                  v-model="currentLanguage"
                  @language-changed="handleLanguageChange"
                />

                <!-- English Product Name -->
                <div v-show="currentLanguage === 'en'">
                  <input
                    v-model="productData.name"
                    type="text"
                    class="vue-form-control"
                    :placeholder="$t('enter_product_name_english')"
                    required
                  />
                  <div v-if="errors.name" class="text-red-500 text-sm mt-1">{{ errors.name }}</div>
                </div>

                <!-- Arabic Product Name -->
                <div v-show="currentLanguage === 'ar'" :dir="isRTL ? 'rtl' : 'ltr'">
                  <input
                    v-model="productData.product_name_arabic"
                    type="text"
                    class="vue-form-control"
                    :class="{ 'text-right': isRTL }"
                    :placeholder="$t('enter_product_name_arabic')"
                    required
                  />
                  <div v-if="errors.product_name_arabic" class="text-red-500 text-sm mt-1">{{ errors.product_name_arabic }}</div>
                </div>
              </div>

              <!-- Category -->
              <div>
                <label for="category_id" class="block vue-text-sm mb-2">
                  {{ $t('vendor.category') }} <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="productData.category_id"
                  class="vue-form-control"
                  required
                  @change="validateCategorySelection"
                >
                  <option value="">{{ $t('vendor.select_category') }}</option>
                  <optgroup v-for="parentCategory in categories" :key="parentCategory.id" :label="parentCategory.name">
                    <option
                      v-for="childCategory in parentCategory.children"
                      :key="childCategory.id"
                      :value="childCategory.id"
                    >
                      {{ childCategory.name }}
                    </option>
                  </optgroup>
                </select>
                <div v-if="errors.category_id" class="text-red-500 text-sm mt-1">{{ errors.category_id }}</div>
              </div>

              <!-- Branch -->
              <div>
                <label for="branch_id" class="block vue-text-sm mb-2">
                  {{ $t('vendor.branch') }} <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="productData.branch_id"
                  class="vue-form-control"
                  :class="{ 'border-red-500': errors.branch_id }"
                  required
                >
                  <option value="">{{ branches.length === 0 ? $t('vendor.no_branches_available') : $t('vendor.select_branch') }}</option>
                  <option
                    v-for="branch in branches"
                    :key="branch.id"
                    :value="branch.id"
                  >
                    {{ branch.name }}
                  </option>
                </select>
                <div v-if="errors.branch_id" class="text-red-500 text-sm mt-1">{{ errors.branch_id }}</div>
                <div v-else-if="branches.length === 0" class="text-amber-600 text-sm mt-1">
                  <i class="fas fa-exclamation-triangle mr-1"></i>
                  {{ $t('vendor.need_create_branch_first') }} <a href="/vendor/branches/create" class="text-blue-600 hover:underline">{{ $t('vendor.create_branch') }}</a>
                </div>
                <div v-else-if="branches.length === 1" class="text-green-600 text-sm mt-1">
                  <i class="fas fa-check-circle mr-1"></i>
                  {{ $t('vendor.branch_automatically_selected') }}
                </div>
              </div>

              <!-- Price -->
              <div>
                <label for="price" class="block vue-text-sm mb-2">
                  {{ $t('vendor.price') }} <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                  </div>
                  <input
                    type="text"
                    v-model="productData.price"
                    class="vue-form-control pl-7"
                    placeholder="0.00"
                    required
                    @input="e => productData.price = e.target.value.replace(/[^0-9.]/g, '')"
                    @blur="e => productData.price = parseFloat(e.target.value) || 0"
                    pattern="[0-9]*\.?[0-9]*"
                    inputmode="decimal"
                  />
                </div>
                <div v-if="errors.price" class="text-red-500 text-sm mt-1">{{ errors.price }}</div>
              </div>

              <!-- Original Price -->
              <div>
                <label class="block vue-text-sm mb-2">{{ $t('vendor.original_price') }}</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                  </div>
                  <input
                    type="text"
                    v-model="productData.original_price"
                    class="vue-form-control pl-7"
                    placeholder="0.00"
                    @input="e => productData.original_price = e.target.value.replace(/[^0-9.]/g, '')"
                    @blur="e => productData.original_price = parseFloat(e.target.value) || null"
                    pattern="[0-9]*\.?[0-9]*"
                    inputmode="decimal"
                  />
                </div>
                <div v-if="errors.original_price" class="text-red-500 text-sm mt-1">{{ errors.original_price }}</div>
              </div>

              <!-- Stock -->
              <div>
                <label for="stock" class="block vue-text-sm mb-2">
                  {{ $t('vendor.total_stock') }} <span class="text-red-500">*</span>
                </label>
                <input
                  type="text" 
                  :value="totalAllocatedStock"
                  class="vue-form-control total-stock-readonly"
                  placeholder="0"
                  required
                  pattern="[0-9]*"
                  inputmode="numeric"
                  readonly
                />
                <p class="mt-1 text-xs text-gray-500">{{ $t('vendor.total_stock_quantity_available') }}</p>
                <div v-if="errors.stock" class="text-red-500 text-sm mt-1">{{ errors.stock }}</div>
              </div>
            </div>

            <!-- Description with Language Switch -->
            <div>
              <label class="block vue-text-sm mb-2">{{ $t('vendor.description') }}</label>

              <!-- Language Switch for Description -->
              <LanguageSwitch
                v-model="currentLanguage"
                @language-changed="handleLanguageChange"
              />

              <!-- English Description -->
              <div v-show="currentLanguage === 'en'">
                <textarea
                  v-model="productData.description"
                  rows="4"
                  class="vue-form-control"
                  :placeholder="$t('enter_product_description_english')"
                ></textarea>
                <div v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</div>
              </div>

              <!-- Arabic Description -->
              <div v-show="currentLanguage === 'ar'" :dir="isRTL ? 'rtl' : 'ltr'">
                <textarea
                  v-model="productData.product_description_arabic"
                  rows="4"
                  class="vue-form-control"
                  :class="{ 'text-right': isRTL }"
                  :placeholder="$t('enter_product_description_arabic')"
                ></textarea>
                <div v-if="errors.product_description_arabic" class="text-red-500 text-sm mt-1">{{ errors.product_description_arabic }}</div>
              </div>
            </div>

            <!-- Availability -->
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input
                  v-model="productData.is_available"
                  type="checkbox"
                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                />
              </div>
              <div class="ml-3 text-sm">
                <label class="font-medium text-gray-700 dark:text-gray-300">{{ $t('vendor.product_available_sale') }}</label>
                  <p class="text-gray-500 dark:text-gray-400">{{ $t('vendor.uncheck_if_not_available') }}</p>
              </div>
            </div>
          </div>

          <!-- Colors & Images Tab -->
          <div v-show="activeTab === 'colors'" class="vue-tab-content space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h3 class="vue-text-lg">{{ $t('vendor.colors_and_images') }}</h3>
            <p class="text-sm text-gray-600">{{ $t('vendor.add_color_variants_images') }}</p>
              </div>
              <button type="button" @click="addNewColor" style="color: #ffffff;
  border-color: transparent;
  box-shadow: 0 2px 4px 0 var(--theme-shadow);" class="vue-btn w-full justify-center sm:w-auto" :class="userRole === 'vendor' ? 'bg-blue-400 hover:bg-blue-500' : 'bg-orange-400 hover:bg-orange-500'">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('vendor.add_color') }}
              </button>
            </div>

            <!-- Colors List -->
            <div v-if="productData.colors.length > 0" class="space-y-4">
                <VendorColorVariantCard
                  v-for="(color, index) in productData.colors"
                  :key="index"
                  :color="color"
                  :index="index"
                  :is-default="color.is_default"
                  :product-id="'new'"
                  :general-stock="productData.stock"
                  :enforce-general-stock="false"
                  :all-colors="productData.colors"
                  :errors="errors"
                  :user-role="userRole"
                  @update="updateColor"
                @remove="removeColor"
                @set-default="setDefaultColor"
                @image-upload="handleImageUpload"
                @sizes-updated="handleColorSizesUpdated"
                @stock-corrected="handleStockCorrected"
              />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
              <i class="fas fa-palette text-gray-400 text-4xl mb-4"></i>
              <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('vendor.no_colors_added_yet') }}</h3>
              <p class="text-gray-500 mb-4">{{ $t('vendor.add_color_variants_appealing') }}</p>
              <button type="button" @click="addNewColor" style="color: #ffffff;
  border-color: transparent;
  box-shadow: 0 2px 4px 0 var(--theme-shadow);" class="vue-btn w-full justify-center sm:w-auto" :class="userRole === 'vendor' ? 'bg-blue-400 hover:bg-blue-500' : 'bg-orange-400 hover:bg-orange-500'">
                <i class="fas fa-plus mr-2"></i>
                {{ $t('vendor.add_first_color') }}
              </button>
            </div>

            <!-- Stock Allocation Summary -->
            <div v-if="productData.colors.length > 0 && productData.stock > 0" class="mt-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-orange-900">{{ $t('vendor.stock_allocation_progress') }}</span>
              <span class="text-sm text-orange-700">{{ totalAllocatedStock }} / {{ productData.stock }} {{ $t('vendor.allocated_stock') }}</span>
              </div>
              <div class="w-full bg-orange-200 rounded-full h-3">
                <div class="bg-orange-600 h-3 rounded-full transition-all duration-300"
                     :style="{ width: stockProgressPercentage + '%' }"
                     :class="{ 'bg-red-600': isStockOverAllocated }"></div>
              </div>
              <div v-if="isStockOverAllocated" class="mt-2 text-xs text-red-600">
                {{ $t('vendor.stock_over_allocated_adjust') }}
              </div>
              <div v-else-if="totalAllocatedStock < productData.stock" class="mt-2 text-xs text-amber-600">
                💡 {{ productData.stock - totalAllocatedStock }} {{ $t('vendor.remaining_stock') }}
              </div>
              <div v-else class="mt-2 text-xs text-green-600">
                ✅ {{ $t('vendor.all_stock_allocated') }}
              </div>
            </div>
          </div>

          <!-- Specifications Tab -->
          <div v-show="activeTab === 'specifications'" class="vue-tab-content space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h3 class="vue-text-lg">{{ $t('vendor.product_specifications') }}</h3>
            <p class="text-sm text-gray-600">{{ $t('vendor.add_detailed_specifications') }}</p>
              </div>
              <button type="button" @click="addNewSpecification" style="color: #ffffff;
  border-color: transparent;
  box-shadow: 0 2px 4px 0 var(--theme-shadow);" class="vue-btn w-full justify-center sm:w-auto" :class="userRole === 'vendor' ? 'bg-blue-400 hover:bg-blue-500' : 'bg-orange-400 hover:bg-orange-500'">
                <i class="fas fa-plus w-4 h-4"></i>
                {{ $t('vendor.add_specification') }}
              </button>
            </div>

            <!-- Specifications List -->
            <div v-if="productData.specifications.length > 0" class="space-y-4">
              <VendorSpecificationItem
                v-for="(spec, index) in productData.specifications"
                :key="index"
                :specification="spec"
                :index="index"
                @update="updateSpecification"
                @remove="removeSpecification"
              />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
              <i class="fas fa-file-text text-gray-400 text-4xl mb-4"></i>
              <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('vendor.no_specifications_added_yet') }}</h3>
              <p class="text-gray-500 mb-4">{{ $t('vendor.add_specifications_detailed_info') }}</p>
              <button type="button" @click="addNewSpecification" class="vue-btn w-full justify-center sm:w-auto" style="color: #ffffff;
  border-color: transparent;
  box-shadow: 0 2px 4px 0 var(--theme-shadow);" :class="userRole === 'vendor' ? 'bg-blue-400 hover:bg-blue-500' : 'bg-orange-400 hover:bg-orange-500'">
                <i class="fas fa-plus mr-2"></i>
                {{ $t('vendor.add_first_specification') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Tab Navigation Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-4">
          <div class="flex flex-col gap-4 sm:flex-row sm:justify-between">
            <button
              v-if="activeTab !== 'basic'"
              type="button"
              @click="previousTab"
              class="vue-btn vue-btn-secondary w-full justify-center sm:w-auto"
            >
              <i class="fas fa-arrow-left" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
              {{ $t('vendor.previous') }}
            </button>
            <div v-else></div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:space-x-3">
              <button
                v-if="activeTab !== 'specifications'"
                type="button"
                @click="nextTab"
                class="vue-btn w-full justify-center sm:w-auto" style="color: #ffffff;
  border-color: transparent;
  box-shadow: 0 2px 4px 0 var(--theme-shadow);" :class="userRole === 'vendor' ? 'bg-blue-400 hover:bg-blue-500' : 'bg-orange-400 hover:bg-orange-500'"
              >
                {{ $t('vendor.next') }}
                <i class="fas fa-arrow-right" :class="isRTL ? 'mr-2' : 'ml-2'"></i>
              </button>
              <button
                v-else
                type="button"
                @click="saveProduct"
                :disabled="saving"
                class="vue-btn vue-btn-success w-full justify-center sm:w-auto"
              >
                <i v-if="saving" class="fas fa-spinner fa-spin" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
                <i v-else class="fas fa-save" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
                {{ saving ? $t('vendor.saving') : $t('vendor.save_product') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto w-11/12 max-w-sm rounded-md border bg-white p-5 shadow-lg sm:top-20">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <i class="fas fa-check text-green-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-4">{{ $t('vendor.product_created_successfully') }}</h3>
          <p class="text-sm text-gray-500 mt-2">{{ $t('vendor.product_created_available_inventory') }}</p>
          <div class="flex justify-center space-x-3 mt-6">
            <button @click="closeSuccessModal" class="vue-btn vue-btn-secondary">
              {{ $t('vendor.view_products') }}
            </button>
            <button @click="createAnother" class="vue-btn vue-btn-primary" :style="userRole === 'vendor' ? 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);' : 'background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);'">
              {{ $t('vendor.create_another') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Error Modal -->
    <div v-if="showErrorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-10 mx-auto w-11/12 max-w-sm rounded-md border bg-white p-5 shadow-lg sm:top-20">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-4">{{ $t('vendor.error_creating_product') }}</h3>
          <div class="text-sm text-gray-500 mt-2 whitespace-pre-line">
            {{ errorMessage || $t('vendor.unexpected_error_try_again') }}
          </div>
          <div class="flex justify-center mt-6">
            <button @click="closeErrorModal" class="vue-btn" :style="userRole === 'vendor' ? 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);' : 'background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);'">
              {{ $t('vendor.try_again') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import VendorColorVariantCard from './VendorColorVariantCard.vue'
import VendorSpecificationItem from './VendorSpecificationItem.vue'
import LanguageSwitch from '../common/LanguageSwitch.vue'

export default {
  name: 'VendorProductCreateApp',
  components: {
    VendorColorVariantCard,
    VendorSpecificationItem,
    LanguageSwitch
  },
  props: {
    backUrl: {
      type: String,
      default: '/vendor/products'
    },
    createDataUrl: {
      type: String,
      default: '/vendor/products/create-data'
    },
    storeUrl: {
      type: String,
      default: '/vendor/products'
    },
    sessionStoreUrl: {
      type: String,
      default: '/vendor/products/session/store'
    },
    sessionGetUrl: {
      type: String,
      default: '/vendor/products/session/get'
    },
    sessionClearUrl: {
      type: String,
      default: '/vendor/products/session/clear'
    },
    userRole: {
      type: String,
      default: 'vendor' // 'vendor' or 'products_manager'
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

    // Modal states
    const showSuccessModal = ref(false)
    const showErrorModal = ref(false)
    const errorMessage = ref('')

    // Language switching
    const currentLanguage = ref('en')

    const handleLanguageChange = (language) => {
      currentLanguage.value = language
    }

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

    // Tab configuration
    const tabs = [
      { id: 'basic', label: translate('vendor.product_details'), icon: 'fas fa-box' },
      { id: 'colors', label: translate('vendor.colors_and_images'), icon: 'fas fa-palette' },
      { id: 'specifications', label: translate('vendor.specifications'), icon: 'fas fa-file-text' }
    ]

    // Computed properties
    const totalAllocatedStock = computed(() => {
      return productData.colors.reduce((total, color) => {
        return total + (parseInt(color.stock) || 0)
      }, 0)
    })

    watch(
      () => productData.colors,
      () => {
        productData.stock = totalAllocatedStock.value
      },
      { deep: true, immediate: true }
    )

    const stockProgressPercentage = computed(() => {
      if (productData.stock === 0) return 0
      return Math.min((totalAllocatedStock.value / productData.stock) * 100, 100)
    })

    const isStockOverAllocated = computed(() => {
      return totalAllocatedStock.value > productData.stock
    })

    // Theme colors based on user role
    const themeColors = computed(() => {
      if (props.userRole === 'products_manager') {
        return {
          primary: '#f59e0b', // Orange
          primaryHover: '#f97316',
          primaryLight: '#fef3c7',
          primaryDark: '#ea580c',
          gradient: 'linear-gradient(135deg, #f59e0b 0%, #f97316 100%)',
          shadow: 'rgba(245, 158, 11, 0.4)',
          ring: 'rgba(245, 158, 11, 0.1)'
        }
      } else {
        return {
          primary: '#3b82f6', // Blue
          primaryHover: '#2563eb',
          primaryLight: '#dbeafe',
          primaryDark: '#1d4ed8',
          gradient: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
          shadow: 'rgba(59, 130, 246, 0.4)',
          ring: 'rgba(59, 130, 246, 0.1)'
        }
      }
    })

    const showSaleBadge = computed(() => {
      return productData.original_price && productData.original_price > productData.price
    })

    const salePercentage = computed(() => {
      if (!showSaleBadge.value) return 0
      return Math.round(((productData.original_price - productData.price) / productData.original_price) * 100)
    })

    // Methods
    const getTabClasses = (tabId) => {
      const baseClasses = 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 themed-tab'
      if (activeTab.value === tabId) {
        return `${baseClasses} active-tab`
      }
      return `${baseClasses} border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300`
    }

    const fetchInitialData = async () => {
      try {
        loading.value = true
        const response = await fetch(props.createDataUrl)
        const data = await response.json()

        if (data.success) {
          categories.value = data.categories || []
          branches.value = data.branches || []

          // Auto-select branch if there's only one available
          if (branches.value.length === 1) {
            productData.branch_id = branches.value[0].id
          }
        } else {
          throw new Error(data.message || 'Failed to load data')
        }
      } catch (error) {
        console.error('Error fetching initial data:', error)
        errorMessage.value = 'Failed to load form data. Please refresh the page.'
        showErrorModal.value = true
      } finally {
        loading.value = false
      }
    }

    const clearErrors = () => {
      Object.keys(errors).forEach(key => delete errors[key])
    }

    const validateForm = () => {
      // Clear previous errors
      clearErrors()

      const newErrors = {}

      // Check if branches are available
      if (branches.value.length === 0) {
        newErrors.branch_id = 'No branches available. Please create a branch first.'
        return false
      }

      // Basic validation - Product name required in both languages
      if (!productData.name.trim()) {
        newErrors.name = translate('vendor.product_name_english_required') || 'Product name in English is required'
      }

      if (!productData.product_name_arabic.trim()) {
        newErrors.product_name_arabic = translate('vendor.product_name_arabic_required') || 'Product name in Arabic is required'
      }

      // Description validation - if one is filled, both are required
      if (productData.description.trim() && !productData.product_description_arabic.trim()) {
        newErrors.product_description_arabic = translate('vendor.arabic_description_required_when_english_provided') || 'Arabic description is required when English description is provided'
      }

      if (productData.product_description_arabic.trim() && !productData.description.trim()) {
        newErrors.description = translate('vendor.english_description_required_when_arabic_provided') || 'English description is required when Arabic description is provided'
      }

      if (!productData.category_id) {
        newErrors.category_id = translate('vendor.category_selection_required') || 'Please select a product category'
      }

      if (!productData.branch_id || productData.branch_id === '') {
        newErrors.branch_id = translate('vendor.branch_selection_required') || 'Please select a branch for this product'
      }

      if (!productData.price || productData.price <= 0) {
        newErrors.price = translate('vendor.price_must_be_greater_than_zero') || 'Product price must be greater than 0 AED'
      }

      if (!productData.stock || productData.stock < 0) {
        newErrors.stock = translate('vendor.stock_must_be_zero_or_greater') || 'Stock quantity must be 0 or greater'
      }

      // Colors validation
      if (productData.colors.length === 0) {
        newErrors.colors = translate('vendor.at_least_one_color_required') || 'At least one color variant is required for this product'
      }

      // Check if at least one color is marked as default
      const hasDefaultColor = productData.colors.some(color => color.is_default)
      if (productData.colors.length > 0 && !hasDefaultColor) {
        newErrors.colors = translate('vendor.one_color_must_be_default') || 'Please mark one color as the default option'
      }

      // Validate color images
      for (let i = 0; i < productData.colors.length; i++) {
        const color = productData.colors[i]
        if (!color.name) {
          newErrors[`colors.${i}.name`] = translate('vendor.color_name_required') || 'Color name is required'
        }
        if (!color.image) {
          newErrors[`colors.${i}.image`] = translate('vendor.color_image_required') || 'Please upload an image for this color'
        }
      }

      Object.assign(errors, newErrors)
      return Object.keys(newErrors).length === 0
    }

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
        return
      }

      try {
        saving.value = true

        const formData = new FormData()

        // Add basic product data
        Object.keys(productData).forEach(key => {
          if (key !== 'colors' && key !== 'specifications') {
            let value = productData[key]
            // Ensure branch_id is sent as a number
            if (key === 'branch_id' && value) {
              value = parseInt(value)
            }
            // Convert boolean is_available to string '1' or '0'
            if (key === 'is_available') {
              value = value ? '1' : '0'
            }
            // Convert boolean is_multi_branch to string '1' or '0'
            if (key === 'is_multi_branch') {
              value = value ? '1' : '0'
            }
            // Only append original_price if it has a value
            if (key === 'original_price' && (value === null || value === '')) {
              return // Skip null or empty original_price
            }
            formData.append(key, value)
          }
        })

        // Debug: Log the branch_id being sent
        console.log('Submitting product with branch_id:', productData.branch_id)

        // Add colors data
        productData.colors.forEach((color, index) => {
          const nameArabic = (typeof color.name_arabic === 'string' && color.name_arabic.trim() !== '')
            ? color.name_arabic
            : (color.name || '')

          Object.keys(color).forEach(key => {
            if (key !== 'image' && key !== 'sizes') {  // Exclude sizes - handle separately
              let value = color[key]
              // Normalize Arabic name and boolean default flag
              if (key === 'name_arabic') {
                value = nameArabic
              } else if (key === 'is_default') {
                value = value ? '1' : '0'
              }
              formData.append(`colors[${index}][${key}]`, value ?? '')
            }
          })

          // Ensure Arabic name is always sent even if the property was missing
          if (!('name_arabic' in color)) {
            formData.append(`colors[${index}][name_arabic]`, nameArabic)
          }

          // Add color image if it exists
          if (color.image instanceof File) {
            formData.append(`color_images[${index}]`, color.image)
          }
        })

        // Add size data for each color
        productData.colors.forEach((color, colorIndex) => {
          if (color.sizes && color.sizes.length > 0) {
            color.sizes.forEach((size, sizeIndex) => {
              // Map the size data to match backend validation rules
              formData.append(`colors[${colorIndex}][sizes][${sizeIndex}][category]`, size.category || 'clothes')
              formData.append(`colors[${colorIndex}][sizes][${sizeIndex}][name]`, size.name)
              formData.append(`colors[${colorIndex}][sizes][${sizeIndex}][value]`, size.value)
              formData.append(`colors[${colorIndex}][sizes][${sizeIndex}][stock]`, size.stock || 0)
              formData.append(`colors[${colorIndex}][sizes][${sizeIndex}][price_adjustment]`, size.price_adjustment || 0)
              formData.append(`colors[${colorIndex}][sizes][${sizeIndex}][display_order]`, size.display_order || 0)
              formData.append(`colors[${colorIndex}][sizes][${sizeIndex}][is_default]`, size.is_default ? '1' : '0')
            })
          }
        })

        // Add specifications data
        productData.specifications.forEach((spec, index) => {
          Object.keys(spec).forEach(key => {
            formData.append(`specifications[${index}][${key}]`, spec[key])
          })
        })

        const response = await fetch(props.storeUrl, {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })

        // Check if response is ok
        if (!response.ok) {
          // Try to get error message from response
          let errorMessage = `HTTP ${response.status}: ${response.statusText}`
          try {
            const errorData = await response.json()
            if (errorData.message) {
              errorMessage = errorData.message
            } else if (errorData.errors) {
              // Handle validation errors - populate the errors object for field-specific errors
              Object.assign(errors, errorData.errors)

              // Create a user-friendly error message
              const validationErrors = Object.values(errorData.errors).flat()
              errorMessage = 'Please fix the following validation errors: ' + validationErrors.join(', ')
            }
          } catch (jsonError) {
            // Response is not JSON, likely HTML error page
            const responseText = await response.text()
            if (responseText.includes('<!DOCTYPE')) {
              errorMessage = 'Server returned an error page instead of JSON. Please check server logs.'
            } else {
              errorMessage = responseText.substring(0, 200) + '...'
            }
          }
          throw new Error(errorMessage)
        }

        const result = await response.json()

        if (result.success) {
          clearErrors() // Clear any previous errors on success
          showSuccessModal.value = true
        } else {
          throw new Error(result.message || 'Failed to create product')
        }
      } catch (error) {
        console.error('Error saving product:', error)
        errorMessage.value = error.message || 'An error occurred while saving the product.'
        showErrorModal.value = true
      } finally {
        saving.value = false
      }
    }

    // Tab navigation methods
    const nextTab = () => {
      const currentIndex = tabs.findIndex(tab => tab.id === activeTab.value)
      if (currentIndex < tabs.length - 1) {
        activeTab.value = tabs[currentIndex + 1].id
      }
    }

    const previousTab = () => {
      const currentIndex = tabs.findIndex(tab => tab.id === activeTab.value)
      if (currentIndex > 0) {
        activeTab.value = tabs[currentIndex - 1].id
      }
    }

    // Color management methods
    const addNewColor = () => {
      const newColor = {
        name: '',
        name_arabic: '',
        color_code: '',
        price_adjustment: 0,
        stock: 0,
        display_order: productData.colors.length,
        is_default: productData.colors.length === 0, // First color is default
        image: null
      }
      productData.colors.push(newColor)
    }

    const updateColor = (index, field, value) => {
      if (productData.colors[index]) {
        productData.colors[index][field] = value
      }
    }

    const removeColor = (index) => {
      if (productData.colors[index]) {
        const wasDefault = productData.colors[index].is_default
        productData.colors.splice(index, 1)

        // If we removed the default color and there are still colors, make the first one default
        if (wasDefault && productData.colors.length > 0) {
          productData.colors[0].is_default = true
        }
      }
    }

    const setDefaultColor = (index) => {
      productData.colors.forEach((color, i) => {
        color.is_default = i === index
      })
    }

    const handleImageUpload = (index, file) => {
      if (productData.colors[index]) {
        productData.colors[index].image = file
      }
    }

    // Specification management methods
    const addNewSpecification = () => {
      const newSpec = {
        key: '',
        value: '',
        display_order: productData.specifications.length
      }
      productData.specifications.push(newSpec)
    }

    const updateSpecification = (index, field, value) => {
      if (productData.specifications[index]) {
        productData.specifications[index][field] = value
      }
    }

    const removeSpecification = (index) => {
      if (productData.specifications[index]) {
        productData.specifications.splice(index, 1)
      }
    }

    // Size management event handlers
    const handleColorSizesUpdated = (colorIndex, sizes) => {
      if (productData.colors[colorIndex]) {
        productData.colors[colorIndex].sizes = sizes
      }
    }

    const handleStockCorrected = (data) => {
      // Handle stock correction feedback if needed
      console.log('Stock corrected:', data)
    }

    // Modal methods
    const closeSuccessModal = () => {
      showSuccessModal.value = false
      window.location.href = props.backUrl
    }

    const closeErrorModal = () => {
      showErrorModal.value = false
    }

    const createAnother = () => {
      showSuccessModal.value = false
      // Reset form data
      Object.assign(productData, {
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
      activeTab.value = 'basic'
      Object.keys(errors).forEach(key => delete errors[key])
    }

    // Utility methods
    const findCategoryById = (id) => {
      for (const parent of categories.value) {
        for (const child of parent.children || []) {
          if (child.id == id) {
            return child
          }
        }
      }
      return null
    }

    const validateCategorySelection = () => {
      const category = findCategoryById(productData.category_id)
      if (category && category.parent_id === null) {
        alert('Please select a subcategory, not a main category.')
        productData.category_id = ''
      }
    }

    // Check if RTL is enabled
    const isRTL = computed(() => {
      return document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar'
    })

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
      totalAllocatedStock,
      stockProgressPercentage,
      isStockOverAllocated,
      showSaleBadge,
      salePercentage,
      themeColors,

      // Methods
      getTabClasses,
      fetchInitialData,
      clearErrors,
      validateForm,
      saveProduct,
      nextTab,
      previousTab,
      addNewColor,
      updateColor,
      removeColor,
      setDefaultColor,
      handleImageUpload,
      addNewSpecification,
      updateSpecification,
      removeSpecification,
      handleColorSizesUpdated,
      handleStockCorrected,
      closeSuccessModal,
      closeErrorModal,
      createAnother,
      findCategoryById,
      validateCategorySelection,
      handleLanguageChange,
      isRTL,
      $t: translate
    }
  }
}
</script>

<style scoped>
/* Vue component specific styles */
.vendor-product-create-app {
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
  border-color: var(--theme-primary);
  box-shadow: 0 0 0 3px var(--theme-ring);
}

.total-stock-readonly {
  background-color: var(--gray-100);
  color: var(--gray-600);
  cursor: default;
  caret-color: transparent;
}

.total-stock-readonly:focus {
  outline: none;
  border-color: #d1d5db;
  box-shadow: none;
}

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
 
  /* Blue for vendor, Orange for products_manager - controlled by themeColors computed property */
  color: #ffffff;
  border-color: transparent;
  box-shadow: 0 2px 4px 0 var(--theme-shadow);
}

.vue-btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px 0 var(--theme-shadow);
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
/* RTL Support */
.vendor-product-create-app.rtl {
  direction: rtl;
}

.vendor-product-create-app.rtl .text-left {
  text-align: right;
}

.vendor-product-create-app.rtl .text-right {
  text-align: left;
}

.vendor-product-create-app.rtl .float-left {
  float: right;
}

.vendor-product-create-app.rtl .float-right {
  float: left;
}

.vendor-product-create-app.rtl .border-l {
  border-left: none;
  border-right: 1px solid;
}

.vendor-product-create-app.rtl .border-r {
  border-right: none;
  border-left: 1px solid;
}

.vendor-product-create-app.rtl .rounded-l {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  border-top-right-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
}

.vendor-product-create-app.rtl .rounded-r {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-top-left-radius: 0.375rem;
  border-bottom-left-radius: 0.375rem;
}

/* Form inputs RTL */
.vendor-product-create-app.rtl input[type="text"],
.vendor-product-create-app.rtl input[type="number"],
.vendor-product-create-app.rtl textarea,
.vendor-product-create-app.rtl select {
  text-align: right;
}

/* Tab navigation RTL */
.vendor-product-create-app.rtl .tab-navigation {
  direction: rtl;
}

/* Button groups RTL */
.vendor-product-create-app.rtl .flex {
  direction: rtl;
}

/* Dynamic Theme Styles */
.themed-tab.active-tab {
  border-color: var(--theme-primary) !important;
  color: var(--theme-primary) !important;
}

.themed-tab.active-tab:hover {
  border-color: var(--theme-primary-hover) !important;
  color: var(--theme-primary-hover) !important;
}

.vendor-product-create-app.rtl .justify-between {
  flex-direction: row-reverse;
}

</style>
