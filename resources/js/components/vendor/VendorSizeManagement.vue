<template>
  <div class="size-management-container" :class="{ 'rtl': isRTL }">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h4 class="vue-text-lg">{{ $t('vendor.size_management') }}</h4>
        <p class="text-sm text-gray-600">{{ $t('vendor.manage_sizes_stock_allocation') }}</p>
      </div>
      <button type="button"
              @click="showAddSizeModal = true"
              class="vue-btn vue-btn-primary">
        <i class="fas fa-plus w-4 h-4"></i>
        {{ $t('vendor.add_size') }}
      </button>
    </div>

    <!-- Stock Allocation Summary -->
    <div v-if="colorStock > 0" class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
      <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-orange-900"> {{ $t('vendor.stock_allocation_for_color') }}</span>
        <span class="text-sm text-orange-700">{{ totalSizeStock }} / {{ colorStock }} {{ $t('vendor.allocated') }}</span>
      </div>
      <div class="w-full bg-orange-200 rounded-full h-2">
        <div class="bg-orange-600 h-2 rounded-full transition-all duration-300"
             :style="{ width: Math.min((totalSizeStock / colorStock) * 100, 100) + '%' }"
             :class="{ 'bg-red-600': totalSizeStock > colorStock }"></div>
      </div>
      <div v-if="totalSizeStock > colorStock" class="mt-2 text-xs text-red-600">
        ⚠️ {{ $t('vendor.stock_allocation_exceeds_limit') }}
      </div>
    </div>

    <!-- Existing Sizes List -->
    <div v-if="sizes.length > 0" class="space-y-4 mb-6">
      <div v-for="(size, index) in sizes" 
           :key="size.id || `temp-${index}`"
           class="vue-card size-item">
        <div class="p-4">
          <div v-if="!size.editing" class="grid grid-cols-4 gap-4 items-center">
            <!-- Size Name -->
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">{{ $t('vendor.size_name') }}</label>
              <div class="text-sm font-medium">{{ size.name }}</div>
            </div>
            
            <!-- Size Value -->
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">{{ $t('vendor.size_value') }}</label>
              <div class="text-sm">{{ size.value || 'N/A' }}</div>
            </div>
            
            <!-- Stock -->
            <div>
              <label class="block text-xs font-medium text-gray-700 mb-1">{{ $t('stock_quantity') }}</label>
              <div class="text-sm font-semibold" :class="{ 'text-red-600': size.stock > availableSizeStock + size.stock }">
                {{ size.stock }} {{ $t('vendor.unit') }}
              </div>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-2 justify-end">
              <button type="button"
                      @click="editSize(index)"
                      class="text-orange-600 hover:text-orange-700 text-sm">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" 
                      @click="removeSize(index)"
                      class="text-red-600 hover:text-red-700 text-sm">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>

          <!-- Edit Mode -->
          <div v-else class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div class="enhanced-form-field space-y-2">
                <label class="enhanced-form-label">
                  <i class="fas fa-tag"></i>
                  {{ $t('size_name') }}
                </label>
                <input type="text"
                       v-model="size.name"
                       class="vue-form-control-enhanced-blue"
                       :class="{ 'border-red-500': size.errors?.name }"
                       @input="validateSizeField(index, 'name')"
                       placeholder="e.g., Small, Medium, Large">
                <div v-if="size.errors?.name" class="text-red-500 text-xs">{{ size.errors.name }}</div>
              </div>

              <div class="enhanced-form-field space-y-2">
                <label class="enhanced-form-label">
                  <i class="fas fa-ruler"></i>
                  {{ $t('vendor.size_value') }}
                </label>
                <input type="text"
                       v-model="size.value"
                       class="vue-form-control-enhanced-blue"
                       :class="{ 'border-red-500': size.errors?.value }"
                       @input="validateSizeField(index, 'value')"
                       placeholder="e.g., S, M, L, XL">
                <div v-if="size.errors?.value" class="text-red-500 text-xs">{{ size.errors.value }}</div>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="enhanced-form-field space-y-2">
                <label class="enhanced-form-label">
                  <i class="fas fa-boxes"></i>
                  {{ $t('stock_quantity') }}
                </label>
                <div class="enhanced-input-group">
                  <input type="number"
                         v-model.number="size.stock"
                         class="vue-form-control-enhanced-blue text-center font-semibold"
                         :class="{ 'border-red-500': size.errors?.stock }"
                         min="0"
                         :max="colorStock"
                         @input="size.stock = validateSizeStock(size.stock, index); validateSizeField(index, 'stock')"
                         placeholder="0">
                  <div class="input-suffix">{{ $t('units') }}</div>
                </div>
                <div class="stock-allocation-info">
                  <span class="available-stock">{{ $t('vendor.available') }}: {{ availableSizeStock + (parseInt(size.stock) || 0) }}</span>
                  <span class="allocated-stock">{{ size.stock || 0 }} {{ $t('vendor.allocated') }}</span>
                </div>
                <div v-if="size.errors?.stock" class="text-red-500 text-xs">{{ size.errors.stock }}</div>
              </div>

              <div class="enhanced-form-field space-y-2">
                <label class="enhanced-form-label">
                  <i class="fas fa-dollar-sign"></i>
                  {{ $t('vendor.price_adjustment') }}
                </label>
                <div class="enhanced-input-group">
                  <input type="number"
                         v-model.number="size.price_adjustment"
                         class="vue-form-control-enhanced-blue text-center"
                         :class="{ 'border-red-500': size.errors?.price_adjustment }"
                         step="0.01"
                         @input="validateSizeField(index, 'price_adjustment')"
                         placeholder="0.00">
                  <div class="input-suffix">{{ $t('vendor.aed') }}</div>
                </div>
                <div v-if="size.errors?.price_adjustment" class="text-red-500 text-xs">{{ size.errors.price_adjustment }}</div>
              </div>
            </div>

            <!-- Edit Actions -->
            <div class="flex items-center gap-2 pt-2">
              <button type="button" 
                      @click="saveSize(index)"
                      :disabled="saving"
                      class="vue-btn vue-btn-primary">
                <i class="fas fa-save"></i>
                {{ saving ? 'Saving...' : 'Save' }}
              </button>
              <button type="button" 
                      @click="cancelEdit(index)"
                      class="vue-btn vue-btn-secondary">
                <i class="fas fa-times"></i>
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-8 text-gray-500">
      <i class="fas fa-ruler-combined text-3xl mb-3"></i>
      <p class="text-sm">{{ $t('vendor.no_sizes_added_yet') }}</p>
      <p class="text-xs">{{ $t('vendor.click_add_size_to_start_managing') }}</p>
    </div>

    <!-- Add Size Modal -->
    <div v-if="showAddSizeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">{{ $t('add_new_size') }}</h3>
          <button @click="closeAddSizeModal" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="space-y-4">
          <!-- Size Category -->
          <div class="enhanced-form-field space-y-1">
            <label class="enhanced-form-label text-sm">
              <i class="fas fa-layer-group"></i>
              {{ $t('vendor.size_category') }}
            </label>
            <div v-if="sizeCategoriesLoading" class="text-xs text-gray-500">
              {{ $t('loading') }}...
            </div>
            <div v-else-if="sizeCategoriesError" class="text-xs text-red-500">
              {{ sizeCategoriesError }}
            </div>
            <select v-model="newSize.category"
                    class="vue-form-control-enhanced-blue"
                    :class="{ 'border-red-500': newSize.errors?.category }"
                    @change="onCategoryChange"
                    :disabled="sizeCategoriesLoading">
              <option value="">{{ $t('select_category') }}</option>
              <option
                v-for="category in sizeCategories"
                :key="category.id"
                :value="category.name">
                {{ getCategoryLabel(category) }}
              </option>
            </select>
            <div v-if="newSize.errors?.category" class="text-red-500 text-xs">{{ newSize.errors.category }}</div>
          </div>

          <!-- Size Name -->
          <div class="enhanced-form-field space-y-1">
            <label class="enhanced-form-label text-sm">
              <i class="fas fa-tag" :class="{ 'ml-2': isRTL, 'mr-2': !isRTL }"></i>
              {{ $t('size_name') }}
            </label>
            <select v-model="newSize.name"
                    class="vue-form-control-enhanced-blue"
                    :class="{ 'border-red-500': newSize.errors?.name }"
                    @change="onSizeNameChange"
                    :disabled="!newSize.category">
              <option value="">{{ $t('select_size_name') }}</option>
              <option v-for="sizeOption in availableSizeNames"
                      :key="sizeOption.value"
                      :value="sizeOption.value">{{ sizeOption.label }}</option>
            </select>
            <div v-if="newSize.errors?.name" class="text-red-500 text-xs">{{ newSize.errors.name }}</div>
          </div>

          <!-- Size Value -->
          <div class="enhanced-form-field space-y-1">
            <label class="enhanced-form-label text-sm">
              <i class="fas fa-ruler" :class="{ 'ml-2': isRTL, 'mr-2': !isRTL }"></i>
              {{ $t('vendor.size_value') }}
            </label>
            <input type="text"
                   v-model="newSize.value"
                   class="vue-form-control-enhanced-blue"
                   :class="{ 'border-red-500': newSize.errors?.value }"
                   @input="validateNewSizeField('value')"
                   :disabled="!newSize.name"
                   readonly
                   :placeholder="$t('auto_filled_based_on_size_name')">
            <div v-if="newSize.errors?.value" class="text-red-500 text-xs">{{ newSize.errors.value }}</div>
          </div>

          <!-- Stock and Price Section -->
          <div class="grid grid-cols-2 gap-3">
            <div class="enhanced-form-field space-y-1">
              <label class="enhanced-form-label text-sm">
                <i class="fas fa-boxes" :class="{ 'ml-2': isRTL, 'mr-2': !isRTL }"></i>
                {{ $t('stock_quantity') }}
              </label>
              <div class="enhanced-input-group">
                <input type="number"
                       v-model.number="newSize.stock"
                       class="vue-form-control-enhanced-blue text-center font-semibold"
                       min="0"
                       :max="availableSizeStock"
                       @input="newSize.stock = validateSizeStock(newSize.stock); validateNewSizeField('stock')"
                       placeholder="0">
                <div class="input-suffix">{{ $t('units') }}</div>
              </div>
              <div class="stock-allocation-info text-xs">
                <span class="available-stock">{{ $t('available') }}: {{ availableSizeStock }}</span>
                <span class="allocated-stock">{{ newSize.stock || 0 }} {{ $t('to_allocate') }}</span>
              </div>
              <div v-if="newSize.errors?.stock" class="text-red-500 text-xs">{{ newSize.errors.stock }}</div>
            </div>

            <div class="enhanced-form-field space-y-1">
              <label class="enhanced-form-label text-sm">
                <i class="fas fa-dollar-sign" :class="{ 'ml-2': isRTL, 'mr-2': !isRTL }"></i>
                {{ $t('vendor.price_adjustment') }}
              </label>
              <div class="enhanced-input-group">
                <input type="number"
                       v-model.number="newSize.price_adjustment"
                       class="vue-form-control-enhanced-blue text-center"
                       step="0.01"
                       @input="validateNewSizeField('price_adjustment')"
                       placeholder="0.00">
                <div class="input-suffix">{{ $t('vendor.aed') }}</div>
              </div>
              <div v-if="newSize.errors?.price_adjustment" class="text-red-500 text-xs">{{ newSize.errors.price_adjustment }}</div>
            </div>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex items-center gap-3 mt-6">
          <button type="button" 
                  @click="addSize"
                  :disabled="saving"
                  class="vue-btn vue-btn-primary flex-1">
            <i class="fas fa-plus"></i>
            {{ saving ? $t('vendor.adding') : $t('vendor.add_size') }}
          </button>
          <button type="button" 
                  @click="closeAddSizeModal"
                  class="vue-btn vue-btn-secondary">
            {{ $t('vendor.cancel') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import axios from 'axios'

export default {
  name: 'VendorSizeManagement',
  props: {
    colorId: {
      type: [String, Number],
      default: null
    },
    productId: {
      type: [String, Number],
      default: 'new'
    },
    colorStock: {
      type: Number,
      default: 0
    }
  },
  emits: ['sizes-updated', 'save-color-first'],
  setup(props, { emit }) {
    // Reactive data
    const loading = ref(false)
    const saving = ref(false)
    const showAddSizeModal = ref(false)
    const sizes = ref([])
    const errorMessage = ref(null)
    const sizeCategories = ref([])
    const sizeCategoriesLoading = ref(false)
    const sizeCategoriesError = ref(null)
    const sizeOptionsMap = ref({})

    const newSize = reactive({
      name: '',
      value: '',
      category: '', // Start with empty category to force selection
      stock: 0,
      price_adjustment: 0,
      is_available: true,
      errors: {}
    })

    // Computed property to determine if we're in product creation mode
    const isProductCreationMode = computed(() => {
      return !props.productId || props.productId === null || props.productId === undefined || props.productId === 'null' || props.productId === 'new'
    })

    // RTL support
    const isRTL = computed(() => {
      return document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar'
    })

    // Computed properties for size options
    const availableSizeNames = computed(() => {
      if (!newSize.category || !sizeOptionsMap.value[newSize.category]) {
        return []
      }
      return sizeOptionsMap.value[newSize.category]
    })

    const availableSizeValues = computed(() => {
      if (!newSize.category || !newSize.name || !sizeOptionsMap.value[newSize.category]) {
        return []
      }
      return [newSize.name]
    })

    // Stock validation computed properties
    const totalSizeStock = computed(() => {
      return sizes.value.reduce((total, size) => total + (parseInt(size.stock) || 0), 0)
    })

    const availableSizeStock = computed(() => {
      return Math.max(0, props.colorStock - totalSizeStock.value)
    })

    const validateSizeStock = (stockValue, excludeIndex = null) => {
      const otherSizesStock = sizes.value
        .filter((_, index) => index !== excludeIndex)
        .reduce((total, size) => total + (parseInt(size.stock) || 0), 0)

      const maxAllowed = Math.max(0, props.colorStock - otherSizesStock)
      return Math.min(stockValue, maxAllowed)
    }

    // Validation methods
    const validateSize = (size) => {
      const errors = {}

      if (!size.category || size.category.trim() === '') {
        errors.category = $t('vendor.size_category_required')
      }

      if (!size.name || size.name.trim() === '') {
        errors.name = $t('vendor.size_name_required')
      }

      if (!size.value || size.value.trim() === '') {
        errors.value = $t('vendor.size_value_required')
      }

      if (size.stock < 0) {
        errors.stock = $t('vendor.stock_cannot_be_negative')
      }

      if (size.stock > props.colorStock) {
        errors.stock = $t('vendor.stock_cannot_exceed_color_stock', { colorStock: props.colorStock })
      }

      return errors
    }

    const validateSizeField = (index, field) => {
      const size = sizes.value[index]
      const errors = validateSize(size)

      if (errors[field]) {
        size.errors = { ...size.errors, [field]: errors[field] }
      } else {
        delete size.errors[field]
      }
    }

    const validateNewSizeField = (field) => {
      const errors = validateSize(newSize)

      if (errors[field]) {
        newSize.errors = { ...newSize.errors, [field]: errors[field] }
      } else {
        delete newSize.errors[field]
      }
    }

    // Handle category change
    const onCategoryChange = () => {
      // Reset name and value when category changes
      newSize.name = ''
      newSize.value = ''
      // Clear any existing errors for name and value
      delete newSize.errors.name
      delete newSize.errors.value
      validateNewSizeField('category')
    }

    // Handle size name change
    const onSizeNameChange = () => {
      if (newSize.name) {
        const option = (sizeOptionsMap.value[newSize.category] || []).find(opt => opt.value === newSize.name)
        newSize.value = option ? option.sizeValue : newSize.name
      } else {
        newSize.value = ''
      }
      // Clear any existing errors for value
      delete newSize.errors.value
      validateNewSizeField('name')
      validateNewSizeField('value')
    }

    // Detect if we're in Products Manager context
    const isProductsManagerContext = computed(() => {
      return window.location.pathname.includes('/products-manager/')
    })

    // Get the appropriate API base path
    const getApiBasePath = () => {
      return isProductsManagerContext.value ? '/products-manager' : '/vendor'
    }

    const getCategoryLabel = (category) => {
      if (!category) return ''
      if (isRTL.value && category.display_name_arabic) {
        return category.display_name_arabic
      }
      return category.display_name || category.name
    }

    const buildSizeOptionsMap = (categories) => {
      const map = {}
      categories.forEach((category) => {
        const sizes = (category.standardized_sizes || []).map((size) => {
          const pieces = []
          pieces.push(size.name)
          const valuePart = size.value && size.value !== size.name ? size.value : null
          if (valuePart) {
            pieces.push(`(${valuePart})`)
          }
          if (size.additional_info) {
            pieces.push(`- ${size.additional_info}`)
          }
          return {
            value: size.name,
            sizeValue: size.value || size.name,
            label: pieces.join(' '),
          }
        })
        map[category.name] = sizes
      })
      sizeOptionsMap.value = map
    }

    const fetchSizeCategories = async () => {
      try {
        sizeCategoriesLoading.value = true
        sizeCategoriesError.value = null

        const response = await axios.get('/api/size-categories', {
          params: { include_inactive: true }
        })
        if (!response.data.success) {
          console.error('Size categories API returned success=false', response.data)
          sizeCategoriesError.value = response.data.message || 'Failed to load size categories'
          return
        }

        const raw = response.data.data ?? response.data.size_categories ?? []
        const normalized = Array.isArray(raw) ? raw.map(cat => ({
          ...cat,
          standardized_sizes: cat.standardized_sizes ?? cat.standardizedSizes ?? []
        })) : []

        sizeCategories.value = normalized
        buildSizeOptionsMap(sizeCategories.value)

        // Auto-select first category if none chosen
        if (!newSize.category && sizeCategories.value.length > 0) {
          newSize.category = sizeCategories.value[0].name
        }
      } catch (error) {
        console.error('Error fetching size categories:', error)
        sizeCategoriesError.value = 'Failed to load size categories'
      } finally {
        sizeCategoriesLoading.value = false
      }
    }

    // Fetch existing sizes from API
    const fetchSizes = async () => {
      // Skip API calls during product creation mode
      if (isProductCreationMode.value) {
        console.log('📝 Skipping fetchSizes during product creation mode')
        loading.value = false
        return
      }

      try {
        loading.value = true
        errorMessage.value = null

        // Validate inputs before making API call
        if (!props.colorId || !props.productId) {
          console.log('Missing colorId or productId, skipping fetchSizes')
          return
        }

        console.log('Fetching sizes for colorId:', props.colorId, 'productId:', props.productId)

        // Use context-aware API path
        const apiPath = `${getApiBasePath()}/api/color-sizes/get-sizes-for-color`
        const response = await axios.post(apiPath, {
          color_id: parseInt(props.colorId),
          product_id: parseInt(props.productId),
          only_allocated: true
        })

        if (response.data.success) {
          const newSizes = response.data.sizes.map(size => ({
            id: size.id,
            name: size.name,
            value: size.value,
            category: size.category || 'clothes',
            additional_info: size.additional_info,
            stock: size.allocated_stock || 0,
            price_adjustment: size.price_adjustment || 0,
            is_available: size.is_available !== false,
            display_order: size.display_order || 0,
            editing: false,
            errors: {}
          }))

          console.log('📊 Sizes fetched successfully:', newSizes.length, 'sizes found')
          console.log('📋 Size details:', newSizes)

          sizes.value = newSizes
          emit('sizes-updated', sizes.value)
        } else {
          console.error('API returned success=false:', response.data)
          throw new Error(response.data.message || 'API returned unsuccessful response')
        }
      } catch (error) {
        console.error('Error fetching sizes:', error)
        errorMessage.value = $t('vendor.failed_load_sizes')
      } finally {
        loading.value = false
      }
    }

    // Size management methods
    const editSize = (index) => {
      const size = sizes.value[index]
      size.editing = true
      size.originalData = { ...size }
      size.errors = {}
    }

    const cancelEdit = (index) => {
      const size = sizes.value[index]
      if (size.originalData) {
        Object.assign(size, size.originalData)
      }
      size.editing = false
      size.errors = {}
    }

    const saveSize = async (index) => {
      const size = sizes.value[index]
      const errors = validateSize(size)

      if (Object.keys(errors).length > 0) {
        size.errors = errors
        return
      }

      // Handle product creation mode (local size management)
      if (isProductCreationMode.value) {
        console.log('💾 Saving size locally during product creation')
        size.editing = false
        size.errors = {}

        // Update the original data for cancel functionality
        size.originalData = { ...size }

        emit('sizes-updated', sizes.value)
        return
      }

      // For existing products, save to backend
      try {
        saving.value = true
        console.log('🔄 Updating size via API:', {
          size_id: size.id,
          color_id: props.colorId,
          name: size.name,
          value: size.value,
          stock: size.stock,
          price_adjustment: size.price_adjustment
        })

        // Use context-aware API path
        const apiPath = `${getApiBasePath()}/api/sizes/update`
        const response = await axios.post(apiPath, {
          size_id: size.id,
          color_id: props.colorId,
          name: size.name,
          value: size.value,
          additional_info: size.additional_info || '',
          stock: size.stock || 0,
          price_adjustment: size.price_adjustment || 0,
          is_available: size.is_available !== false
        })

        if (response.data.success) {
          console.log('✅ Size updated successfully:', response.data.size)

          // Update the size with the response data to ensure consistency
          Object.assign(size, {
            id: response.data.size.id,
            name: response.data.size.name,
            value: response.data.size.value,
            additional_info: response.data.size.additional_info,
            stock: response.data.size.allocated_stock || response.data.size.stock || 0,
            price_adjustment: response.data.size.price_adjustment || 0,
            is_available: response.data.size.is_available !== false,
            editing: false,
            errors: {},
            originalData: {
              id: response.data.size.id,
              name: response.data.size.name,
              value: response.data.size.value,
              additional_info: response.data.size.additional_info,
              stock: response.data.size.allocated_stock || response.data.size.stock || 0,
              price_adjustment: response.data.size.price_adjustment || 0,
              is_available: response.data.size.is_available !== false
            }
          })

          emit('sizes-updated', sizes.value)
        } else {
          console.error('Failed to update size:', response.data)
          errorMessage.value = response.data.message || $t('vendor.failed_update_size')
        }
      } catch (error) {
        console.error('Error updating size:', error)
        if (error.response && error.response.data && error.response.data.message) {
          errorMessage.value = error.response.data.message
        } else {
          errorMessage.value = $t('vendor.failed_update_size')
        }
      } finally {
        saving.value = false
      }
    }

    const removeSize = async (index) => {
      const size = sizes.value[index]

      if (!confirm($t('vendor.confirm_remove_size'))) {
        return
      }

      // Handle product creation mode (local size management)
      if (isProductCreationMode.value) {
        console.log('🗑️ Removing size locally during product creation')
        sizes.value.splice(index, 1)
        emit('sizes-updated', sizes.value)
        return
      }

      // For existing products, delete from backend
      try {
        saving.value = true

        // Use context-aware API path
        const apiPath = `${getApiBasePath()}/api/sizes/delete`
        const response = await axios.post(apiPath, {
          size_id: size.id,
          color_id: props.colorId
        })

        if (response.data.success) {
          sizes.value.splice(index, 1)
          emit('sizes-updated', sizes.value)
        }
      } catch (error) {
        console.error('Error removing size:', error)
        errorMessage.value = $t('vendor.failed_remove_size')
      } finally {
        saving.value = false
      }
    }

    const closeAddSizeModal = () => {
      showAddSizeModal.value = false
      // Reset form
      Object.assign(newSize, {
        name: '',
        value: '',
        category: sizeCategories.value.length ? sizeCategories.value[0].name : '',
        stock: 0,
        price_adjustment: 0,
        is_available: true,
        errors: {}
      })
    }

    /**
     * Resume size creation after color has been saved.
     * This method is called from the parent component after a color is saved.
     */
    const resumeSizeCreation = async (pendingSizeData) => {
      console.log('🔄 Resuming size creation after color save:', pendingSizeData)

      try {
        saving.value = true

        const colorId = props.colorId
        if (!colorId) {
          console.error('❌ Color ID still not available after save')
          errorMessage.value = $t('vendor.color_save_failed')
          return
        }

        // Call the API to create the size with the pending data
        // Use context-aware API path
        const apiPath = `${getApiBasePath()}/api/sizes/create`
        const response = await axios.post(apiPath, {
          product_id: props.productId,
          color_id: colorId,
          name: pendingSizeData.name,
          value: pendingSizeData.value,
          category: pendingSizeData.category || 'clothes',
          price_adjustment: pendingSizeData.price_adjustment || 0,
          stock: pendingSizeData.stock || 0,
          is_available: pendingSizeData.is_available
        })

        if (response.data.success) {
          console.log('✅ Size created successfully after color save:', response.data.size)

          // Close the modal
          closeAddSizeModal()

          // Add the new size to the list with proper stock mapping
          const newSizeData = {
            id: response.data.size.id,
            name: response.data.size.name,
            value: response.data.size.value,
            category: response.data.size.category || 'clothes',
            additional_info: response.data.size.additional_info,
            stock: response.data.size.allocated_stock || response.data.size.stock || 0,
            price_adjustment: response.data.size.price_adjustment || 0,
            is_available: response.data.size.is_available !== false,
            display_order: response.data.size.display_order || sizes.value.length,
            editing: false,
            errors: {},
            originalData: {
              id: response.data.size.id,
              name: response.data.size.name,
              value: response.data.size.value,
              category: response.data.size.category || 'clothes',
              additional_info: response.data.size.additional_info,
              stock: response.data.size.allocated_stock || response.data.size.stock || 0,
              price_adjustment: response.data.size.price_adjustment || 0,
              is_available: response.data.size.is_available !== false,
              display_order: response.data.size.display_order || sizes.value.length
            }
          }

          sizes.value.push(newSizeData)
          emit('sizes-updated', sizes.value)
        } else {
          console.error('Failed to create size after color save:', response.data)
          errorMessage.value = response.data.message || $t('vendor.failed_create_size')
        }
      } catch (error) {
        console.error('Error creating size after color save:', error)
        if (error.response && error.response.data && error.response.data.message) {
          errorMessage.value = error.response.data.message
        } else {
          errorMessage.value = $t('vendor.failed_create_size')
        }
      } finally {
        saving.value = false
      }
    }

    const addSize = async () => {
      const errors = validateSize(newSize)

      if (Object.keys(errors).length > 0) {
        newSize.errors = errors
        return
      }

      try {
        saving.value = true

        // Handle product creation mode (local size management)
        if (isProductCreationMode.value) {
          console.log('📝 Adding size locally during product creation')

          // Create a new size object with a temporary ID
          const newSizeData = {
            id: `temp_${Date.now()}_${Math.random().toString(36).substring(2, 11)}`, // Temporary ID
            name: newSize.name,
            value: newSize.value,
            category: newSize.category || 'clothes',
            stock: newSize.stock || 0,
            price_adjustment: newSize.price_adjustment || 0,
            is_available: newSize.is_available !== false,
            display_order: sizes.value.length,
            editing: false,
            errors: {},
            originalData: {
              name: newSize.name,
              value: newSize.value,
              category: newSize.category || 'clothes',
              stock: newSize.stock || 0,
              price_adjustment: newSize.price_adjustment || 0,
              is_available: newSize.is_available !== false,
              display_order: sizes.value.length
            }
          }

          sizes.value.push(newSizeData)
          emit('sizes-updated', sizes.value)
          closeAddSizeModal()
          return
        }

        // For existing products, save to backend
        const colorId = props.colorId
        if (!colorId) {
          console.log('🔄 Color needs to be saved first, storing pending size data')
          // Store the pending size data to resume creation after color is saved
          const pendingSizeData = {
            name: newSize.name,
            value: newSize.value,
            category: newSize.category || 'clothes',
            price_adjustment: newSize.price_adjustment || 0,
            stock: newSize.stock || 0,
            is_available: newSize.is_available
          }
          emit('save-color-first', pendingSizeData)
          return
        }

        // Call the API to create the size
        // Use context-aware API path
        const apiPath = `${getApiBasePath()}/api/sizes/create`
        const response = await axios.post(apiPath, {
          product_id: props.productId,
          color_id: colorId,
          name: newSize.name,
          value: newSize.value,
          category: newSize.category || 'clothes',
          price_adjustment: newSize.price_adjustment || 0,
          stock: newSize.stock || 0,
          is_available: newSize.is_available
        })

        if (response.data.success) {
          console.log('✅ Size created successfully:', response.data.size)

          // Close the modal first
          closeAddSizeModal()

          // Add the new size to the list with proper stock mapping
          const newSizeData = {
            id: response.data.size.id,
            name: response.data.size.name,
            value: response.data.size.value,
            category: response.data.size.category || 'clothes',
            additional_info: response.data.size.additional_info,
            stock: response.data.size.allocated_stock || response.data.size.stock || 0, // CRITICAL FIX: Map allocated_stock to stock
            price_adjustment: response.data.size.price_adjustment || 0,
            is_available: response.data.size.is_available !== false,
            display_order: response.data.size.display_order || 0,
            editing: false,
            errors: {},
            originalData: {
              id: response.data.size.id,
              name: response.data.size.name,
              value: response.data.size.value,
              category: response.data.size.category || 'clothes',
              additional_info: response.data.size.additional_info,
              stock: response.data.size.allocated_stock || response.data.size.stock || 0,
              price_adjustment: response.data.size.price_adjustment || 0,
              is_available: response.data.size.is_available !== false,
              display_order: response.data.size.display_order || 0
            }
          }

          sizes.value.push(newSizeData)
          emit('sizes-updated', sizes.value)
        }
      } catch (error) {
        console.error('Error adding size:', error)
        errorMessage.value = $t('vendor.failed_add_size')
      } finally {
        saving.value = false
      }
    }

    // Lifecycle hooks
    onMounted(() => {
      fetchSizeCategories().then(() => {
        // Only fetch sizes if we're not in product creation mode
        if (props.colorId && !isProductCreationMode.value) {
          fetchSizes()
        }
      })
    })

    // Watch for color changes
    watch(() => props.colorId, (newColorId, oldColorId) => {
      // Only fetch sizes if we're not in product creation mode
      if (!isProductCreationMode.value && newColorId && newColorId !== oldColorId) {
        // Clear existing sizes first to show loading state
        sizes.value = []
        fetchSizes()
      }
    })

    // Ensure categories are loaded when the add modal opens (create flow)
    watch(() => showAddSizeModal.value, (open) => {
      if (open && sizeCategories.value.length === 0) {
        fetchSizeCategories()
      }
    })

    // Additional watch for immediate updates when colorId changes from null/undefined to a value
    watch(() => props.colorId, (newColorId) => {
      // Only fetch sizes if we're not in product creation mode
      if (!isProductCreationMode.value && newColorId && !loading.value) {
        // If we have a colorId and we're not currently loading, fetch sizes
        fetchSizes()
      }
    }, { immediate: true })

    // Translation method using Laravel's translation system
    const $t = (key, replacements = {}) => {
      if (typeof window.Laravel !== 'undefined' && window.Laravel.translations) {
        let translation = window.Laravel.translations[key] || key
        
        // Handle replacements
        Object.keys(replacements).forEach(placeholder => {
          translation = translation.replace(`:${placeholder}`, replacements[placeholder])
        })
        
        return translation
      }
      return key
    }

    return {
      loading,
      saving,
      showAddSizeModal,
      sizes,
      newSize,
      errorMessage,
      totalSizeStock,
      availableSizeStock,
      isProductCreationMode,
      isRTL,
      availableSizeNames,
      availableSizeValues,
      sizeCategoriesLoading,
      sizeCategoriesError,
      validateSizeStock,
      validateSizeField,
      validateNewSizeField,
      onCategoryChange,
      onSizeNameChange,
      sizeCategories,
      getCategoryLabel,
      fetchSizes,
      editSize,
      cancelEdit,
      saveSize,
      removeSize,
      closeAddSizeModal,
      addSize,
      resumeSizeCreation,
      $t
    }
  }
}
</script>

<style scoped>
/* Size management specific styles */
.size-management-container {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--gray-200);
}

.size-item {
  transition: all 0.2s ease;
}

.size-item:hover {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.enhanced-form-field {
  position: relative;
}

.enhanced-form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.25rem;
}

.enhanced-form-label i {
  color: #6b7280;
  width: 1rem;
}

.vue-form-control-enhanced-blue {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.vue-form-control-enhanced-blue:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.enhanced-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.enhanced-input-group input {
  padding-right: 3rem;
}

.input-suffix {
  position: absolute;
  right: 0.75rem;
  font-size: 0.75rem;
  color: #6b7280;
  font-weight: 500;
  pointer-events: none;
}

.stock-allocation-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

.available-stock {
  color: #059669;
  font-weight: 500;
}

.allocated-stock {
  color: #3b82f6;
  font-weight: 500;
}

.vue-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.15s ease;
  cursor: pointer;
  border: 1px solid transparent;
}

.vue-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.vue-btn-primary {
  background-color: #3b82f6;
  color: #ffffff;
  border-color: #3b82f6;
}

.vue-btn-primary:hover:not(:disabled) {
  background-color: #2563eb;
  border-color: #2563eb;
}

.vue-btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  border-color: #d1d5db;
}

