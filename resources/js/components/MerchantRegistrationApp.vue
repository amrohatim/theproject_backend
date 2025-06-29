<template>
  <div class="merchant-registration-app">
    <div class="registration-container">
      <!-- Logo Section -->
      <div class="logo-section">
        <div class="logo">
          <i class="fas fa-store"></i>
        </div>
        <h1 class="welcome-text">Join as Merchant</h1>
        <p class="subtitle">Start selling your products and services on our platform</p>
      </div>

      <!-- Form Section -->
      <div class="form-section">
        <!-- Progress Steps -->
        <div class="progress-steps">
          <div 
            v-for="(step, index) in steps" 
            :key="index"
            class="step"
            :class="{ 
              'active': currentStep === index + 1, 
              'completed': currentStep > index + 1 
            }"
          >
            <div class="step-number">
              <i v-if="currentStep > index + 1" class="fas fa-check"></i>
              <span v-else>{{ index + 1 }}</span>
            </div>
            <span class="step-name">{{ step.name }}</span>
          </div>
        </div>

        <!-- Error/Success Messages -->
        <div v-if="error" class="error-message global-error">
          <i class="fas fa-exclamation-triangle"></i>
          {{ error }}
        </div>

        <div v-if="success" class="success-message global-success">
          <i class="fas fa-check-circle"></i>
          {{ success }}
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
        <div v-if="currentStep === 5" class="success-step">
          <div class="success-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <h2>Registration Complete!</h2>
          <p>Your merchant registration has been submitted successfully.</p>
          <p>We'll review your application and get back to you within 24-48 hours.</p>
          <div class="next-steps">
            <h3>What's Next?</h3>
            <ul>
              <li>Check your email for confirmation</li>
              <li>We'll review your license and documents</li>
              <li>You'll receive approval notification</li>
              <li>Start adding your products and services</li>
            </ul>
          </div>
          <button @click="goToLogin" class="form-button">
            Go to Login
          </button>
        </div>

        <!-- Navigation -->
        <div v-if="currentStep > 1 && currentStep < 5" class="navigation">
          <button 
            @click="goBack"
            class="back-button"
            :disabled="loading"
          >
            <i class="fas fa-arrow-left"></i>
            Back
          </button>
        </div>
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
      steps: [
        { name: 'Business Info' },
        { name: 'Email Verification' },
        { name: 'Phone Verification' },
        { name: 'License Upload' },
      ],
      formData: {
        merchantInfo: {
          name: '',
          email: '',
          phone: '',
          password: '',
          password_confirmation: '',
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
  methods: {
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
          this.error = response.message || 'Registration failed. Please try again.';
        }
      } catch (error) {
        console.error('Merchant info submission error:', error);
        
        if (error.message.includes('422')) {
          // Handle validation errors
          try {
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
              this.$refs.merchantInfoStep.setErrors(errorData.errors);
            }
          } catch (parseError) {
            this.error = 'Please check your input and try again.';
          }
        } else {
          this.error = error.message || 'Registration failed. Please try again.';
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
          this.error = response.message || 'Email verification failed.';
        }
      } catch (error) {
        console.error('Email verification error:', error);
        this.error = error.message || 'Email verification failed. Please try again.';
        
        if (this.$refs.emailVerificationStep) {
          this.$refs.emailVerificationStep.setErrors({ verification_code: [error.message] });
        }
      } finally {
        this.loading = false;
      }
    },

    async sendOtp() {
      try {
        await merchantRegistrationApi.sendOtp(this.formData.merchantInfo.phone);
      } catch (error) {
        console.error('Send OTP error:', error);
        // Don't show error for OTP sending, as it's automatic
      }
    },

    async handleOtpVerification(otpData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.verifyOtp(
          otpData.phone_number,
          otpData.otp_code
        );
        
        if (response.success) {
          this.success = response.message;
          this.currentStep = 4;
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.success = null;
          }, 3000);
        } else {
          this.error = response.message || 'OTP verification failed.';
        }
      } catch (error) {
        console.error('OTP verification error:', error);
        this.error = error.message || 'OTP verification failed. Please try again.';
        
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
          this.error = response.message || 'License upload failed.';
        }
      } catch (error) {
        console.error('License upload error:', error);
        this.error = error.message || 'License upload failed. Please try again.';
        
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
          this.error = response.message || 'Failed to resend verification email.';
        }
      } catch (error) {
        console.error('Resend email error:', error);
        this.error = error.message || 'Failed to resend verification email.';
      } finally {
        this.loading = false;
      }
    },

    async resendOtpVerification(phoneNumber) {
      this.loading = true;
      this.error = null;

      try {
        const response = await merchantRegistrationApi.sendOtp(phoneNumber);
        
        if (response.success) {
          this.success = response.message || 'OTP sent successfully.';
          
          // Clear success message after 3 seconds
          setTimeout(() => {
            this.success = null;
          }, 3000);
        } else {
          this.error = response.message || 'Failed to resend OTP.';
        }
      } catch (error) {
        console.error('Resend OTP error:', error);
        this.error = error.message || 'Failed to resend OTP.';
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
    }
  }
};
</script>

