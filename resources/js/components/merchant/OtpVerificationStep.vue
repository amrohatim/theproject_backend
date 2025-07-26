<template>
  <div class="otp-verification-step">
    <div class="step-header">
      <div class="verification-icon">
        <i class="fas fa-mobile-alt"></i>
      </div>
      <h2 class="step-title">{{ $t('verify_your_phone') }}</h2>
      <p class="step-description">
        {{ $t('verification_code_sent_to') }} <strong>{{ phone }}</strong>
      </p>
      <p class="step-subdescription">
        {{ $t('enter_6_digit_code_below') }}
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="verification-form">
      <div class="form-group">
        <label for="otp_code" class="form-label">{{ $t('otp_code') }}</label>
        <div class="otp-input-container">
          <input
            v-for="(digit, index) in otpDigits"
            :key="index"
            :ref="`otpInput${index}`"
            type="text"
            class="otp-digit-input"
            :class="{ 'error': errors.otp_code }"
            v-model="otpDigits[index]"
            maxlength="1"
            @input="handleOtpInput(index, $event)"
            @keydown="handleKeyDown(index, $event)"
            @paste="handlePaste($event)"
          >
        </div>
        <div v-if="errors.otp_code" class="error-message">{{ errors.otp_code[0] }}</div>
      </div>

      <button type="submit" class="form-button" :disabled="loading || !isOtpComplete">
        <div v-if="loading" class="loading-spinner"></div>
        <span class="button-text">{{ loading ? $t('verifying') : $t('verify_phone') }}</span>
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
        <span v-if="resendCooldown > 0">{{ $t('resend_in') }} {{ resendCooldown }}s</span>
        <span v-else>{{ $t('resend_otp') }}</span>
      </button>
    </div>

    <div class="help-section">
      <div class="help-item">
        <i class="fas fa-info-circle"></i>
        <span>{{ $t('phone_sms_check') }}</span>
      </div>
      <div class="help-item">
        <i class="fas fa-clock"></i>
        <span>{{ $t('otp_expires_10_minutes') }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'OtpVerificationStep',
  props: {
    phone: {
      type: String,
      required: true
    },
    userId: {
      type: [String, Number],
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
      otpDigits: ['', '', '', '', '', ''],
      errors: {},
      resendCooldown: 0,
      resendTimer: null
    };
  },
  computed: {
    isOtpComplete() {
      return this.otpDigits.every(digit => digit !== '');
    },
    otpCode() {
      return this.otpDigits.join('');
    }
  },
  methods: {
    handleSubmit() {
      this.errors = {};
      if (this.isOtpComplete) {
        this.$emit('submit', {
          otp_code: this.otpCode
        });
      }
    },
    handleOtpInput(index, event) {
      const value = event.target.value.replace(/\D/g, ''); // Only allow digits
      
      if (value) {
        this.otpDigits[index] = value;
        
        // Move to next input
        if (index < 5) {
          this.$refs[`otpInput${index + 1}`][0].focus();
        }
        
        // Auto-submit when all digits are filled
        if (this.isOtpComplete) {
          this.handleSubmit();
        }
      } else {
        this.otpDigits[index] = '';
      }
    },
    handleKeyDown(index, event) {
      // Handle backspace
      if (event.key === 'Backspace' && !this.otpDigits[index] && index > 0) {
        this.$refs[`otpInput${index - 1}`][0].focus();
      }
      
      // Handle arrow keys
      if (event.key === 'ArrowLeft' && index > 0) {
        this.$refs[`otpInput${index - 1}`][0].focus();
      }
      if (event.key === 'ArrowRight' && index < 5) {
        this.$refs[`otpInput${index + 1}`][0].focus();
      }
    },
    handlePaste(event) {
      event.preventDefault();
      const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');

      if (pastedData.length === 6) {
        for (let i = 0; i < 6; i++) {
          this.otpDigits[i] = pastedData[i] || '';
        }

        // Focus last input or submit if complete
        if (this.isOtpComplete) {
          this.handleSubmit();
        } else {
          this.$refs[`otpInput5`][0].focus();
        }
      }
    },
    handleResend() {
      this.$emit('resend', this.phone);
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
    clearOtp() {
      this.otpDigits = ['', '', '', '', '', ''];
      this.$refs.otpInput0[0].focus();
    }
  },
  mounted() {
    // Focus first input on mount
    this.$refs.otpInput0[0].focus();
  },
  beforeUnmount() {
    if (this.resendTimer) {
      clearInterval(this.resendTimer);
    }
  }
};
</script>

<style scoped>
.otp-verification-step {
  max-width: 500px;
  margin: 0 auto;
  text-align: center;
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

.otp-input-container {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

.otp-digit-input {
  width: 50px;
  height: 50px;
  text-align: center;
  font-size: 1.25rem;
  font-weight: 600;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  background: white;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.otp-digit-input:focus {
  outline: none;
  border-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.otp-digit-input.error {
  border-color: #e53e3e;
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

/* Responsive design for smaller screens */
@media (max-width: 480px) {
  .otp-input-container {
    gap: 0.25rem;
    max-width: 320px;
  }

  .otp-digit-input {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }
}
</style>
