<template>
  <div class="vue-card  transition-all duration-200"
       :class= "{ 'ring-2 border-primary-200': isDefault, 'rtl': isRTL } "
       :style="isDefault ? { '--tw-ring-color': 'var(--primary-blue)' } : {}">
    <div class="p-6 border-b" style="border-color: var(--gray-200);">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm"
               :style="{ backgroundColor: color.color_code || '#000000' }"></div>
          <h4 class="vue-text-lg">
            {{ $t('vendor.color_variant') }} {{ index + 1 }}
            <span v-if="isDefault"
                  :class="isRTL ? 'mr-2' : 'ml-2'"
                  class="inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                  style="background-color: var(--gray-100); color: var(--primary-blue-hover);">
              <i :class="isRTL ? 'ml-1' : 'mr-1'" class="fas fa-star w-3 h-3"></i>
              {{ $t('vendor.default') }}
            </span>
          </h4>
        </div>
        <div class="flex items-center gap-2">
          <button v-if="!isDefault"
                  type="button"
                  class="vue-btn-blue-solid text-sm font-medium"
                  @click="$emit('set-default', index)">
            <i :class="isRTL ? 'ml-2' : 'mr-2'" class="fas fa-star"></i>
            {{ $t('vendor.set_as_default') }}
          </button>
          <button type="button" 
                  class="remove-item p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                  @click="$emit('remove', index)">
            <i class="fas fa-trash w-4 h-4"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="p-6 space-y-6">
      <div class="grid lg:grid-cols-2 gap-6">
        <!-- Color Details -->
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block vue-text-sm">
                {{ $t('vendor.color_name') }} <span class="text-red-500">*</span>
              </label>

              <!-- Color Selection Dropdown -->
              <div class="color-selection-container">
                <div @click="toggleColorDropdown"
                     :class="[
                       { 'active': showColorDropdown },
                       userRole === 'vendor' ? 'selected-color-display-vendor' : 'selected-color-display-pm'
                     ]">
                  <div class="selected-color-preview">
                    <div class="color-swatch"
                         :style="{ backgroundColor: getColorCode(color.name) || '#e5e7eb' }"></div>
                    <div class="color-info">
                      <span class="color-name">{{ localizedSelectedName }}</span>
                      <span v-if="color.name" class="color-code">{{ getColorCode(color.name) }}</span>
                    </div>
                  </div>
                  <i class="fas fa-chevron-down dropdown-arrow"
                     :class="{ 'rotated': showColorDropdown }"></i>
                </div>

                <!-- Color Dropdown -->
                <div v-if="showColorDropdown" class="color-dropdown">
                  <!-- Search Input -->
                  <div class="color-search">
                    <input type="text"
                           v-model="colorSearchQuery"
                           :placeholder="$t('vendor.search_colors')"
                           :class="userRole === 'vendor' ? 'color-search-input-vendor' : 'color-search-input-pm'"
                           @click.stop>
                  </div>

                  <!-- Color Grid -->
                  <div :class="userRole === 'vendor' ? 'color-grid-vendor' : 'color-grid-pm'">
                    <div v-for="colorOption in filteredColorOptions"
                         :key="colorOption.name"
                         :class="[
                           userRole === 'vendor' ? 'color-option-vendor' : 'color-option-pm',
                           { 'selected': color.name === colorOption.name }
                         ]"
                         @click="selectColor(colorOption.name)">
                      <div class="color-swatch"
                           :style="{ backgroundColor: colorOption.code }"></div>
                      <span class="color-name">{{ getLocalizedColorLabel(colorOption) }}</span>
                      <span class="color-code">{{ colorOption.code }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Color Name Error Display -->
            <div v-if="errors && errors[`colors.${index}.name`]" class="text-red-500 text-sm mt-1">
              {{ errors[`colors.${index}.name`] }}
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm font-medium">
                <i :class="isRTL ? 'ml-2' : 'mr-2'" class="fas fa-palette w-4 h-4 text-blue-500"></i>
                {{ $t('vendor.color_code') }}
              </label>
              <div class="flex gap-3">
                <div class="relative">
                  <input type="color"
                         :value="color.color_code || '#000000'"
                         @input="updateColor('color_code', $event.target.value)"
                         class="w-14 h-12 p-1 border-2 border-blue-200 rounded-md shadow-sm hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 cursor-pointer">
                  <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-eye-dropper text-white text-xs"></i>
                  </div>
                </div>
                <div class="flex-1 relative">
                  <input type="text"
                         :value="color.color_code"
                         @input="updateColor('color_code', $event.target.value)"
                         placeholder="#FF0000"
                         class="vue-form-control  text-sm font-mono tracking-wider"
                         :class="userRole === 'vendor' ? 'vue-form-control-vendor' : 'vue-form-control-pm'"
                         pattern="^#[0-9A-Fa-f]{6}$"
                         title="Enter a valid hex color code (e.g., #FF0000)">
                  
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block vue-text-sm">{{ $t('vendor.price_adjustment') }}</label>
              <input type="number"
                     step="0.01"
                     :value="color.price_adjustment"
                     @input="updateColor('price_adjustment', parseFloat($event.target.value) || 0)"
                     @keypress="$event.key.match(/[0-9.]/) === null && $event.preventDefault()"
                     @paste="e => { e.preventDefault(); const text = e.clipboardData.getData('text'); if(text.match(/^[0-9.]*$/)) e.target.value = text; }"
                     placeholder="0.00"
                     :class="userRole === 'vendor' ? 'vue-form-control-vendor' : 'vue-form-control-pm'">
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm">
                {{ $t('vendor.stock') }}
                <span v-if="isStockExceeded" :class="isRTL ? 'mr-1' : 'ml-1'" class="text-red-500 text-xs">
                  {{ $t('vendor.exceeds_available') }} : {{ availableStock }}
                </span>
              </label>
              <div class="relative">
                <input type="number"
                       min="0"
                       :max="availableStock"
                       :value="color.stock"
                       @input="e => updateColor('stock', parseInt(e.target.value) || 0)"
                       @keypress="$event.key.match(/[0-9]/) === null && $event.preventDefault()"
                       @blur="handleStockBlur($event)"
                       @paste="handleStockPaste($event)"
                       @keydown="handleStockKeydown($event)"
                       placeholder="0"
                       class="vue-form-control transition-all duration-200"
                       :class="[
                         userRole === 'vendor' ? 'vue-form-control-vendor' : 'vue-form-control-pm',
                         isStockExceeded ? 'border-red-500 bg-red-50' : '',
                         stockCorrectionApplied ? 'border-green-500 bg-green-50' : ''
                       ]"
                       ref="stockInput">
                <div v-if="showStockCorrection" class="absolute top-full left-0 right-0 mt-1 p-2 bg-amber-50 border border-amber-200 rounded text-xs text-amber-700 animate-fade-in">
                  {{ stockCorrectionMessage }}
                </div>
              </div>
              <!-- Stock allocation info -->
              <div class="text-xs text-gray-600 space-y-1">
                <div class="flex justify-between">
                  <span>{{ $t('vendor.available_for_this_color') }}:</span>
                  <span class="font-medium">{{ availableStock }}</span>
                </div>
                <div class="flex justify-between">
                  <span>{{ $t('vendor.currently_allocated') }}:</span>
                  <span class="font-medium" :class="{ 'text-red-600': isStockExceeded }">{{ color.stock || 0 }}</span>
                </div>
              </div>
              <!-- Stock progress bar -->
              <div v-if="generalStock > 0" class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                     :style="{ width: Math.min((parseInt(color.stock) || 0) / availableStock * 100, 100) + '%' }"
                     :class="{ 'bg-red-600': isStockExceeded }"></div>
              </div>
            </div>
          </div>

          <div class="space-y-2">
            <label class="block vue-text-sm">{{ $t('vendor.display_order') }}</label>
            <input type="number"
                   :value="color.display_order"
                   @input="updateColor('display_order', parseInt($event.target.value) || 0)"
                   @keypress="$event.key.match(/[0-9]/) === null && $event.preventDefault()"
                   min="0"
                   placeholder="0"
                   :class="userRole === 'vendor' ? 'vue-form-control-vendor' : 'vue-form-control-pm'">
          </div>
        </div>

        <!-- Image Upload -->
        <div class="space-y-4">
          <div class="space-y-2">
            <label class="block vue-text-sm">
              {{ $t('vendor.color_image') }} <span class="text-red-500">*</span>
            </label>

            <!-- Image Preview Container -->
            <div class="image-preview-container"
                 :class="{ 'has-image': color.image }"
                 style="width: 300px; height: 400px; border: 2px dashed #d1d5db; border-radius: 8px; background-color: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
              <img v-if="imagePreviewUrl"
                   :src="imagePreviewUrl"
                   class="image-preview"
                   style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;"
                   alt="Image Preview">
              <div v-else class="image-placeholder text-center">
                <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                <p class="text-gray-500 text-sm">{{ $t('vendor.no_image_selected') }}</p>
                <p class="text-gray-400 text-xs">{{ $t('vendor.image_preview_size') }}</p>
              </div>
            </div>

            <input type="file"
                   @change="handleImageUpload"
                   class="modern-file-input block w-full text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gradient-to-r file:from-amber-50 file:to-orange-50 file:text-amber-700 hover:file:from-amber-100 hover:file:to-orange-100 file:transition-all file:duration-200 file:shadow-sm hover:file:shadow-md file:cursor-pointer"
                   accept="image/*"
                   style="max-width: 300px;">

            <p class="mt-1 text-xs text-gray-500" style="max-width: 300px;">
              {{ $t('vendor.image_format_info') }}
            </p>

            <!-- Color Image Error Display -->
            <div v-if="errors && (errors[`colors.${index}.image`] || errors[`color_images.${index}`])" class="text-red-500 text-sm mt-1">
              {{ errors[`colors.${index}.image`] || errors[`color_images.${index}`] }}
            </div>

            <!-- Default Color Checkbox -->
            <div class="mt-4">
              <label class="block vue-text-sm mb-2">{{ $t('vendor.default_color') }}</label>
              <div class="flex items-start">
                <input type="checkbox"
                       :checked="isDefault"
                       @change="$emit('set-default', index)"
                       class="default-color-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1">
                <span :class="isRTL ? 'mr-2' : 'ml-2'" class="text-sm text-gray-500">{{ $t('vendor.main_product_image_info') }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Size Management Section -->
      <div class="col-span-full">
        <VendorSizeManagement
          v-if="shouldShowSizeManagement"
          ref="sizeManagementRef"
          :key="`size-mgmt-${index}-${color.id || 'new'}`"
          :color-id="color.id"
          :product-id="productId"
          :color-stock="parseInt(color.stock) || 0"
          @sizes-updated="handleSizesUpdated"
          @save-color-first="handleSaveColorFirst"
        />
        <div v-else-if="!color.name || !color.stock" class="text-center py-8" style="border-top: 1px solid var(--gray-200); margin-top: 1.5rem; padding-top: 1.5rem;">
          <i class="fas fa-info-circle w-6 h-6 mb-2" style="color: var(--gray-400);"></i>
          <p class="text-sm" style="color: var(--gray-600);">
            {{ $t('vendor.set_color_name_stock_for_sizes') }}
          </p>
        </div>
        <div v-else class="text-center py-8" style="border-top: 1px solid var(--gray-200); margin-top: 1.5rem; padding-top: 1.5rem;">
          <i class="fas fa-info-circle w-6 h-6 mb-2" style="color: var(--gray-400);"></i>
          <p class="text-sm" style="color: var(--gray-600);">
            {{ $t('vendor.save_color_first_for_sizes') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import VendorSizeManagement from './VendorSizeManagement.vue'

const colorOptions = [
  { name: 'Red', value: '#FF0000' },
  { name: 'Blue', value: '#0000FF' },
  { name: 'Green', value: '#008000' },
  { name: 'Yellow', value: '#FFFF00' },
  { name: 'Orange', value: '#FFA500' },
  { name: 'Purple', value: '#800080' },
  { name: 'Pink', value: '#FFC0CB' },
  { name: 'Brown', value: '#A52A2A' },
  { name: 'Black', value: '#000000' },
  { name: 'White', value: '#FFFFFF' },
  { name: 'Gray', value: '#808080' },
  { name: 'Navy', value: '#000080' },
  { name: 'Maroon', value: '#800000' },
  { name: 'Olive', value: '#808000' },
  { name: 'Lime', value: '#00FF00' },
  { name: 'Aqua', value: '#00FFFF' },
  { name: 'Teal', value: '#008080' },
  { name: 'Silver', value: '#C0C0C0' },
  { name: 'Fuchsia', value: '#FF00FF' },
  { name: 'Coral', value: '#FF7F50' },
  { name: 'Salmon', value: '#FA8072' },
  { name: 'Khaki', value: '#F0E68C' },
  { name: 'Violet', value: '#EE82EE' },
  { name: 'Indigo', value: '#4B0082' },
  { name: 'Turquoise', value: '#40E0D0' },
  { name: 'Gold', value: '#FFD700' },
  { name: 'Crimson', value: '#DC143C' },
  { name: 'Chocolate', value: '#D2691E' },
  { name: 'Beige', value: '#F5F5DC' },
  
]

export default {
  name: 'VendorColorVariantCard',
  components: {
    VendorSizeManagement
  },
  props: {
    color: {
      type: Object,
      required: true
    },
    index: {
      type: Number,
      required: true
    },
    totalStock: {
      type: Number,
      default: 0
    },
    allocatedStock: {
      type: Number,
      default: 0
    },
    isDefault: {
      type: Boolean,
      default: false
    },
    allColors: {
      type: Array,
      default: () => []
    },
    generalStock: {
      type: Number,
      default: 0
    },
    productId: {
      type: [String, Number],
      default: null
    },
    errors: {
      type: Object,
      default: () => ({})
    },
    userRole: {
      type: String,
      default: 'vendor'
    }
  },
  emits: ['update', 'remove', 'set-default', 'image-upload', 'sizes-updated', 'stock-corrected'],
  setup(props, { emit }) {
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
    
    // RTL support
    const currentLocale = computed(() => {
      return (window.Laravel?.locale || document.documentElement.lang || 'en').toString()
    })

    const isArabicLocale = computed(() => currentLocale.value.toLowerCase().startsWith('ar'))

    const isRTL = computed(() => {
      const locale = currentLocale.value.toLowerCase()
      const docDir = (document.documentElement.getAttribute('dir') || '').toLowerCase()
      // Treat any Arabic/Hebrew/Farsi variant (e.g., ar_AE) as RTL, or if document dir is rtl
      return docDir === 'rtl' || ['ar', 'he', 'fa'].some(lang => locale.startsWith(lang))
    })
    
    // Currency formatting
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
      }).format(amount)
    }
    const showColorDropdown = ref(false)
    const colorSearchQuery = ref('')
    const imagePreviewUrl = ref('')

    // Component refs
    const sizeManagementRef = ref(null)

    // Stock validation reactive refs
    const stockCorrectionMessage = ref('')
    const showStockCorrection = ref(false)
    const stockCorrectionApplied = ref(false)
    const stockInput = ref(null)

    // Color options array
    const colorOptionsArray = [
      { name: 'No Color', name_arabic: 'بدون لون', code: '#F3F4F6' },
      { name: 'Red', name_arabic: 'أحمر', code: '#FF0000' },
      { name: 'Crimson', name_arabic: 'قرمزي', code: '#DC143C' },
      { name: 'FireBrick', name_arabic: 'قرميد', code: '#B22222' },
      { name: 'DarkRed', name_arabic: 'أحمر داكن', code: '#8B0000' },
      { name: 'Orange', name_arabic: 'برتقالي', code: '#FFA500' },
      { name: 'DarkOrange', name_arabic: 'برتقالي داكن', code: '#FF8C00' },
      { name: 'Gold', name_arabic: 'ذهبي', code: '#FFD700' },
      { name: 'Yellow', name_arabic: 'أصفر', code: '#FFFF00' },
      { name: 'Green', name_arabic: 'أخضر', code: '#008000' },
      { name: 'Lime', name_arabic: 'ليموني', code: '#00FF00' },
      { name: 'ForestGreen', name_arabic: 'أخضر غامق', code: '#228B22' },
      { name: 'DarkGreen', name_arabic: 'أخضر داكن', code: '#006400' },
      { name: 'Blue', name_arabic: 'أزرق', code: '#0000FF' },
      { name: 'MediumBlue', name_arabic: 'أزرق متوسط', code: '#0000CD' },
      { name: 'DarkBlue', name_arabic: 'أزرق داكن', code: '#00008B' },
      { name: 'Navy', name_arabic: 'كحلي', code: '#000080' },
      { name: 'SkyBlue', name_arabic: 'أزرق سماوي', code: '#87CEEB' },
      { name: 'Purple', name_arabic: 'بنفسجي', code: '#800080' },
      { name: 'Violet', name_arabic: 'بنفسجي فاتح', code: '#EE82EE' },
      { name: 'Magenta', name_arabic: 'أرجواني', code: '#FF00FF' },
      { name: 'Pink', name_arabic: 'وردي', code: '#FFC0CB' },
      { name: 'Brown', name_arabic: 'بني', code: '#A52A2A' },
      { name: 'Chocolate', name_arabic: 'شوكولا', code: '#D2691E' },
      { name: 'Tan', name_arabic: 'أسمر فاتح', code: '#D2B48C' },
      { name: 'Black', name_arabic: 'أسود', code: '#000000' },
      { name: 'Gray', name_arabic: 'رمادي', code: '#808080' },
      { name: 'Silver', name_arabic: 'فضي', code: '#C0C0C0' },
      { name: 'White', name_arabic: 'أبيض', code: '#FFFFFF' },
      
    ]

    const filteredColorOptions = computed(() => {
      if (!colorSearchQuery.value) {
        return colorOptionsArray
      }
      return colorOptionsArray.filter(color =>
        color.name.toLowerCase().includes(colorSearchQuery.value.toLowerCase()) ||
        (color.name_arabic && color.name_arabic.toLowerCase().includes(colorSearchQuery.value.toLowerCase()))
      )
    })

    // Stock validation computed properties
    const otherColorsStock = computed(() => {
      if (!props.allColors || !Array.isArray(props.allColors)) {
        return 0
      }
      return props.allColors
        .filter((_, index) => index !== props.index)
        .reduce((total, color) => total + (parseInt(color.stock) || 0), 0)
    })

    const availableStock = computed(() => {
      return Math.max(0, props.generalStock - otherColorsStock.value)
    })

    const isStockExceeded = computed(() => {
      return (parseInt(props.color.stock) || 0) > availableStock.value
    })

    // Computed property to determine when to show size management
    const shouldShowSizeManagement = computed(() => {
      // Show size management if:
      // 1. Color has an ID (existing color), OR
      // 2. Color has both name and stock > 0 (new color with required fields set)
      return props.color.id || (props.color.name && (parseInt(props.color.stock) || 0) > 0)
    })

    const getColorCode = (colorName) => {
      const colorOption = colorOptionsArray.find(option => option.name === colorName)
      return colorOption ? colorOption.code : null
    }

    const getLocalizedColorLabel = (colorOption) => {
      if (isArabicLocale.value && colorOption.name_arabic) {
        return colorOption.name_arabic
      }
      return colorOption.name
    }

    const localizedSelectedName = computed(() => {
      if (isArabicLocale.value && props.color.name_arabic) {
        return props.color.name_arabic
      }
      return props.color.name || translate('vendor.select_color')
    })

    const toggleColorDropdown = () => {
      showColorDropdown.value = !showColorDropdown.value
    }

    const selectColor = (colorName) => {
      console.log('selectColor called with:', colorName)
      const colorOption = colorOptionsArray.find(option => option.name === colorName)
      updateColor('name', colorName)
      updateColor('name_arabic', colorOption?.name_arabic || '')
      const colorCode = colorOption?.code || getColorCode(colorName)
      console.log('Color code for', colorName, ':', colorCode)
      if (colorCode) {
        updateColor('color_code', colorCode)
      }
      showColorDropdown.value = false
      colorSearchQuery.value = ''
    }

    // Enhanced stock input handlers for comprehensive validation
    const handleStockInput = (event) => {
      const value = parseInt(event.target.value) || 0
      updateColor('stock', value)
    }

    const handleStockBlur = (event) => {
      const value = parseInt(event.target.value) || 0
      updateColor('stock', value)
    }

    const handleStockPaste = (event) => {
      // Allow paste to complete, then validate
      setTimeout(() => {
        const value = parseInt(event.target.value) || 0
        updateColor('stock', value)
      }, 0)
    }

    const handleStockKeydown = (event) => {
      // Handle spinner controls (up/down arrows)
      if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
        setTimeout(() => {
          const value = parseInt(event.target.value) || 0
          updateColor('stock', value)
        }, 0)
      }
    }

    const updateColor = (field, value) => {
      console.log('updateColor called in child with:', { field, value, index: props.index })
      let finalValue = value

      // Special handling for stock field with validation and auto-correction
      if (field === 'stock') {
        const stockValue = parseInt(value) || 0
        const maxAllowed = availableStock.value

        if (stockValue > maxAllowed) {
          finalValue = maxAllowed
          showStockCorrectionFeedback(stockValue, maxAllowed)
          emit('stock-corrected', {
            colorIndex: props.index,
            attempted: stockValue,
            corrected: maxAllowed,
            available: maxAllowed
          })
        } else {
          // Clear any previous correction message
          hideStockCorrectionFeedback()
        }
      }

      console.log('Emitting update event:', { index: props.index, field, finalValue })
      emit('update', props.index, field, finalValue)
    }

    // Stock correction feedback methods
    const showStockCorrectionFeedback = (attempted, corrected) => {
      stockCorrectionMessage.value = translate('vendor.stock_auto_corrected', { attempted, corrected })
      showStockCorrection.value = true
      stockCorrectionApplied.value = true

      // Add visual feedback animation to the input field
      if (stockInput.value) {
        stockInput.value.classList.add('animate-pulse')
        setTimeout(() => {
          if (stockInput.value) {
            stockInput.value.classList.remove('animate-pulse')
          }
        }, 1000)
      }

      // Auto-hide after 5 seconds
      setTimeout(() => {
        hideStockCorrectionFeedback()
      }, 5000)
    }

    const hideStockCorrectionFeedback = () => {
      showStockCorrection.value = false
      stockCorrectionMessage.value = ''
      stockCorrectionApplied.value = false
    }

    // Size management event handlers
    const handleSizesUpdated = (sizes) => {
      // Update the color's sizes data
      emit('sizes-updated', props.index, sizes)
    }

    const handleSaveColorFirst = (pendingSizeData) => {
      // Emit event to parent to save color first, including pending size data
      emit('save-color-first', props.index, pendingSizeData)
    }

    /**
     * Resume size creation after color has been saved.
     * This method is called from the parent component.
     */
    const resumeSizeCreation = (pendingSizeData) => {
      console.log('🔄 VendorColorVariantCard: Resuming size creation for color', props.index, pendingSizeData)

      if (sizeManagementRef.value && sizeManagementRef.value.resumeSizeCreation) {
        sizeManagementRef.value.resumeSizeCreation(pendingSizeData)
      } else {
        console.error('Could not access size management component or resumeSizeCreation method for color', props.index)
      }
    }

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
          alert($t('vendor.select_valid_image_file'))
          event.target.value = ''
          return
        }

        // Validate file size (20MB limit)
        if (file.size > 20 * 1024 * 1024) {
          const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2)
          alert($t('vendor.file_size_exceeds_limit', { size: fileSizeMB }))
          event.target.value = ''
          return
        }

        // Create preview URL
        const reader = new FileReader()
        reader.onload = (e) => {
          imagePreviewUrl.value = e.target.result
        }
        reader.readAsDataURL(file)

        // Emit the file to parent
        emit('image-upload', props.index, file)
        updateColor('image', file)
      }
    }

    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
      if (!event.target.closest('.color-selection-container')) {
        showColorDropdown.value = false
      }
    }

    onMounted(() => {
      document.addEventListener('click', handleClickOutside)

      // Set initial preview if image exists
      if (props.color.image) {
        if (props.color.image instanceof File) {
          // Handle File objects (newly uploaded images)
          const reader = new FileReader()
          reader.onload = (e) => {
            imagePreviewUrl.value = e.target.result
          }
          reader.readAsDataURL(props.color.image)
        } else if (typeof props.color.image === 'string' && props.color.image.trim() !== '') {
          // Handle existing image URLs from database
          imagePreviewUrl.value = props.color.image
        }
      }
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
    })



    return {
      $t: translate,
      showColorDropdown,
      colorSearchQuery,
      imagePreviewUrl,
      sizeManagementRef,
      stockCorrectionMessage,
      showStockCorrection,
      stockCorrectionApplied,
      stockInput,
      colorOptionsArray,
      filteredColorOptions,
      otherColorsStock,
      availableStock,
      isStockExceeded,
      shouldShowSizeManagement,
      isRTL,
      getColorCode,
      getLocalizedColorLabel,
      localizedSelectedName,
      toggleColorDropdown,
      selectColor,
      updateColor,
      handleStockInput,
      handleStockBlur,
      handleStockPaste,
      handleStockKeydown,
      handleImageUpload,
      handleSizesUpdated,
      handleSaveColorFirst,
      resumeSizeCreation
    }
  }
}
</script>

