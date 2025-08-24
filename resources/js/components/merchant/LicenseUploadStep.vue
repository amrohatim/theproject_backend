<template>
  <div class="license-upload-step">
    <div class="step-header">
      <div class="upload-icon">
        <i class="fas fa-certificate"></i>
      </div>
      <h2 class="step-title">{{ $t('upload_business_license') }}</h2>
      <p class="step-description">
        {{ $t('upload_license_description_merchant') }}
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="license-form">
      <!-- License File Upload -->
      <div class="form-group">
        <label for="license_file" class="form-label">{{ $t('business_license_pdf') }} *</label>
        <div class="file-upload-area" :class="{ 'has-file': licenseFile, 'drag-over': isDragOver }">
          <input 
            type="file" 
            id="license_file" 
            ref="licenseInput"
            @change="handleLicenseUpload"
            @dragover.prevent="isDragOver = true"
            @dragleave.prevent="isDragOver = false"
            @drop.prevent="handleDrop"
            accept=".pdf"
            class="file-input"
            required
          >
          <div class="file-upload-content">
            <div v-if="!licenseFile" class="upload-placeholder">
              <i class="fas fa-cloud-upload-alt upload-icon-large"></i>
              <h3>{{ $t('drop_license_or_browse') }}</h3>
              <p>{{ $t('pdf_files_max_10mb') }}</p>
            </div>
            <div v-else class="file-preview">
              <div class="file-info">
                <i class="fas fa-file-pdf file-icon"></i>
                <div class="file-details">
                  <h4>{{ licenseFile.name }}</h4>
                  <p>{{ formatFileSize(licenseFile.size) }}</p>
                </div>
              </div>
              <button type="button" class="remove-file-btn" @click="removeLicenseFile">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>
        <div v-if="errors.license_file" class="error-message">{{ errors.license_file[0] }}</div>
      </div>

      <!-- License Start Date -->
      <div class="form-group">
        <label for="license_start_date" class="form-label">{{ $t('license_start_date') }} <span class="required">*</span></label>
        <input
          type="date"
          id="license_start_date"
          v-model="formData.license_start_date"
          class="form-input"
          :class="{ 'error': errors.license_start_date }"
          :min="minStartDate"
          required
        />
        <div v-if="errors.license_start_date" class="error-message">{{ errors.license_start_date[0] }}</div>
      </div>

      <!-- License End Date -->
      <div class="form-group">
        <label for="license_end_date" class="form-label">{{ $t('license_end_date') }} <span class="required">*</span></label>
        <input
          type="date"
          id="license_end_date"
          v-model="formData.license_end_date"
          class="form-input"
          :class="{ 'error': errors.license_end_date }"
          :min="minEndDate"
          required
        />
        <div v-if="errors.license_end_date" class="error-message">{{ errors.license_end_date[0] }}</div>
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label for="notes" class="form-label">{{ $t('additional_notes_optional') }}</label>
        <textarea
          id="notes"
          v-model="formData.notes"
          class="form-textarea"
          :class="{ 'error': errors.notes }"
          :placeholder="$t('license_additional_info_placeholder')"
          rows="4"
        ></textarea>
        <div v-if="errors.notes" class="error-message">{{ errors.notes[0] }}</div>
      </div>

      <button type="submit" class="form-button" :disabled="loading || !licenseFile">
        <div v-if="loading" class="loading-spinner"></div>
        <span class="button-text">{{ loading ? $t('uploading_license') : $t('complete_registration') }}</span>
      </button>
    </form>

    <div class="info-section">
      <div class="info-item">
        <i class="fas fa-info-circle"></i>
        <span>{{ $t('license_review_24_48_hours') }}</span>
      </div>
      <div class="info-item">
        <i class="fas fa-shield-alt"></i>
        <span>{{ $t('documents_securely_encrypted') }}</span>
      </div>
      <div class="info-item">
        <i class="fas fa-check-circle"></i>
        <span>{{ $t('email_confirmation_once_approved') }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LicenseUploadStep',
  props: {
    userId: {
      type: [String, Number],
      required: true
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['submit'],
  data() {
    return {
      formData: {
        license_start_date: '',
        license_end_date: '',
        notes: ''
      },
      licenseFile: null,
      errors: {},
      isDragOver: false
    };
  },
  computed: {
    minStartDate() {
      // Start date cannot be in the past
      const today = new Date();
      return today.toISOString().split('T')[0];
    },
    minEndDate() {
      // End date must be after start date
      if (this.formData.license_start_date) {
        const startDate = new Date(this.formData.license_start_date);
        startDate.setDate(startDate.getDate() + 1); // At least one day after start date
        return startDate.toISOString().split('T')[0];
      }
      return this.minStartDate;
    }
  },
  methods: {
    handleSubmit() {
      this.errors = {};

      // Validate dates
      if (!this.validateDates()) {
        return;
      }

      if (this.licenseFile) {
        this.$emit('submit', {
          user_id: this.userId,
          license_file: this.licenseFile,
          license_start_date: this.formData.license_start_date,
          license_end_date: this.formData.license_end_date,
          notes: this.formData.notes
        });
      }
    },
    validateDates() {
      let isValid = true;

      // Check if start date is provided
      if (!this.formData.license_start_date) {
        this.errors.license_start_date = [this.$t('license_start_date_required')];
        isValid = false;
      }

      // Check if end date is provided
      if (!this.formData.license_end_date) {
        this.errors.license_end_date = [this.$t('license_end_date_required')];
        isValid = false;
      }

      // Check if both dates are provided for further validation
      if (this.formData.license_start_date && this.formData.license_end_date) {
        const startDate = new Date(this.formData.license_start_date);
        const endDate = new Date(this.formData.license_end_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Check if start date is not in the past
        if (startDate < today) {
          this.errors.license_start_date = [this.$t('license_start_date_cannot_past')];
          isValid = false;
        }

        // Check if end date is after start date
        if (endDate <= startDate) {
          this.errors.license_end_date = [this.$t('license_end_date_after_start')];
          isValid = false;
        }
      }

      return isValid;
    },
    handleLicenseUpload(event) {
      const file = event.target.files[0];
      this.validateAndSetFile(file);
    },
    handleDrop(event) {
      this.isDragOver = false;
      const file = event.dataTransfer.files[0];
      this.validateAndSetFile(file);
    },
    validateAndSetFile(file) {
      if (!file) return;

      // Validate file type
      if (file.type !== 'application/pdf') {
        this.errors = { license_file: [this.$t('please_select_pdf_file')] };
        return;
      }

      // Validate file size (10MB max)
      if (file.size > 10 * 1024 * 1024) {
        this.errors = { license_file: [this.$t('file_size_less_10mb')] };
        return;
      }

      this.licenseFile = file;
      this.errors = {};
    },
    removeLicenseFile() {
      this.licenseFile = null;
      this.$refs.licenseInput.value = '';
    },
    formatFileSize(bytes) {
      if (bytes === 0) return '0 ' + this.$t('bytes');
      const k = 1024;
      const sizes = [this.$t('bytes'), this.$t('kb'), this.$t('mb'), this.$t('gb')];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    setErrors(errors) {
      this.errors = errors;
    }
  },
  watch: {
    'formData.license_start_date'() {
      // Clear start date error when changed
      if (this.errors.license_start_date) {
        delete this.errors.license_start_date;
      }
      // Clear end date error if it was due to date comparison
      if (this.errors.license_end_date && this.formData.license_end_date) {
        delete this.errors.license_end_date;
      }
    },
    'formData.license_end_date'() {
      // Clear end date error when changed
      if (this.errors.license_end_date) {
        delete this.errors.license_end_date;
      }
    }
  }
};
</script>

<style scoped>
.license-upload-step {
  max-width: 600px;
  margin: 0 auto;
  padding: 1rem;
}

/* Override global styles that might interfere */
.license-upload-step .form-group {
  margin-bottom: 1.5rem;
  position: relative;
  z-index: 1;
}

.license-upload-step .form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.license-upload-step .form-input {
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

.license-upload-step .form-input:focus {
  outline: none !important;
  border-color: #f59e0b !important;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
  transform: none !important;
}

.license-upload-step .form-input:hover {
  border-color: #f59e0b !important;
  background: white !important;
  transform: none !important;
}

.license-upload-step .form-input.error {
  border-color: #e53e3e !important;
  box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1) !important;
}

.license-upload-step .form-button {
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

.license-upload-step .form-button:hover:not(:disabled) {
  background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.license-upload-step .form-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.license-upload-step .error-message {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 0.5rem;
  text-align: left;
}

.step-header {
  text-align: center;
  margin-bottom: 2rem;
}

.upload-icon {
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
}

.file-upload-area {
  border: 2px dashed #e1e5e9;
  border-radius: 12px;
  padding: 2rem;
  text-align: center;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
}

.file-upload-area:hover {
  border-color: #f59e0b;
  background: rgba(245, 158, 11, 0.05);
}

.file-upload-area.drag-over {
  border-color: #f59e0b;
  background: rgba(245, 158, 11, 0.1);
}

.file-upload-area.has-file {
  border-color: #48bb78;
  background: rgba(72, 187, 120, 0.05);
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
}

.upload-placeholder h3 {
  color: #333;
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.upload-placeholder p {
  color: #666;
  font-size: 0.9rem;
}

.upload-icon-large {
  font-size: 3rem;
  color: #f59e0b;
  margin-bottom: 1rem;
}

.file-preview {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.file-info {
  display: flex;
  align-items: center;
}

.file-icon {
  font-size: 2rem;
  color: #e53e3e;
  margin-right: 1rem;
}

.file-details h4 {
  color: #333;
  margin-bottom: 0.25rem;
  font-size: 1rem;
}

.file-details p {
  color: #666;
  font-size: 0.9rem;
}

.remove-file-btn {
  background: #e53e3e;
  color: white;
  border: none;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s ease;
}

.remove-file-btn:hover {
  background: #c53030;
}

.form-textarea {
  width: 100%;
  padding: 15px 20px;
  border: 2px solid #e1e5e9;
  border-radius: 12px;
  font-size: 1rem;
  font-family: inherit;
  resize: vertical;
  min-height: 100px;
  transition: all 0.3s ease;
}

.form-textarea:focus {
  outline: none;
  border-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

.info-section {
  margin-top: 2rem;
  padding: 1.5rem;
  background: #f8f9fa;
  border-radius: 12px;
}

.info-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
  color: #666;
  font-size: 0.9rem;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-item i {
  margin-right: 0.75rem;
  color: #f59e0b;
  width: 16px;
}

.required {
  color: #e53e3e;
  font-weight: bold;
}
</style>
