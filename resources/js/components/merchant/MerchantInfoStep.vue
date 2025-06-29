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
        <input
          type="tel"
          id="phone"
          v-model="formData.phone"
          class="form-input"
          :class="{ 'error': errors.phone }"
          placeholder="+971 50 123 4567"
          required
          @input="updateField('phone', $event.target.value)"
        >
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
        <div class="file-upload">
          <input 
            type="file" 
            id="logo" 
            ref="logoInput"
            @change="handleLogoUpload"
            accept="image/*"
            class="file-input"
          >
          <label for="logo" class="file-upload-label">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>{{ logoFileName || 'Click to upload logo' }}</span>
          </label>
        </div>
        <div v-if="errors.logo" class="error-message">{{ errors.logo[0] }}</div>
      </div>

      <!-- UAE ID Front -->
      <div class="form-group">
        <label for="uae_id_front" class="form-label">UAE ID Front Side *</label>
        <div class="file-upload">
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
            <i class="fas fa-id-card"></i>
            <span>{{ uaeIdFrontFileName || 'Upload front side of UAE ID' }}</span>
          </label>
        </div>
        <div v-if="errors.uae_id_front" class="error-message">{{ errors.uae_id_front[0] }}</div>
      </div>

      <!-- UAE ID Back -->
      <div class="form-group">
        <label for="uae_id_back" class="form-label">UAE ID Back Side *</label>
        <div class="file-upload">
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
            <i class="fas fa-id-card"></i>
            <span>{{ uaeIdBackFileName || 'Upload back side of UAE ID' }}</span>
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
      logoFileName: '',
      uaeIdFrontFileName: '',
      uaeIdBackFileName: '',
      locationSearch: '',
      showMap: false,
      mapLoading: false,
      mapError: null,
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
    handleSubmit() {
      this.errors = {};
      this.$emit('submit', this.formData);
    },
    handleLogoUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.logo = file;
        this.logoFileName = file.name;
      }
    },
    handleUaeIdFrontUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.uae_id_front = file;
        this.uaeIdFrontFileName = file.name;
      }
    },
    handleUaeIdBackUpload(event) {
      const file = event.target.files[0];
      if (file) {
        this.formData.uae_id_back = file;
        this.uaeIdBackFileName = file.name;
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
  border: 2px solid #e1e5e9;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
  box-sizing: border-box;
  position: relative;
  z-index: 1;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input.error {
  border-color: #e53e3e;
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
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
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
}
</style>
