<template>
  <div class="merchant-info-step">
    <div class="step-header">
      <h2 class="step-title">Business Information</h2>
      <p class="step-description">Please provide your business details and UAE ID</p>
    </div>

    <form @submit.prevent="handleSubmit" class="merchant-form">
      <!-- Business Name -->
      <div class="form-group">
        <label for="name" class="form-label">Business Name *</label>
        <input
          type="text"
          id="name"
          v-model="formData.name"
          class="form-input"
          :class="{ 'error': errors.name }"
          placeholder="Enter your business name"
          required
          @input="updateField('name', $event.target.value)"
        >
        <div v-if="errors.name" class="error-message">{{ errors.name[0] }}</div>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label for="email" class="form-label">Email Address *</label>
        <input
          type="email"
          id="email"
          v-model="formData.email"
          class="form-input"
          :class="{ 'error': errors.email }"
          placeholder="Enter your business email address"
          required
          @input="updateField('email', $event.target.value)"
        >
        <div v-if="errors.email" class="error-message">{{ errors.email[0] }}</div>
      </div>

      <!-- Phone -->
      <div class="form-group">
        <label for="phone" class="form-label">Phone Number *</label>
        <div class="phone-input-container">
          <div class="country-code-display">
            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMTgiIHZpZXdCb3g9IjAgMCAyNCAxOCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjI0IiBoZWlnaHQ9IjYiIGZpbGw9IiMwMDczMzMiLz4KPHJlY3QgeT0iNiIgd2lkdGg9IjI0IiBoZWlnaHQ9IjYiIGZpbGw9IiNGRkZGRkYiLz4KPHJlY3QgeT0iMTIiIHdpZHRoPSIyNCIgaGVpZ2h0PSI2IiBmaWxsPSIjRkYwMDAwIi8+Cjwvc3ZnPgo=" alt="UAE Flag" class="flag-icon">
            <span class="country-code">+971</span>
          </div>
          <input
            type="tel"
            id="phone"
            v-model="phoneNumber"
            class="form-input phone-input"
            :class="{ 'error': errors.phone }"
            placeholder="50 123 4567"
            required
            maxlength="11"
            @input="handlePhoneInput"
            @keypress="validatePhoneKeypress"
          >
        </div>
        <div v-if="errors.phone" class="error-message">{{ errors.phone[0] }}</div>
      </div>

      <!-- Password -->
      <div class="form-group">
        <label for="password" class="form-label">Password *</label>
        <input
          type="password"
          id="password"
          v-model="formData.password"
          class="form-input"
          :class="{ 'error': errors.password }"
          placeholder="Create a strong password"
          required
          @input="updateField('password', $event.target.value)"
        >
        <div v-if="errors.password" class="error-message">{{ errors.password[0] }}</div>
      </div>

      <!-- Confirm Password -->
      <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm Password *</label>
        <input
          type="password"
          id="password_confirmation"
          v-model="formData.password_confirmation"
          class="form-input"
          :class="{ 'error': errors.password_confirmation }"
          placeholder="Confirm your password"
          required
          @input="updateField('password_confirmation', $event.target.value)"
        >
        <div v-if="errors.password_confirmation" class="error-message">{{ errors.password_confirmation[0] }}</div>
      </div>

      <!-- Logo Upload -->
      <div class="form-group">
        <label for="logo" class="form-label">Business Logo (Optional)</label>
        <div class="file-upload" :class="{ 'has-image': logoPreviewUrl }">
          <input
            type="file"
            id="logo"
            ref="logoInput"
            @change="handleLogoUpload"
            accept="image/*"
            class="file-input"
          >
          <label for="logo" class="file-upload-label">
            <div v-if="logoPreviewUrl" class="image-preview">
              <img :src="logoPreviewUrl" :alt="logoFileName" class="preview-thumbnail">
              <div class="image-info">
                <span class="file-name">{{ logoFileName }}</span>
                <span class="change-text">Click to change</span>
              </div>
            </div>
            <div v-else class="upload-placeholder">
              <i class="fas fa-cloud-upload-alt"></i>
              <span>Click to upload logo</span>
            </div>
          </label>
        </div>
        <div v-if="errors.logo" class="error-message">{{ errors.logo[0] }}</div>
      </div>

      <!-- UAE ID Front -->
      <div class="form-group">
        <label for="uae_id_front" class="form-label">UAE ID Front Side *</label>
        <div class="file-upload" :class="{ 'has-image': uaeIdFrontPreviewUrl }">
          <input
            type="file"
            id="uae_id_front"
            ref="uaeIdFrontInput"
            @change="handleUaeIdFrontUpload"
            accept="image/*"
            class="file-input"
            required
          >
          <label for="uae_id_front" class="file-upload-label">
            <div v-if="uaeIdFrontPreviewUrl" class="image-preview">
              <img :src="uaeIdFrontPreviewUrl" :alt="uaeIdFrontFileName" class="preview-thumbnail">
              <div class="image-info">
                <span class="file-name">{{ uaeIdFrontFileName }}</span>
                <span class="change-text">Click to change</span>
              </div>
            </div>
            <div v-else class="upload-placeholder">
              <i class="fas fa-id-card"></i>
              <span>Upload front side of UAE ID</span>
            </div>
          </label>
        </div>
        <div v-if="errors.uae_id_front" class="error-message">{{ errors.uae_id_front[0] }}</div>
      </div>

      <!-- UAE ID Back -->
      <div class="form-group">
        <label for="uae_id_back" class="form-label">UAE ID Back Side *</label>
        <div class="file-upload" :class="{ 'has-image': uaeIdBackPreviewUrl }">
          <input
            type="file"
            id="uae_id_back"
            ref="uaeIdBackInput"
            @change="handleUaeIdBackUpload"
            accept="image/*"
            class="file-input"
            required
          >
          <label for="uae_id_back" class="file-upload-label">
            <div v-if="uaeIdBackPreviewUrl" class="image-preview">
              <img :src="uaeIdBackPreviewUrl" :alt="uaeIdBackFileName" class="preview-thumbnail">
              <div class="image-info">
                <span class="file-name">{{ uaeIdBackFileName }}</span>
                <span class="change-text">Click to change</span>
              </div>
            </div>
            <div v-else class="upload-placeholder">
              <i class="fas fa-id-card"></i>
              <span>Upload back side of UAE ID</span>
            </div>
          </label>
        </div>
        <div v-if="errors.uae_id_back" class="error-message">{{ errors.uae_id_back[0] }}</div>
      </div>

      <!-- Store Location -->
      <div class="form-group">
        <label for="store_location_address" class="form-label">Store Location (Optional)</label>
        <div class="location-search-container">
          <input
            type="text"
            id="location-search"
            v-model="locationSearch"
            class="form-input"
            placeholder="Search for your store location..."
            @focus="handleLocationFocus"
            @input="handleLocationSearch"
            autocomplete="off"
          >
          <div class="search-icon">
            <i class="fas fa-search"></i>
          </div>
          <button
            v-if="formData.store_location_lat && formData.store_location_lng"
            type="button"
            class="clear-location-btn"
            @click="clearLocation"
            title="Clear location"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Map Loading State -->
        <div v-if="mapLoading" class="map-loading">
          <div class="loading-spinner"></div>
          <span>Loading map...</span>
        </div>

        <!-- Map Error State -->
        <div v-if="mapError" class="map-error">
          <i class="fas fa-exclamation-triangle"></i>
          <span>{{ mapError }}</span>
          <button type="button" @click="retryMapLoad" class="retry-btn">Retry</button>
        </div>

        <!-- Map Container -->
        <div v-if="showMap" id="map-container">
          <!-- Always render the google-map element, but control visibility -->
          <div id="google-map"
               :style="{
                 height: '350px',
                 width: '100%',
                 borderRadius: '8px',
                 marginTop: '10px',
                 display: mapLoading || mapError ? 'none' : 'block'
               }">
          </div>

          <!-- Loading State Overlay -->
          <div v-if="mapLoading" class="map-loading">
            <div class="loading-spinner"></div>
            <p>Loading Google Maps...</p>
          </div>

          <!-- Error State Overlay -->
          <div v-if="mapError" class="map-error">
            <i class="fas fa-exclamation-triangle"></i>
            <p>{{ mapError }}</p>
            <button @click="retryMapLoad" class="retry-btn">
              <i class="fas fa-redo"></i>
              Retry
            </button>
          </div>

          <!-- Map Instructions (only show when map is visible) -->
          <div v-if="!mapLoading && !mapError" class="map-instructions">
            <i class="fas fa-info-circle"></i>
            Click anywhere on the map to set your store location, or drag the marker to fine-tune the position.
          </div>

          <!-- Selected Address (only show when map is visible and address exists) -->
          <div v-if="!mapLoading && !mapError && formData.store_location_address" class="selected-address">
            <i class="fas fa-map-marker-alt"></i>
            <span>{{ formData.store_location_address }}</span>
          </div>
        </div>
        <div v-if="formData.store_location_address" class="selected-location">
          <input
            type="text"
            v-model="formData.store_location_address"
            class="form-input location-selected"
            placeholder="Selected address will appear here"
            readonly
          >
          <button type="button" class="clear-location-btn" @click="clearLocation">
            <i class="fas fa-times"></i> Clear Location
          </button>
        </div>
        <div v-if="errors.store_location_address" class="error-message">{{ errors.store_location_address[0] }}</div>
      </div>

      <!-- Delivery Capability -->
      <div class="form-group">
        <div class="checkbox-group">
          <input 
            type="checkbox" 
            id="delivery_capability" 
            v-model="formData.delivery_capability"
          >
          <label for="delivery_capability" class="form-label">I can deliver to customers</label>
        </div>
      </div>

      <!-- Delivery Fees -->
      <div v-if="formData.delivery_capability" class="delivery-fees">
        <h4>Delivery Fees by Emirate (AED)</h4>
        <div class="emirate-fee" v-for="emirate in emirates" :key="emirate.key">
          <label>{{ emirate.name }}:</label>
          <input 
            type="number" 
            v-model="formData.delivery_fees[emirate.key]"
            placeholder="0" 
            min="0" 
            step="0.01"
          >
        </div>
      </div>

      <button type="submit" class="form-button" :disabled="loading">
        <div v-if="loading" class="loading-spinner"></div>
        <span class="button-text">{{ loading ? 'Creating Account...' : 'Continue to Verification' }}</span>
      </button>
    </form>

    <!-- Validation Error Modal -->
    <div v-if="showValidationModal" class="modal-overlay" @click="closeValidationModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">
            <i class="fas fa-exclamation-triangle"></i>
            Validation Errors
          </h3>
          <button type="button" class="modal-close" @click="closeValidationModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p class="modal-description">Please fix the following errors before submitting:</p>
          <ul class="error-list">
            <li v-for="error in validationErrors" :key="error.field">
              <i class="fas fa-exclamation-circle"></i>
              <div>
                <strong>{{ getFieldDisplayName(error.field) }}:</strong> {{ error.message }}
              </div>
            </li>
          </ul>
        </div>
        <div class="modal-footer">
          <button type="button" class="modal-btn modal-btn-primary" @click="closeValidationModal">
            Fix Errors
          </button>
        </div>
      </div>
    </div>

    <!-- Login Dialog Modal -->
    <div v-if="showLoginModal" class="modal-overlay" @click="closeLoginModal">
      <div class="modal-container" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">
            <i class="fas fa-user-check"></i>
            Account Already Exists
          </h3>
          <button type="button" class="modal-close" @click="closeLoginModal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <p class="modal-description">{{ loginModalMessage }}</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="modal-btn modal-btn-secondary" @click="closeLoginModal">
            Cancel
          </button>
          <button type="button" class="modal-btn modal-btn-primary" @click="redirectToLogin">
            Go to Login
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MerchantInfoStep',
  props: {
    data: {
      type: Object,
      default: () => ({})
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  emits: ['submit', 'update'],
  data() {
    return {
      formData: {
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
      errors: {},
      phoneNumber: '',
      logoFileName: '',
      uaeIdFrontFileName: '',
      uaeIdBackFileName: '',
      logoPreviewUrl: '',
      uaeIdFrontPreviewUrl: '',
      uaeIdBackPreviewUrl: '',
      locationSearch: '',
      showMap: false,
      mapLoading: false,
      mapError: null,
      showValidationModal: false,
      showLoginModal: false,
      validationErrors: [],
      loginModalMessage: '',
      map: null,
      marker: null,
      autocomplete: null,
      geocoder: null,
      _updatingFromParent: false,
      emirates: [
        { key: 'dubai', name: 'Dubai' },
        { key: 'abu_dhabi', name: 'Abu Dhabi' },
        { key: 'sharjah', name: 'Sharjah' },
        { key: 'ajman', name: 'Ajman' },
        { key: 'ras_al_khaimah', name: 'Ras Al Khaimah' },
        { key: 'fujairah', name: 'Fujairah' },
        { key: 'umm_al_quwain', name: 'Umm Al Quwain' }
      ]
    };
  },
  watch: {
    data: {
      handler(newData) {
        if (newData) {
          // Use a flag to prevent recursive updates
          this._updatingFromParent = true;
          this.formData = { ...this.formData, ...newData };
          // Update phone number display
          if (newData.phone) {
            this.phoneNumber = this.extractPhoneNumber(newData.phone);
          }
          this.$nextTick(() => {
            this._updatingFromParent = false;
          });
        }
      },
      immediate: true,
      deep: true
    },
    formData: {
      handler(newData) {
        // Only emit update if not updating from parent
        if (!this._updatingFromParent) {
          this.$emit('update', newData);
        }
      },
      deep: true
    }
  },
  methods: {
    updateField(fieldName, value) {
      this.formData[fieldName] = value;
      this.$emit('update', this.formData);
      // Clear error for this field when user starts typing
      if (this.errors[fieldName]) {
        this.errors = { ...this.errors };
        delete this.errors[fieldName];
      }
    },
    handlePhoneInput(event) {
      let value = event.target.value;

      // Remove any non-digit characters
      value = value.replace(/\D/g, '');

      // Limit to 9 digits
      if (value.length > 9) {
        value = value.substring(0, 9);
      }

      // Format the display value with spaces
      let formattedValue = value;
      if (value.length > 2) {
        formattedValue = value.substring(0, 2) + ' ' + value.substring(2);
      }
      if (value.length > 5) {
        formattedValue = value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5);
      }

      // Update the display value
      this.phoneNumber = formattedValue;

      // Update the actual form data with full international format
      if (value.length === 9) {
        this.formData.phone = '+971' + value;
      } else {
        this.formData.phone = value ? '+971' + value : '';
      }

      this.$emit('update', this.formData);

      // Clear phone error when user starts typing
      if (this.errors.phone) {
        this.errors = { ...this.errors };
        delete this.errors.phone;
      }
    },
    validatePhoneKeypress(event) {
      // Only allow digits
      const char = String.fromCharCode(event.which);
      if (!/[0-9]/.test(char)) {
        event.preventDefault();
      }
    },
    extractPhoneNumber(fullPhone) {
      // Extract the 9-digit number from +971XXXXXXXXX format
      if (fullPhone && fullPhone.startsWith('+971')) {
        const digits = fullPhone.substring(4);
        // Format with spaces for display
        if (digits.length > 2) {
          let formatted = digits.substring(0, 2) + ' ' + digits.substring(2);
          if (digits.length > 5) {
            formatted = digits.substring(0, 2) + ' ' + digits.substring(2, 5) + ' ' + digits.substring(5);
          }
          return formatted;
        }
        return digits;
      }
      return '';
    },
    handleSubmit() {
      this.errors = {};

      // Perform client-side validation
      const validationErrors = this.validateForm();

      if (validationErrors.length > 0) {
        this.showValidationErrorModal(this.convertValidationErrorsToObject(validationErrors));
        return;
      }

      this.$emit('submit', this.formData);
    },
    validateForm() {
      const errors = [];

      // Business name validation
      if (!this.formData.name || this.formData.name.trim() === '') {
        errors.push({ field: 'name', message: 'Business name is required' });
      } else if (this.formData.name.length > 255) {
        errors.push({ field: 'name', message: 'Business name cannot exceed 255 characters' });
      }

      // Email validation
      if (!this.formData.email || this.formData.email.trim() === '') {
        errors.push({ field: 'email', message: 'Email address is required' });
      } else if (!this.isValidEmail(this.formData.email)) {
        errors.push({ field: 'email', message: 'Please enter a valid email address' });
      }

      // Phone validation
      if (!this.formData.phone || this.formData.phone.trim() === '') {
        errors.push({ field: 'phone', message: 'Phone number is required' });
      } else if (!this.isValidUAEPhone(this.formData.phone)) {
        errors.push({ field: 'phone', message: 'Please enter a valid 9-digit UAE phone number' });
      }

      // Password validation
      if (!this.formData.password || this.formData.password.trim() === '') {
        errors.push({ field: 'password', message: 'Password is required' });
      } else if (this.formData.password.length < 8) {
        errors.push({ field: 'password', message: 'Password must be at least 8 characters' });
      }

      // Password confirmation validation
      if (!this.formData.password_confirmation || this.formData.password_confirmation.trim() === '') {
        errors.push({ field: 'password_confirmation', message: 'Password confirmation is required' });
      } else if (this.formData.password !== this.formData.password_confirmation) {
        errors.push({ field: 'password_confirmation', message: 'Password confirmation does not match' });
      }

      // UAE ID validation
      if (!this.formData.uae_id_front) {
        errors.push({ field: 'uae_id_front', message: 'UAE ID front side is required' });
      }

      if (!this.formData.uae_id_back) {
        errors.push({ field: 'uae_id_back', message: 'UAE ID back side is required' });
      }

      // Delivery fees validation
      if (this.formData.delivery_capability) {
        const requiredEmirates = ['dubai', 'abu_dhabi', 'sharjah', 'ajman', 'ras_al_khaimah', 'fujairah', 'umm_al_quwain'];
        requiredEmirates.forEach(emirate => {
          if (!this.formData.delivery_fees[emirate] || this.formData.delivery_fees[emirate] === '') {
            errors.push({
              field: `delivery_fees.${emirate}`,
              message: `Delivery fee for ${this.getEmirateDisplayName(emirate)} is required when delivery capability is enabled`
            });
          }
        });
      }

      return errors;
    },
    convertValidationErrorsToObject(errors) {
      const errorObject = {};
      errors.forEach(error => {
        if (!errorObject[error.field]) {
          errorObject[error.field] = [];
        }
        errorObject[error.field].push(error.message);
      });
      return errorObject;
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
    isValidUAEPhone(phone) {
      // Check if phone is in +971XXXXXXXXX format with exactly 9 digits after +971
      const phoneRegex = /^\+971[0-9]{9}$/;
      return phoneRegex.test(phone);
    },
    getEmirateDisplayName(emirate) {
      const emirateNames = {
        'dubai': 'Dubai',
        'abu_dhabi': 'Abu Dhabi',
        'sharjah': 'Sharjah',
        'ajman': 'Ajman',
        'ras_al_khaimah': 'Ras Al Khaimah',
        'fujairah': 'Fujairah',
        'umm_al_quwain': 'Umm Al Quwain'
      };
      return emirateNames[emirate] || emirate;
    },
    handleLogoUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.logo = file;
        this.logoFileName = file.name;

        // Create image preview URL
        if (this.logoPreviewUrl) {
          URL.revokeObjectURL(this.logoPreviewUrl);
        }
        this.logoPreviewUrl = URL.createObjectURL(file);
      }
    },
    handleUaeIdFrontUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.uae_id_front = file;
        this.uaeIdFrontFileName = file.name;

        // Create image preview URL
        if (this.uaeIdFrontPreviewUrl) {
          URL.revokeObjectURL(this.uaeIdFrontPreviewUrl);
        }
        this.uaeIdFrontPreviewUrl = URL.createObjectURL(file);
      }
    },
    handleUaeIdBackUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.uae_id_back = file;
        this.uaeIdBackFileName = file.name;

        // Create image preview URL
        if (this.uaeIdBackPreviewUrl) {
          URL.revokeObjectURL(this.uaeIdBackPreviewUrl);
        }
        this.uaeIdBackPreviewUrl = URL.createObjectURL(file);
      }
    },
    handleLocationFocus() {
      // Immediately show map when user focuses on location input
      this.initializeMapIfNeeded();
    },
    handleLocationSearch() {
      // Also initialize map when user starts typing
      if (this.locationSearch.length > 0) {
        this.initializeMapIfNeeded();
      }
    },
    initializeMapIfNeeded() {
      if (!this.showMap && !this.mapLoading && !this.mapError) {
        this.showMap = true;
        this.mapLoading = true;
        this.mapError = null;

        this.$nextTick(() => {
          this.initializeGoogleMaps();
        });
      }
    },
    clearLocation() {
      this.formData.store_location_lat = null;
      this.formData.store_location_lng = null;
      this.formData.store_location_address = '';
      this.locationSearch = '';

      // Reset marker to default position if map exists
      if (this.map && this.marker) {
        const defaultCenter = { lat: 25.2048, lng: 55.2708 }; // Dubai center
        this.marker.setPosition(defaultCenter);
        this.map.setCenter(defaultCenter);
      }
    },
    retryMapLoad() {
      this.mapError = null;
      this.mapLoading = true;

      // Check if Google Maps API is available
      if (window.google && window.google.maps) {
        this.$nextTick(() => {
          this.setupGoogleMaps();
        });
      } else {
        // Wait for Google Maps to load or timeout
        let attempts = 0;
        const maxAttempts = 50; // 5 seconds with 100ms intervals

        const checkGoogleMaps = () => {
          attempts++;
          if (window.google && window.google.maps) {
            this.$nextTick(() => {
              this.setupGoogleMaps();
            });
          } else if (attempts >= maxAttempts) {
            this.mapError = 'Google Maps failed to load after multiple attempts. Please check your internet connection and refresh the page.';
            this.mapLoading = false;
          } else {
            setTimeout(checkGoogleMaps, 100);
          }
        };

        checkGoogleMaps();
      }
    },
    initializeGoogleMaps() {
      // Check if Google Maps is already loaded
      if (window.google && window.google.maps) {
        this.setupGoogleMaps();
        return;
      }

      // Check if Google Maps failed to load
      if (window.googleMapsLoaded === false) {
        this.mapError = 'Google Maps failed to load. Please check your internet connection and try again.';
        this.mapLoading = false;
        return;
      }

      // Wait for Google Maps to load with timeout
      let attempts = 0;
      const maxAttempts = 100; // 10 seconds with 100ms intervals

      const checkGoogleMaps = () => {
        attempts++;
        if (window.google && window.google.maps) {
          this.setupGoogleMaps();
        } else if (window.googleMapsLoaded === false) {
          this.mapError = 'Google Maps failed to load. Please check your internet connection and try again.';
          this.mapLoading = false;
        } else if (attempts >= maxAttempts) {
          this.mapError = 'Google Maps is taking too long to load. Please check your internet connection and try again.';
          this.mapLoading = false;
        } else {
          // Keep checking
          setTimeout(checkGoogleMaps, 100);
        }
      };

      checkGoogleMaps();
    },
    setupGoogleMaps() {
      try {
        // Wait for the next tick to ensure DOM is updated
        this.$nextTick(() => {
          const mapContainer = document.getElementById('google-map');
          if (!mapContainer) {
            this.mapError = 'Map container not found. Please refresh the page and try again.';
            this.mapLoading = false;
            return;
          }

          this.initializeMap(mapContainer);
        });
      } catch (error) {
        console.error('Error setting up Google Maps:', error);
        this.mapError = 'Failed to initialize map. Please try again.';
        this.mapLoading = false;
      }
    },
    initializeMap(mapContainer) {
      try {

        // Enhanced map options
        const mapOptions = {
          center: { lat: 25.2048, lng: 55.2708 }, // Dubai center
          zoom: 12,
          mapTypeId: 'roadmap',
          zoomControl: true,
          mapTypeControl: false,
          scaleControl: true,
          streetViewControl: true,
          rotateControl: true,
          fullscreenControl: true,
          gestureHandling: 'cooperative'
        };

        // Initialize map
        this.map = new google.maps.Map(mapContainer, mapOptions);

        // Initialize geocoder
        this.geocoder = new google.maps.Geocoder();

        // Initialize marker
        this.marker = new google.maps.Marker({
          position: mapOptions.center,
          map: this.map,
          draggable: true,
          title: 'Drag me to set your store location',
          animation: google.maps.Animation.DROP
        });

        // Set up places autocomplete
        const searchInput = document.getElementById('location-search');
        if (searchInput) {
          this.autocomplete = new google.maps.places.Autocomplete(searchInput, {
            types: ['establishment', 'geocode'],
            componentRestrictions: { country: 'ae' }, // Restrict to UAE
            fields: ['place_id', 'geometry', 'name', 'formatted_address']
          });

          // Handle place selection
          this.autocomplete.addListener('place_changed', () => {
            const place = this.autocomplete.getPlace();
            if (place.geometry) {
              this.setLocationFromPlace(place);
            }
          });
        }

        // Handle map clicks
        this.map.addListener('click', (event) => {
          this.setLocationFromLatLng(event.latLng);
        });

        // Handle marker drag
        this.marker.addListener('dragend', (event) => {
          this.setLocationFromLatLng(event.latLng);
        });

        // If we already have a location, set it on the map
        if (this.formData.store_location_lat && this.formData.store_location_lng) {
          const existingLocation = {
            lat: parseFloat(this.formData.store_location_lat),
            lng: parseFloat(this.formData.store_location_lng)
          };
          this.marker.setPosition(existingLocation);
          this.map.setCenter(existingLocation);
        }

        this.mapLoading = false;
        this.mapError = null;

      } catch (error) {
        console.error('Error setting up Google Maps:', error);
        this.mapError = 'Failed to initialize map. Please try again.';
        this.mapLoading = false;
      }
    },
    setLocationFromPlace(place) {
      try {
        const location = place.geometry.location;
        this.formData.store_location_lat = location.lat();
        this.formData.store_location_lng = location.lng();
        this.formData.store_location_address = place.formatted_address || place.name || 'Selected location';

        this.map.setCenter(location);
        this.marker.setPosition(location);
        this.marker.setAnimation(google.maps.Animation.BOUNCE);

        // Stop animation after 2 seconds
        setTimeout(() => {
          if (this.marker) {
            this.marker.setAnimation(null);
          }
        }, 2000);

        // Update search input with the selected address
        this.locationSearch = this.formData.store_location_address;

      } catch (error) {
        console.error('Error setting location from place:', error);
        this.mapError = 'Failed to set location. Please try again.';
      }
    },
    setLocationFromLatLng(latLng) {
      try {
        this.formData.store_location_lat = latLng.lat();
        this.formData.store_location_lng = latLng.lng();

        // Animate marker
        this.marker.setPosition(latLng);
        this.marker.setAnimation(google.maps.Animation.BOUNCE);

        // Stop animation after 2 seconds
        setTimeout(() => {
          if (this.marker) {
            this.marker.setAnimation(null);
          }
        }, 2000);

        // Reverse geocoding to get address
        if (this.geocoder) {
          this.geocoder.geocode({ location: latLng }, (results, status) => {
            try {
              if (status === 'OK' && results && results[0]) {
                this.formData.store_location_address = results[0].formatted_address;
                this.locationSearch = this.formData.store_location_address;
              } else {
                console.warn('Reverse geocoding failed:', status);
                // Provide a more user-friendly fallback
                const lat = latLng.lat().toFixed(6);
                const lng = latLng.lng().toFixed(6);
                this.formData.store_location_address = `Selected Location (${lat}, ${lng})`;
                this.locationSearch = this.formData.store_location_address;

                // Show a subtle notification that address lookup failed
                console.info('Address lookup failed, using coordinates instead');
              }
            } catch (geocodeError) {
              console.error('Error processing geocoding results:', geocodeError);
              const lat = latLng.lat().toFixed(6);
              const lng = latLng.lng().toFixed(6);
              this.formData.store_location_address = `Selected Location (${lat}, ${lng})`;
              this.locationSearch = this.formData.store_location_address;
            }
          });
        } else {
          // Fallback if geocoder is not available
          const lat = latLng.lat().toFixed(6);
          const lng = latLng.lng().toFixed(6);
          this.formData.store_location_address = `Selected Location (${lat}, ${lng})`;
          this.locationSearch = this.formData.store_location_address;
        }

      } catch (error) {
        console.error('Error setting location from coordinates:', error);
        this.mapError = 'Failed to set location. Please try again.';
      }
    },
    setErrors(errors) {
      this.errors = errors;
    },
    showValidationErrorModal(errors) {
      this.validationErrors = [];

      // Convert Laravel validation errors to our format
      if (errors) {
        Object.keys(errors).forEach(field => {
          const messages = Array.isArray(errors[field]) ? errors[field] : [errors[field]];
          messages.forEach(message => {
            this.validationErrors.push({
              field: field,
              message: message
            });
          });
        });
      }

      this.showValidationModal = true;
      document.body.style.overflow = 'hidden';
    },
    closeValidationModal() {
      this.showValidationModal = false;
      this.validationErrors = [];
      document.body.style.overflow = 'auto';
    },
    showLoginDialog(message) {
      this.loginModalMessage = message;
      this.showLoginModal = true;
      document.body.style.overflow = 'hidden';
    },
    closeLoginModal() {
      this.showLoginModal = false;
      this.loginModalMessage = '';
      document.body.style.overflow = 'auto';
    },
    redirectToLogin() {
      // Redirect to login page
      window.location.href = '/login';
    },
    getFieldDisplayName(field) {
      const fieldNames = {
        'name': 'Business Name',
        'email': 'Email Address',
        'phone': 'Phone Number',
        'password': 'Password',
        'password_confirmation': 'Confirm Password',
        'logo': 'Business Logo',
        'uae_id_front': 'UAE ID Front Side',
        'uae_id_back': 'UAE ID Back Side',
        'store_location_address': 'Store Location',
        'delivery_capability': 'Delivery Capability',
        'delivery_fees.dubai': 'Dubai Delivery Fee',
        'delivery_fees.abu_dhabi': 'Abu Dhabi Delivery Fee',
        'delivery_fees.sharjah': 'Sharjah Delivery Fee',
        'delivery_fees.ajman': 'Ajman Delivery Fee',
        'delivery_fees.ras_al_khaimah': 'Ras Al Khaimah Delivery Fee',
        'delivery_fees.fujairah': 'Fujairah Delivery Fee',
        'delivery_fees.umm_al_quwain': 'Umm Al Quwain Delivery Fee'
      };

      return fieldNames[field] || field.charAt(0).toUpperCase() + field.slice(1);
    },
    handleGoogleMapsLoaded() {
      // Google Maps has loaded successfully
      if (this.showMap && this.mapLoading) {
        this.initializeGoogleMaps();
      }
    },
    handleGoogleMapsFailed() {
      // Google Maps failed to load
      this.mapError = 'Google Maps failed to load. Please check your internet connection and refresh the page.';
      this.mapLoading = false;
    }
  },
  mounted() {
    // Listen for Google Maps load events
    window.addEventListener('google-maps-loaded', this.handleGoogleMapsLoaded);
    window.addEventListener('google-maps-failed', this.handleGoogleMapsFailed);

    // If Google Maps is already loaded, we can initialize immediately if needed
    if (window.googleMapsLoaded === true && this.showMap) {
      this.initializeGoogleMaps();
    }
  },
  beforeUnmount() {
    // Clean up event listeners
    window.removeEventListener('google-maps-loaded', this.handleGoogleMapsLoaded);
    window.removeEventListener('google-maps-failed', this.handleGoogleMapsFailed);

    // Clean up Google Maps instances
    if (this.autocomplete) {
      google.maps.event.clearInstanceListeners(this.autocomplete);
    }
    if (this.map) {
      google.maps.event.clearInstanceListeners(this.map);
    }
    if (this.marker) {
      google.maps.event.clearInstanceListeners(this.marker);
    }

    // Clean up image preview URLs to prevent memory leaks
    if (this.logoPreviewUrl) {
      URL.revokeObjectURL(this.logoPreviewUrl);
    }
    if (this.uaeIdFrontPreviewUrl) {
      URL.revokeObjectURL(this.uaeIdFrontPreviewUrl);
    }
    if (this.uaeIdBackPreviewUrl) {
      URL.revokeObjectURL(this.uaeIdBackPreviewUrl);
    }
  }
};
</script>

