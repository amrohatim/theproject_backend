// API service for merchant registration
class MerchantRegistrationApi {
  constructor() {
    this.baseUrl = '/api/merchant-registration';
    this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  }

  async makeRequest(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    
    const defaultOptions = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
    };

    // Add CSRF token if available
    if (this.csrfToken) {
      defaultOptions.headers['X-CSRF-TOKEN'] = this.csrfToken;
    }

    // Merge options
    const requestOptions = {
      ...defaultOptions,
      ...options,
      headers: {
        ...defaultOptions.headers,
        ...options.headers,
      },
    };

    try {
      const response = await fetch(url, requestOptions);

      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      let data;

      if (contentType && contentType.includes('application/json')) {
        data = await response.json();
      } else {
        // If not JSON, get text content for error handling
        const textContent = await response.text();
        data = {
          success: false,
          message: `Server error: ${response.status} ${response.statusText}`,
          error: textContent
        };
      }

      if (!response.ok) {
        throw new Error(data.message || `HTTP error! status: ${response.status}`);
      }

      return data;
    } catch (error) {
      console.error('API request failed:', error);
      throw error;
    }
  }

  async makeFormRequest(endpoint, formData) {
    const url = `${this.baseUrl}${endpoint}`;
    
    const options = {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: formData,
    };

    // Add CSRF token if available
    if (this.csrfToken) {
      options.headers['X-CSRF-TOKEN'] = this.csrfToken;
    }

    try {
      const response = await fetch(url, options);

      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      let data;

      if (contentType && contentType.includes('application/json')) {
        data = await response.json();
      } else {
        // If not JSON, get text content for error handling
        const textContent = await response.text();
        data = {
          success: false,
          message: `Server error: ${response.status} ${response.statusText}`,
          error: textContent
        };
      }

      if (!response.ok) {
        throw new Error(data.message || `HTTP error! status: ${response.status}`);
      }

      return data;
    } catch (error) {
      console.error('API form request failed:', error);
      throw error;
    }
  }

  // Step 1: Submit merchant information with UAE ID and store location
  async submitMerchantInfo(merchantData) {
    const formData = new FormData();
    
    // Add text fields
    formData.append('name', merchantData.name);
    formData.append('email', merchantData.email);
    formData.append('phone', merchantData.phone);
    formData.append('password', merchantData.password);
    formData.append('password_confirmation', merchantData.password_confirmation);
    
    // Add optional fields
    if (merchantData.store_location_lat) {
      formData.append('store_location_lat', merchantData.store_location_lat);
    }
    if (merchantData.store_location_lng) {
      formData.append('store_location_lng', merchantData.store_location_lng);
    }
    if (merchantData.store_location_address) {
      formData.append('store_location_address', merchantData.store_location_address);
    }
    
    // Add delivery capability
    formData.append('delivery_capability', merchantData.delivery_capability ? '1' : '0');
    
    // Add delivery fees
    if (merchantData.delivery_fees) {
      Object.keys(merchantData.delivery_fees).forEach(emirate => {
        if (merchantData.delivery_fees[emirate]) {
          formData.append(`delivery_fees[${emirate}]`, merchantData.delivery_fees[emirate]);
        }
      });
    }
    
    // Add file uploads
    if (merchantData.logo) {
      formData.append('logo', merchantData.logo);
    }
    if (merchantData.uae_id_front) {
      formData.append('uae_id_front', merchantData.uae_id_front);
    }
    if (merchantData.uae_id_back) {
      formData.append('uae_id_back', merchantData.uae_id_back);
    }

    return await this.makeFormRequest('/info', formData);
  }

  // Step 2: Verify email
  async verifyEmail(registrationToken, verificationCode) {
    return await this.makeRequest('/verify-email', {
      method: 'POST',
      body: JSON.stringify({
        registration_token: registrationToken,
        verification_code: verificationCode,
      }),
    });
  }

  // Step 3: Send OTP for phone verification
  async sendOtp(phoneNumber, type = 'registration') {
    return await this.makeRequest('/send-otp', {
      method: 'POST',
      body: JSON.stringify({
        phone_number: phoneNumber,
        type: type,
      }),
    });
  }

  // Step 3: Verify OTP
  async verifyOtp(requestId, otpCode) {
    return await this.makeRequest('/verify-otp', {
      method: 'POST',
      body: JSON.stringify({
        request_id: requestId,
        otp_code: otpCode,
      }),
    });
  }

  // Step 3: Resend OTP
  async resendOtp(phoneNumber, type = 'registration') {
    return await this.makeRequest('/resend-otp', {
      method: 'POST',
      body: JSON.stringify({
        phone_number: phoneNumber,
        type: type,
      }),
    });
  }

  // Step 3: Send phone OTP using registration token
  async sendPhoneOtp(registrationToken) {
    return await this.makeRequest('/send-phone-otp', {
      method: 'POST',
      body: JSON.stringify({
        registration_token: registrationToken,
      }),
    });
  }

  // Step 3: Verify phone OTP using registration token
  async verifyPhoneOtp(registrationToken, otpCode) {
    return await this.makeRequest('/verify-phone-otp', {
      method: 'POST',
      body: JSON.stringify({
        registration_token: registrationToken,
        otp_code: otpCode,
      }),
    });
  }

  // Step 3: Resend phone OTP using registration token
  async resendPhoneOtp(registrationToken) {
    return await this.makeRequest('/resend-phone-otp', {
      method: 'POST',
      body: JSON.stringify({
        registration_token: registrationToken,
      }),
    });
  }

  // Step 4: Upload license
  async uploadLicense(userId, licenseData) {
    const formData = new FormData();
    formData.append('user_id', userId);
    formData.append('license_file', licenseData.license_file);

    if (licenseData.license_start_date) {
      formData.append('license_start_date', licenseData.license_start_date);
    }

    if (licenseData.license_end_date) {
      formData.append('license_end_date', licenseData.license_end_date);
    }

    if (licenseData.notes) {
      formData.append('notes', licenseData.notes);
    }

    return await this.makeFormRequest('/license', formData);
  }

  // Resend email verification
  async resendEmailVerification(registrationToken) {
    return await this.makeRequest('/resend-email-verification', {
      method: 'POST',
      body: JSON.stringify({
        registration_token: registrationToken,
      }),
    });
  }

  // Get registration status
  async getRegistrationStatus(registrationToken) {
    return await this.makeRequest(`/status?registration_token=${registrationToken}`, {
      method: 'GET',
    });
  }
}

// Export singleton instance
export const merchantRegistrationApi = new MerchantRegistrationApi();