.vue-btn-secondary:hover:not(:disabled) {
  background-color: #e5e7eb;
  border-color: #9ca3af;
}

.vue-card {
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

/* RTL Support */
.rtl {
  direction: rtl;
  text-align: right;
}

.rtl .enhanced-form-label {
  flex-direction: row-reverse;
}

.rtl .vue-form-control-enhanced-blue {
  text-align: right;
}

.rtl .enhanced-input-group {
  flex-direction: row-reverse;
}

.rtl .input-suffix {
  left: 0.75rem;
  right: auto;
}

.rtl .enhanced-input-group input {
  padding-left: 3rem;
  padding-right: 0.75rem;
}

.rtl .stock-allocation-info {
  flex-direction: row-reverse;
}

.rtl .grid {
  direction: rtl;
}

.rtl .flex {
  flex-direction: row-reverse;
}

.rtl .gap-3 > * + * {
  margin-left: 0;
  margin-right: 0.75rem;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .enhanced-form-label {
    color: #f9fafb;
  }

  .vue-form-control-enhanced-blue {
    background-color: #374151;
    border-color: #4b5563;
    color: #f9fafb;
  }

  .vue-form-control-enhanced-blue:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  .vue-card {
    background-color: #1f2937;
    border-color: #374151;
  }

  .vue-btn-secondary {
    background-color: #374151;
    color: #f9fafb;
    border-color: #4b5563;
  }

  .vue-btn-secondary:hover:not(:disabled) {
    background-color: #4b5563;
    border-color: #6b7280;
  }
}
</style>
