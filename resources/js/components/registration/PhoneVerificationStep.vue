<template>
  <div>
b    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $t('phone_verification') }}</h2>
      <p class="text-gray-600">
        {{ $t('well_send_otp_code_to') }} <strong>{{ phone }}</strong> {{ $t('to_verify_your_phone_number') }}.
      </p>
    </div>

    <!-- Step 1: Send OTP -->
    <div v-if="!otpSent" class="space-y-6">
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
          <i class="fas fa-mobile-alt text-blue-500 mr-3"></i>
          <div>
            <p class="text-sm font-medium text-blue-900">{{ $t('phone_number_to_verify') }}:</p>
            <p class="text-blue-700">{{ phone }}</p>
          </div>
        </div>
      </div>

      <button
        @click="sendOtp"
        :disabled="loading"
        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <span v-if="loading" class="flex items-center justify-center">
          <i class="fas fa-spinner fa-spin mr-2"></i>
          {{ $t('sending_otp') }}...
        </span>
        <span v-else class="flex items-center justify-center">
          <i class="fas fa-paper-plane mr-2"></i>
          {{ $t('send_otp_code') }}
        </span>
      </button>
    </div>

    <!-- Step 2: Verify OTP -->
    <div v-else class="space-y-6">
      <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
          <i class="fas fa-check-circle text-green-500 mr-3"></i>
          <div>
            <p class="text-sm font-medium text-green-900">{{ $t('otp_sent_successfully') }}!</p>
            <p class="text-green-700">{{ $t('check_your_phone_for_verification_code') }}</p>
          </div>
        </div>
      </div>

      <form @submit.prevent="handleSubmit">
        <!-- OTP Code Input -->
        <div class="mb-6">
          <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-2">
            {{ $t('enter_otp_code') }} <span class="text-red-500">*</span>
          </label>
          <div class="flex justify-center">
            <div class="flex space-x-2">
              <input
                v-for="(digit, index) in otpCode"
                :key="index"
                :ref="`otp-${index}`"
                v-model="otpCode[index]"
                type="text"
                maxlength="1"
                class="w-12 h-12 text-center text-xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                :disabled="loading"
                @input="handleOtpInput(index, $event)"
                @keydown="handleKeyDown(index, $event)"
                @paste="handlePaste($event)"
              />
            </div>
          </div>
          <div v-if="errors.otp_code" class="mt-2 text-sm text-red-600 text-center">
            {{ errors.otp_code }}
          </div>
        </div>

        <!-- Timer and Resend -->
        <div class="text-center mb-6">
          <div v-if="timeLeft > 0" class="text-sm text-gray-600 mb-2">
            <i class="fas fa-clock mr-1"></i>
            {{ $t('resend_otp_in') }} {{ formatTime(timeLeft) }}
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
              {{ $t('sending') }}...
            </span>
            <span v-else>
              <i class="fas fa-redo mr-1"></i>
              {{ $t('resend_otp') }}
            </span>
          </button>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="loading || !isOtpComplete"
          class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            {{ $t('verifying') }}...
          </span>
          <span v-else class="flex items-center justify-center">
            {{ $t('verify_phone_number') }}
            <i class="fas fa-arrow-right ml-2"></i>
          </span>
        </button>
      </form>

      <!-- Help Text -->
      <div class="text-center text-sm text-gray-500">
        <p>{{ $t('didnt_receive_otp_make_sure_phone_on_and') }}</p>
        <button
          type="button"
          @click="handleResend"
          :disabled="loading || resendLoading || timeLeft > 0"
          class="text-blue-600 hover:text-blue-800 underline disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ $t('try_sending_again') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PhoneVerificationStep',
  props: {
    phone: {
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
      otpSent: false,
      otpCode: ['', '', '', '', '', ''],
      errors: {},
      timeLeft: 0,
      timer: null,
      resendLoading: false,
    };
  },
  computed: {
    isOtpComplete() {
      return this.otpCode.every(digit => digit !== '');
    },
    otpString() {
      return this.otpCode.join('');
    },
  },
  beforeUnmount() {
    if (this.timer) {
      clearInterval(this.timer);
    }
  },
  methods: {
    // Translation method
    $t(key, replacements = {}) {
      const translations = {
        'phone_verification': 'Phone Verification',
        'well_send_otp_code_to': "We'll send an OTP code to",
        'to_verify_your_phone_number': 'to verify your phone number',
        'phone_number_to_verify': 'Phone number to verify',
        'sending_otp': 'Sending OTP',
        'send_otp_code': 'Send OTP Code',
        'otp_sent_successfully': 'OTP sent successfully',
        'check_your_phone_for_verification_code': 'Check your phone for the verification code',
        'enter_otp_code': 'Enter OTP Code',
        'resend_otp_in': 'Resend OTP in',
        'sending': 'Sending',
        'resend_otp': 'Resend OTP',
        'verifying': 'Verifying',
        'verify_phone_number': 'Verify Phone Number',
        'didnt_receive_otp_make_sure_phone_on_and': "Didn't receive the OTP? Make sure your phone is on and",
        'try_sending_again': 'try sending again',
        'failed_to_send_otp_please_try_again': 'Failed to send OTP. Please try again.'
      }
      
      let translation = translations[key] || key
      
      // Handle replacements
      Object.keys(replacements).forEach(placeholder => {
        translation = translation.replace(`[${placeholder}]`, replacements[placeholder])
      })
      
      return translation
    },
    async sendOtp() {
      this.errors = {};
      
      try {
        await this.$emit('resend'); // This will trigger the parent's resend method
        this.otpSent = true;
        this.timeLeft = 120; // 2 minutes
        this.startTimer();
        
        // Focus first OTP input
        this.$nextTick(() => {
          if (this.$refs['otp-0'] && this.$refs['otp-0'][0]) {
            this.$refs['otp-0'][0].focus();
          }
        });
      } catch (error) {
        this.errors.otp_code = this.$t('failed_to_send_otp_please_try_again');
      }
    },

    handleOtpInput(index, event) {
      const value = event.target.value;
      
      // Only allow numbers
      if (!/^\d*$/.test(value)) {
        this.otpCode[index] = '';
        return;
      }

      this.otpCode[index] = value;

      // Move to next input if current is filled
      if (value && index < 5) {
        const nextInput = this.$refs[`otp-${index + 1}`];
        if (nextInput && nextInput[0]) {
          nextInput[0].focus();
        }
      }

      // Auto-submit when all digits are filled
      if (this.isOtpComplete) {
        this.$nextTick(() => {
          this.handleSubmit();
        });
      }
    },

    handleKeyDown(index, event) {
      // Handle backspace
      if (event.key === 'Backspace' && !this.otpCode[index] && index > 0) {
        const prevInput = this.$refs[`otp-${index - 1}`];
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
        this.otpCode[i] = digits[i] || '';
      }

      // Focus the last filled input or first empty one
      const lastFilledIndex = digits.length - 1;
      const targetIndex = Math.min(lastFilledIndex + 1, 5);
      const targetInput = this.$refs[`otp-${targetIndex}`];
      if (targetInput && targetInput[0]) {
        targetInput[0].focus();
      }
    },

    handleSubmit() {
      if (this.isOtpComplete) {
        this.errors = {};
        this.$emit('submit', this.otpString);
      }
    },

    async handleResend() {
      this.resendLoading = true;
      this.errors = {};
      
      try {
        await this.$emit('resend');
        this.timeLeft = 120;
        this.startTimer();
        // Clear the OTP inputs
        this.otpCode = ['', '', '', '', '', ''];
        // Focus first input
        this.$nextTick(() => {
          if (this.$refs['otp-0'] && this.$refs['otp-0'][0]) {
            this.$refs['otp-0'][0].focus();
          }
        });
      } catch (error) {
        this.errors.otp_code = 'Failed to resend OTP';
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
