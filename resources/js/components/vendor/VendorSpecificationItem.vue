<template>
  <div class="specification-item grid grid-cols-12 gap-4 items-center p-4 rounded-lg border border-gray-200 bg-white">
    <div class="col-span-4">
      <label class="block vue-text-sm mb-2">Key</label>
      <input type="text"
             :value="specification.key"
             @input="updateSpecification('key', $event.target.value)"
             placeholder="e.g., Material"
             class="vue-form-control">
    </div>
    <div class="col-span-6">
      <label class="block vue-text-sm mb-2">Value</label>
      <input type="text"
             :value="specification.value"
             @input="updateSpecification('value', $event.target.value)"
             placeholder="e.g., 100% Cotton"
             class="vue-form-control">
    </div>
    <div class="col-span-1">
      <label class="block vue-text-sm mb-2">Order</label>
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
    }
  },
  emits: ['update', 'remove'],
  setup(props, { emit }) {
    const updateSpecification = (field, value) => {
      emit('update', props.index, field, value)
    }

    return {
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
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  }

  .specification-item {
    background-color: #1f2937;
    border-color: #374151;
  }
}
</style>
