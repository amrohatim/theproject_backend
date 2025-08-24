<template>
  <div class="vue-card color-item transition-all duration-200"
       :class="{ 'ring-2 border-primary-200': isDefault, 'rtl': isRTL }"
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
                <div class="selected-color-display"
                     @click="toggleColorDropdown"
                     :class="{ 'active': showColorDropdown }">
                  <div class="selected-color-preview">
                    <div class="color-swatch"
                         :style="{ backgroundColor: getColorCode(color.name) || '#e5e7eb' }"></div>
                    <div class="color-info">
                      <span class="color-name">{{ color.name || $t('vendor.select_color') }}</span>
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
                           class="color-search-input"
                           @click.stop>
                  </div>

                  <!-- Color Grid -->
                  <div class="color-grid">
                    <div v-for="colorOption in filteredColorOptions"
                         :key="colorOption.name"
                         class="color-option"
                         :class="{ 'selected': color.name === colorOption.name }"
                         @click="selectColor(colorOption.name)">
                      <div class="color-swatch"
                           :style="{ backgroundColor: colorOption.code }"></div>
                      <span class="color-name">{{ colorOption.name }}</span>
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
                         class="vue-form-control text-sm font-mono tracking-wider"
                         pattern="^#[0-9A-Fa-f]{6}$"
                         title="Enter a valid hex color code (e.g., #FF0000)">
                  <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                    <div class="w-6 h-6 rounded border border-gray-300 shadow-sm"
                         :style="{ backgroundColor: color.color_code || '#ffffff' }"></div>
                  </div>
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
                     placeholder="0.00"
                     class="vue-form-control">
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
                       @input="handleStockInput($event)"
                       @blur="handleStockBlur($event)"
                       @paste="handleStockPaste($event)"
                       @keydown="handleStockKeydown($event)"
                       placeholder="0"
                       class="vue-form-control transition-all duration-200"
                       :class="{
                         'border-red-500 bg-red-50': isStockExceeded,
                         'border-green-500 bg-green-50': stockCorrectionApplied
                       }"
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
                   placeholder="0"
                   class="vue-form-control">
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
                   class="color-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
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
  { name: 'Beige', value: '#F5F5DC' }
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
    const isRTL = computed(() => {
      return ['ar', 'he', 'fa'].includes(window.Laravel?.locale || 'en')
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
      { name: 'Red', code: '#FF0000' },
      { name: 'Crimson', code: '#DC143C' },
      { name: 'FireBrick', code: '#B22222' },
      { name: 'DarkRed', code: '#8B0000' },
      { name: 'Orange', code: '#FFA500' },
      { name: 'DarkOrange', code: '#FF8C00' },
      { name: 'Gold', code: '#FFD700' },
      { name: 'Yellow', code: '#FFFF00' },
      { name: 'Green', code: '#008000' },
      { name: 'Lime', code: '#00FF00' },
      { name: 'ForestGreen', code: '#228B22' },
      { name: 'DarkGreen', code: '#006400' },
      { name: 'Blue', code: '#0000FF' },
      { name: 'MediumBlue', code: '#0000CD' },
      { name: 'DarkBlue', code: '#00008B' },
      { name: 'Navy', code: '#000080' },
      { name: 'SkyBlue', code: '#87CEEB' },
      { name: 'Purple', code: '#800080' },
      { name: 'Violet', code: '#EE82EE' },
      { name: 'Magenta', code: '#FF00FF' },
      { name: 'Pink', code: '#FFC0CB' },
      { name: 'Brown', code: '#A52A2A' },
      { name: 'Chocolate', code: '#D2691E' },
      { name: 'Tan', code: '#D2B48C' },
      { name: 'Black', code: '#000000' },
      { name: 'Gray', code: '#808080' },
      { name: 'Silver', code: '#C0C0C0' },
      { name: 'White', code: '#FFFFFF' }
    ]

    const filteredColorOptions = computed(() => {
      if (!colorSearchQuery.value) {
        return colorOptionsArray
      }
      return colorOptionsArray.filter(color =>
        color.name.toLowerCase().includes(colorSearchQuery.value.toLowerCase())
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

    const toggleColorDropdown = () => {
      showColorDropdown.value = !showColorDropdown.value
    }

    const selectColor = (colorName) => {
      console.log('selectColor called with:', colorName)
      updateColor('name', colorName)
      const colorCode = getColorCode(colorName)
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
.color-selection-container {
  position: relative;
}

.selected-color-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background-color: #ffffff;
  cursor: pointer;
  transition: border-color 0.15s ease-in-out;
}

.selected-color-display:hover {
  border-color: #9ca3af;
}

.selected-color-display.active {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.selected-color-preview {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.color-swatch {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  border: 2px solid #e5e7eb;
  flex-shrink: 0;
  margin-bottom: 0.25rem;
}

.color-option .color-name {
  font-size: 0.75rem;
  font-weight: 500;
  color: #374151;
  line-height: 1.2;
}

.color-option .color-code {
  font-size: 0.625rem;
  color: #6b7280;
  font-family: monospace;
}

.color-info {
  display: flex;
  flex-direction: column;
}

.color-name {
  font-size: 0.875rem;
  font-weight: 500;
  color: #1f2937;
}

.color-code {
  font-size: 0.75rem;
  color: #6b7280;
}

.dropdown-arrow {
  transition: transform 0.2s ease;
}

.dropdown-arrow.rotated {
  transform: rotate(180deg);
}

.color-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  z-index: 50;
  background-color: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  max-height: 400px;
  overflow-y: auto;
  min-width: 480px;
  width: max-content;
}

.color-search {
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.color-search-input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  font-size: 0.875rem;
}

.color-search-input:focus {
  outline: none;
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.color-grid {
  padding: 0.5rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
}

.color-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.15s ease;
  border: 1px solid transparent;
  text-align: center;
}

.color-option:hover {
  background-color: #f3f4f6;
  border-color: #d1d5db;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.color-option.selected {
  background-color: #dbeafe;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.color-details {
  display: flex;
  flex-direction: column;
}

.image-preview-container {
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.image-preview-container:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  transform: translateY(-1px);
}

.image-preview-container.has-image {
  border-color: #10b981 !important;
  background-color: #f0fdf4 !important;
  box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1), 0 2px 4px -1px rgba(16, 185, 129, 0.06);
}

.vue-btn-blue-solid {
  background-color: #3b82f6;
  color: #ffffff;
  border: 1px solid #3b82f6;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.15s ease;
}

.vue-btn-blue-solid:hover {
  background-color: #2563eb;
  border-color: #2563eb;
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

  .color-search-input {
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
</style>
