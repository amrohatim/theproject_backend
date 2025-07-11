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
                  class="vue-btn vue-btn-secondary text-sm"
                  @click="$emit('set-default', index)">
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

              <!-- Hidden select for form submission -->
              <select :value="color.name"
                      @change="updateColor('name', $event.target.value)"
                      class="hidden"
                      required>
                <option value="">Select color</option>
                <option v-for="colorOption in colorOptionsArray"
                        :key="colorOption"
                        :value="colorOption">
                  {{ colorOption }}
                </option>
              </select>
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm">Color Code</label>
              <div class="flex gap-2">
                <input type="color" 
                       :value="color.color_code || '#000000'"
                       @input="updateColor('color_code', $event.target.value)"
                       class="w-12 h-10 p-1 border border-slate-300 rounded-lg">
                <input type="text" 
                       :value="color.color_code"
                       @input="updateColor('color_code', $event.target.value)"
                       placeholder="#000000"
                       class="vue-form-control flex-1">
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block vue-text-sm">Price Adjustment</label>
              <div class="relative">
                <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="number" 
                       step="0.01" 
                       :value="color.price_adjustment"
                       @input="updateColor('price_adjustment', parseFloat($event.target.value) || 0)"
                       class="vue-form-control pl-10">
              </div>
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm">Stock Allocation</label>
              <input type="number" 
                     :value="color.stock"
                     @input="updateColor('stock', parseInt($event.target.value) || 0)"
                     min="0"
                     class="vue-form-control color-stock-input">
            </div>
          </div>
        </div>

        <!-- Image Upload -->
        <div class="space-y-4">
          <label class="block vue-text-sm">
            Product Image <span class="text-red-500">*</span>
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
                    <span class="sr-only">Change Image</span>
                  </button>
                </div>
                
                <!-- Main Image Badge -->
                <div v-if="isDefault" class="absolute top-2 right-2">
                  <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded"
                        style="background-color: var(--primary-600); color: white;">
                    <i class="fas fa-star w-3 h-3 mr-1"></i>
                    Main Image
                  </span>
                </div>
              </div>
              
              <!-- Upload Placeholder -->
              <div v-else class="w-full h-full flex flex-col items-center justify-center cursor-pointer hover:bg-slate-100 transition-colors image-placeholder">
                <i class="fas fa-image w-12 h-12 text-slate-400 mb-3"></i>
                <p class="text-sm font-medium text-slate-600 mb-1">Upload Image</p>
                <p class="text-xs text-slate-500">PNG, JPG up to 2MB</p>
                <p class="text-xs text-slate-400 mt-1">Click or drag to upload</p>
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
          v-if="color.id"
          :color-id="color.id"
          :product-id="productId"
          @sizes-updated="handleSizesUpdated"
        />
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
import { ref, computed, watch } from 'vue'
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
    }
  },
  emits: ['update', 'remove', 'set-default', 'image-upload', 'sizes-updated'],
  setup(props, { emit }) {
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

    // Methods
    const updateColor = (field, value) => {
      const updatedColor = { ...props.color, [field]: value }
      emit('update', props.index, updatedColor)
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
      updateColor('name', colorName)
      // Auto-update color code if not already set
      if (!props.color.color_code) {
        const colorCode = getColorCode(colorName)
        if (colorCode) {
          updateColor('color_code', colorCode)
        }
      }
      showColorDropdown.value = false
    }

    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
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

    const triggerFileInput = () => {
      fileInput.value?.click()
    }

    const validateFile = (file) => {
      imageError.value = ''
      
      if (!file.type.startsWith('image/')) {
        imageError.value = 'Please select a valid image file.'
        return false
      }

      if (file.size > 2 * 1024 * 1024) {
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2)
        imageError.value = `File size (${fileSizeMB}MB) exceeds the 2MB limit.`
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
      imageError.value = 'Failed to load image'
    }

    const handleSizesUpdated = (sizes) => {
      emit('sizes-updated', props.index, sizes)
    }

    return {
      fileInput,
      imageError,
      showColorDropdown,
      colorSearchQuery,
      colorOptionsArray,
      filteredColorOptions,
      hasImage,
      imagePreviewUrl,
      updateColor,
      getColorCode,
      toggleColorDropdown,
      selectColor,
      triggerFileInput,
      handleFileSelect,
      handleDrop,
      handleImageError,
      handleSizesUpdated
    }
  }
}
</script>

<style scoped>
.color-item {
  transition: all 0.3s ease;
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
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
  padding: 0.75rem;
  max-height: 15rem;
  overflow-y: auto;
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
  border-color: var(--primary-blue);
  background-color: var(--primary-50);
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.color-option.selected {
  border-color: var(--primary-blue);
  background-color: var(--primary-100);
  box-shadow: 0 0 0 2px var(--primary-blue-light);
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
