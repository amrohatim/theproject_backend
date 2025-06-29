<template>
  <div class="license-upload-step">
    <div class="step-header">
      <div class="upload-icon">
        <i class="fas fa-certificate"></i>
      </div>
      <h2 class="step-title">Upload Business License</h2>
      <p class="step-description">
        Please upload your business license to complete your registration
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="license-form">
      <!-- License File Upload -->
      <div class="form-group">
        <label for="license_file" class="form-label">Business License (PDF) *</label>
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
              <h3>Drop your license here or click to browse</h3>
              <p>PDF files only, max 10MB</p>
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

      <!-- License Duration -->
      <div class="form-group">
        <label for="duration_days" class="form-label">License Duration (Days)</label>
        <select 
          id="duration_days" 
          v-model="formData.duration_days"
          class="form-input"
          :class="{ 'error': errors.duration_days }"
        >
          <option value="">Select duration</option>
          <option value="365">1 Year (365 days)</option>
          <option value="730">2 Years (730 days)</option>
          <option value="1095">3 Years (1095 days)</option>
          <option value="1825">5 Years (1825 days)</option>
        </select>
        <div v-if="errors.duration_days" class="error-message">{{ errors.duration_days[0] }}</div>
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label for="notes" class="form-label">Additional Notes (Optional)</label>
        <textarea
          id="notes"
          v-model="formData.notes"
          class="form-textarea"
          :class="{ 'error': errors.notes }"
          placeholder="Any additional information about your license..."
          rows="4"
        ></textarea>
        <div v-if="errors.notes" class="error-message">{{ errors.notes[0] }}</div>
      </div>

      <button type="submit" class="form-button" :disabled="loading || !licenseFile">
        <div v-if="loading" class="loading-spinner"></div>
        <span class="button-text">{{ loading ? 'Uploading License...' : 'Complete Registration' }}</span>
      </button>
    </form>

    <div class="info-section">
      <div class="info-item">
        <i class="fas fa-info-circle"></i>
        <span>Your license will be reviewed within 24-48 hours</span>
      </div>
      <div class="info-item">
        <i class="fas fa-shield-alt"></i>
        <span>All documents are securely encrypted and stored</span>
      </div>
      <div class="info-item">
        <i class="fas fa-check-circle"></i>
        <span>You'll receive an email confirmation once approved</span>
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
        duration_days: '',
        notes: ''
      },
      licenseFile: null,
      errors: {},
      isDragOver: false
    };
  },
  methods: {
    handleSubmit() {
      this.errors = {};
      if (this.licenseFile) {
        this.$emit('submit', {
          user_id: this.userId,
          license_file: this.licenseFile,
          duration_days: this.formData.duration_days,
          notes: this.formData.notes
        });
      }
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
        this.errors = { license_file: ['Please select a PDF file.'] };
        return;
      }

      // Validate file size (10MB max)
      if (file.size > 10 * 1024 * 1024) {
        this.errors = { license_file: ['File size must be less than 10MB.'] };
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
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    setErrors(errors) {
      this.errors = errors;
    }
  }
};
</script>

<style scoped>
.license-upload-step {
  max-width: 600px;
  margin: 0 auto;
}

.step-header {
  text-align: center;
  margin-bottom: 2rem;
}

.upload-icon {
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
  border-color: #667eea;
  background: rgba(102, 126, 234, 0.05);
}

.file-upload-area.drag-over {
  border-color: #667eea;
  background: rgba(102, 126, 234, 0.1);
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
  color: #667eea;
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
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
  color: #667eea;
  width: 16px;
}
</style>