<style scoped>
.merchant-info-step {
  width: 100%;
  max-width: none;
}

.step-header {
  margin-bottom: 2rem;
  text-align: center;
}

.step-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 0.5rem;
}

.step-description {
  color: #666;
  font-size: 1rem;
}

.merchant-form {
  width: 100%;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.form-input {
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

.form-input:focus {
  outline: none !important;
  border-color: #f59e0b !important;
  box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
  transform: none !important;
}

.form-input:hover {
  border-color: #f59e0b !important;
  background: white !important;
  transform: none !important;
}

.form-input.error {
  border-color: #e53e3e !important;
  box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1) !important;
}

.error-message {
  color: #e53e3e;
  font-size: 0.85rem;
  margin-top: 0.5rem;
}

.file-upload {
  position: relative;
  display: inline-block;
  width: 100%;
}

.file-input {
  position: absolute;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
  z-index: 2;
}

.file-upload-label {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px 16px;
  border: 2px dashed #e1e5e9;
  border-radius: 8px;
  background: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s ease;
}

.file-upload-label:hover {
  border-color: #667eea;
  background: rgba(102, 126, 234, 0.05);
}

.file-upload-label i {
  margin-right: 0.5rem;
  color: #667eea;
}

.phone-input-container {
  display: flex;
  align-items: center;
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  background: white;
  transition: all 0.3s ease;
}

.phone-input-container:focus-within {
  border-color: #f59e0b;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.phone-input-container.error {
  border-color: #e53e3e;
}

.country-code-display {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  background-color: #f8f9fa;
  border-right: 1px solid #e1e5e9;
  border-radius: 6px 0 0 6px;
  white-space: nowrap;
}

.flag-icon {
  width: 20px;
  height: 15px;
  margin-right: 8px;
  border-radius: 2px;
}

.country-code {
  font-weight: 600;
  color: #333;
  font-size: 0.95rem;
}

.phone-input {
  border: none !important;
  border-radius: 0 6px 6px 0 !important;
  box-shadow: none !important;
  flex: 1;
  padding-left: 16px !important;
}

.phone-input:focus {
  outline: none;
  border: none !important;
  box-shadow: none !important;
}

.location-search-container {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  pointer-events: none;
}

.clear-location-btn {
  position: absolute;
  right: 40px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #ef4444;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
  z-index: 10;
}

.clear-location-btn:hover {
  background-color: #fee2e2;
  color: #dc2626;
}

.map-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
  background-color: #f9fafb;
  border: 2px dashed #d1d5db;
  border-radius: 8px;
  margin-top: 10px;
  color: #6b7280;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.map-error {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  margin-top: 10px;
  color: #dc2626;
}

