<template>
  <div class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex">
      <!-- Left Side - Marketing Content -->
      <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-amber-600 via-amber-400 to-amber-600 text-white p-12 flex-col justify-top">
        <div class="max-w-md mx-auto space-y-8">
          <!-- Logo -->
          <div class="text-center">
            <h1 class="text-3xl font-bold mb-8">Data3Chic</h1>
          </div>

          <!-- Main Heading -->
          <div class="text-center space-y-4">
            <h2 class="text-4xl font-bold leading-tight">
              {{ $t('join_our_merchant_community') }}
            </h2>
            <p class="text-amber-100 text-lg">
              {{ $t('expand_business_reach') }}
            </p>
          </div>

          <!-- Features -->
          <div class="space-y-6 mt-12">
            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-lg mb-1">{{ $t('grow_your_sales') }}</h3>
                <p class="text-amber-100 text-sm">{{ $t('access_large_customer_base') }}</p>
              </div>
            </div>

            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-lg mb-1">{{ $t('powerful_tools') }}</h3>
                <p class="text-amber-100 text-sm">{{ $t('easy_dashboard_manage_products') }}</p>
              </div>
            </div>

            <div class="flex items-start space-x-4">
              <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-lg mb-1">{{ $t('dedicated_support') }}</h3>
                <p class="text-amber-100 text-sm">{{ $t('our_team_help_succeed') }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Registration Form -->
      <div class="w-full lg:w-1/2 bg-white p-8 lg:p-12 flex items-center justify-center">
        <div class="w-full max-w-md space-y-6">
          <div class="text-center space-y-2">
            <h2 class="text-2xl font-bold text-gray-900">{{ $t('create_merchant_account') }}</h2>
            <p class="text-gray-600">{{ $t('complete_all_steps') }}</p>
          </div>

          <!-- Progress Indicator -->
          <div class="w-full mb-8">
            <!-- Desktop Progress Indicator -->
            <div class="hidden md:flex items-center justify-between" id="desktop-progress">
              <div class="flex items-center">
                <div class="flex flex-col items-center group">
                  <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2"
                       :class="getStepClasses(1)"
                       data-step="1"
                       tabindex="0"
                       role="button"
                       :aria-label="`Step 1: ${translatedSteps[0].name} - Business information`"
                       :title="`${translatedSteps[0].name} - Business information`">
                    <span v-if="currentStep > 1">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </span>
                    <span v-else>1</span>
                  </div>
                  <div class="mt-3 text-center">
                    <div class="text-xs font-semibold" :class="getStepLabelClasses(1)">{{ translatedSteps[0].name }}</div>
                  </div>
                </div>
                <div class="progress-line flex-1 h-1 mx-4 rounded-full" :class="currentStep > 1 ? 'bg-amber-500' : 'bg-gray-300'"></div>
              </div>
              <div class="flex items-center">
                <div class="flex flex-col items-center group">
                  <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2"
                       :class="getStepClasses(2)"
                       data-step="2"
                       tabindex="0"
                       role="button"
                       :aria-label="`Step 2: ${translatedSteps[1].name} - Verify your email`"
                       :title="`${translatedSteps[1].name} - Verify your email`">
                    <span v-if="currentStep > 2">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </span>
                    <span v-else>2</span>
                  </div>
                  <div class="mt-3 text-center">
                    <div class="text-xs font-semibold" :class="getStepLabelClasses(2)">{{ translatedSteps[1].name }}</div>
                  </div>
                </div>
                <div class="progress-line flex-1 h-1 mx-4 rounded-full" :class="currentStep > 2 ? 'bg-amber-500' : 'bg-gray-300'"></div>
              </div>
              <div class="flex items-center">
                <div class="flex flex-col items-center group">
                  <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2"
                       :class="getStepClasses(3)"
                       data-step="3"
                       tabindex="0"
                       role="button"
                       :aria-label="`Step 3: ${translatedSteps[2].name} - Verify your phone`"
                       :title="`${translatedSteps[2].name} - Verify your phone`">
                    <span v-if="currentStep > 3">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </span>
                    <span v-else>3</span>
                  </div>
                  <div class="mt-3 text-center">
                    <div class="text-xs font-semibold" :class="getStepLabelClasses(3)">{{ translatedSteps[2].name }}</div>
                  </div>
                </div>
                <div class="progress-line flex-1 h-1 mx-4 rounded-full" :class="currentStep > 3 ? 'bg-amber-500' : 'bg-gray-300'"></div>
              </div>
              <div class="flex items-center">
                <div class="flex flex-col items-center group">
                  <div class="step-circle w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold border-2"
                       :class="getStepClasses(4)"
                       data-step="4"
                       tabindex="0"
                       role="button"
                       :aria-label="`Step 4: ${translatedSteps[3].name} - Upload documents`"
                       :title="`${translatedSteps[3].name} - Upload documents`">
                    <span v-if="currentStep > 4">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                      </svg>
                    </span>
                    <span v-else>4</span>
                  </div>
                  <div class="mt-3 text-center">
                    <div class="text-xs font-semibold" :class="getStepLabelClasses(4)">{{ translatedSteps[3].name }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mobile Progress Indicator -->
            <div class="md:hidden">
              <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-semibold text-amber-600" id="mobile-step">{{ $t('step_of').replace('{current}', currentStep).replace('{total}', steps.length) }}</span>
                <span class="text-xs text-gray-500" id="mobile-progress">{{ $t('percent_complete').replace('{percent}', Math.round((currentStep / steps.length) * 100)) }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3 mb-4 shadow-inner">
                <div class="h-3 rounded-full transition-all duration-500 ease-out shadow-sm"
                     :style="`width: ${(currentStep / translatedSteps.length) * 100}%; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);`"></div>
              </div>
              <div class="text-center">
                <h3 class="text-base font-semibold text-gray-900" id="mobile-title">{{ translatedSteps[currentStep - 1]?.name }}</h3>
                <p class="text-xs text-gray-500" id="mobile-description">{{ getMobileStepDescription(currentStep) }}</p>
              </div>

              <!-- Mobile Step Dots -->
              <div class="flex justify-center mt-4 space-x-2" id="mobile-dots">
                <div v-for="(step, index) in translatedSteps" :key="index"
                     class="w-2 h-2 rounded-full transition-all duration-300"
                     :class="currentStep === index + 1 ? 'bg-amber-600' : 'bg-gray-300'"
                     :data-mobile-step="index + 1"></div>
              </div>
            </div>
          </div>

          <!-- Step Components -->
          <MerchantInfoStep
            v-if="currentStep === 1"
            :data="formData.merchantInfo"
            :loading="loading"
            @submit="handleMerchantInfoSubmit"
            @update="updateMerchantInfo"
            ref="merchantInfoStep"
          />

          <EmailVerificationStep
            v-if="currentStep === 2"
            :email="formData.merchantInfo.email"
            :registration-token="registrationToken"
            :loading="loading"
            @submit="handleEmailVerification"
            @resend="resendEmailVerification"
            ref="emailVerificationStep"
          />

          <OtpVerificationStep
            v-if="currentStep === 3"
            :phone="formData.merchantInfo.phone"
            :user-id="userId"
            :loading="loading"
            @submit="handleOtpVerification"
            @resend="resendOtpVerification"
            ref="otpVerificationStep"
          />

          <LicenseUploadStep
            v-if="currentStep === 4"
            :user-id="userId"
            :loading="loading"
            @submit="handleLicenseUpload"
            ref="licenseUploadStep"
          />

          <!-- Success Step -->
          <div v-if="currentStep === 5" class="text-center">
            <svg class="w-12 h-12 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $t('registration_complete') }}</h3>
            <p class="text-gray-600 mb-4">{{ $t('registration_submitted_successfully') }}</p>
            <button @click="goToLogin" class="px-6 py-2 text-white rounded-md" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
              {{ $t('go_to_login') }}
            </button>
          </div>

          <!-- Error/Success Messages -->
          <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-800">{{ error }}</p>
              </div>
            </div>
          </div>

          <div v-if="success" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-green-800">{{ success }}</p>
              </div>
            </div>
          </div>

          <!-- Navigation -->
          <div v-if="currentStep > 1 && currentStep < 5" class="flex justify-between pt-4">
            <button @click="goBack" :disabled="loading" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
              {{ $t('previous') }}
            </button>
          </div>

          <div class="text-center">
            <p class="text-sm text-gray-600">
              {{ $t('already_have_account') }}
              <a href="/login" class="text-amber-600 hover:text-amber-700 font-medium">{{ $t('log_in') }}</a>
            </p>
          </div>
        </div>
      </div>

      <!-- Mobile Header for smaller screens -->
      <div class="lg:hidden absolute top-0 left-0 right-0 text-white p-4 text-center" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <h1 class="text-xl font-bold">Data3Chic</h1>
      </div>
    </div>
  </div>
</template>



<script>
import MerchantInfoStep from './merchant/MerchantInfoStep.vue';
import EmailVerificationStep from './merchant/EmailVerificationStep.vue';
import OtpVerificationStep from './merchant/OtpVerificationStep.vue';
import LicenseUploadStep from './merchant/LicenseUploadStep.vue';
import { merchantRegistrationApi } from '../services/merchantRegistrationApi.js';

export default {
  name: 'MerchantRegistrationApp',
  components: {
    MerchantInfoStep,
    EmailVerificationStep,
    OtpVerificationStep,
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
      otpRequestId: null,
      steps: [
        { name: 'business_info' },
        { name: 'email_verification' },
        { name: 'phone_verification' },
        { name: 'license_upload' },
      ],
      formData: {
        merchantInfo: {
          name: '',
          email: '',
          phone: '',
          password: '',
          password_confirmation: '',
          emirate: '',
          logo: null,
          uae_id_front: null,
          uae_id_back: null,
          store_location_lat: null,
          store_location_lng: null,
          store_location_address: '',
          delivery_capability: false,
          delivery_fees: {
            dubai: '',
            abu_dhabi: '',
            sharjah: '',
            ajman: '',
            ras_al_khaimah: '',
            fujairah: '',
            umm_al_quwain: ''
          }
        },
      },
    };
  },
  computed: {
    translatedSteps() {
      return [
        { name: this.$t('business_info') },
        { name: this.$t('email_verification') },
        { name: this.$t('phone_verification') },
        { name: this.$t('license_upload') },
      ];
    }
  },
  methods: {
    // Translation method
    $t(key) {
      return window.appTranslations && window.appTranslations[key] ? window.appTranslations[key] : key;
    },
    async handleMerchantInfoSubmit(merchantData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.submitMerchantInfo(merchantData);
        
        if (response.success) {
          this.registrationToken = response.registration_token;
          this.success = response.message;
          this.currentStep = 2;
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.success = null;
          }, 3000);
        } else {
          this.error = response.message || this.$t('registration_failed_try_again');
        }
      } catch (error) {
        console.error('Merchant info submission error:', error);

        if (error.message.includes('422')) {
          // Handle validation errors
          try {
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
              // Check if we should show login dialog
              if (errorData.show_login_dialog) {
                this.$refs.merchantInfoStep.showLoginDialog(
                  errorData.errors.email?.[0] || errorData.errors.phone?.[0] || this.$t('account_already_exists')
                );
              } else {
                // Show validation error modal
                this.$refs.merchantInfoStep.showValidationErrorModal(errorData.errors);
              }
            }
          } catch (parseError) {
            this.error = this.$t('check_input_try_again');
          }
        } else {
          this.error = error.message || this.$t('registration_failed_try_again');
        }
      } finally {
        this.loading = false;
      }
    },

    async handleEmailVerification(verificationData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.verifyEmail(
          verificationData.registration_token,
          verificationData.verification_code
        );
        
        if (response.success) {
          this.userId = response.user_id;
          this.success = response.message;
          
          // Send OTP automatically
          await this.sendOtp();
          this.currentStep = 3;
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.success = null;
          }, 3000);
        } else {
          this.error = response.message || this.$t('email_verification_failed');
        }
      } catch (error) {
        console.error('Email verification error:', error);
        this.error = error.message || this.$t('email_verification_failed_try_again');
        
        if (this.$refs.emailVerificationStep) {
          this.$refs.emailVerificationStep.setErrors({ verification_code: [error.message] });
        }
      } finally {
        this.loading = false;
      }
    },

    async sendOtp() {
      try {
        const response = await merchantRegistrationApi.sendPhoneOtp(this.registrationToken);
        if (response.success) {
          // Store request_id for verification
          this.otpRequestId = response.request_id;
        }
      } catch (error) {
        console.error('Send OTP error:', error);
        // Don't show error for OTP sending, as it's automatic
      }
    },

    async handleOtpVerification(otpData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.verifyPhoneOtp(
          this.registrationToken,
          otpData.otp_code
        );

        if (response.success) {
          this.success = response.message;
          this.userId = response.user_id;
          this.currentStep = 4;

          // Clear success message after 3 seconds
          setTimeout(() => {
            this.success = null;
          }, 3000);
        } else {
          this.error = response.message || this.$t('otp_verification_failed');
        }
      } catch (error) {
        console.error('OTP verification error:', error);
        this.error = error.message || this.$t('otp_verification_failed_try_again');

        if (this.$refs.otpVerificationStep) {
          this.$refs.otpVerificationStep.setErrors({ otp_code: [error.message] });
          this.$refs.otpVerificationStep.clearOtp();
        }
      } finally {
        this.loading = false;
      }
    },

    async handleLicenseUpload(licenseData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.uploadLicense(
          licenseData.user_id,
          licenseData
        );
        
        if (response.success) {
          this.success = response.message;
          this.currentStep = 5;
        } else {
          this.error = response.message || this.$t('license_upload_failed');
        }
      } catch (error) {
        console.error('License upload error:', error);
        this.error = error.message || this.$t('license_upload_failed_try_again');
        
        if (this.$refs.licenseUploadStep) {
          this.$refs.licenseUploadStep.setErrors({ license_file: [error.message] });
        }
      } finally {
        this.loading = false;
      }
    },

    async resendEmailVerification(registrationToken) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.resendEmailVerification(registrationToken);
        
        if (response.success) {
          this.success = response.message;
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.success = null;
          }, 3000);
        } else {
          this.error = response.message || this.$t('failed_resend_verification_email');
        }
      } catch (error) {
        console.error('Resend email error:', error);
        this.error = error.message || this.$t('failed_resend_verification_email');
      } finally {
        this.loading = false;
      }
    },

    async resendOtpVerification(phoneNumber) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.resendPhoneOtp(this.registrationToken);

        if (response.success) {
          this.success = response.message || this.$t('otp_sent_successfully');
          // Store new request_id if provided
          if (response.request_id) {
            this.otpRequestId = response.request_id;
          }

          // Clear success message after 3 seconds
          setTimeout(() => {
            this.success = null;
          }, 3000);
        } else {
          this.error = response.message || this.$t('failed_resend_otp');
        }
      } catch (error) {
        console.error('Resend OTP error:', error);
        this.error = error.message || this.$t('failed_resend_otp');
      } finally {
        this.loading = false;
      }
    },

    updateMerchantInfo(data) {
      this.formData.merchantInfo = { ...this.formData.merchantInfo, ...data };
    },

    goBack() {
      if (this.currentStep > 1) {
        this.currentStep--;
        this.error = null;
        this.success = null;
      }
    },

    goToLogin() {
      window.location.href = '/login';
    },

    getStepClasses(stepNumber) {
      const isCompleted = stepNumber < this.currentStep;
      const isCurrent = stepNumber === this.currentStep;

      if (isCompleted) {
        return 'text-white bg-amber-500 border-amber-500';
      } else if (isCurrent) {
        return 'bg-amber-100 border-amber-600 text-amber-600';
      } else {
        return 'bg-gray-100 border-gray-300 text-gray-400';
      }
    },

    getStepLabelClasses(stepNumber) {
      const isCompleted = stepNumber < this.currentStep;
      const isCurrent = stepNumber === this.currentStep;

      if (isCompleted) {
        return 'text-amber-600';
      } else if (isCurrent) {
        return 'text-amber-600';
      } else {
        return 'text-gray-400';
      }
    },

    getMobileStepDescription(stepNumber) {
      const descriptions = [
        this.$t('business_information'),
        this.$t('verify_your_email_step'),
        this.$t('verify_your_phone_step'),
        this.$t('upload_documents_step')
      ];
      return descriptions[stepNumber - 1] || '';
    }
  }
};
</script>

<style scoped>
/* Remove all custom styles since we're using Tailwind CSS classes */
</style>
