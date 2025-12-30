<template>
  <div class="size-management-container">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h5 class="vue-text-sm font-semibold">{{ $t('size_management') }}</h5>
        <p class="text-xs" style="color: var(--gray-500);">
          {{ $t('manage_sizes_stock_allocation') }}
        </p>
      </div>
      <div class="flex items-center gap-2">
                <p class="text-xs text-slate-500">{{ $t('please_click_refresh') }}</p>
        <button type="button"
                class="vue-btn vue-btn-secondary bg-orange-500 text-white p-2  rounded-md text-sm "
                @click="() => refreshSizes()"
                :disabled="loading"
                :title="$t('refresh_sizes_list')">
          <i class="fas fa-sync-alt  ml-1 mr-1 w-3 h-3" :class="{ 'fa-spin': loading }"></i>
          {{ $t('refresh') }}
        </button>
        <button type="button"
                class="vue-btn vue-btn-primary bg-blue-500 text-white p-2 rounded-md text-sm"
                @click="showAddSizeModal = true">
          <i class="fas fa-plus w-3 h-3"></i>
          {{ $t('add_size') }}
        </button>

      </div>
    </div>

    <!-- Error State -->
    <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
      <div class="flex items-start gap-3">
        <i class="fas fa-exclamation-triangle w-4 h-4 mt-0.5" style="color: var(--red-500);"></i>
        <div class="flex-1">
          <h6 class="text-sm font-medium" style="color: var(--red-800);">{{ $t('error_loading_sizes') }}</h6>
          <p class="text-xs mt-1" style="color: var(--red-600);">{{ errorMessage }}</p>
          <button type="button"
                  class="mt-2 text-xs underline"
                  style="color: var(--red-600);"
                  @click="() => refreshSizes()">
            {{ $t('try_again') }}
          </button>
        </div>
        <button type="button"
                class="text-red-400 hover:text-red-600"
                @click="errorMessage = null">
          <i class="fas fa-times w-3 h-3"></i>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="flex items-center gap-2">
        <i class="fas fa-spinner fa-spin w-4 h-4" style="color: var(--primary-blue);"></i>
        <span class="text-sm" style="color: var(--gray-600);">{{ $t('loading_sizes') }}</span>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="sizes.length === 0 && !errorMessage" class="text-center py-8">
      <i class="fas fa-ruler w-8 h-8 mb-3" style="color: var(--gray-400);"></i>
      <h6 class="vue-text-sm mb-2">{{ $t('no_sizes_added') }}</h6>
      <p class="text-xs mb-4" style="color: var(--gray-500);">
        <span v-if="isProductCreationMode">
          {{ $t('add_sizes_inventory_management') }}
        </span>
        <span v-else>
          {{ $t('add_sizes_stock_allocation') }}
        </span>
      </p>
      <button type="button"
              class="vue-btn vue-btn-primary text-sm"
              @click="showAddSizeModal = true">
        <i class="fas fa-plus w-3 h-3"></i>
        {{ $t('add_first_size') }}
      </button>
    </div>

    <!-- Sizes List -->
    <div v-else class="space-y-3">
      <div v-for="(size, index) in sizes" 
           :key="size.id || index"
           class="size-item p-4 border rounded-lg"
           style="border-color: var(--gray-200); background-color: var(--gray-50);">
        
        <!-- Size Display Mode -->
        <div v-if="!size.editing" class="flex items-center justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3">
              <div class="size-info">
                <h6 class="vue-text-sm font-medium">{{ size.name }}</h6>
                <div class="flex items-center gap-4 mt-1">
                  <span v-if="size.value" class="text-xs" style="color: var(--gray-600);">
                    {{ $t('value') }}: {{ size.value }}
                  </span>
                  <span class="text-xs" style="color: var(--gray-600);">
                    {{ $t('stock') }}: {{ size.stock || 0 }}
                  </span>
                  <span v-if="size.price_adjustment && size.price_adjustment !== 0" 
                        class="text-xs" 
                        :style="{ color: size.price_adjustment > 0 ? 'var(--green-600)' : 'var(--red-600)' }">
                    {{ size.price_adjustment > 0 ? '+' : '' }}${{ size.price_adjustment }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          
          <div class="flex items-center gap-2">
            <button type="button" 
                    class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors"
                    @click="editSize(index)"
                    :title="$t('edit_size')">
              <i class="fas fa-edit w-4 h-4"></i>
            </button>
            <button type="button" 
                    class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                    @click="removeSize(index)"
                    :title="$t('remove_size')">
              <i class="fas fa-trash w-4 h-4"></i>
            </button>
          </div>
        </div>

        <!-- Size Edit Mode -->
        <div v-else class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="enhanced-form-field space-y-2">
              <label class="enhanced-form-label">
                <i class="fas fa-ruler"></i>
                {{ $t('size_name') }} <span class="text-red-500">*</span>
              </label>
              <select v-model="size.name"
                      class="vue-form-control-enhanced-blue"
                      :class="{ 'border-red-500': size.errors?.name }"
                      @change="handleSizeNameChange(size.name, true, index)"
                      required>
                <option value="">{{ $t('select_size') }}</option>
                <option v-for="option in getSizeNameOptionsForEdit(size.category)"
                        :key="option.value"
                        :value="option.value">
                  {{ option.label }}
                </option>
              </select>
              <div v-if="size.errors?.name" class="text-red-500 text-xs">{{ size.errors.name }}</div>
            </div>

            <div class="enhanced-form-field space-y-2">
              <label class="enhanced-form-label">
                <i class="fas fa-tag"></i>
                {{ $t('size_value') }}
              </label>
              <input type="text"
                     v-model="size.value"
                     class="vue-form-control-enhanced-blue"
                     :placeholder="$t('auto_filled_selection')"
                     readonly>
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
                <span class="available-stock">{{ $t('available') }}: {{ availableSizeStock + (parseInt(size.stock) || 0) }}</span>
                <span class="allocated-stock">{{ size.stock || 0 }} {{ $t('allocated') }}</span>
              </div>
              <div v-if="size.errors?.stock" class="text-red-500 text-xs">{{ size.errors.stock }}</div>
            </div>

            <div class="enhanced-form-field space-y-2">
              <label class="enhanced-form-label">
                <i class="fas fa-dollar-sign"></i>
                {{ $t('price_adjustment') }}
              </label>
              <div class="enhanced-price-input">
                <div class="currency-prefix">{{ $t('ae') }}</div>
                <input type="number"
                       v-model.number="size.price_adjustment"
                       step="0.01"
                       class="vue-form-control-enhanced-blue"
                       placeholder="0.00">
              </div>
              <p class="enhanced-help-text">
                <i class="fas fa-info-circle"></i>
                {{ $t('additional_cost_size_variant') }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <input :id="'size-available-' + colorId + '-' + index"
                     v-model="size.is_available"
                     type="checkbox"
                     class="w-4 h-4 bg-gray-100 border-gray-300 rounded"
                     style="color: var(--primary-blue); --tw-ring-color: var(--primary-blue);">
              <label :for="'size-available-' + colorId + '-' + index" class="vue-text-sm">
                {{ $t('available_for_purchase') }}
              </label>
            </div>

            <div class="flex items-center gap-2">
              <button type="button" 
                      class="vue-btn vue-btn-secondary text-sm"
                      @click="cancelEdit(index)">
               " {{ $t('cancel') }}"
              </button>
              <button type="button" 
                      class="vue-btn vue-btn-primary text-sm"
                      @click="saveSize(index)"
                      :disabled="saving">
                <i v-if="saving" class="fas fa-spinner fa-spin w-3 h-3"></i>
                <i v-else class="fas fa-save w-3 h-3"></i>
                {{ saving ? $t('saving') : $t('save') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Size Modal -->
    <div v-if="showAddSizeModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
         @click.self="closeAddSizeModal">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] flex flex-col">
        <div class="p-4 border-b flex-shrink-0" style="border-color: var(--gray-200);">
          <h3 class="vue-text-lg">{{ $t('add_new_size') }}</h3>
        </div>

        <div class="p-4 space-y-4 overflow-y-auto flex-1">
          <!-- Size Category Selection -->
          <div class="space-y-1">
            <label class="block vue-text-sm font-medium">
              <i class="fas fa-layer-group w-4 h-4 mr-2 text-blue-500"></i>
              {{ $t('size_category') }} <span class="text-red-500">*</span>
            </label>
            <div v-if="sizeCategoriesLoading" class="text-xs text-gray-500">{{ $t('loading') }}...</div>
            <div v-else-if="sizeCategoriesError" class="text-xs text-red-500">{{ sizeCategoriesError }}</div>
            <select v-model="newSize.category"
                    class="vue-form-control-enhanced-blue text-left"
                    style="text-align: left; direction: ltr;"
                    @change="newSize.name = ''; newSize.value = ''"
                    :disabled="sizeCategoriesLoading">
              <option value="">{{ $t('select_category') }}</option>
              <option v-for="category in sizeCategories"
                      :key="category.id || category.name"
                      :value="category.name">
                {{ isRTL ? (category.display_name_arabic || category.display_name || category.name) : (category.display_name || category.name) }}
              </option>
            </select>
          </div>

          <!-- Size Name Selection -->
          <div class="enhanced-form-field space-y-1">
            <label class="enhanced-form-label">
              <i class="fas fa-ruler"></i>
              {{ $t('size_name') }} <span class="text-red-500">*</span>
            </label>
            <select v-model="newSize.name"
                    class="vue-form-control-enhanced-blue text-left"
                    style="text-align: left; direction: ltr;"
                    :class="{ 'border-red-500': newSize.errors?.name }"
                    @change="handleSizeNameChange(newSize.name)"
                    required>
              <option value="">{{ $t('select_size') }}</option>
              <option v-for="option in sizeNameOptions"
                      :key="option.value"
                      :value="option.value">
                {{ option.label }}
              </option>
            </select>
            <div v-if="newSize.errors?.name" class="text-red-500 text-xs">{{ newSize.errors.name }}</div>
          </div>

          <!-- Size Value (Auto-filled) -->
          <div class="enhanced-form-field space-y-1">
            <label class="enhanced-form-label">
              <i class="fas fa-tag"></i>
              {{ $t('size_value') }}
            </label>
            <input type="text"
                   v-model="newSize.value"
                   class="vue-form-control-enhanced-blue bg-gray-50"
                   :placeholder="$t('select_size_first')"
                   readonly>
          </div>

          <!-- Stock and Price Section -->
          <div class="grid grid-cols-2 gap-3">
            <div class="enhanced-form-field space-y-1">
              <label class="enhanced-form-label gap-1 text-sm">
                <i class="fas fa-boxes"></i>
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
                <span class="allocated-stock">{{ newSize.stock || 0 }} {{ $t('allocated') }}</span>
              </div>
            </div>

            <div class="enhanced-form-field space-y-1">
              <label class="enhanced-form-label gap-1 text-sm">
                <i class="fas fa-dollar-sign"></i>
                {{ $t('price_adjustment') }}
              </label>
              <div class="enhanced-price-input">
                <div class="currency-prefix">{{$t('aed')}}</div>
                <input type="number"
                       v-model.number="newSize.price_adjustment"
                       step="0.01"
                       class="vue-form-control-enhanced-blue"
                       placeholder="0.00">
              </div>
              <p class="enhanced-help-text text-xs">
                <i class="fas fa-info-circle"></i>
                {{ $t('additional_cost_size_variant') }}
              </p>
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <input id="new-size-available"
                   v-model="newSize.is_available"
                   type="checkbox"
                   class="w-4 h-4 bg-gray-100 border-gray-300 rounded"
                   style="color: var(--primary-blue); --tw-ring-color: var(--primary-blue);">
            <label for="new-size-available" class="vue-text-sm">
              {{ $t('available_for_purchase') }}
            </label>
          </div>
        </div>

        <div class="p-4 border-t flex items-center justify-end gap-3 flex-shrink-0" style="border-color: var(--gray-200);">
          <button type="button"
                  class="vue-btn vue-btn-secondary"
                  @click="closeAddSizeModal">
            {{ $t('cancel') }}
          </button>
          <button type="button"
                  class="vue-btn vue-btn-primary bg-blue-500 text-white p-2 rounded-md"
                  @click="addSize"
                  :disabled="saving">
            <i v-if="saving" class="fas fa-spinner fa-spin w-4 h-4"></i>
            <i v-else class="fas fa-plus w-4 h-4"></i>
            {{ saving ? $t('adding_size') : $t('add_size') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'SizeManagement',
  props: {
    colorId: {
      type: [String, Number],
      required: true
    },
    productId: {
      type: [String, Number],
      required: true
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
    const sizeOptions = ref({})
    const loadingSizeOptions = ref(false)
    const sizeCategories = ref([])
    const sizeCategoriesLoading = ref(false)
    const sizeCategoriesError = ref(null)
    const errorMessage = ref(null)
    const colorInfo = ref(null)

    const newSize = reactive({
      name: '',
      value: '',
      category: '', // Filled from size categories once loaded
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

    // Methods
    const fetchSizeOptions = async () => {
      try {
        loadingSizeOptions.value = true
        sizeCategoriesLoading.value = true
        sizeCategoriesError.value = null

        const response = await axios.get('/api/size-categories', {
          params: { include_inactive: true }
        })

        if (!response.data.success) {
          sizeCategoriesError.value = response.data.message || 'Failed to load size categories'
          return
        }

        const raw = response.data.data ?? response.data.size_categories ?? []
        const normalized = Array.isArray(raw) ? raw.map(cat => ({
          ...cat,
          standardized_sizes: cat.standardized_sizes ?? cat.standardizedSizes ?? []
        })) : []

        sizeCategories.value = normalized

        const map = {}
        normalized.forEach(category => {
          const options = {}
          category.standardized_sizes.forEach(size => {
            options[size.name] = size.value || size.name
          })
          map[category.name] = options
        })
        sizeOptions.value = map

        // Default new size category
        if (!newSize.category && sizeCategories.value.length > 0) {
          newSize.category = sizeCategories.value[0].name
        }
      } catch (error) {
        console.error('Error loading size options:', error)
        sizeCategoriesError.value = 'Failed to load size categories'
      } finally {
        loadingSizeOptions.value = false
        sizeCategoriesLoading.value = false
      }
    }

    const fetchSizes = async () => {
      // Skip API calls during product creation mode
      if (isProductCreationMode.value) {
        console.log('📝 Skipping fetchSizes during product creation mode')
        loading.value = false
        return
      }

      try {
        loading.value = true
        errorMessage.value = null // Clear any previous error

        // Validate inputs before making API call
        if (!props.colorId || !props.productId) {
          throw new Error('Color ID and Product ID are required')
        }

        console.log('Fetching sizes for colorId:', props.colorId, 'productId:', props.productId)

        const response = await axios.post('/merchant/api/color-sizes/get-sizes-for-color', {
          color_id: parseInt(props.colorId),
          product_id: parseInt(props.productId),
          only_allocated: true // Only show sizes that have actual allocations for this color
        })

        if (response.data.success) {
          const newSizes = response.data.sizes.map(size => ({
            id: size.id,
            name: size.name,
            value: size.value,
            category: size.category || 'clothes', // Ensure category is present
            additional_info: size.additional_info,
            stock: size.allocated_stock || 0,
            price_adjustment: size.price_adjustment || 0,
            is_available: size.is_available !== false,
            display_order: size.display_order || 0,
            editing: false,
            errors: {},
            originalData: {
              id: size.id,
              name: size.name,
              value: size.value,
              category: size.category || 'clothes',
              additional_info: size.additional_info,
              stock: size.allocated_stock || 0,
              price_adjustment: size.price_adjustment || 0,
              is_available: size.is_available !== false,
              display_order: size.display_order || 0
            }
          }))

          console.log('📊 Regular fetchSizes - Sizes found:', newSizes.length)
          console.log('📋 Regular fetchSizes - Size details:', newSizes)

          sizes.value = newSizes
          emit('sizes-updated', sizes.value)

          // Update color info if available
          if (response.data.color) {
            colorInfo.value = response.data.color
            console.log('🎨 Regular fetchSizes - Color info updated:', response.data.color)
          }
        } else {
          console.error('API returned success=false:', response.data)
          throw new Error(response.data.message || 'API returned unsuccessful response')
        }
      } catch (error) {
        console.error('Error fetching sizes:', error)

        // Handle different types of errors
        let errorMsg = 'Failed to load sizes. Please try again.'

        if (error.response) {
          // Server responded with error status
          if (error.response.status === 422) {
            errorMsg = 'Validation error: ' + (error.response.data?.message || 'Invalid data provided')
          } else if (error.response.status === 404) {
            errorMsg = 'Color or product not found'
          } else if (error.response.status === 403) {
            errorMsg = 'Access denied to this resource'
          } else {
            errorMsg = error.response.data?.message || 'Server error occurred'
          }
        } else if (error.request) {
          // Network error
          errorMsg = 'Network error. Please check your connection and try again.'
        } else {
          // Other error
          errorMsg = error.message || errorMsg
        }

        // Set error message for display
        if (!error.isFromAddSize) {
          errorMessage.value = errorMsg
        }

        // Re-throw the error so calling code can handle it
        throw error
      } finally {
        loading.value = false
      }
    }

    const fetchSizesWithColorId = async (colorId) => {
      // Skip API calls during product creation mode
      if (isProductCreationMode.value) {
        console.log('📝 Skipping fetchSizesWithColorId during product creation mode')
        loading.value = false
        return
      }

      try {
        loading.value = true
        errorMessage.value = null // Clear any previous error

        // Validate inputs before making API call
        if (!colorId || !props.productId) {
          throw new Error('Color ID and Product ID are required')
        }

        console.log('Fetching sizes for colorId:', colorId, 'productId:', props.productId)

        const response = await axios.post('/merchant/api/color-sizes/get-sizes-for-color', {
          color_id: parseInt(colorId),
          product_id: parseInt(props.productId),
          only_allocated: true // Only show sizes that have actual allocations for this color
        })

        if (response.data.success) {
          const newSizes = response.data.sizes.map(size => ({
            id: size.id,
            name: size.name,
            value: size.value,
            category: size.category || 'clothes', // Ensure category is present
            additional_info: size.additional_info,
            stock: size.allocated_stock || 0,
            price_adjustment: size.price_adjustment || 0,
            is_available: size.is_available !== false,
            display_order: size.display_order || 0,
            editing: false,
            errors: {},
            originalData: {
              id: size.id,
              name: size.name,
              value: size.value,
              category: size.category || 'clothes',
              additional_info: size.additional_info,
              stock: size.allocated_stock || 0,
              price_adjustment: size.price_adjustment || 0,
              is_available: size.is_available !== false,
              display_order: size.display_order || 0
            }
          }))

          console.log('📊 Sizes fetched successfully:', newSizes.length, 'sizes found')
          console.log('📋 Size details:', newSizes)

          sizes.value = newSizes
          emit('sizes-updated', sizes.value)

          // Update color info if available
          if (response.data.color) {
            colorInfo.value = response.data.color
            console.log('🎨 Color info updated:', response.data.color)
          }
        } else {
          console.error('API returned success=false:', response.data)
          throw new Error(response.data.message || 'API returned unsuccessful response')
        }
      } catch (error) {
        console.error('Error fetching sizes with colorId:', error)

        // Handle different types of errors
        let errorMsg = 'Failed to load sizes. Please try again.'

        if (error.response) {
          // Server responded with error status
          if (error.response.status === 422) {
            errorMsg = 'Validation error: ' + (error.response.data?.message || 'Invalid data provided')
          } else if (error.response.status === 404) {
            errorMsg = 'Color or product not found'
          } else if (error.response.status === 403) {
            errorMsg = 'Access denied to this resource'
          } else {
            errorMsg = error.response.data?.message || 'Server error occurred'
          }
        } else if (error.request) {
          // Network error
          errorMsg = 'Network error. Please check your connection and try again.'
        } else {
          // Other error
          errorMsg = error.message || errorMsg
        }

        // Set error message for display
        if (!error.isFromAddSize) {
          errorMessage.value = errorMsg
        }

        // Re-throw the error so calling code can handle it
        throw error
      } finally {
        loading.value = false
      }
    }

    const refreshSizes = async (forceColorId = null) => {
      // Skip refresh during product creation mode
      if (isProductCreationMode.value) {
        console.log('📝 Skipping refreshSizes during product creation mode')
        return
      }

      // Check if we have a valid colorId (either from props, parameter, or recently updated)
      const colorIdToUse = forceColorId || props.colorId

      if (colorIdToUse) {
        try {
          // Clear any previous error messages before refreshing
          errorMessage.value = null

          console.log('Refreshing sizes with colorId:', colorIdToUse)

          // For forced colorId (newly created colors), we need to call fetchSizes with the specific colorId
          if (forceColorId) {
            await fetchSizesWithColorId(forceColorId)
          } else {
            await fetchSizes()
          }
        } catch (error) {
          // Error is already handled in fetchSizes methods
          console.error('Failed to refresh sizes:', error)

          // Don't re-throw the error for refresh operations to prevent cascading failures
          // The error message is already set by the fetch methods
          if (!errorMessage.value) {
            errorMessage.value = 'Failed to refresh sizes. Please try again manually.'
          }
        }
      } else {
        console.warn('Cannot refresh sizes: colorId is not available')
        errorMessage.value = 'Color ID is required to refresh sizes'
      }
    }

    // Computed properties for dropdown options
    const sizeNameOptions = computed(() => {
      const category = newSize.category
      if (!category || !sizeOptions.value[category]) return []
      return Object.keys(sizeOptions.value[category] || {}).map(key => ({
        value: key,
        label: `${key} (${sizeOptions.value[category][key]})`
      }))
    })

    const getSizeNameOptionsForEdit = (category) => {
      if (!category || !sizeOptions.value[category]) return []
      return Object.keys(sizeOptions.value[category] || {}).map(key => ({
        value: key,
        label: `${key} (${sizeOptions.value[category][key]})`
      }))
    }

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

    const handleSizeNameChange = (selectedName, isEdit = false, editIndex = null) => {
      const category = isEdit ? sizes.value[editIndex].category : newSize.category
      const sizeData = sizeOptions.value[category]

      if (sizeData && sizeData[selectedName]) {
        if (isEdit) {
          sizes.value[editIndex].name = selectedName
          sizes.value[editIndex].value = sizeData[selectedName] || selectedName
        } else {
          newSize.name = selectedName
          newSize.value = sizeData[selectedName] || selectedName
        }
      }
    }

    const editSize = (index) => {
      // Store original data for cancel functionality
      sizes.value[index].originalData = { ...sizes.value[index] }
      sizes.value[index].editing = true
      sizes.value[index].errors = {}
    }

    const cancelEdit = (index) => {
      // Restore original data
      Object.assign(sizes.value[index], sizes.value[index].originalData)
      sizes.value[index].editing = false
      sizes.value[index].errors = {}
    }

    const validateSize = (size) => {
      const errors = {}

      // Name validation
      if (!size.name?.trim()) {
        errors.name = 'Size name is required'
      } else if (size.name.trim().length > 255) {
        errors.name = 'Size name cannot exceed 255 characters'
      } else {
        // Check for duplicate names (excluding current size if editing)
        const duplicateSize = sizes.value.find(s =>
          s.name.toLowerCase() === size.name.toLowerCase() &&
          s.id !== size.id
        )
        if (duplicateSize) {
          errors.name = 'A size with this name already exists'
        }
      }

      // Value validation
      if (size.value && size.value.length > 255) {
        errors.value = 'Size value cannot exceed 255 characters'
      }

      // Additional info validation
      if (size.additional_info && size.additional_info.length > 255) {
        errors.additional_info = 'Additional info cannot exceed 255 characters'
      }

      // Stock validation
      if (size.stock !== undefined && size.stock !== null) {
        if (size.stock < 0) {
          errors.stock = 'Stock cannot be negative'
        } else if (!Number.isInteger(Number(size.stock))) {
          errors.stock = 'Stock must be a whole number'
        }
      }

      // Price adjustment validation
      if (size.price_adjustment !== undefined && size.price_adjustment !== null) {
        if (isNaN(Number(size.price_adjustment))) {
          errors.price_adjustment = 'Price adjustment must be a valid number'
        }
      }

      return errors
    }

    const validateSizeField = (index, field) => {
      const size = sizes.value[index]
      if (!size.errors) size.errors = {}

      // Clear the specific field error
      delete size.errors[field]

      // Validate the specific field
      const fieldErrors = validateSize(size)
      if (fieldErrors[field]) {
        size.errors[field] = fieldErrors[field]
      }
    }

    const validateNewSizeField = (field) => {
      // Clear the specific field error
      delete newSize.errors[field]

      // Validate the specific field
      const fieldErrors = validateSize(newSize)
      if (fieldErrors[field]) {
        newSize.errors[field] = fieldErrors[field]
      }
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

      // Handle existing product mode (API-based management)
      try {
        saving.value = true

        // Call the API to update the size
        // Only send fields that have actually changed or are required
        const updateData = {
          size_id: size.id,
          color_id: props.colorId,
          name: size.name,
          value: size.value,
          additional_info: size.additional_info,
          is_available: size.is_available !== false
        }

        // Only include stock if it's a valid number (including 0)
        if (typeof size.stock === 'number' && !isNaN(size.stock)) {
          updateData.stock = size.stock
        }

        // Only include price_adjustment if it's a valid number
        if (typeof size.price_adjustment === 'number' && !isNaN(size.price_adjustment)) {
          updateData.price_adjustment = size.price_adjustment
        }

        const response = await axios.post('/merchant/api/sizes/update', updateData)

        if (response.data.success) {
          size.editing = false
          size.errors = {}

          // Update the size with the response data, mapping allocated_stock to stock
          const responseSize = response.data.size
          Object.assign(size, {
            ...responseSize,
            stock: responseSize.allocated_stock || responseSize.stock || 0
          })

          emit('sizes-updated', sizes.value)
        } else {
          alert(response.data.message || 'Failed to save size.')
        }

      } catch (error) {
        console.error('Error saving size:', error)
        if (error.response?.data?.message) {
          alert(error.response.data.message)
        } else {
          alert('Failed to save size. Please try again.')
        }
      } finally {
        saving.value = false
      }
    }

    const removeSize = async (index) => {
      if (!confirm('Are you sure you want to remove this size?')) {
        return
      }

      const size = sizes.value[index]

      // Handle product creation mode (local size management)
      if (isProductCreationMode.value) {
        console.log('🗑️ Removing size locally during product creation')
        sizes.value.splice(index, 1)
        emit('sizes-updated', sizes.value)
        return
      }

      // Handle existing product mode (API-based management)
      try {
        // Call the API to delete the size
        const response = await axios.post('/merchant/api/sizes/delete', {
          size_id: size.id,
          color_id: props.colorId
        })

        if (response.data.success) {
          sizes.value.splice(index, 1)
          emit('sizes-updated', sizes.value)
        } else {
          alert(response.data.message || 'Failed to remove size.')
        }

      } catch (error) {
        console.error('Error removing size:', error)
        if (error.response?.data?.message) {
          alert(error.response.data.message)
        } else {
          alert('Failed to remove size. Please try again.')
        }
      }
    }

    const resetNewSize = () => {
      newSize.name = ''
      newSize.value = ''
      newSize.category = sizeCategories.value.length ? sizeCategories.value[0].name : ''
      newSize.stock = 0
      newSize.price_adjustment = 0
      newSize.is_available = true
      newSize.errors = {}
    }

    const closeAddSizeModal = () => {
      showAddSizeModal.value = false
      resetNewSize()
    }

    const saveColorFirst = async () => {
      try {
        // Emit event to parent to save the color and wait for response
        return new Promise((resolve, reject) => {
          emit('save-color-first', {
            resolve,
            reject
          })
        })
      } catch (error) {
        console.error('Error saving color first:', error)
        throw error
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
          category: newSize.category || null,
            additional_info: newSize.additional_info,
            stock: newSize.stock || 0,
            price_adjustment: newSize.price_adjustment || 0,
            is_available: newSize.is_available !== false,
            display_order: sizes.value.length,
            editing: false,
            errors: {},
            originalData: {
              name: newSize.name,
              value: newSize.value,
              category: newSize.category || null,
              additional_info: newSize.additional_info,
              stock: newSize.stock || 0,
              price_adjustment: newSize.price_adjustment || 0,
              is_available: newSize.is_available !== false,
              display_order: sizes.value.length
            }
          }

          // Add the new size to the local array
          sizes.value.push(newSizeData)

          // Close the modal
          closeAddSizeModal()

          // Emit the updated sizes to parent component
          emit('sizes-updated', sizes.value)

          console.log('✅ Size added locally for product creation')
          return
        }

        // Handle existing product mode (API-based management)
        let colorId = props.colorId
        let wasNewColor = false

        // Check if we need to save the color first
        if (!colorId || colorId === null || colorId === undefined) {
          // Emit event to parent to save the color first
          const savedColor = await saveColorFirst()
          if (!savedColor) {
            throw new Error('Failed to save color before adding size')
          }
          colorId = savedColor.id
          wasNewColor = true
          console.log('🆕 Color was newly created with ID:', colorId)
        }

        // Call the API to create the size
        const response = await axios.post('/merchant/api/sizes/create', {
          product_id: props.productId,
          color_id: colorId,
          name: newSize.name,
          value: newSize.value,
          category: newSize.category || 'clothes', // Include the category
          additional_info: newSize.additional_info,
          price_adjustment: newSize.price_adjustment || 0,
          stock: newSize.stock || 0,
          is_available: newSize.is_available
        })

        if (response.data.success) {
          console.log('✅ Size created successfully:', response.data.size)

          // Close the modal first
          closeAddSizeModal()

          // Immediately add the new size to the local array for instant UI feedback
          const newSizeData = {
            id: response.data.size.id,
            name: response.data.size.name,
            value: response.data.size.value,
            category: response.data.size.category || 'clothes',
            additional_info: response.data.size.additional_info,
            stock: response.data.size.allocated_stock || response.data.size.stock || 0,
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

          // Add the new size to the sizes array for immediate UI update
          sizes.value.push(newSizeData)

          // Emit the updated sizes immediately
          emit('sizes-updated', sizes.value)

          console.log('✅ Size added to UI immediately with stock:', newSizeData.stock)

          // CRITICAL FIX: Remove automatic refresh after size creation to prevent stock value override
          // The size was already added to the local array with correct data from the API response
          // Automatic refresh can cause timing issues and override the correct stock values
          console.log('ℹ️ Skipping automatic refresh to preserve stock values')
          
        } else {
          alert(response.data.message || 'Failed to add size.')
        }

      } catch (error) {
        console.error('Error adding size:', error)
        if (error.response?.data?.message) {
          alert(error.response.data.message)
        } else {
          alert('Failed to add size. Please try again.')
        }
      } finally {
        saving.value = false
      }
    }

    // Lifecycle
    onMounted(() => {
      fetchSizeOptions()
      // Only fetch sizes if we're not in product creation mode
      if (props.colorId && !isProductCreationMode.value) {
        fetchSizes()
      }
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
      if (open && sizeCategories.value.length === 0 && !sizeCategoriesLoading.value) {
        fetchSizeOptions()
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

    return {
      loading,
      saving,
      showAddSizeModal,
      sizes,
      newSize,
      sizeOptions,
      loadingSizeOptions,
      sizeCategories,
      sizeCategoriesLoading,
      sizeCategoriesError,
      errorMessage,
      sizeNameOptions,
      totalSizeStock,
      availableSizeStock,
      isProductCreationMode,
      isRTL,
      fetchSizeOptions,
      fetchSizes,
      fetchSizesWithColorId,
      refreshSizes,
      getSizeNameOptionsForEdit,
      handleSizeNameChange,
      validateSizeStock,
      editSize,
      cancelEdit,
      validateSizeField,
      validateNewSizeField,
      saveSize,
      removeSize,
      closeAddSizeModal,
      addSize
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

/* Enhanced Form Controls for Size Management */
.vue-form-control-enhanced-blue {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.75rem;
  background-color: white;
  color: #1f2937;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  line-height: 1.5;
}

.vue-form-control-enhanced-blue:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12), 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  background-color: #fafbff;
  transform: translateY(-1px);
}

.vue-form-control-enhanced-blue:hover:not(:focus) {
  border-color: #9ca3af;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.vue-form-control-enhanced-blue::placeholder {
  color: #9ca3af;
  font-weight: 400;
}

.vue-form-control-enhanced-blue:disabled,
.vue-form-control-enhanced-blue[readonly] {
  background-color: #f9fafb;
  border-color: #e5e7eb;
  color: #6b7280;
  cursor: not-allowed;
}

/* Enhanced form field containers */
.enhanced-form-field {
  position: relative;
}

.enhanced-form-field .space-y-2 {
  gap: 0.625rem;
}

/* Enhanced labels */
.enhanced-form-label {
  display: flex;
  align-items: center;
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  letter-spacing: 0.025em;
}

.enhanced-form-label i {
  margin-right: 0.5rem;
  color: #3b82f6;
  font-size: 1rem;
}

/* Enhanced input groups with better spacing */
.enhanced-input-group {
  position: relative;
  display: flex;
  align-items: center;
}

.enhanced-input-group .vue-form-control-enhanced-blue {
  padding-right: 4rem;
}

.enhanced-input-group .input-suffix {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background-color: #f3f4f6;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 500;
  color: #6b7280;
  border: 1px solid #e5e7eb;
}

/* Enhanced price input with currency prefix */
.enhanced-price-input {
  position: relative;
}

.enhanced-price-input .currency-prefix {
  position: absolute;
  left: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background-color: #f3f4f6;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #374151;
  border: 1px solid #e5e7eb;
  z-index: 1;
}

.enhanced-price-input .vue-form-control-enhanced-blue {
  padding-left: 4.5rem;
  text-align: right;
  font-weight: 600;
}

/* Enhanced stock allocation display */
.stock-allocation-info {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 0.5rem;
  padding: 0.5rem 0.75rem;
  background-color: #f8fafc;
  border-radius: 0.5rem;
  border: 1px solid #e2e8f0;
}

.stock-allocation-info .available-stock {
  color: #64748b;
  font-size: 0.75rem;
  font-weight: 500;
}

.stock-allocation-info .allocated-stock {
  color: #3b82f6;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Enhanced help text */
.enhanced-help-text {
  display: flex;
  align-items: center;
  margin-top: 0.5rem;
  font-size: 0.75rem;
  color: #64748b;
  line-height: 1.4;
}

.enhanced-help-text i {
  margin-right: 0.375rem;
  color: #94a3b8;
  font-size: 0.75rem;
}

/* Modal styles */
.fixed {
  position: fixed;
}

.inset-0 {
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

.z-50 {
  z-index: 50;
}

/* Utility classes */
.space-y-3 > * + * { margin-top: 0.75rem; }
.space-y-4 > * + * { margin-top: 1rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.gap-4 { gap: 1rem; }
.grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.text-xs { font-size: 0.75rem; }
.font-semibold { font-weight: 600; }
.font-medium { font-weight: 500; }
</style>
