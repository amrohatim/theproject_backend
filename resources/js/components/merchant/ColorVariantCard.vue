<template>
  <div class="vue-card color-item transition-all duration-200"
       :class="{ 'ring-2 border-primary-200': isDefault }"
       :style="isDefault ? { '--tw-ring-color': 'var(--primary-blue)' } : {}">
    <div class="p-6 border-b" style="border-color: var(--gray-200);">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-6 h-6 rounded-full border-2 border-white shadow-sm"
               :style="{ backgroundColor: color.color_code || '#000000' }"></div>
          <h4 class="vue-text-lg">
            {{ t.color_variant }} {{ index + 1 }}
            <span v-if="isDefault"
                  class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                  style="background-color: var(--gray-100); color: var(--primary-blue-hover);">
              <i class="fas fa-star w-3 h-3 mr-1"></i>
              {{ t.default }}
            </span>
          </h4>
        </div>
        <div class="flex items-center gap-2">
          <button v-if="!isDefault"
                  type="button"
                  class="vue-btn-blue-solid text-sm font-medium"
                  @click="$emit('set-default', index)">
            <i class="fas fa-star mr-2"></i>
            {{ t.set_as_default }}
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
                {{ t.color_name }} <span class="text-red-500">*</span>
              </label>

              <!-- Custom Color Selection Interface -->
              <div class="color-selection-container">
                <!-- Selected Color Display -->
                <div class="selected-color-display"
                     @click="toggleColorDropdown"
                     :class="{ 'active': showColorDropdown }">
                  <div class="selected-color-preview">
                    <div class="color-swatch"
                         :style="{ backgroundColor: getColorCode(color.name) || '#e5e7eb' }"></div>
                    <div class="color-info">
                      <span class="color-name">{{ color.name || t.select_color }}</span>
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
                           :placeholder="t.search_colors"
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
                      <div class="color-details">
                        <span class="color-name">{{ colorOption.name }}</span>
                        <span class="color-code">{{ colorOption.code }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Hidden select for form submission -->
              <select :value="color.name"
                      @change="updateColor('name', $event.target.value)"
                      class="hidden"
                      required>
                <option value="">{{ t.select_color }}</option>
                <option v-for="colorOption in colorOptionsArray"
                        :key="colorOption"
                        :value="colorOption">
                  {{ colorOption }}
                </option>
              </select>
            </div>

            <div class="space-y-2 mt-2">
              <label class="block vue-text-sm font-medium">
                <i class="fas fa-palette w-4 h-4 mr-2 text-blue-500"></i>
                {{ t.color_code }}
              </label>
              <div class="flex gap-3">
                <div class="relative">
                  <input  type="color"
                         :value="color.color_code || '#000000'"
                         @input="updateColor('color_code', $event.target.value)"
                         class="w-14 h-12  p-1 border-2 border-blue-200 rounded-md shadow-sm hover:border-blue-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 cursor-pointer">
                  <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-eye-dropper  text-white text-xs"></i>
                  </div>
                </div>
                <div class="flex-1 relative">
                  <input type="text"
                         :value="color.color_code"
                         @input="updateColor('color_code', $event.target.value)"
                         placeholder="#000000"
                         class="vue-form-control-enhanced-blue w-full font-mono text-sm tracking-wider">
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4">
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <label class="block vue-text-sm font-medium">
                  <i class="fas fa-coins w-4 h-4 mr-2 text-blue-500"></i>
                  {{ t.price }}
                </label>
                
              </div>
              <div class="relative">
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 flex items-center">
                
                </div>
                <input type="number"
                       step="1"
                       :value="color.price_adjustment"
                       @input="updateColor('price_adjustment', Math.abs(parseInt($event.target.value)) || 0)"
                       @keypress="$event.key.match(/[0-9]/) === null && $event.preventDefault()"
                       class="vue-form-control-enhanced-blue pl-8 text-right font-medium"
                       placeholder="0.00">
              </div>
              <p class="text-xs text-slate-500 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                {{ t.additional_cost_color_variant }}
              </p>
            </div>

            <div class="space-y-2">
              <label class="block vue-text-xs font-medium">
                <i class="fas fa-boxes w-4 h-4 mr-2 text-blue-500"></i>
                {{ t.stock }}
              </label>
              <div class="relative">
                <input type="number"
                       :value="color.stock"
                       @input="updateColor('stock', parseInt($event.target.value) || 0)"
                       @keypress="$event.key.match(/[0-9]/) === null && $event.preventDefault()"
                       min="0"
                       class="vue-form-control-enhanced-blue pr-16 text-center font-semibold"
                       placeholder="0">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                  
                </div>
              </div>
              <div class="flex items-center justify-between text-xs mt-1">

                <span class="text-blue-600 font-medium" v-if="color.stock > 0">
                  {{ color.stock }} {{ t.in_stock }}
                </span>
                <span class="text-red-500 font-medium" v-else>
                  {{ t.out_of_stock }}
                </span>
              </div>

              <!-- Stock Correction Feedback -->
              <div v-if="showStockCorrection"
                   class="mt-2 p-2 bg-amber-50 border border-amber-200 rounded-md">
                <div class="flex items-start gap-2">
                  <i class="fas fa-exclamation-triangle text-amber-500 text-sm mt-0.5"></i>
                  <div class="flex-1">
                    <p class="text-xs text-amber-700 font-medium">{{ t.stock_auto_corrected }}</p>
                    <p class="text-xs text-amber-600 mt-1">{{ stockCorrectionMessage }}</p>
                  </div>
                  <button type="button"
                          @click="hideStockCorrectionFeedback"
                          class="text-amber-400 hover:text-amber-600 text-xs">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm font-medium">
                <i class="fas fa-sort-numeric-down w-4 h-4 mr-2 text-blue-500"></i>
                {{ t.display_order }}
              </label>
              <div class="relative">
                <input type="number"
                       :value="color.display_order || 0"
                       @input="updateColor('display_order', parseInt($event.target.value) || 0)"
                       @keypress="$event.key.match(/[0-9]/) === null && $event.preventDefault()"
                       min="0"
                       class="vue-form-control-enhanced-blue text-center font-semibold text-md"
                       title="Order in which this color variant appears"
                       placeholder="0">
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                 
                </div>
              </div>
              <p class="text-xs text-slate-500 mt-1">
                <i class="fas fa-info-circle mr-1"></i>
                {{ t.lower_numbers_appear_first }}
              </p>
            </div>
          </div>
        </div>

        <!-- Image Upload -->
        <div class="space-y-4">
          <label class="block vue-text-sm">
            {{ t.product_image }} <span class="text-red-500">*</span>
          </label>
          <div class="relative">
            <div class="aspect-[3/4] bg-slate-50 border-2 border-dashed border-slate-300 rounded-lg overflow-hidden hover:border-primary-400 transition-colors image-preview-container"
                 :class="{ 'has-image': hasImage }"
                 @click="triggerFileInput"
                 @dragover.prevent
                 @drop.prevent="handleDrop">
              
              <!-- Image Preview -->
              <div v-if="hasImage" class="relative w-full h-full group">
                <img :src="imagePreviewUrl" 
                     :alt="color.name + ' variant'"
                     class="w-full h-full object-cover image-preview"
                     @error="handleImageError">
                
                <!-- Change Image Button - Top Left Corner -->
                <div class="absolute top-2 left-2 z-10">
                  <button type="button" 
                          class="flex items-center justify-center w-8 h-8 bg-white bg-opacity-90 hover:bg-opacity-100 text-slate-700 rounded-full shadow-lg hover:shadow-xl transition-all duration-200 trigger-image-upload group/btn"
                          @click.stop="triggerFileInput">
                    <i class="fas fa-camera w-4 h-4 group-hover/btn:scale-110 transition-transform"></i>
                    <span class="sr-only">{{ t.change_image }}</span>
                  </button>
                </div>
                
                <!-- Main Image Badge -->
                <div v-if="isDefault" class="absolute top-2 right-2">
                  <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                        style="background-color: var(--primary-600); color: white;">
                    <i class="fas fa-star w-3 h-3 mr-1"></i>
                    {{ t.main_image }}
                  </span>
                </div>
              </div>
              
              <!-- Upload Placeholder -->
              <div v-else class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors image-placeholder">
                <i class="fas fa-image w-12 h-12 text-slate-400 mb-3"></i>
                <p class="text-sm font-medium text-slate-600 mb-1">{{ t.upload_image }}</p>
                <p class="text-xs text-slate-500">{{ t.png_jpg_up_to_20mb }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ t.click_drag_upload }}</p>
              </div>
            </div>
            
            <!-- Hidden File Input -->
            <input ref="fileInput"
                   type="file" 
                   class="hidden" 
                   accept="image/*"
                   @change="handleFileSelect">
          </div>
          
          <!-- Image Error Message -->
          <div v-if="imageError" class="text-red-500 text-xs mt-1">
            {{ imageError }}
          </div>
        </div>
      </div>

      <!-- Size Management Section -->
      <div class="col-span-full">
        <SizeManagement
          v-if="shouldShowSizeManagement"
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
            {{ t.set_color_name_stock_manage_sizes }}
          </p>
        </div>
        <div v-else class="text-center py-8" style="border-top: 1px solid var(--gray-200); margin-top: 1.5rem; padding-top: 1.5rem;">
          <i class="fas fa-info-circle w-6 h-6 mb-2" style="color: var(--gray-400);"></i>
          <p class="text-sm" style="color: var(--gray-600);">
            {{ t.save_color_variant_first_manage_sizes }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted } from 'vue'
