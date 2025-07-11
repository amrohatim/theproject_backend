<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">Email Verification</h2>
      <p class="text-gray-600">
        We've sent a verification code to <strong>{{ email }}</strong>. 
        Please enter the 6-digit code below.
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- Email Display -->
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
          <i class="fas fa-envelope text-blue-500 mr-3"></i>
          <div>
            <p class="text-sm font-medium text-blue-900">Verification email sent to:</p>
            <p class="text-blue-700">{{ email }}</p>
          </div>
        </div>
      </div>

      <!-- Verification Code Input -->
      <div>
        <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-2">
          Verification Code <span class="text-red-500">*</span>
        </label>
        <div class="flex justify-center">
          <div class="flex space-x-2">
            <input
              v-for="(digit, index) in verificationCode"
              :key="index"
              :ref="`digit-${index}`"
              v-model="verificationCode[index]"
              type="text"
              maxlength="1"
              class="w-12 h-12 text-center text-xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
              :disabled="loading"
              @input="handleDigitInput(index, $event)"
              @keydown="handleKeyDown(index, $event)"
              @paste="handlePaste($event)"
            />
          </div>
        </div>
        <div v-if="errors.verification_code" class="mt-2 text-sm text-red-600 text-center">
          {{ errors.verification_code }}
        </div>
      </div>

      <!-- Timer and Resend -->
      <div class="text-center">
        <div v-if="timeLeft > 0" class="text-sm text-gray-600 mb-2">
          <i class="fas fa-clock mr-1"></i>
          Resend code in {{ formatTime(timeLeft) }}
        </div>
        <button
          v-else
          type="button"
          @click="handleResend"
          :disabled="loading || resendLoading"
          class="text-blue-600 hover:text-blue-800 text-sm font-medium underline disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="resendLoading">
            <i class="fas fa-spinner fa-spin mr-1"></i>
            Sending...
          </span>
          <span v-else>
            <i class="fas fa-redo mr-1"></i>
            Resend verification code
          </span>
        </button>
      </div>

      <!-- Submit Button -->
      <div class="pt-4">
        <button
          type="submit"
          :disabled="loading || !isCodeComplete"
          class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Verifying...
          </span>
          <span v-else class="flex items-center justify-center">
            Verify Email
            <i class="fas fa-arrow-right ml-2"></i>
          </span>
        </button>
      </div>

      <!-- Help Text -->
      <div class="text-center text-sm text-gray-500">
        <p>Didn't receive the email? Check your spam folder or</p>
        <button
          type="button"
          @click="handleResend"
          :disabled="loading || resendLoading || timeLeft > 0"
          class="text-blue-600 hover:text-blue-800 underline disabled:opacity-50 disabled:cursor-not-allowed"
        >
          request a new code
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'EmailVerificationStep',
  props: {
    email: {
      type: String,
      required: true,
    },
    registrationToken: {
      type: String,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['submit', 'resend'],
  data() {
    return {
      verificationCode: ['', '', '', '', '', ''],
      errors: {},
      timeLeft: 60, // 60 seconds countdown
      timer: null,
      resendLoading: false,
    };
  },
  computed: {
    isCodeComplete() {
      return this.verificationCode.every(digit => digit !== '');
    },
    codeString() {
      return this.verificationCode.join('');
    },
  },
  mounted() {
    this.startTimer();
    // Focus first input
    this.$nextTick(() => {
      if (this.$refs['digit-0'] && this.$refs['digit-0'][0]) {
        this.$refs['digit-0'][0].focus();
      }
    });
  },
  beforeUnmount() {
    if (this.timer) {
      clearInterval(this.timer);
    }
  },
  methods: {
    handleDigitInput(index, event) {
      const value = event.target.value;

      // Only allow numbers
      if (!/^\d*$/.test(value)) {
        this.verificationCode[index] = '';
        return;
      }

      this.verificationCode[index] = value;

      // Move to next input if current is filled
      if (value && index < 5) {
        const nextInput = this.$refs[`digit-${index + 1}`];
        if (nextInput && nextInput[0]) {
          nextInput[0].focus();
        }
      }

      // Auto-submit when all digits are filled (with debounce to prevent duplicates)
      if (this.isCodeComplete && !this.loading) {
        this.$nextTick(() => {
          this.handleSubmit();
        });
      }
    },

    handleKeyDown(index, event) {
      // Handle backspace
      if (event.key === 'Backspace' && !this.verificationCode[index] && index > 0) {
        const prevInput = this.$refs[`digit-${index - 1}`];
        if (prevInput && prevInput[0]) {
          prevInput[0].focus();
        }
      }
    },

    handlePaste(event) {
      event.preventDefault();
      const pastedData = event.clipboardData.getData('text');
      const digits = pastedData.replace(/\D/g, '').slice(0, 6).split('');
      
      for (let i = 0; i < 6; i++) {
        this.verificationCode[i] = digits[i] || '';
      }

      // Focus the last filled input or first empty one
      const lastFilledIndex = digits.length - 1;
      const targetIndex = Math.min(lastFilledIndex + 1, 5);
      const targetInput = this.$refs[`digit-${targetIndex}`];
      if (targetInput && targetInput[0]) {
        targetInput[0].focus();
      }
    },

    handleSubmit() {
      if (this.isCodeComplete && !this.loading) {
        this.errors = {};
        this.$emit('submit', {
          registration_token: this.registrationToken,
          verification_code: this.codeString
        });
      }
    },

    async handleResend() {
      this.resendLoading = true;
      this.errors = {};
      
      try {
        await this.$emit('resend');
        this.timeLeft = 60;
        this.startTimer();
        // Clear the code inputs
        this.verificationCode = ['', '', '', '', '', ''];
        // Focus first input
        this.$nextTick(() => {
          if (this.$refs['digit-0'] && this.$refs['digit-0'][0]) {
            this.$refs['digit-0'][0].focus();
          }
        });
      } catch (error) {
        this.errors.verification_code = 'Failed to resend verification code';
      } finally {
        this.resendLoading = false;
      }
    },

    startTimer() {
      if (this.timer) {
        clearInterval(this.timer);
      }
      
      this.timer = setInterval(() => {
        if (this.timeLeft > 0) {
          this.timeLeft--;
        } else {
          clearInterval(this.timer);
        }
      }, 1000);
    },

    formatTime(seconds) {
      const mins = Math.floor(seconds / 60);
      const secs = seconds % 60;
      return `${mins}:${secs.toString().padStart(2, '0')}`;
    },
  },
};
</script>
