<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8" :class="{ 'rtl': isRTL }">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $t('vendor_registration') }}</h1>
        <p class="text-gray-600">{{ $t('complete_registration_5_steps') }}</p>
      </div>

      <!-- Progress Bar -->
      <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
          <div 
            v-for="(step, index) in steps" 
            :key="index"
            class="flex items-center"
            :class="{ 'flex-1': index < steps.length - 1 }"
          >
            <div 
              class="flex items-center justify-center w-10 h-10 rounded-full border-2 text-sm font-semibold"
              :class="getStepClasses(index + 1)"
            >
              <i v-if="currentStep > index + 1" class="fas fa-check text-white"></i>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <div v-if="index < steps.length - 1" class="flex-1 h-1 mx-4 bg-gray-200 rounded">
              <div 
                class="h-full bg-blue-500 rounded transition-all duration-300"
                :style="{ width: currentStep > index + 1 ? '100%' : '0%' }"
              ></div>
            </div>
          </div>
        </div>
        <div class="flex justify-between text-xs text-gray-500">
          <span v-for="(step, index) in steps" :key="index" class="text-center">
            {{ step.name }}
          </span>
        </div>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Error Display -->
        <div v-if="error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center" :class="isRTL ? 'flex-row-reverse' : ''">
            <i class="fas fa-exclamation-circle text-red-500" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
            <span class="text-red-700">{{ error }}</span>
          </div>
        </div>

        <!-- Success Display -->
        <div v-if="success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center" :class="isRTL ? 'flex-row-reverse' : ''">
            <i class="fas fa-check-circle text-green-500" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
            <span class="text-green-700">{{ success }}</span>
          </div>
        </div>

        <!-- Step Components -->
        <PersonalInfoStep 
          v-if="currentStep === 1"
          :data="formData.personalInfo"
          :loading="loading"
          @submit="handlePersonalInfoSubmit"
          @update="updatePersonalInfo"
        />

        <EmailVerificationStep
          v-if="currentStep === 2"
          :email="formData.personalInfo.email"
          :loading="loading"
          @submit="handleEmailVerification"
          @resend="resendEmailVerification"
        />

        <PhoneVerificationStep
          v-if="currentStep === 3"
          :phone="formData.personalInfo.phone"
          :loading="loading"
          @submit="handlePhoneVerification"
          @resend="resendPhoneVerification"
        />

        <CompanyInfoStep 
          v-if="currentStep === 4"
          :data="formData.companyInfo"
          :user-id="userId"
          :loading="loading"
          @submit="handleCompanyInfoSubmit"
          @update="updateCompanyInfo"
        />

        <LicenseUploadStep 
          v-if="currentStep === 5"
          :user-id="userId"
          :loading="loading"
          @submit="handleLicenseUpload"
        />

        <!-- Navigation -->
        <div class="flex justify-between mt-8 pt-6 border-t border-gray-200">
          <button 
            v-if="currentStep > 1 && currentStep < 6"
            @click="goBack"
            class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            :disabled="loading"
          >
            <i class="fas fa-arrow-left" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
            {{ $t('back') }}
          </button>
          <div v-else></div>

          <div v-if="currentStep === 6" class="text-center w-full">
            <div class="mb-4">
              <i class="fas fa-check-circle text-green-500 text-4xl mb-2"></i>
              <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $t('registration_complete') }}</h2>
              <p class="text-gray-600 mb-4">{{ $t('vendor_registration_submitted_successfully') }}</p>
            </div>
            <a 
              href="/" 
              class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <i class="fas fa-home" :class="isRTL ? 'ml-2' : 'mr-2'"></i>
              {{ $t('go_to_dashboard') }}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PersonalInfoStep from './registration/PersonalInfoStep.vue';
import EmailVerificationStep from './registration/EmailVerificationStep.vue';
import PhoneVerificationStep from './registration/PhoneVerificationStep.vue';
import CompanyInfoStep from './registration/CompanyInfoStep.vue';
import LicenseUploadStep from './registration/LicenseUploadStep.vue';
import { registrationApi } from '../services/registrationApi.js';