.map-error i {
  margin-right: 8px;
}

.retry-btn {
  background-color: #dc2626;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 12px;
  transition: background-color 0.2s ease;
}

.retry-btn:hover {
  background-color: #b91c1c;
}

.checkbox-group {
  display: flex;
  align-items: center;
}

.checkbox-group input[type="checkbox"] {
  margin-right: 0.75rem;
  width: auto;
}

.delivery-fees {
  margin-top: 1rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.delivery-fees h4 {
  margin-bottom: 1rem;
  color: #333;
  font-size: 1.1rem;
}

.emirate-fee {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
}

.emirate-fee label {
  min-width: 120px;
  margin-right: 1rem;
  margin-bottom: 0;
  font-weight: 500;
}

.emirate-fee input {
  flex: 1;
  max-width: 150px;
}

.form-button {
  width: 100%;
  padding: 15px 20px;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.form-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
}

.form-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.loading-spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 0.5rem;
  display: none;
}

.form-button:disabled .loading-spinner {
  display: inline-block;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Ensure inputs are always interactive */
.form-input,
.file-input {
  pointer-events: auto !important;
  user-select: auto !important;
}

/* Remove any potential overlays */
.form-group {
  position: relative;
  z-index: 1;
}

/* Map-specific styles */
.map-instructions {
  margin-top: 10px;
  padding: 12px;
  background-color: #f0f9ff;
  border: 1px solid #bae6fd;
  border-radius: 6px;
  color: #0369a1;
  font-size: 0.9rem;
  display: flex;
  align-items: center;
}

.map-instructions i {
  margin-right: 8px;
  color: #0284c7;
  flex-shrink: 0;
}

.selected-address {
  margin-top: 10px;
  padding: 12px;
  background-color: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 6px;
  color: #166534;
  font-size: 0.9rem;
  display: flex;
  align-items: flex-start;
}

.selected-address i {
  margin-right: 8px;
  color: #16a34a;
  flex-shrink: 0;
  margin-top: 2px;
}

.selected-address span {
  line-height: 1.4;
}

#map-container {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

#google-map {
  border-radius: 8px;
}