<style scoped>
.merchant-registration-app {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.registration-container {
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  width: 100%;
  max-width: 1200px;
  display: grid;
  grid-template-columns: 1fr 2fr;
  min-height: 700px;
}

.logo-section {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
  color: white;
  padding: 60px 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.logo {
  width: 80px;
  height: 80px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 30px;
  backdrop-filter: blur(10px);
}

.logo i {
  font-size: 2.5rem;
}

.welcome-text {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 15px;
  line-height: 1.2;
}

.subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  line-height: 1.5;
}

.form-section {
  padding: 40px;
  overflow-y: auto;
  max-height: 700px;
}

.progress-steps {
  display: flex;
  justify-content: space-between;
  margin-bottom: 2rem;
  padding: 0 1rem;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  position: relative;
}

.step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 20px;
  left: 60%;
  right: -40%;
  height: 2px;
  background: #e1e5e9;
  z-index: 1;
}

.step.completed:not(:last-child)::after {
  background: #48bb78;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e1e5e9;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  margin-bottom: 0.5rem;
  position: relative;
  z-index: 2;
  transition: all 0.3s ease;
}

.step.active .step-number {
  background: #667eea;
  color: white;
}

.step.completed .step-number {
  background: #48bb78;
  color: white;
}

.step-name {
  font-size: 0.85rem;
  color: #666;
  text-align: center;
  font-weight: 500;
}

.step.active .step-name {
  color: #667eea;
  font-weight: 600;
}

.step.completed .step-name {
  color: #48bb78;
  font-weight: 600;
}

.global-error, .global-success {
  padding: 1rem 1.5rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  font-weight: 500;
}

.global-error {
  background: rgba(229, 62, 62, 0.1);
  color: #e53e3e;
  border: 1px solid rgba(229, 62, 62, 0.2);
}

.global-success {
  background: rgba(72, 187, 120, 0.1);
  color: #48bb78;
  border: 1px solid rgba(72, 187, 120, 0.2);
}

.global-error i, .global-success i {
  margin-right: 0.75rem;
  font-size: 1.1rem;
}

.success-step {
  text-align: center;
  padding: 2rem 0;
}

.success-icon {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 2rem;
  color: white;
  font-size: 3rem;
}

.success-step h2 {
  font-size: 2rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 1rem;
}

.success-step p {
  font-size: 1.1rem;
  color: #666;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.next-steps {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 1.5rem;
  margin: 2rem 0;
  text-align: left;
}

.next-steps h3 {
  color: #333;
  margin-bottom: 1rem;
  font-size: 1.2rem;
}

.next-steps ul {
  list-style: none;
  padding: 0;
}

.next-steps li {
  padding: 0.5rem 0;
  color: #666;
  position: relative;
  padding-left: 1.5rem;
}

.next-steps li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #48bb78;
  font-weight: bold;
}

.navigation {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e1e5e9;
}

.back-button {
  background: none;
  border: 2px solid #e1e5e9;
  color: #666;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.back-button:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.back-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.back-button i {
  margin-right: 0.5rem;
}

/* Global form styles */
:deep(.form-group) {
  margin-bottom: 1.5rem;
}

:deep(.form-label) {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

:deep(.form-input) {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid #e1e5e9;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
}

:deep(.form-input:focus) {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

:deep(.form-input.error) {
  border-color: #e53e3e;
}

:deep(.form-button) {
  width: 100%;
  padding: 15px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

:deep(.form-button:hover:not(:disabled)) {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

:deep(.form-button:disabled) {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

:deep(.loading-spinner) {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 0.5rem;
  display: none;
}

:deep(.form-button:disabled .loading-spinner) {
  display: inline-block;
}

:deep(.error-message) {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 0.5rem;
  display: flex;
  align-items: center;
}

:deep(.error-message::before) {
  content: '⚠';
  margin-right: 0.5rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive design */
@media (max-width: 768px) {
  .registration-container {
    grid-template-columns: 1fr;
    max-width: 500px;
  }

  .logo-section {
    padding: 40px 20px;
  }

  .form-section {
    padding: 30px 20px;
  }

  .progress-steps {
    flex-direction: column;
    gap: 1rem;
  }

  .step:not(:last-child)::after {
    display: none;
  }

  .step {
    flex-direction: row;
    justify-content: flex-start;
    text-align: left;
  }

  .step-number {
    margin-right: 1rem;
    margin-bottom: 0;
  }
}
</style>
