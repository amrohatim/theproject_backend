<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $t('company_information') }}</h2>
      <p class="text-gray-600">{{ $t('provide_company_details_for_vendor_registration') }}</p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Company Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('company_name') }} <span class="text-red-500">*</span>
        </label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('enter_your_company_name')"
          :disabled="loading"
        />
        <div v-if="errors.name" class="mt-1 text-sm text-red-600">
          {{ errors.name[0] }}
        </div>
      </div>

      <!-- Company Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('company_email') }} <span class="text-red-500">*</span>
        </label>
        <input
          id="email"
          v-model="formData.email"
          type="email"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('enter_company_email_address')"
          :disabled="loading"
        />
        <div v-if="errors.email" class="mt-1 text-sm text-red-600">
          {{ errors.email[0] }}
        </div>
      </div>

      <!-- Primary Contact Number -->
      <div>
        <label for="contact_number_1" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('primary_contact_number') }} <span class="text-red-500">*</span>
        </label>
        <input
          id="contact_number_1"
          v-model="formData.contact_number_1"
          type="tel"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('enter_primary_contact_number')"
          :disabled="loading"
        />
        <div v-if="errors.contact_number_1" class="mt-1 text-sm text-red-600">
          {{ errors.contact_number_1[0] }}
        </div>
      </div>

      <!-- Secondary Contact Number -->
      <div>
        <label for="contact_number_2" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('secondary_contact_number') }}
        </label>
        <input
          id="contact_number_2"
          v-model="formData.contact_number_2"
          type="tel"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('enter_secondary_contact_number_optional')"
          :disabled="loading"
        />
        <div v-if="errors.contact_number_2" class="mt-1 text-sm text-red-600">
          {{ errors.contact_number_2[0] }}
        </div>
      </div>

      <!-- Address -->
      <div>
        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('company_address') }} <span class="text-red-500">*</span>
        </label>
        <textarea
          id="address"
          v-model="formData.address"
          required
          rows="3"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('enter_your_complete_company_address')"
          :disabled="loading"
        ></textarea>
        <div v-if="errors.address" class="mt-1 text-sm text-red-600">
          {{ errors.address[0] }}
        </div>
      </div>

      <!-- Emirate -->
      <div>
        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('emirate') }} <span class="text-red-500">*</span>
        </label>
        <select
          id="emirate"
          v-model="formData.emirate"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :disabled="loading"
        >
          <option value="">{{ $t('select_emirate') }}</option>
          <option value="Abu Dhabi">{{ $t('abu_dhabi') }}</option>
          <option value="Dubai">{{ $t('dubai') }}</option>
          <option value="Sharjah">{{ $t('sharjah') }}</option>
          <option value="Ajman">{{ $t('ajman') }}</option>
          <option value="Umm Al Quwain">{{ $t('umm_al_quwain') }}</option>
          <option value="Ras Al Khaimah">{{ $t('ras_al_khaimah') }}</option>
          <option value="Fujairah">{{ $t('fujairah') }}</option>
        </select>
        <div v-if="errors.emirate" class="mt-1 text-sm text-red-600">
          {{ errors.emirate[0] }}
        </div>
      </div>

      <!-- City -->
      <div>
        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('city') }} <span class="text-red-500">*</span>
        </label>
        <input
          id="city"
          v-model="formData.city"
          type="text"
          required
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('enter_city_name')"
          :disabled="loading"
        />
        <div v-if="errors.city" class="mt-1 text-sm text-red-600">
          {{ errors.city[0] }}
        </div>
      </div>

      <!-- Street -->
      <div>
        <label for="street" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('street') }}
        </label>
        <input
          id="street"
          v-model="formData.street"
          type="text"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('enter_street_name_optional')"
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
            {{ $t('we_offer_delivery_services') }}
          </label>
        </div>
        <div v-if="errors.delivery_capability" class="mt-1 text-sm text-red-600">
          {{ errors.delivery_capability[0] }}
        </div>
      </div>

      <!-- Company Logo -->
      <div>
        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('company_logo') }}
        </label>
        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
          <div class="space-y-1 text-center">
            <div v-if="logoPreview" class="mb-4">
              <img :src="logoPreview" alt="Logo preview" class="mx-auto h-32 w-32 object-cover rounded-lg" />
              <button
                type="button"
                @click="removeLogo"
                class="mt-2 text-sm text-red-600 hover:text-red-800"
                :disabled="loading"
              >
                {{ $t('remove_logo') }}
              </button>
            </div>
            <div v-else>
              <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="flex text-sm text-gray-600">
                <label for="logo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                  <span>{{ $t('upload_a_logo') }}</span>
                  <input
                    id="logo"
                    ref="logoInput"
                    type="file"
                    accept="image/*"
                    class="sr-only"
                    @change="handleLogoChange"
                    :disabled="loading"
                    required
                  />
                </label>
                <p class="pl-1">{{ $t('or_drag_and_drop') }}</p>
              </div>
              <p class="text-xs text-gray-500">{{ $t('png_jpg_gif_up_to_2mb') }}</p>
            </div>
          </div>
        </div>
        <div v-if="errors.logo" class="mt-1 text-sm text-red-600">
          {{ errors.logo[0] }}
        </div>
      </div>

      <!-- Company Description -->
      <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
          {{ $t('company_description') }}
        </label>
        <textarea
          id="description"
          v-model="formData.description"
          rows="4"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :placeholder="$t('describe_your_company_products_services')"
          :disabled="loading"
        ></textarea>
        <div v-if="errors.description" class="mt-1 text-sm text-red-600">
          {{ errors.description[0] }}
        </div>
        <div class="mt-1 text-sm text-gray-500">
          {{ $t('tell_us_about_your_business') }}
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
            {{ $t('saving') }}...
          </span>
          <span v-else class="flex items-center justify-center">
            {{ $t('continue_to_license_upload') }}
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
      logoFile: null,
      logoPreview: null,
    };
  },
  computed: {
    isFormValid() {
      return this.formData.name &&
             this.formData.email &&
             this.formData.contact_number_1 &&
             this.formData.address &&
             this.formData.emirate &&
             this.formData.city &&
             (this.logoFile || this.formData.logo || this.logoPreview);
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
    // Translation method
    $t(key) {
      return window.appTranslations && window.appTranslations[key] ? window.appTranslations[key] : key;
    },
    handleSubmit() {
      this.errors = {};

      if (!this.logoFile && !this.formData.logo) {
        this.errors = { ...this.errors, logo: [this.$t('required_field')] };
        return;
      }

      if (this.isFormValid) {
        this.$emit('submit', this.logoFile);
      }
    },
    handleLogoChange(event) {
      const file = event.target.files[0];
      if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
          this.errors = { ...this.errors, logo: [this.$t('file_size_must_be_less_than_2mb')] };
          return;
        }
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
          this.errors = { ...this.errors, logo: [this.$t('please_select_valid_image_file')] };
          return;
        }
        
        this.logoFile = file;
        this.formData.logo = file;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
          this.logoPreview = e.target.result;
        };
        reader.readAsDataURL(file);
        
        // Clear any previous errors
        if (this.errors.logo) {
          const { logo, ...otherErrors } = this.errors;
          this.errors = otherErrors;
        }
      }
    },
    removeLogo() {
      this.logoFile = null;
      this.logoPreview = null;
      this.formData.logo = null;
      if (this.$refs.logoInput) {
        this.$refs.logoInput.value = '';
      }
    },
  },
};
</script>
