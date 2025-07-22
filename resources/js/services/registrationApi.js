// API service for vendor registration
class RegistrationApi {
  constructor() {
    this.baseUrl = '/api/vendor-registration';
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
      const data = await response.json();

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
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || `HTTP error! status: ${response.status}`);
      }

      return data;
    } catch (error) {
      console.error('API form request failed:', error);
      throw error;
    }
  }

  // Step 1: Submit personal information
  async submitPersonalInfo(personalData) {
    return await this.makeRequest('/info', {
      method: 'POST',
      body: JSON.stringify(personalData),
    });
  }

  // Step 2: Verify email (session-based)
  async verifyEmail(verificationCode) {
    return await this.makeRequest('/verify-email', {
      method: 'POST',
      body: JSON.stringify({
        verification_code: verificationCode,
      }),
    });
  }

  // Step 3: Send OTP for phone verification (session-based)
  async sendOtp() {
    return await this.makeRequest('/send-otp', {
      method: 'POST',
    });
  }

  // Step 3: Verify OTP (session-based)
  async verifyOtp(otpCode) {
    return await this.makeRequest('/verify-otp', {
      method: 'POST',
      body: JSON.stringify({
        otp_code: otpCode,
      }),
    });
  }

  // Step 4: Submit company information (session-based)
  async submitCompanyInfo(companyData, logoFile = null) {
    if (logoFile) {
      // Use FormData for file upload
      const formData = new FormData();
      
      // Add all company data fields
      Object.keys(companyData).forEach(key => {
        if (companyData[key] !== null && companyData[key] !== undefined) {
          formData.append(key, companyData[key]);
        }
      });
      
      // Add logo file
      formData.append('logo', logoFile);
      
      return await this.makeFormRequest('/company', formData);
    } else {
      // Use JSON for regular data
      return await this.makeRequest('/company', {
        method: 'POST',
        body: JSON.stringify(companyData),
      });
    }
  }

  // Step 5: Upload license
  async uploadLicense(userId, licenseData) {
    const formData = new FormData();

    // Only append user_id if it's provided (for backward compatibility)
    if (userId) {
      formData.append('user_id', userId);
    }

    formData.append('license_file', licenseData.license_file);

    if (licenseData.license_start_date) {
      formData.append('license_start_date', licenseData.license_start_date);
    }

    if (licenseData.license_expiry_date) {
      formData.append('license_expiry_date', licenseData.license_expiry_date);
    }

    if (licenseData.notes) {
      formData.append('notes', licenseData.notes);
    }

    return await this.makeFormRequest('/license', formData);
  }

  // Resend email verification (session-based)
  async resendEmailVerification() {
    return await this.makeRequest('/resend-email-verification', {
      method: 'POST',
    });
  }

  // Get registration status (session-based)
  async getRegistrationStatus() {
    return await this.makeRequest('/status', {
      method: 'GET',
    });
  }
}

// Export singleton instance
export const registrationApi = new RegistrationApi();