export default {
  name: 'VendorRegistrationApp',
  components: {
    PersonalInfoStep,
    EmailVerificationStep,
    PhoneVerificationStep,
    CompanyInfoStep,
    LicenseUploadStep,
  },
  data() {
    return {
      currentStep: 1,
      loading: false,
      error: null,
      success: null,
      userId: null, // Will be set after company info is completed
      steps: [
        { name: 'personal_info' },
        { name: 'email_verification' },
        { name: 'phone_verification' },
        { name: 'company_info' },
        { name: 'license_upload' },
      ],
      formData: {
        personalInfo: {
          name: '',
          email: '',
          phone: '',
          password: '',
          password_confirmation: '',
        },
        companyInfo: {
          name: '',
          email: '',
          contact_number_1: '',
          contact_number_2: '',
          address: '',
          emirate: '',
          city: '',
          street: '',
          delivery_capability: false,
          delivery_areas: [],
          description: '',
        },
      },
    };
  },
  computed: {
    // RTL support
    isRTL() {
      return document.documentElement.dir === 'rtl' || document.documentElement.lang === 'ar';
    },
  },
  methods: {
    getStepClasses(step) {
      if (this.currentStep > step) {
        return 'bg-blue-500 border-blue-500 text-white';
      } else if (this.currentStep === step) {
        return 'bg-blue-100 border-blue-500 text-blue-600';
      } else {
        return 'bg-gray-100 border-gray-300 text-gray-500';
      }
    },

    clearMessages() {
      this.error = null;
      this.success = null;
    },

    async handlePersonalInfoSubmit() {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.submitPersonalInfo(this.formData.personalInfo);

        if (response.success) {
          this.success = response.message;
          this.currentStep = 2;
        } else {
          this.error = response.message || this.$t('vendor.registration_failed_try_again');
        }
      } catch (error) {
        this.error = error.message || this.$t('vendor.error_occurred_try_again');
      } finally {
        this.loading = false;
      }
    },

    async handleEmailVerification(verificationData) {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.verifyEmail(verificationData.verification_code);

        if (response.success) {
          this.success = response.message;
          this.currentStep = 3;
        } else {
          this.error = response.message || this.$t('vendor.email_verification_failed_try_again');
        }
      } catch (error) {
        this.error = error.message || this.$t('vendor.error_occurred_try_again');
      } finally {
        this.loading = false;
      }
    },

    async handlePhoneVerification(otpCode) {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.verifyOtp(otpCode);

        if (response.success) {
          this.success = response.message;
          this.currentStep = 4;
        } else {
          this.error = response.message || this.$t('vendor.phone_verification_failed_try_again');
        }
      } catch (error) {
        this.error = error.message || this.$t('vendor.error_occurred_try_again');
      } finally {
        this.loading = false;
      }
    },

    async handleCompanyInfoSubmit(logoFile = null) {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.submitCompanyInfo(this.formData.companyInfo, logoFile);

        if (response.success) {
          this.success = response.message;
          this.currentStep = 5;
          // Store user_id for license upload step
          this.userId = response.user_id;
        } else {
          this.error = response.message || this.$t('vendor.company_info_submission_failed_try_again');
        }
      } catch (error) {
        this.error = error.message || this.$t('vendor.error_occurred_try_again');
      } finally {
        this.loading = false;
      }
    },

    async handleLicenseUpload(licenseData) {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.uploadLicense(this.userId, licenseData);
        
        if (response.success) {
          this.success = response.message;
          this.currentStep = 6;
        } else {
          // Handle specific error cases
          if (response.error_code === 'LICENSE_UPLOAD_ERROR') {
            this.error = this.$t('vendor.server_error_license_upload_contact_support');
          } else if (response.errors && response.errors.session) {
            this.error = this.$t('vendor.registration_session_expired_restart');
          } else {
            this.error = response.message || this.$t('vendor.license_upload_failed_check_file');
          }
        }
      } catch (error) {
        console.error('License upload error:', error);
        this.error = this.$t('vendor.network_error_check_connection');
      } finally {
        this.loading = false;
      }
    },

    async resendEmailVerification() {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.resendEmailVerification();

        if (response.success) {
          this.success = response.message;
        } else {
          this.error = response.message || this.$t('vendor.failed_resend_verification_email');
        }
      } catch (error) {
        this.error = error.message || this.$t('vendor.error_occurred_try_again');
      } finally {
        this.loading = false;
      }
    },

    async resendPhoneVerification() {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.sendOtp();

        if (response.success) {
          this.success = response.message;
        } else {
          this.error = response.message || this.$t('vendor.failed_resend_otp');
        }
      } catch (error) {
        this.error = error.message || this.$t('vendor.error_occurred_try_again');
      } finally {
        this.loading = false;
      }
    },

    updatePersonalInfo(data) {
      this.formData.personalInfo = { ...this.formData.personalInfo, ...data };
    },

    updateCompanyInfo(data) {
      this.formData.companyInfo = { ...this.formData.companyInfo, ...data };
    },

    goBack() {
      if (this.currentStep > 1) {
        this.currentStep--;
        this.clearMessages();
      }
    },
  },
};
</script>

<style scoped>
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

.rtl .ml-2 {
  margin-left: 0;
  margin-right: 0.5rem;
}

.rtl .mr-2 {
  margin-right: 0;
  margin-left: 0.5rem;
}

.rtl .ml-4 {
  margin-left: 0;
  margin-right: 1rem;
}

.rtl .mr-4 {
  margin-right: 0;
  margin-left: 1rem;
}

.rtl .pl-4 {
  padding-left: 0;
  padding-right: 1rem;
}

.rtl .pr-4 {
  padding-right: 0;
  padding-left: 1rem;
}

.rtl .flex-row {
  flex-direction: row-reverse;
}

.rtl input[type="text"],
.rtl input[type="email"],
.rtl input[type="tel"],
.rtl textarea {
  text-align: right;
}
</style>