<style scoped>
/* Modern Color Selection Container */
.color-selection-container {
  position: relative;
}

.selected-color-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  background-color: #ffffff;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.selected-color-display:hover {
  border-color: #f59e0b;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

/* Vendor Color Display (Blue Theme) */
.selected-color-display-vendor {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  background-color: #ffffff;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.selected-color-display-vendor:hover {
  border-color: #3b82f6;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

.selected-color-display-vendor.active {
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

/* Products Manager Color Display (Orange Theme) */
.selected-color-display-pm {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  background-color: #ffffff;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.selected-color-display-pm:hover {
  border-color: #f59e0b;
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

.selected-color-display-pm.active {
  border-color: #f59e0b;
  box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transform: translateY(-1px);
}

.selected-color-preview {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.color-swatch {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  border: 3px solid #ffffff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15), inset 0 1px 2px rgba(0, 0, 0, 0.1);
  flex-shrink: 0;
  margin-bottom: 0.25rem;
  transition: all 0.2s ease;
}

.color-option:hover .color-swatch {
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2), inset 0 1px 2px rgba(0, 0, 0, 0.1);
}

.color-option .color-name {
  font-size: 0.8125rem;
  font-weight: 600;
  color: #374151;
  line-height: 1.2;
}

.color-option .color-code {
  font-size: 0.6875rem;
  color: #6b7280;
  font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Code', monospace;
  font-weight: 500;
}

.color-info {
  display: flex;
  flex-direction: column;
  gap: 0.125rem;
}

.color-name {
  font-size: 0.9375rem;
  font-weight: 600;
  color: #1f2937;
  letter-spacing: -0.01em;
}

.color-code {
  font-size: 0.8125rem;
  color: #6b7280;
  font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Code', monospace;
  font-weight: 500;
}

.dropdown-arrow {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  color: #9ca3af;
}

.selected-color-display-vendor .dropdown-arrow.rotated {
  transform: rotate(180deg);
  color: #3b82f6;
}

.selected-color-display-pm .dropdown-arrow.rotated {
  transform: rotate(180deg);
  color: #f59e0b;
}

/* Enhanced Color Dropdown */
.color-dropdown {
  position: absolute;
  top: calc(100% + 0.5rem);
  left: 0;
  right: 0;
  z-index: 50;
  background-color: #ffffff;
  border: 2px solid #e5e7eb;
  border-radius: 1rem;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-height: 450px;
  overflow: hidden;
  min-width: 520px;
  width: max-content;
  animation: dropdownSlideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes dropdownSlideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.color-search {
  padding: 1rem;
  border-bottom: 2px solid #f3f4f6;
  background: linear-gradient(to bottom, #ffffff, #f9fafb);
}

/* Vendor Color Search Input (Blue Theme) */
.color-search-input-vendor {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  font-size: 0.9375rem;
  font-weight: 500;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.color-search-input-vendor:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 2px 4px 0 rgba(0, 0, 0, 0.06);
}

.color-search-input-vendor::placeholder {
  color: #9ca3af;
  font-weight: 400;
}

/* Products Manager Color Search Input (Orange Theme) */
.color-search-input-pm {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  font-size: 0.9375rem;
  font-weight: 500;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.color-search-input-pm:focus {
  outline: none;
  border-color: #f59e0b;
  box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1), 0 2px 4px 0 rgba(0, 0, 0, 0.06);
}

.color-search-input-pm::placeholder {
  color: #9ca3af;
  font-weight: 400;
}

/* Vendor Color Grid (Blue Theme) */
.color-grid-vendor {
  padding: 1rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
  max-height: 350px;
  overflow-y: auto;
  background-color: #fafafa;
}

.color-grid-vendor::-webkit-scrollbar {
  width: 8px;
}

.color-grid-vendor::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 4px;
}

.color-grid-vendor::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

.color-grid-vendor::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.color-option-vendor {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.625rem;
  padding: 1rem 0.75rem;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  text-align: center;
  background-color: #ffffff;
}

.color-option-vendor:hover {
  background-color: #dbeafe;
  border-color: #60a5fa;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(96, 165, 250, 0.3);
}

.color-option-vendor.selected {
  background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2), 0 4px 12px rgba(59, 130, 246, 0.3);
  transform: translateY(-2px);
}

/* Products Manager Color Grid (Orange Theme) */
.color-grid-pm {
  padding: 1rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
  max-height: 350px;
  overflow-y: auto;
  background-color: #fafafa;
}

.color-grid-pm::-webkit-scrollbar {
  width: 8px;
}

.color-grid-pm::-webkit-scrollbar-track {
  background: #f3f4f6;
  border-radius: 4px;
}

.color-grid-pm::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 4px;
}

.color-grid-pm::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}

.color-option-pm {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.625rem;
  padding: 1rem 0.75rem;
  border-radius: 0.75rem;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  border: 2px solid transparent;
  text-align: center;
  background-color: #ffffff;
}

.color-option-pm:hover {
  background-color: #fef3c7;
  border-color: #fbbf24;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.color-option-pm.selected {
  background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
  border-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2), 0 4px 12px rgba(245, 158, 11, 0.3);
  transform: translateY(-2px);
}

