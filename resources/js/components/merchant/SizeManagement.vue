<template>
  <div class="size-management-container">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div>
        <h5 class="vue-text-sm font-semibold">Size Management</h5>
        <p class="text-xs" style="color: var(--gray-500);">
          Manage sizes and stock allocation for this color variant
        </p>
      </div>
      <button type="button" 
              class="vue-btn vue-btn-primary text-sm"
              @click="showAddSizeModal = true">
        <i class="fas fa-plus w-3 h-3"></i>
        Add Size
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="flex items-center gap-2">
        <i class="fas fa-spinner fa-spin w-4 h-4" style="color: var(--primary-blue);"></i>
        <span class="text-sm" style="color: var(--gray-600);">Loading sizes...</span>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="sizes.length === 0" class="text-center py-8">
      <i class="fas fa-ruler w-8 h-8 mb-3" style="color: var(--gray-400);"></i>
      <h6 class="vue-text-sm mb-2">No sizes added</h6>
      <p class="text-xs mb-4" style="color: var(--gray-500);">
        Add sizes to this color variant to manage stock allocation
      </p>
      <button type="button" 
              class="vue-btn vue-btn-primary text-sm"
              @click="showAddSizeModal = true">
        <i class="fas fa-plus w-3 h-3"></i>
        Add First Size
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
                    Value: {{ size.value }}
                  </span>
                  <span class="text-xs" style="color: var(--gray-600);">
                    Stock: {{ size.stock || 0 }}
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
                    title="Edit size">
              <i class="fas fa-edit w-4 h-4"></i>
            </button>
            <button type="button" 
                    class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                    @click="removeSize(index)"
                    title="Remove size">
              <i class="fas fa-trash w-4 h-4"></i>
            </button>
          </div>
        </div>

        <!-- Size Edit Mode -->
        <div v-else class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block vue-text-sm">
                Size Name <span class="text-red-500">*</span>
              </label>
              <input type="text"
                     v-model="size.name"
                     class="vue-form-control"
                     :class="{ 'border-red-500': size.errors?.name }"
                     placeholder="e.g., S, M, L, XL"
                     @input="validateSizeField(index, 'name')"
                     required>
              <div v-if="size.errors?.name" class="text-red-500 text-xs">{{ size.errors.name }}</div>
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm">Size Value</label>
              <input type="text" 
                     v-model="size.value"
                     class="vue-form-control"
                     placeholder="e.g., 32, Medium, etc.">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block vue-text-sm">Stock Quantity</label>
              <input type="number"
                     v-model.number="size.stock"
                     class="vue-form-control"
                     :class="{ 'border-red-500': size.errors?.stock }"
                     min="0"
                     @input="validateSizeField(index, 'stock')"
                     placeholder="0">
              <div v-if="size.errors?.stock" class="text-red-500 text-xs">{{ size.errors.stock }}</div>
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm">Price Adjustment</label>
              <div class="relative">
                <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                <input type="number" 
                       v-model.number="size.price_adjustment"
                       step="0.01"
                       class="vue-form-control pl-10"
                       placeholder="0.00">
              </div>
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
                Available for purchase
              </label>
            </div>

            <div class="flex items-center gap-2">
              <button type="button" 
                      class="vue-btn vue-btn-secondary text-sm"
                      @click="cancelEdit(index)">
                Cancel
              </button>
              <button type="button" 
                      class="vue-btn vue-btn-primary text-sm"
                      @click="saveSize(index)"
                      :disabled="saving">
                <i v-if="saving" class="fas fa-spinner fa-spin w-3 h-3"></i>
                <i v-else class="fas fa-save w-3 h-3"></i>
                {{ saving ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Size Modal -->
    <div v-if="showAddSizeModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeAddSizeModal">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b" style="border-color: var(--gray-200);">
          <h3 class="vue-text-lg">Add New Size</h3>
        </div>

        <div class="p-6 space-y-4">
          <div class="space-y-2">
            <label class="block vue-text-sm">
              Size Name <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   v-model="newSize.name"
                   class="vue-form-control"
                   :class="{ 'border-red-500': newSize.errors?.name }"
                   placeholder="e.g., S, M, L, XL"
                   @input="validateNewSizeField('name')"
                   required>
            <div v-if="newSize.errors?.name" class="text-red-500 text-xs">{{ newSize.errors.name }}</div>
          </div>

          <div class="space-y-2">
            <label class="block vue-text-sm">Size Value</label>
            <input type="text"
                   v-model="newSize.value"
                   class="vue-form-control"
                   placeholder="e.g., 32, Medium, etc.">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block vue-text-sm">Stock Quantity</label>
              <input type="number"
                     v-model.number="newSize.stock"
                     class="vue-form-control"
                     min="0"
                     @input="validateNewSizeField('stock')"
                     placeholder="0">
            </div>

            <div class="space-y-2">
              <label class="block vue-text-sm">Price Adjustment</label>
              <div class="relative">
                <i class="fas fa-dollar-sign absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4" style="color: var(--gray-400);"></i>
                <input type="number"
                       v-model.number="newSize.price_adjustment"
                       step="0.01"
                       class="vue-form-control pl-10"
                       placeholder="0.00">
              </div>
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <input id="new-size-available"
                   v-model="newSize.is_available"
                   type="checkbox"
                   class="w-4 h-4 bg-gray-100 border-gray-300 rounded"
                   style="color: var(--primary-blue); --tw-ring-color: var(--primary-blue);">
            <label for="new-size-available" class="vue-text-sm">
              Available for purchase
            </label>
          </div>
        </div>

        <div class="p-6 border-t flex items-center justify-end gap-3" style="border-color: var(--gray-200);">
          <button type="button"
                  class="vue-btn vue-btn-secondary"
                  @click="closeAddSizeModal">
            Cancel
          </button>
          <button type="button"
                  class="vue-btn vue-btn-primary"
                  @click="addSize"
                  :disabled="saving">
            <i v-if="saving" class="fas fa-spinner fa-spin w-4 h-4"></i>
            <i v-else class="fas fa-plus w-4 h-4"></i>
            {{ saving ? 'Adding...' : 'Add Size' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue'

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
    }
  },
  emits: ['sizes-updated'],
  setup(props, { emit }) {
    // Reactive data
    const loading = ref(false)
    const saving = ref(false)
    const showAddSizeModal = ref(false)
    const sizes = ref([])
    
    const newSize = reactive({
      name: '',
      value: '',
      stock: 0,
      price_adjustment: 0,
      is_available: true,
      errors: {}
    })

    // Methods
    const fetchSizes = async () => {
      try {
        loading.value = true
        const response = await window.axios.post('/merchant/api/color-sizes/get-sizes-for-color', {
          color_id: props.colorId,
          product_id: props.productId
        })

        if (response.data.success) {
          sizes.value = response.data.sizes.map(size => ({
            id: size.id,
            name: size.name,
            value: size.value,
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
              additional_info: size.additional_info,
              stock: size.allocated_stock || 0,
              price_adjustment: size.price_adjustment || 0,
              is_available: size.is_available !== false,
              display_order: size.display_order || 0
            }
          }))
        }
      } catch (error) {
        console.error('Error fetching sizes:', error)
        alert('Failed to load sizes. Please try again.')
      } finally {
        loading.value = false
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

      try {
        saving.value = true

        // Call the API to update the size
        const response = await window.axios.post('/merchant/api/sizes/update', {
          size_id: size.id,
          color_id: props.colorId,
          name: size.name,
          value: size.value,
          additional_info: size.additional_info,
          price_adjustment: size.price_adjustment || 0,
          stock: size.stock || 0,
          is_available: size.is_available !== false
        })

        if (response.data.success) {
          size.editing = false
          size.errors = {}

          // Update the size with the response data
          Object.assign(size, response.data.size)

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

      try {
        // Call the API to delete the size
        const response = await window.axios.post('/merchant/api/sizes/delete', {
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
      newSize.stock = 0
      newSize.price_adjustment = 0
      newSize.is_available = true
      newSize.errors = {}
    }

    const closeAddSizeModal = () => {
      showAddSizeModal.value = false
      resetNewSize()
    }

    const addSize = async () => {
      const errors = validateSize(newSize)

      if (Object.keys(errors).length > 0) {
        newSize.errors = errors
        return
      }

      try {
        saving.value = true

        // Call the API to create the size
        const response = await window.axios.post('/merchant/api/sizes/create', {
          product_id: props.productId,
          color_id: props.colorId,
          name: newSize.name,
          value: newSize.value,
          additional_info: newSize.additional_info,
          price_adjustment: newSize.price_adjustment || 0,
          stock: newSize.stock || 0,
          is_available: newSize.is_available
        })

        if (response.data.success) {
          const newSizeData = {
            ...response.data.size,
            editing: false,
            errors: {},
            originalData: { ...response.data.size }
          }

          sizes.value.push(newSizeData)
          emit('sizes-updated', sizes.value)
          closeAddSizeModal()
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
      if (props.colorId) {
        fetchSizes()
      }
    })

    // Watch for color changes
    watch(() => props.colorId, (newColorId) => {
      if (newColorId) {
        fetchSizes()
      }
    })

    return {
      loading,
      saving,
      showAddSizeModal,
      sizes,
      newSize,
      fetchSizes,
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
