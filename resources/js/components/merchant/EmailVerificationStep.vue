<template>
  <div class="email-verification-step">
    <div class="step-header">
      <div class="verification-icon">
        <i class="fas fa-envelope-open-text"></i>
      </div>
      <h2 class="step-title">{{ $t('verify_your_email_title') }}</h2>
      <p class="step-description">
        {{ $t('weve_sent_verification_code_to') }} <strong>{{ email }}</strong>
      </p>
      <p class="step-subdescription">
        {{ $t('please_check_email_enter_code') }}
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="verification-form">
      <div class="form-group">
        <label for="verification_code" class="form-label">{{ $t('verification_code') }}</label>
        <input
          type="text"
          id="verification_code"
          v-model="verificationCode"
          class="form-input verification-input"
          :class="{ 'error': errors.verification_code }"
          :placeholder="$t('enter_6_digit_code_placeholder')"
          maxlength="6"
          required
          @input="handleCodeInput"
          autocomplete="off"
          inputmode="numeric"
        >
        <div v-if="errors.verification_code" class="error-message">{{ errors.verification_code[0] }}</div>
      </div>

      <button type="submit" class="form-button" :disabled="loading || verificationCode.length !== 6">
        <div v-if="loading" class="loading-spinner"></div>
        <span class="button-text">{{ loading ? $t('verifying') : $t('verify_email') }}</span>
      </button>
    </form>

    <div class="resend-section">
      <p class="resend-text">{{ $t('didnt_receive_code') }}</p>
      <button 
        type="button" 
        class="resend-button" 
        @click="handleResend"
        :disabled="loading || resendCooldown > 0"
      >
        <span v-if="resendCooldown > 0">{{ $t('resend_in_seconds').replace('{seconds}', resendCooldown) }}</span>
        <span v-else>{{ $t('resend_code') }}</span>
      </button>
    </div>

    <div class="help-section">
      <div class="help-item">
        <i class="fas fa-info-circle"></i>
        <span>{{ $t('check_spam_junk_folder') }}</span>
      </div>
      <div class="help-item">
        <i class="fas fa-clock"></i>
        <span>{{ $t('verification_code_expires_10_minutes') }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EmailVerificationStep',
  props: {
    email: {
      type: String,
      required: true
    },
    registrationToken: {
      type: String,
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['submit', 'resend'],
  data() {
    return {
      verificationCode: '',
      errors: {},
      resendCooldown: 0,
      resendTimer: null
    };
  },
  methods: {
    handleSubmit() {
      this.errors = {};
      if (this.verificationCode.length === 6) {
        this.$emit('submit', {
          registration_token: this.registrationToken,
          verification_code: this.verificationCode
        });
      }
    },
    handleCodeInput() {
      // Remove any non-numeric characters
      this.verificationCode = this.verificationCode.replace(/\D/g, '');
      
      // Auto-submit when 6 digits are entered
      if (this.verificationCode.length === 6) {
        this.handleSubmit();
      }
    },
    handleResend() {
      this.$emit('resend', this.registrationToken);
      this.startResendCooldown();
    },
    startResendCooldown() {
      this.resendCooldown = 60; // 60 seconds cooldown
      this.resendTimer = setInterval(() => {
        this.resendCooldown--;
        if (this.resendCooldown <= 0) {
          clearInterval(this.resendTimer);
          this.resendTimer = null;
        }
      }, 1000);
    },
    setErrors(errors) {
      this.errors = errors;
    },
    $t(key) {
      return window.appTranslations && window.appTranslations[key] ? window.appTranslations[key] : key;
    }
  },
  beforeUnmount() {
    if (this.resendTimer) {
      clearInterval(this.resendTimer);
    }
  }
};
</script>

<style scoped>
.email-verification-step {
  max-width: 500px;
  margin: 0 auto;
  text-align: center;
  padding: 1rem;
}

/* Override global styles that might interfere */
.email-verification-step .form-group {
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 1;
}

.email-verification-step .form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.email-verification-step .form-input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e1e5e9 !important;
  border-radius: 8px !important;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white !important;
  box-sizing: border-box;
  position: relative;
  z-index: 1;
  min-height: auto !important;
  transform: none !important;
  box-shadow: none !important;
}

.email-verification-step .form-input:focus {
  outline: none !important;
  border-color: #f59e0b !important;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
  transform: none !important;
}

.email-verification-step .form-input:hover {
  border-color: #f59e0b !important;
  background: white !important;
  transform: none !important;
}

.email-verification-step .form-input.error {
  border-color: #e53e3e !important;
  box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1) !important;
}

.email-verification-step .form-button {
  width: 100%;
  padding: 12px 24px;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 48px;
}

.email-verification-step .form-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.email-verification-step .form-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.email-verification-step .error-message {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 0.5rem;
  text-align: left;
}

.step-header {
  margin-bottom: 2rem;
}

.verification-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  color: white;
  font-size: 2rem;
}

.step-title {
  font-size: 2rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 0.5rem;
}

.step-description {
  font-size: 1.1rem;
  color: #666;
  margin-bottom: 0.5rem;
}

.step-subdescription {
  font-size: 0.95rem;
  color: #888;
}

.verification-form {
  margin-bottom: 2rem;
}

.verification-input {
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  letter-spacing: 0.5rem;
  padding: 1rem;
  border: 2px solid #e1e5e9 !important;
  border-radius: 12px !important;
  background: white !important;
  box-shadow: none !important;
  min-height: auto !important;
  transform: none !important;
}

.verification-input:focus {
  border-color: #f59e0b !important;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
  transform: none !important;
  outline: none !important;
}

.verification-input:hover {
  border-color: #f59e0b !important;
  background: white !important;
  transform: none !important;
}

.resend-section {
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 12px;
}

.resend-text {
  color: #666;
  margin-bottom: 0.5rem;
}

.resend-button {
  background: none;
  border: none;
  color: #f59e0b;
  font-weight: 600;
  cursor: pointer;
  text-decoration: underline;
  font-size: 0.95rem;
}

.resend-button:hover:not(:disabled) {
  color: #5a67d8;
}

.resend-button:disabled {
  color: #ccc;
  cursor: not-allowed;
  text-decoration: none;
}

.help-section {
  text-align: left;
}

.help-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
  color: #666;
  font-size: 0.9rem;
}

.help-item i {
  margin-right: 0.75rem;
  color: #f59e0b;
  width: 16px;
}
</style>