.color-details {
  display: flex;
  flex-direction: column;
}

/* Enhanced Image Upload Container */
.image-preview-container {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.06);
  position: relative;
  overflow: hidden;
}

.image-preview-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(249, 115, 22, 0.05) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  pointer-events: none;
}

.image-preview-container:hover::before {
  opacity: 1;
}

.image-preview-container:hover {
  box-shadow: 0 8px 16px -2px rgba(0, 0, 0, 0.1), 0 4px 8px -2px rgba(0, 0, 0, 0.06);
  transform: translateY(-2px);
  border-color: #f59e0b !important;
}

.image-preview-container.has-image {
  border-color: #10b981 !important;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
  box-shadow: 0 4px 12px -1px rgba(16, 185, 129, 0.2), 0 2px 6px -1px rgba(16, 185, 129, 0.1);
}

.image-preview-container.has-image::before {
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(5, 150, 105, 0.05) 100%);
}

.image-placeholder {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

/* Modern Button Styles - Orange Theme for Products Manager */
.vue-btn-blue-solid {
  background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
  color: #ffffff;
  border: 2px solid transparent;
  padding: 0.75rem 1.25rem;
  border-radius: 0.75rem;
  font-size: 0.9375rem;
  font-weight: 600;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px 0 rgba(245, 158, 11, 0.2);
  letter-spacing: -0.01em;
}

.vue-btn-blue-solid:hover {
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  box-shadow: 0 6px 16px 0 rgba(245, 158, 11, 0.4);
  transform: translateY(-2px);
}

.vue-btn-blue-solid:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px 0 rgba(245, 158, 11, 0.2);
}



