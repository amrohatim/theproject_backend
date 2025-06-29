<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">Personal Information</h2>
      <p class="text-gray-600">Please provide your personal details to get started.</p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Full Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
          Full Name <span class="text-red-500">*</span>
        </label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter your full name"
          :disabled="loading"
        />
        <div v-if="errors.name" class="mt-1 text-sm text-red-600">
          {{ errors.name[0] }}
        </div>
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
          Email Address <span class="text-red-500">*</span>
        </label>
        <input
          id="email"
          v-model="formData.email"
          type="email"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter your email address"
          :disabled="loading"
        />
        <div v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email[0] }}
        </div>
      </div>

      <!-- Phone -->
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
          Phone Number <span class="text-red-500">*</span>
        </label>
        <input
          id="phone"
          v-model="formData.phone"
          type="tel"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Enter your phone number"
          :disabled="loading"
        />
        <div v-if="errors.phone" class="mt-1 text-sm text-red-600">
          {{ errors.phone[0] }}
        </div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
          Password <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input
            id="password"
            v-model="formData.password"
            :type="showPassword ? 'text' : 'password'"
            required
            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            placeholder="Create a strong password"
            :disabled="loading"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
          </button>
        </div>
        <div v-if="errors.password" class="mt-1 text-sm text-red-600">
          {{ errors.password[0] }}
        </div>
        <div class="mt-1 text-sm text-gray-500">
          Password must be at least 8 characters long
        </div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
          Confirm Password <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input
            id="password_confirmation"
            v-model="formData.password_confirmation"
            :type="showConfirmPassword ? 'text' : 'password'"
            required
            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
            placeholder="Confirm your password"
            :disabled="loading"
          />
          <button
            type="button"
            @click="showConfirmPassword = !showConfirmPassword"
            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
          >
            <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
          </button>
        </div>
        <div v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">
          {{ errors.password_confirmation[0] }}
        </div>
        <div v-if="passwordMismatch" class="mt-1 text-sm text-red-600">
          Passwords do not match
        </div>
      </div>

      <!-- Terms and Conditions -->
      <div class="flex items-start">
        <input
          id="terms"
          v-model="acceptTerms"
          type="checkbox"
          required
          class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
          :disabled="loading"
        />
        <label for="terms" class="ml-3 text-sm text-gray-700">
          I agree to the 
          <a href="#" class="text-blue-600 hover:text-blue-800 underline">Terms and Conditions</a>
          and 
          <a href="#" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</a>
          <span class="text-red-500">*</span>
        </label>
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
            Processing...
          </span>
          <span v-else class="flex items-center justify-center">
            Continue to Email Verification
            <i class="fas fa-arrow-right ml-2"></i>
          </span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'PersonalInfoStep',
  props: {
    data: {
      type: Object,
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
      showPassword: false,
      showConfirmPassword: false,
      acceptTerms: false,
      errors: {},
    };
  },
  computed: {
    passwordMismatch() {
      return this.formData.password && 
             this.formData.password_confirmation && 
             this.formData.password !== this.formData.password_confirmation;
    },
    isFormValid() {
      return this.formData.name &&
             this.formData.email &&
             this.formData.phone &&
             this.formData.password &&
             this.formData.password_confirmation &&
             this.formData.password === this.formData.password_confirmation &&
             this.formData.password.length >= 8 &&
             this.acceptTerms;
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
