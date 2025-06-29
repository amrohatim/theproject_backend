<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">Company Information</h2>
      <p class="text-gray-600">Please provide your company details for vendor registration.</p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Company Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
          Company Name <span class="text-red-500">*</span>
        </label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter your company name"
          :disabled="loading"
        />
        <div v-if="errors.name" class="mt-1 text-sm text-red-600">
          {{ errors.name[0] }}
        </div>
      </div>

      <!-- Company Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
          Company Email <span class="text-red-500">*</span>
        </label>
        <input
          id="email"
          v-model="formData.email"
          type="email"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter company email address"
          :disabled="loading"
        />
        <div v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email[0] }}
        </div>
      </div>

      <!-- Primary Contact Number -->
      <div>
        <label for="contact_number_1" class="block text-sm font-medium text-gray-700 mb-2">
          Primary Contact Number <span class="text-red-500">*</span>
        </label>
        <input
          id="contact_number_1"
          v-model="formData.contact_number_1"
          type="tel"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter primary contact number"
          :disabled="loading"
        />
        <div v-if="errors.contact_number_1" class="mt-1 text-sm text-red-600">
          {{ errors.contact_number_1[0] }}
        </div>
      </div>

      <!-- Secondary Contact Number -->
      <div>
        <label for="contact_number_2" class="block text-sm font-medium text-gray-700 mb-2">
          Secondary Contact Number
        </label>
        <input
          id="contact_number_2"
          v-model="formData.contact_number_2"
          type="tel"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter secondary contact number (optional)"
          :disabled="loading"
        />
        <div v-if="errors.contact_number_2" class="mt-1 text-sm text-red-600">
          {{ errors.contact_number_2[0] }}
        </div>
      </div>

      <!-- Address -->
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
          Company Address <span class="text-red-500">*</span>
        </label>
        <textarea
          id="address"
          v-model="formData.address"
          required
          rows="3"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter your complete company address"
          :disabled="loading"
        ></textarea>
        <div v-if="errors.address" class="mt-1 text-sm text-red-600">
          {{ errors.address[0] }}
        </div>
      </div>

      <!-- Emirate -->
      <div>
        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">
          Emirate <span class="text-red-500">*</span>
        </label>
        <select
          id="emirate"
          v-model="formData.emirate"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :disabled="loading"
        >
          <option value="">Select emirate</option>
          <option value="Abu Dhabi">Abu Dhabi</option>
          <option value="Dubai">Dubai</option>
          <option value="Sharjah">Sharjah</option>
          <option value="Ajman">Ajman</option>
          <option value="Umm Al Quwain">Umm Al Quwain</option>
          <option value="Ras Al Khaimah">Ras Al Khaimah</option>
          <option value="Fujairah">Fujairah</option>
        </select>
        <div v-if="errors.emirate" class="mt-1 text-sm text-red-600">
          {{ errors.emirate[0] }}
        </div>
      </div>

      <!-- City -->
      <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
          City <span class="text-red-500">*</span>
        </label>
        <input
          id="city"
          v-model="formData.city"
          type="text"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter city name"
          :disabled="loading"
        />
        <div v-if="errors.city" class="mt-1 text-sm text-red-600">
          {{ errors.city[0] }}
        </div>
      </div>

      <!-- Street -->
      <div>
        <label for="street" class="block text-sm font-medium text-gray-700 mb-2">
          Street
        </label>
        <input
          id="street"
          v-model="formData.street"
          type="text"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter street name (optional)"
          :disabled="loading"
        />
        <div v-if="errors.street" class="mt-1 text-sm text-red-600">
          {{ errors.street[0] }}
        </div>
      </div>

      <!-- Delivery Capability -->
      <div>
        <div class="flex items-center">
          <input
            id="delivery_capability"
            v-model="formData.delivery_capability"
            type="checkbox"
            class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            :disabled="loading"
          />
          <label for="delivery_capability" class="ml-3 text-sm font-medium text-gray-700">
            We offer delivery services
          </label>
        </div>
        <div v-if="errors.delivery_capability" class="mt-1 text-sm text-red-600">
          {{ errors.delivery_capability[0] }}
        </div>
      </div>

      <!-- Company Description -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
          Company Description
        </label>
        <textarea
          id="description"
          v-model="formData.description"
          rows="4"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Describe your company, products, and services..."
          :disabled="loading"
        ></textarea>
        <div v-if="errors.description" class="mt-1 text-sm text-red-600">
          {{ errors.description[0] }}
        </div>
        <div class="mt-1 text-sm text-gray-500">
          Tell us about your business to help customers understand what you offer
        </div>
      </div>

      <!-- Submit Button -->
      <div class="pt-4">
        <button
          type="submit"
          :disabled="loading || !isFormValid"
          class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Saving...
          </span>
          <span v-else class="flex items-center justify-center">
            Continue to License Upload
            <i class="fas fa-arrow-right ml-2"></i>
          </span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'CompanyInfoStep',
  props: {
    data: {
      type: Object,
      required: true,
    },
    userId: {
      type: [String, Number],
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['submit', 'update'],
  data() {
    return {
      formData: { ...this.data },
      errors: {},
    };
  },
  computed: {
    isFormValid() {
      return this.formData.name &&
             this.formData.email &&
             this.formData.contact_number_1 &&
             this.formData.address &&
             this.formData.emirate &&
             this.formData.city;
    },
  },
  watch: {
    formData: {
      handler(newData) {
        this.$emit('update', newData);
      },
      deep: true,
    },
  },
  methods: {
    handleSubmit() {
      if (this.isFormValid) {
        this.errors = {};
        this.$emit('submit');
      }
    },
  },
};
</script>