.map-loading {
  position: absolute;
  top: 10px;
  left: 0;
  right: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 350px;
  background: rgba(248, 250, 252, 0.95);
  border-radius: 8px;
  z-index: 10;
  backdrop-filter: blur(2px);
}

.map-loading .loading-spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.map-loading p {
  color: #6b7280;
  font-size: 0.875rem;
  margin: 0;
}

.map-error {
  position: absolute;
  top: 10px;
  left: 0;
  right: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 350px;
  background: rgba(254, 242, 242, 0.95);
  border: 1px solid #fecaca;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  z-index: 10;
  backdrop-filter: blur(2px);
}

.map-error i {
  font-size: 2rem;
  color: #ef4444;
  margin-bottom: 12px;
}

.map-error p {
  color: #dc2626;
  font-size: 0.875rem;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.retry-btn {
  background: #ef4444;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 0.875rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background-color 0.2s;
}

.retry-btn:hover {
  background: #dc2626;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-title {
  display: flex;
  align-items: center;
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.modal-title i {
  margin-right: 8px;
  color: #f59e0b;
}

.modal-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background-color: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 20px 24px;
}

.modal-description {
  color: #6b7280;
  margin-bottom: 16px;
  line-height: 1.5;
}

.error-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.error-list li {
  display: flex;
  align-items: flex-start;
  padding: 12px;
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 6px;
  margin-bottom: 8px;
}

.error-list li:last-child {
  margin-bottom: 0;
}

.error-list li i {
  color: #ef4444;
  margin-right: 8px;
  margin-top: 2px;
  flex-shrink: 0;
}

.error-list li div {
  flex: 1;
  color: #dc2626;
  font-size: 0.9rem;
  line-height: 1.4;
}

.error-list li strong {
  color: #991b1b;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid #e5e7eb;
}

.modal-btn {
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  font-size: 0.9rem;
}

.modal-btn-primary {
  background-color: #3b82f6;
  color: white;
}

.modal-btn-primary:hover {
  background-color: #2563eb;
}

.modal-btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.modal-btn-secondary:hover {
  background-color: #e5e7eb;
}

/* Responsive design for mobile */
@media (max-width: 768px) {
  #google-map {
    height: 280px !important;
  }

  .map-instructions,
  .selected-address {
    font-size: 0.85rem;
    padding: 10px;
  }

  .clear-location-btn {
    right: 35px;
  }

  .modal-overlay {
    padding: 10px;
  }

  .modal-container {
    max-height: 95vh;
  }

  .modal-header,
  .modal-body,
  .modal-footer {
    padding: 16px 20px;
  }

  .modal-footer {
    flex-direction: column;
  }

  .modal-btn {
    width: 100%;
  }
}

/* Image Preview Styles */
.file-upload.has-image .file-upload-label {
  padding: 0;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  overflow: hidden;
}

.image-preview {
  display: flex;
  align-items: center;
  padding: 12px;
  background: #f9fafb;
  transition: all 0.2s ease;
}

.image-preview:hover {
  background: #f3f4f6;
}

.preview-thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  margin-right: 12px;
  flex-shrink: 0;
}

.image-info {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
  min-width: 0;
}

.file-name {
  font-weight: 500;
  color: #374151;
  font-size: 0.9rem;
  margin-bottom: 2px;
  word-break: break-word;
}

.change-text {
  font-size: 0.8rem;
  color: #6b7280;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.upload-placeholder i {
  font-size: 1.5rem;
  color: #9ca3af;
}

.upload-placeholder span {
  color: #6b7280;
  font-size: 0.9rem;
}
</style>
