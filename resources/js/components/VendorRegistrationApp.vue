<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Vendor Registration</h1>
        <p class="text-gray-600">Complete your registration in 5 simple steps</p>
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
          <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
            <span class="text-red-700">{{ error }}</span>
          </div>
        </div>

        <!-- Success Display -->
        <div v-if="success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
          <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-2"></i>
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
          :registration-token="registrationToken"
          :loading="loading"
          @submit="handleEmailVerification"
          @resend="resendEmailVerification"
        />

        <PhoneVerificationStep 
          v-if="currentStep === 3"
          :phone="formData.personalInfo.phone"
          :user-id="userId"
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
            <i class="fas fa-arrow-left mr-2"></i>
            Back
          </button>
          <div v-else></div>

          <div v-if="currentStep === 6" class="text-center w-full">
            <div class="mb-4">
              <i class="fas fa-check-circle text-green-500 text-4xl mb-2"></i>
              <h2 class="text-2xl font-bold text-gray-900 mb-2">Registration Complete!</h2>
              <p class="text-gray-600 mb-4">Your vendor registration has been submitted successfully.</p>
            </div>
            <a 
              href="/" 
              class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <i class="fas fa-home mr-2"></i>
              Go to Dashboard
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
      registrationToken: null,
      userId: null,
      steps: [
        { name: 'Personal Info' },
        { name: 'Email Verification' },
        { name: 'Phone Verification' },
        { name: 'Company Info' },
        { name: 'License Upload' },
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
          this.registrationToken = response.registration_token;
          this.success = response.message;
          this.currentStep = 2;
        } else {
          this.error = response.message || 'Registration failed. Please try again.';
        }
      } catch (error) {
        this.error = error.message || 'An error occurred. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    async handleEmailVerification(verificationData) {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.verifyEmail(
          verificationData.registration_token,
          verificationData.verification_code
        );

        if (response.success) {
          this.userId = response.user_id;
          this.success = response.message;
          this.currentStep = 3;
        } else {
          this.error = response.message || 'Email verification failed. Please try again.';
        }
      } catch (error) {
        this.error = error.message || 'An error occurred. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    async handlePhoneVerification(otpCode) {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.verifyOtp(this.formData.personalInfo.phone, otpCode);
        
        if (response.success) {
          this.success = response.message;
          this.currentStep = 4;
        } else {
          this.error = response.message || 'Phone verification failed. Please try again.';
        }
      } catch (error) {
        this.error = error.message || 'An error occurred. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    async handleCompanyInfoSubmit() {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.submitCompanyInfo(this.userId, this.formData.companyInfo);
        
        if (response.success) {
          this.success = response.message;
          this.currentStep = 5;
        } else {
          this.error = response.message || 'Company information submission failed. Please try again.';
        }
      } catch (error) {
        this.error = error.message || 'An error occurred. Please try again.';
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
          this.error = response.message || 'License upload failed. Please try again.';
        }
      } catch (error) {
        this.error = error.message || 'An error occurred. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    async resendEmailVerification() {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.resendEmailVerification(this.registrationToken);
        
        if (response.success) {
          this.success = response.message;
        } else {
          this.error = response.message || 'Failed to resend verification email.';
        }
      } catch (error) {
        this.error = error.message || 'An error occurred. Please try again.';
      } finally {
        this.loading = false;
      }
    },

    async resendPhoneVerification() {
      this.clearMessages();
      this.loading = true;

      try {
        const response = await registrationApi.sendOtp(this.formData.personalInfo.phone);
        
        if (response.success) {
          this.success = response.message;
        } else {
          this.error = response.message || 'Failed to resend OTP.';
        }
      } catch (error) {
        this.error = error.message || 'An error occurred. Please try again.';
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
