<template>
  <div class="specification-item grid grid-cols-12 gap-4 items-center p-4 rounded-lg">
    <div class="col-span-4">
      <label class="block vue-text-sm mb-2">{{ $t('key') }}</label>
      <input type="text"
             :value="specification.key"
             @input="updateSpecification('key', $event.target.value)"
             :placeholder="$t('eg_material')"
             class="vue-form-control  bg-white border p-2 border-amber-50 rounded-md">
    </div>
    <div class="col-span-6">
      <label class="block vue-text-sm mb-2">{{ $t('value') }}</label>
      <input type="text"
             :value="specification.value"
             @input="updateSpecification('value', $event.target.value)"
             :placeholder="$t('eg_cotton')"
             class="vue-form-control  bg-white border p-2 border-amber-50 rounded-md" >
    </div>
    <div class="col-span-1">
      <label class="block vue-text-sm mb-2">{{ $t('order') }}</label>
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
  name: 'SpecificationItem',
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
      const updatedSpec = { ...props.specification, [field]: value }
      emit('update', props.index, updatedSpec)
    }

    return {
      updateSpecification
    }
  }
}
</script>

<style scoped>
.specification-item {
  background-color: var(--gray-50, #f9fafb);
  border: 1px solid var(--gray-200, #e5e7eb);
  transition: all 0.3s ease;
}

.specification-item:hover {
  background-color: var(--gray-100, #f3f4f6);
}

.col-span-1 { grid-column: span 1 / span 1; }
.col-span-4 { grid-column: span 4 / span 4; }
.col-span-6 { grid-column: span 6 / span 6; }
.justify-center { justify-content: center; }
</style>
