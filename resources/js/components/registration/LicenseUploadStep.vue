<template>
  <div>
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">License Upload</h2>
      <p class="text-gray-600">Upload your business license or registration document to complete your vendor registration.</p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-6">
      <!-- File Upload Area -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Business License Document <span class="text-red-500">*</span>
        </label>
        
        <!-- Drag and Drop Area -->
        <div
          @drop="handleDrop"
          @dragover="handleDragOver"
          @dragenter="handleDragEnter"
          @dragleave="handleDragLeave"
          :class="[
            'border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer',
            isDragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400'
          ]"
          @click="triggerFileInput"
        >
          <input
            ref="fileInput"
            type="file"
            accept=".pdf"
            @change="handleFileSelect"
            class="hidden"
            :disabled="loading"
          />
          
          <div v-if="!selectedFile">
            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
            <p class="text-lg font-medium text-gray-700 mb-2">
              Drop your license file here, or click to browse
            </p>
            <p class="text-sm text-gray-500">
              PDF files only, maximum 10MB
            </p>
          </div>
          
          <div v-else class="flex items-center justify-center">
            <i class="fas fa-file-pdf text-red-500 text-3xl mr-3"></i>
            <div class="text-left">
              <p class="font-medium text-gray-900">{{ selectedFile.name }}</p>
              <p class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
            </div>
            <button
              type="button"
              @click.stop="removeFile"
              class="ml-4 text-red-500 hover:text-red-700"
              :disabled="loading"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        
        <div v-if="errors.license_file" class="mt-2 text-sm text-red-600">
          {{ errors.license_file[0] }}
        </div>
      </div>

      <!-- License Start Date -->
      <div>
        <label for="license_start_date" class="block text-sm font-medium text-gray-700 mb-2">
          License Start Date *
        </label>
        <input
          type="date"
          id="license_start_date"
          v-model="formData.license_start_date"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :class="{ 'border-red-500': errors.license_start_date }"
          :min="todayDate"
          :disabled="loading"
          required
        />
        <div v-if="errors.license_start_date" class="mt-1 text-sm text-red-600">
          {{ errors.license_start_date[0] }}
        </div>
        <div class="mt-1 text-sm text-gray-500">
          Select the start date of your business license (cannot be in the past)
        </div>
      </div>

      <!-- License Expiry Date -->
      <div>
        <label for="license_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
          License Expiration Date *
        </label>
        <input
          type="date"
          id="license_expiry_date"
          v-model="formData.license_expiry_date"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          :class="{ 'border-red-500': errors.license_expiry_date }"
          :min="formData.license_start_date || minDate"
          :disabled="loading"
          required
        />
        <div v-if="errors.license_expiry_date" class="mt-1 text-sm text-red-600">
          {{ errors.license_expiry_date[0] }}
        </div>
        <div class="mt-1 text-sm text-gray-500">
          Select the expiration date of your business license (must be after start date)
        </div>
      </div>

      <!-- Additional Notes -->
      <div>
        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
          Additional Notes
        </label>
        <textarea
          id="notes"
          v-model="formData.notes"
          rows="3"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
          placeholder="Any additional information about your license or business..."
          :disabled="loading"
        ></textarea>
        <div v-if="errors.notes" class="mt-1 text-sm text-red-600">
          {{ errors.notes[0] }}
        </div>
        <div class="mt-1 text-sm text-gray-500">
          Optional: Provide any relevant details about your license or registration
        </div>
      </div>

      <!-- Important Notice -->
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
          <i class="fas fa-exclamation-triangle text-yellow-500 mr-3 mt-1"></i>
          <div class="text-sm">
            <p class="font-medium text-yellow-900 mb-1">Important Notice</p>
            <p class="text-yellow-700">
              Your uploaded license will be reviewed by our team. You will receive an email notification 
              once your vendor account is approved and activated.
            </p>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="pt-4">
        <button
          type="submit"
          :disabled="loading || !selectedFile || !formData.license_start_date || !formData.license_expiry_date"
          class="w-full px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span v-if="loading" class="flex items-center justify-center">
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Uploading...
          </span>
          <span v-else class="flex items-center justify-center">
            <i class="fas fa-check mr-2"></i>
            Complete Registration
          </span>
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'LicenseUploadStep',
  props: {
    userId: {
      type: [String, Number],
      required: false,
      default: null,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['submit'],
  data() {
    return {
      selectedFile: null,
      isDragging: false,
      formData: {
        license_start_date: '',
        license_expiry_date: '',
        notes: '',
      },
      errors: {},
    };
  },
  computed: {
    todayDate() {
      // Set minimum date to today for start date
      const today = new Date();
      return today.toISOString().split('T')[0];
    },
    minDate() {
      // Set minimum date to tomorrow for expiry date
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      return tomorrow.toISOString().split('T')[0];
    }
  },
  methods: {
    triggerFileInput() {
      if (!this.loading) {
        this.$refs.fileInput.click();
      }
    },

    handleFileSelect(event) {
      const file = event.target.files[0];
      this.validateAndSetFile(file);
    },

    handleDrop(event) {
      event.preventDefault();
      this.isDragging = false;
      
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        this.validateAndSetFile(files[0]);
      }
    },

    handleDragOver(event) {
      event.preventDefault();
    },

    handleDragEnter(event) {
      event.preventDefault();
      this.isDragging = true;
    },

    handleDragLeave(event) {
      event.preventDefault();
      this.isDragging = false;
    },

    validateAndSetFile(file) {
      this.errors = {};

      if (!file) {
        return;
      }

      // Check file type
      if (file.type !== 'application/pdf') {
        this.errors.license_file = ['Please select a PDF file.'];
        return;
      }

      // Check file size (10MB = 10 * 1024 * 1024 bytes)
      const maxSize = 10 * 1024 * 1024;
      if (file.size > maxSize) {
        this.errors.license_file = ['File size must be less than 10MB.'];
        return;
      }

      this.selectedFile = file;
    },

    removeFile() {
      this.selectedFile = null;
      this.$refs.fileInput.value = '';
      this.errors = {};
    },

    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    handleSubmit() {
      this.errors = {};

      // Validate required fields
      if (!this.selectedFile) {
        this.errors.license_file = ['Please select a license file.'];
        return;
      }

      if (!this.formData.license_start_date) {
        this.errors.license_start_date = ['Please select a license start date.'];
        return;
      }

      if (!this.formData.license_expiry_date) {
        this.errors.license_expiry_date = ['Please select a license expiration date.'];
        return;
      }

      // Validate that the start date is not in the past
      const startDate = new Date(this.formData.license_start_date);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      if (startDate < today) {
        this.errors.license_start_date = ['License start date cannot be in the past.'];
        return;
      }

      // Validate that the expiry date is after the start date
      const expiryDate = new Date(this.formData.license_expiry_date);
      if (expiryDate <= startDate) {
        this.errors.license_expiry_date = ['License expiration date must be after the start date.'];
        return;
      }

      const licenseData = {
        license_file: this.selectedFile,
        license_start_date: this.formData.license_start_date,
        license_expiry_date: this.formData.license_expiry_date,
        notes: this.formData.notes || null,
      };

      this.$emit('submit', licenseData);
    },
  },
};
</script>