/* RTL Support */
.rtl {
  direction: rtl;
}

.rtl .flex {
  flex-direction: row-reverse;
}

.rtl .text-left {
  text-align: right;
}

.rtl .text-right {
  text-align: left;
}

.rtl .float-left {
  float: right;
}

.rtl .float-right {
  float: left;
}

.rtl .border-l {
  border-left: none;
  border-right: 1px solid;
}

.rtl .border-r {
  border-right: none;
  border-left: 1px solid;
}

.rtl .rounded-l {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  border-top-right-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
}

.rtl .rounded-r {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-top-left-radius: 0.375rem;
  border-bottom-left-radius: 0.375rem;
}

.rtl input[type="text"],
.rtl input[type="number"],
.rtl textarea {
  text-align: right;
}

.rtl .color-dropdown {
  left: auto;
  right: 0;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .selected-color-display {
    background-color: #374151;
    border-color: #4b5563;
  }

  .color-name {
    color: #f9fafb;
  }

  .color-dropdown {
    background-color: #1f2937;
    border-color: #374151;
  }

  .color-search {
    border-color: #374151;
  }

  .color-search-input-vendor,
  .color-search-input-pm {
    background-color: #374151;
    border-color: #4b5563;
    color: #f9fafb;
  }

  .color-option:hover {
    background-color: #374151;
  }

  .color-option.selected {
    background-color: #1e3a8a;
    border-color: #3b82f6;
  }

  .image-preview-container.has-image {
    border-color: #10b981 !important;
    background-color: #064e3b !important;
  }
}

/* Modern File Input Styling */
.modern-file-input {
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.modern-file-input:hover {
  transform: translateY(-1px);
}

/* Enhanced Card Styling */
.vue-card.color-item {
  background: linear-gradient(to bottom, #ffffff, #fafafa);
  border: 2px solid #e5e7eb;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.vue-card.color-item:hover {
  border-color: #f59e0b;
  box-shadow: 0 12px 24px -4px rgba(245, 158, 11, 0.15), 0 8px 16px -4px rgba(0, 0, 0, 0.1);
}

/* Stock validation animations */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Loading and Success States */
@keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.loading-shimmer {
  animation: shimmer 2s infinite linear;
  background: linear-gradient(to right, #f3f4f6 0%, #e5e7eb 20%, #f3f4f6 40%, #f3f4f6 100%);
  background-size: 1000px 100%;
}

/* Success Checkmark Animation */
@keyframes checkmark {
  0% {
    transform: scale(0) rotate(45deg);
  }
  50% {
    transform: scale(1.2) rotate(45deg);
  }
  100% {
    transform: scale(1) rotate(45deg);
  }
}

.success-checkmark {
  animation: checkmark 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
