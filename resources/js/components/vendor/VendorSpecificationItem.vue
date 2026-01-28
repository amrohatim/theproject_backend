<template>
  <div
    class="specification-item grid grid-cols-12 gap-4 items-center p-4 rounded-lg border border-gray-200 bg-white"
    :class="{ 'rtl': isRTL }"
    :style="{
      '--spec-focus-border': isProductsManagerContext ? '#f59e0b' : '#3b82f6',
      '--spec-focus-ring': isProductsManagerContext ? 'rgba(245, 158, 11, 0.1)' : 'rgba(59, 130, 246, 0.1)'
    }"
  >
    <div class="col-span-4">
      <label class="block vue-text-sm mb-2">{{ $t('vendor.specification_key') }}</label>
      <input type="text"
             :value="specification.key"
             @input="updateSpecification('key', $event.target.value)"
             :placeholder="$t('vendor.specification_key_placeholder')"
             class="vue-form-control">
    </div>
    <div class="col-span-6">
      <label class="block vue-text-sm mb-2">{{ $t('vendor.specification_value') }}</label>
      <input type="text"
             :value="specification.value"
             @input="updateSpecification('value', $event.target.value)"
             :placeholder="$t('vendor.specification_value_placeholder')"
             class="vue-form-control">
    </div>
    <div class="col-span-1">
      <label class="block vue-text-sm mb-2">{{ $t('vendor.order') }}</label>
      <input type="number"
             :value="specification.display_order"
             @input="updateSpecification('display_order', parseInt($event.target.value) || 0)"
             class="vue-form-control">
    </div>
    <div class="col-span-1 flex justify-center">
      <button type="button"
              class="remove-item p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
              @click="$emit('remove', index)">
        <i class="fas fa-trash w-4 h-4"></i>
      </button>
    </div>
  </div>
</template>

<script>
import { computed, getCurrentInstance } from 'vue'

export default {
  name: 'VendorSpecificationItem',
  props: {
    specification: {
      type: Object,
      required: true
    },
    index: {
      type: Number,
      required: true
    },
    userRole: {
      type: String,
      default: 'vendor'
    }
  },
  emits: ['update', 'remove'],
  setup(props, { emit }) {
    const instance = getCurrentInstance()
    
    // RTL support
    const isRTL = computed(() => {
      return ['ar', 'he', 'fa'].includes(window.Laravel?.locale || 'en')
    })

    const isProductsManagerContext = computed(() => {
      return props.userRole === 'products_manager' || window.location.pathname.includes('/products-manager/')
    })

    const updateSpecification = (field, value) => {
      emit('update', props.index, field, value)
    }

    return {
      isRTL,
      isProductsManagerContext,
      updateSpecification
    }
  }
}
</script>

<style scoped>
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
  border-color: var(--spec-focus-border, #3b82f6);
  box-shadow: 0 0 0 3px var(--spec-focus-ring, rgba(59, 130, 246, 0.1));
}

.specification-item {
  transition: all 0.2s ease;
}

.specification-item:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.remove-item {
  transition: all 0.15s ease;
}

.remove-item:hover {
  transform: scale(1.1);
}

/* RTL Support */
.rtl {
  direction: rtl;
}

.rtl .text-left {
  text-align: right;
}

.rtl .text-right {
  text-align: left;
}

.rtl input[type="text"],
.rtl input[type="number"],
.rtl textarea {
  text-align: right;
}

.rtl .grid {
  direction: rtl;
}

.rtl .flex {
  flex-direction: row-reverse;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .vue-text-sm {
    color: #d1d5db;
  }

  .vue-form-control {
    background-color: #374151;
    border-color: #4b5563;
    color: #f9fafb;
  }

  .vue-form-control:focus {
    border-color: var(--spec-focus-border, #3b82f6);
    box-shadow: 0 0 0 3px var(--spec-focus-ring, rgba(59, 130, 246, 0.1));
  }

  .specification-item {
    background-color: #1f2937;
    border-color: #374151;
  }
}
</style>