import SizeManagement from './SizeManagement.vue'

export default {
  name: 'ColorVariantCard',
  components: {
    SizeManagement
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
    isDefault: {
      type: Boolean,
      default: false
    },
    productId: {
      type: [String, Number],
      required: true
    },
    generalStock: {
      type: Number,
      default: 0
    },
    allColors: {
      type: Array,
      default: () => []
    }
  },
  emits: ['update', 'remove', 'set-default', 'image-upload', 'sizes-updated', 'stock-corrected'],
  setup(props, { emit }) {
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

    // Computed translations for commonly used keys
    const t = computed(() => ({
      color_variant: $t('color_variant'),
      default: $t('default'),
      set_as_default: $t('set_as_default'),
      color_name: $t('color_name'),
      select_color: $t('select_color'),
      color_code: $t('color_code'),
      price: $t('price'),
      additional_cost_color_variant: $t('additional_cost_color_variant'),
      stock: $t('stock'),
      units: $t('units'),
      in_stock: $t('in_stock'),
      out_of_stock: $t('out_of_stock'),
      display_order: $t('display_order'),
      lower_numbers_appear_first: $t('lower_numbers_appear_first'),
      product_image: $t('product_image'),
      upload_image: $t('upload_image'),
      png_jpg_up_to_2mb: $t('png_jpg_up_to_2mb'),
      click_drag_upload: $t('click_drag_upload'),
      set_color_name_stock_manage_sizes: $t('set_color_name_stock_manage_sizes'),
      save_color_variant_first_manage_sizes: $t('save_color_variant_first_manage_sizes'),
      change_image: $t('change_image'),
      main_image: $t('main_image'),
      search_colors: $t('search_colors')
    }))

    // RTL support
    const isRTL = computed(() => {
      return document.documentElement.dir === 'rtl' || 
             document.documentElement.lang === 'ar' ||
             document.body.classList.contains('rtl')
    })

    const fileInput = ref(null)
    const imageError = ref('')
    const showColorDropdown = ref(false)
    const colorSearchQuery = ref('')

    // Color options with hex codes
    const colorOptionsWithCodes = [
      { name: 'DarkRed', code: '#8B0000' },
      { name: 'IndianRed', code: '#CD5C5C' },
      { name: 'LightCoral', code: '#F08080' },
      { name: 'Salmon', code: '#FA8072' },
      { name: 'DarkSalmon', code: '#E9967A' },
      { name: 'LightSalmon', code: '#FFA07A' },
      { name: 'Orange', code: '#FFA500' },
      { name: 'DarkOrange', code: '#FF8C00' },
      { name: 'Coral', code: '#FF7F50' },
      { name: 'Red', code: '#FF0000' },
      { name: 'Blue', code: '#0000FF' },
      { name: 'Green', code: '#008000' },
      { name: 'Navy Blue', code: '#000080' },
      { name: 'Forest Green', code: '#228B22' },
      { name: 'Black', code: '#000000' },
      { name: 'White', code: '#FFFFFF' },
      { name: 'Gray', code: '#808080' },
      { name: 'Yellow', code: '#FFFF00' },
      { name: 'Purple', code: '#800080' },
      { name: 'Pink', code: '#FFC0CB' },
      { name: 'Brown', code: '#A52A2A' },
      { name: 'Silver', code: '#C0C0C0' },
      { name: 'Gold', code: '#FFD700' },
      { name: 'Maroon', code: '#800000' },
      { name: 'Teal', code: '#008080' },
      { name: 'Olive', code: '#808000' },
      { name: 'Lime', code: '#00FF00' },
      { name: 'Aqua', code: '#00FFFF' },
      { name: 'Fuchsia', code: '#FF00FF' }
    ]

    // Legacy array for backward compatibility
    const colorOptionsArray = colorOptionsWithCodes.map(color => color.name)

    // Computed properties
    const filteredColorOptions = computed(() => {
      if (!colorSearchQuery.value) {
        return colorOptionsWithCodes
      }
      return colorOptionsWithCodes.filter(color =>
        color.name.toLowerCase().includes(colorSearchQuery.value.toLowerCase())
      )
    })

    const hasImage = computed(() => {
      return !!(props.color.image || props.color.imagePreview || props.color.imageFile)
    })

    const imagePreviewUrl = computed(() => {
      if (props.color.imagePreview) {
        return props.color.imagePreview
      }
      if (props.color.image) {
        return props.color.image
      }
      return null
    })

    // Stock validation computed properties
    const otherColorsStock = computed(() => {
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

    // Reactive refs for user feedback
    const stockCorrectionMessage = ref('')
    const showStockCorrection = ref(false)

    // Methods
    const updateColor = (field, value) => {
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

      const updatedColor = { ...props.color, [field]: finalValue }
      emit('update', props.index, updatedColor)

      // Auto-update color name when color code changes
      if (field === 'color_code' && finalValue) {
        const matchingColor = colorOptionsWithCodes.find(color =>
          color.code.toLowerCase() === finalValue.toLowerCase()
        )
        if (matchingColor && matchingColor.name !== props.color.name) {
          const updatedColorWithName = { ...updatedColor, name: matchingColor.name }
          emit('update', props.index, updatedColorWithName)
        }
      }
    }

    const showStockCorrectionFeedback = (attempted, corrected) => {
      stockCorrectionMessage.value = $t('stock_auto_adjusted', { 
        attempted, 
        corrected, 
        available: availableStock.value 
      })
      showStockCorrection.value = true

      // Auto-hide the message after 5 seconds
      setTimeout(() => {
        hideStockCorrectionFeedback()
      }, 5000)
    }

    const hideStockCorrectionFeedback = () => {
      showStockCorrection.value = false
      stockCorrectionMessage.value = ''
    }

    const getColorCode = (colorName) => {
      const colorOption = colorOptionsWithCodes.find(color => color.name === colorName)
      return colorOption ? colorOption.code : null
    }

    const toggleColorDropdown = () => {
      showColorDropdown.value = !showColorDropdown.value
      if (showColorDropdown.value) {
        colorSearchQuery.value = ''
      }
    }

    const selectColor = (colorName) => {
      // Update the color name first
      updateColor('name', colorName)

      // Then update the color code
      const colorCode = getColorCode(colorName)
      if (colorCode) {
        updateColor('color_code', colorCode)
      }

      showColorDropdown.value = false
    }

    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
      // Don't close if clicking on a color option
      if (event.target.closest('.color-option')) {
        return
      }

      if (!event.target.closest('.color-selection-container')) {
        showColorDropdown.value = false
      }
    }

    // Add event listener for clicking outside
    watch(showColorDropdown, (newValue) => {
      if (newValue) {
        document.addEventListener('click', handleClickOutside)
      } else {
        document.removeEventListener('click', handleClickOutside)
      }
    })

    // Watch for color code changes and auto-update color name
    watch(() => props.color.color_code, (newColorCode) => {
      if (newColorCode) {
        // Find the color name that matches this color code
        const matchingColor = colorOptionsWithCodes.find(color =>
          color.code.toLowerCase() === newColorCode.toLowerCase()
        )
        if (matchingColor && matchingColor.name !== props.color.name) {
          updateColor('name', matchingColor.name)
        }
      }
    })

    const triggerFileInput = () => {
      fileInput.value?.click()
    }

    const validateFile = (file) => {
      imageError.value = ''
      
      if (!file.type.startsWith('image/')) {
        imageError.value = $t('select_valid_image_file')
        return false
      }

      if (file.size > 20 * 1024 * 1024) {
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2)
        imageError.value = $t('file_size_exceeds_limit', { size: fileSizeMB })
        return false
      }

      return true
    }

    const handleFileSelect = (event) => {
      const file = event.target.files[0]
      if (file && validateFile(file)) {
        emit('image-upload', props.index, file)
      }
      // Reset input value to allow selecting the same file again
      event.target.value = ''
    }

    const handleDrop = (event) => {
      const files = event.dataTransfer.files
      if (files.length > 0) {
        const file = files[0]
        if (validateFile(file)) {
          emit('image-upload', props.index, file)
        }
      }
    }

    const handleImageError = () => {
      imageError.value = ""
    }

    const handleSizesUpdated = (sizes) => {
      emit('sizes-updated', props.index, sizes)
    }

    const handleSaveColorFirst = async ({ resolve, reject }) => {
      try {
        // Validate that the color has required fields
        if (!props.color.name || !props.color.stock) {
          reject(new Error($t('merchant.color_must_have_name_stock_before_sizes')))
          return
        }

        // Call the API to save the color
        const response = await window.axios.post('/merchant/api/colors/save', {
          product_id: props.productId,
          name: props.color.name,
          color_code: props.color.color_code || '#000000',
          price_adjustment: props.color.price_adjustment || 0,
          stock: props.color.stock,
          display_order: props.color.display_order || props.index,
          is_default: props.color.is_default || false
        })

        if (response.data.success) {
          // Update the color with the new ID from the server
          const savedColor = response.data.color
          
          // Update all relevant fields from the server response
          Object.keys(savedColor).forEach(key => {
            updateColor(key, savedColor[key])
          })

          // Force a re-render by updating the key for SizeManagement component
          // This ensures the component re-mounts with the new colorId
          const sizeManagementKey = `size-mgmt-${props.index}-${savedColor.id}`
          
          // Resolve with the saved color data
          resolve(savedColor)
        } else {
          reject(new Error(response.data.message || $t('merchant.failed_save_color')))
        }
      } catch (error) {
        console.error('Error saving color:', error)
        reject(error)
      }
    }

    return {
      t,
      isRTL,
      fileInput,
      imageError,
      showColorDropdown,
      colorSearchQuery,
      colorOptionsArray,
      filteredColorOptions,
      hasImage,
      imagePreviewUrl,
      otherColorsStock,
      availableStock,
      isStockExceeded,
      shouldShowSizeManagement,
      stockCorrectionMessage,
      showStockCorrection,
      updateColor,
      getColorCode,
      toggleColorDropdown,
      selectColor,
      triggerFileInput,
      handleFileSelect,
      handleDrop,
      handleImageError,
      handleSizesUpdated,
      handleSaveColorFirst,
      showStockCorrectionFeedback,
      hideStockCorrectionFeedback
    }
  }
}
</script>

