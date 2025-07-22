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
            Color Variant {{ index + 1 }}
            <span v-if="isDefault"
                  class="ml-2 inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                  style="background-color: var(--gray-100); color: var(--primary-blue-hover);">
              <i class="fas fa-star w-3 h-3 mr-1"></i>
              Default
            </span>
          </h4>
        </div>
        <div class="flex items-center gap-2">
          <button v-if="!isDefault"
                  type="button"
                  class="vue-btn-blue-solid text-sm font-medium"
                  @click="$emit('set-default', index)">
            <i class="fas fa-star mr-2"></i>
            Set as Default
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
                Color Name <span class="text-red-500">*</span>
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
                      <span class="color-name">{{ color.name || 'Select color' }}</span>
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
                           placeholder="Search colors..."
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
            </div>

            <!-- Color Name Error Display -->
            <div v-if="errors[`colors.${index}.name`]" class="text-red-500 text-sm mt-1">
              {{ errors[`colors.${index}.name`] }}
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm font-medium">
                <i class="fas fa-palette w-4 h-4 mr-2 text-blue-500"></i>
                Color Code
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
              <label class="block vue-text-sm">Price Adjustment</label>
              <input type="number"
                     step="0.01"
                     :value="color.price_adjustment"
                     @input="updateColor('price_adjustment', parseFloat($event.target.value) || 0)"
                     placeholder="0.00"
                     class="vue-form-control">
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm">
                Stock
                <span v-if="isStockExceeded" class="text-red-500 text-xs ml-1">
                  (Exceeds available: {{ availableStock }})
                </span>
              </label>
              <div class="relative">
                <input type="number"
                       min="0"
                       :max="availableStock"
                       :value="color.stock"
                       @input="updateColor('stock', parseInt($event.target.value) || 0)"
                       placeholder="0"
                       class="vue-form-control"
                       :class="{ 'border-red-500 bg-red-50': isStockExceeded }">
                <div v-if="showStockCorrection" class="absolute top-full left-0 right-0 mt-1 p-2 bg-amber-50 border border-amber-200 rounded text-xs text-amber-700">
                  {{ stockCorrectionMessage }}
                </div>
              </div>
              <!-- Stock allocation info -->
              <div class="text-xs text-gray-600 space-y-1">
                <div class="flex justify-between">
                  <span>Available for this color:</span>
                  <span class="font-medium">{{ availableStock }}</span>
                </div>
                <div class="flex justify-between">
                  <span>Currently allocated:</span>
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
            <label class="block vue-text-sm">Display Order</label>
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
              Color Image <span class="text-red-500">*</span>
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
                <p class="text-gray-500 text-sm">No image selected</p>
                <p class="text-gray-400 text-xs">300x400px preview</p>
              </div>
            </div>

            <input type="file"
                   @change="handleImageUpload"
                   class="color-image-input block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                   accept="image/*"
                   style="max-width: 300px;">

            <p class="mt-1 text-xs text-gray-500" style="max-width: 300px;">
              PNG, JPG, GIF up to 2MB
            </p>

            <!-- Color Image Error Display -->
            <div v-if="errors[`colors.${index}.image`] || errors[`color_images.${index}`]" class="text-red-500 text-sm mt-1">
              {{ errors[`colors.${index}.image`] || errors[`color_images.${index}`] }}
            </div>

            <!-- Default Color Checkbox -->
            <div class="mt-4">
              <label class="block vue-text-sm mb-2">Default Color</label>
              <div class="flex items-start">
                <input type="checkbox"
                       :checked="isDefault"
                       @change="$emit('set-default', index)"
                       class="default-color-checkbox focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded mt-1">
                <span class="ml-2 text-sm text-gray-500">This image will be the main product image</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Size Management Section -->
      <div class="col-span-full">
        <VendorSizeManagement
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
            Set color name and stock quantity to manage sizes
          </p>
        </div>
        <div v-else class="text-center py-8" style="border-top: 1px solid var(--gray-200); margin-top: 1.5rem; padding-top: 1.5rem;">
          <i class="fas fa-info-circle w-6 h-6 mb-2" style="color: var(--gray-400);"></i>
          <p class="text-sm" style="color: var(--gray-600);">
            Save this color variant first to manage sizes
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import VendorSizeManagement from './VendorSizeManagement.vue'

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
    isDefault: {
      type: Boolean,
      default: false
    },
    productId: {
      type: [String, Number],
      default: 'new'
    },
    generalStock: {
      type: Number,
      default: 0
    },
    allColors: {
      type: Array,
      default: () => []
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  emits: ['update', 'remove', 'set-default', 'image-upload', 'sizes-updated', 'stock-corrected'],
  setup(props, { emit }) {
    const showColorDropdown = ref(false)
    const colorSearchQuery = ref('')
    const imagePreviewUrl = ref('')

    // Stock validation reactive refs
    const stockCorrectionMessage = ref('')
    const showStockCorrection = ref(false)

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
      stockCorrectionMessage.value = `Stock automatically corrected from ${attempted} to ${corrected} (available stock limit)`
      showStockCorrection.value = true

      // Auto-hide after 5 seconds
      setTimeout(() => {
        hideStockCorrectionFeedback()
      }, 5000)
    }

    const hideStockCorrectionFeedback = () => {
      showStockCorrection.value = false
      stockCorrectionMessage.value = ''
    }

    // Size management event handlers
    const handleSizesUpdated = (sizes) => {
      // Update the color's sizes data
      emit('sizes-updated', props.index, sizes)
    }

    const handleSaveColorFirst = () => {
      // Emit event to parent to save color first
      emit('save-color-first', props.index)
    }

    const handleImageUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
          alert('Please select a valid image file.')
          event.target.value = ''
          return
        }

        // Validate file size (2MB limit)
        if (file.size > 2 * 1024 * 1024) {
          const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2)
          alert(`File size (${fileSizeMB}MB) exceeds the 2MB limit. Please choose a smaller image.`)
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
      showColorDropdown,
      colorSearchQuery,
      imagePreviewUrl,
      stockCorrectionMessage,
      showStockCorrection,
      colorOptionsArray,
      filteredColorOptions,
      otherColorsStock,
      availableStock,
      isStockExceeded,
      shouldShowSizeManagement,
      getColorCode,
      toggleColorDropdown,
      selectColor,
      updateColor,
      handleImageUpload,
      handleSizesUpdated,
      handleSaveColorFirst
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
  width: 1.5rem;
  height: 1.5rem;
  border-radius: 50%;
  border: 2px solid #ffffff;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
  max-height: 300px;
  overflow-y: auto;
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
}

.color-option {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 0.25rem;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.color-option:hover {
  background-color: #f3f4f6;
}

.color-option.selected {
  background-color: #eff6ff;
  border: 1px solid #3b82f6;
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
</style>
