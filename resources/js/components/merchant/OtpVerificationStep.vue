<template>
  <div class="otp-verification-step">
    <div class="step-header">
      <div class="verification-icon">
        <i class="fas fa-mobile-alt"></i>
      </div>
      <h2 class="step-title">Verify Your Phone</h2>
      <p class="step-description">
        We've sent a verification code to <strong>{{ phone }}</strong>
      </p>
      <p class="step-subdescription">
        Please enter the 4-digit code below
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="verification-form">
      <div class="form-group">
        <label for="otp_code" class="form-label">OTP Code</label>
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
        <span class="button-text">{{ loading ? 'Verifying...' : 'Verify Phone' }}</span>
      </button>
    </form>

    <div class="resend-section">
      <p class="resend-text">Didn't receive the code?</p>
      <button 
        type="button" 
        class="resend-button" 
        @click="handleResend"
        :disabled="loading || resendCooldown > 0"
      >
        <span v-if="resendCooldown > 0">Resend in {{ resendCooldown }}s</span>
        <span v-else>Resend OTP</span>
      </button>
    </div>

    <div class="help-section">
      <div class="help-item">
        <i class="fas fa-info-circle"></i>
        <span>Make sure your phone can receive SMS messages</span>
      </div>
      <div class="help-item">
        <i class="fas fa-clock"></i>
        <span>The OTP code expires in 5 minutes</span>
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
      otpDigits: ['', '', '', ''],
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
          phone_number: this.phone,
          otp_code: this.otpCode
        });
      }
    },
    handleOtpInput(index, event) {
      const value = event.target.value.replace(/\D/g, ''); // Only allow digits
      
      if (value) {
        this.otpDigits[index] = value;
        
        // Move to next input
        if (index < 3) {
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
      if (event.key === 'ArrowRight' && index < 3) {
        this.$refs[`otpInput${index + 1}`][0].focus();
      }
    },
    handlePaste(event) {
      event.preventDefault();
      const pastedData = event.clipboardData.getData('text').replace(/\D/g, '');
      
      if (pastedData.length === 4) {
        for (let i = 0; i < 4; i++) {
          this.otpDigits[i] = pastedData[i] || '';
        }
        
        // Focus last input or submit if complete
        if (this.isOtpComplete) {
          this.handleSubmit();
        } else {
          this.$refs[`otpInput3`][0].focus();
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
      this.otpDigits = ['', '', '', ''];
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
  gap: 1rem;
  margin-bottom: 1rem;
}

.otp-digit-input {
  width: 60px;
  height: 60px;
  text-align: center;
  font-size: 1.5rem;
  font-weight: 600;
  border: 2px solid #e1e5e9;
  border-radius: 12px;
  background: white;
  transition: all 0.3s ease;
}

.otp-digit-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
  color: #667eea;
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
  color: #667eea;
  width: 16px;
}
</style>