<style scoped>
.color-item {
  transition: all 0.3s ease;
}

/* Enhanced Form Controls */
.vue-form-control-enhanced {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  background-color: white;
  color: #1f2937;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.vue-form-control-enhanced:focus {
  outline: none;
  border-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1), 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  background-color: #fffbeb;
}

.vue-form-control-enhanced:hover {
  border-color: #d1d5db;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Enhanced Form Controls with Blue Theme */
.vue-form-control-enhanced-blue {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  background-color: white;
  color: #1f2937;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.vue-form-control-enhanced-blue:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  background-color: #eff6ff;
}

.vue-form-control-enhanced-blue:hover {
  border-color: #3b82f6;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Enhanced blue Button */
.vue-btn-blue {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
}

.vue-btn-blue:hover {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(245, 158, 11, 0.3);
}

.vue-btn-blue:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
}

/* Solid Blue Button */
.vue-btn-blue-solid {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.vue-btn-blue-solid:hover {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.vue-btn-blue-solid:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

/* Color Selection Interface Styles */
.color-selection-container {
  position: relative;
}

.selected-color-display {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem;
  border: 1px solid var(--gray-300);
  border-radius: 0.5rem;
  background-color: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

.selected-color-display:hover {
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 2px var(--primary-blue-light);
}

.selected-color-display.active {
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 2px var(--primary-blue-light);
}

.selected-color-preview {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.color-swatch {
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 0.25rem;
  border: 2px solid white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.color-info {
  display: flex;
  flex-direction: column;
}

.color-name {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--gray-900);
}

.color-code {
  font-size: 0.75rem;
  color: var(--gray-500);
  font-family: monospace;
}

.dropdown-arrow {
  width: 1rem;
  height: 1rem;
  color: var(--gray-400);
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
  background: white;
  border: 1px solid var(--gray-200);
  border-radius: 0.5rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  margin-top: 0.25rem;
  max-height: 20rem;
  overflow: hidden;
  min-width: 320px;
  width: max-content;
}

.color-search {
  padding: 0.75rem;
  border-bottom: 1px solid var(--gray-200);
}

.color-search-input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid var(--gray-300);
  border-radius: 0.375rem;
  font-size: 0.875rem;
  outline: none;
  transition: all 0.2s ease;
}

.color-search-input:focus {
  border-color: var(--primary-blue);
  box-shadow: 0 0 0 2px var(--primary-blue-light);
}

.color-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
  padding: 0.75rem;
  max-height: 15rem;
  overflow-y: auto;
}

@media (max-width: 768px) {
  .color-grid {
    grid-template-columns: repeat(3, 1fr);
  }

  .color-dropdown {
    min-width: 280px;
  }
}

.color-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0.75rem;
  border: 1px solid var(--gray-200);
  border-radius: 0.375rem;
  cursor: pointer;
  transition: all 0.2s ease;
  background: white;
}

.color-option:hover {
  border-color: #3b82f6;
  background-color: #eff6ff;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.color-option.selected {
  border-color: #3b82f6;
  background-color: #eff6ff;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

.color-option .color-swatch {
  width: 2rem;
  height: 2rem;
  margin-bottom: 0.5rem;
}

.color-option .color-details {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.color-option .color-name {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--gray-900);
  margin-bottom: 0.125rem;
}

.color-option .color-code {
  font-size: 0.625rem;
  color: var(--gray-500);
  font-family: monospace;
}

.image-preview-container {
  transition: all 0.3s ease;
  border: 2px dashed var(--slate-300);
  border-radius: 0.5rem;
  background-color: var(--slate-50);
  overflow: hidden;
  cursor: pointer;
}

.image-preview-container:hover {
  border-color: var(--primary-400);
  background-color: var(--primary-50);
}

.image-preview-container.has-image {
  border-color: var(--primary-500);
  background-color: var(--primary-50);
}

.aspect-\[3\/4\] {
  aspect-ratio: 3 / 4;
}

.trigger-image-upload {
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
}

.trigger-image-upload:hover {
  transform: scale(1.05);
}

/* Ensure button is visible on mobile */
@media (max-width: 768px) {
  .trigger-image-upload {
    background-color: rgba(255, 255, 255, 0.95) !important;
  }

  .color-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .color-grid {
    grid-template-columns: 1fr;
  }
}
</style>
