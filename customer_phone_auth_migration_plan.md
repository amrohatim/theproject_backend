# Customer Phone Authentication Migration Plan

## Overview

This document outlines the strategy for repurposing the existing OTP API infrastructure for future customer phone authentication in the Flutter marketplace app. With vendor and provider registration now using Firebase email verification, the OTP system can be dedicated to customer authentication flows.

## Current OTP Infrastructure Analysis

### Existing Components

#### 1. OTP Service Classes
- **`App\Services\OTPService`** - SmartVision API integration
- **`App\Services\FirebaseOTPService`** - Firebase phone authentication
- Both services implement the same interface with methods:
  - `sendOTP(string $phoneNumber): array`
  - `verifyOTP(string $requestId, string $otp): array`
  - `resendOTP(string $requestId): array`
  - `getOTPStatus(string $requestId): array`

#### 2. Configuration
- SmartVision API credentials in `config/services.php`
- Firebase configuration for phone authentication
- Rate limiting: 60+ seconds between requests
- OTP expiration: 10 minutes
- Maximum attempts: 3 per OTP

#### 3. Security Features
- Phone number validation (UAE +971 format)
- Rate limiting and attempt tracking
- Secure session management
- Comprehensive error handling

## Migration Strategy for Customer Authentication

### Phase 1: Customer Registration (Flutter App)

#### Implementation Steps

1. **Create Customer Authentication Controller**
   ```php
   // app/Http/Controllers/API/CustomerAuthController.php
   class CustomerAuthController extends Controller
   {
       protected $otpService;
       
       public function __construct(OTPService $otpService)
       {
           $this->otpService = $otpService;
       }
       
       public function sendRegistrationOTP(Request $request)
       public function verifyRegistrationOTP(Request $request)
       public function sendLoginOTP(Request $request)
       public function verifyLoginOTP(Request $request)
   }
   ```

2. **API Endpoints for Customer Authentication**
   ```php
   // routes/api.php
   Route::prefix('customer/auth')->group(function () {
       Route::post('/register/send-otp', [CustomerAuthController::class, 'sendRegistrationOTP']);
       Route::post('/register/verify-otp', [CustomerAuthController::class, 'verifyRegistrationOTP']);
       Route::post('/login/send-otp', [CustomerAuthController::class, 'sendLoginOTP']);
       Route::post('/login/verify-otp', [CustomerAuthController::class, 'verifyLoginOTP']);
       Route::post('/resend-otp', [CustomerAuthController::class, 'resendOTP']);
   });
   ```

3. **Database Schema Modifications**
   ```sql
   -- Add customer-specific fields to users table
   ALTER TABLE users ADD COLUMN phone_verified_at TIMESTAMP NULL;
   ALTER TABLE users ADD COLUMN last_otp_sent_at TIMESTAMP NULL;
   ALTER TABLE users ADD COLUMN otp_attempts INT DEFAULT 0;
   
   -- Create customer_sessions table for OTP tracking
   CREATE TABLE customer_sessions (
       id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       phone VARCHAR(20) NOT NULL,
       otp_request_id VARCHAR(255) NOT NULL,
       purpose ENUM('registration', 'login', 'password_reset') NOT NULL,
       expires_at TIMESTAMP NOT NULL,
       verified_at TIMESTAMP NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       INDEX idx_phone (phone),
       INDEX idx_request_id (otp_request_id),
       INDEX idx_expires_at (expires_at)
   );
   ```

### Phase 2: Flutter Integration

#### 1. Flutter OTP Service
```dart
class CustomerOTPService {
  static const String baseUrl = 'https://your-api.com/api/customer/auth';
  
  Future<OTPResponse> sendRegistrationOTP(String phoneNumber) async {
    // Implementation for sending registration OTP
  }
  
  Future<VerificationResponse> verifyRegistrationOTP(
    String requestId, 
    String otp
  ) async {
    // Implementation for verifying registration OTP
  }
  
  Future<OTPResponse> sendLoginOTP(String phoneNumber) async {
    // Implementation for sending login OTP
  }
  
  Future<VerificationResponse> verifyLoginOTP(
    String requestId, 
    String otp
  ) async {
    // Implementation for verifying login OTP
  }
}
```

#### 2. Flutter UI Components
- **OTP Input Widget**: Reusable 6-digit OTP input field
- **Phone Number Input**: UAE phone number validation
- **Timer Widget**: Countdown for resend functionality
- **Loading States**: Proper loading indicators

#### 3. State Management (GetX)
```dart
class CustomerAuthController extends GetxController {
  final CustomerOTPService _otpService = CustomerOTPService();
  
  // Observable variables
  var isLoading = false.obs;
  var otpSent = false.obs;
  var phoneNumber = ''.obs;
  var requestId = ''.obs;
  var resendTimer = 0.obs;
  
  // Methods
  Future<void> sendOTP(String phone, String purpose) async {}
  Future<void> verifyOTP(String otp) async {}
  Future<void> resendOTP() async {}
}
```

### Phase 3: Advanced Features

#### 1. Multi-Purpose OTP System
- **Registration**: New customer account creation
- **Login**: Existing customer authentication
- **Password Reset**: Account recovery
- **Phone Verification**: Update phone number
- **Two-Factor Authentication**: Additional security layer

#### 2. Enhanced Security
```php
// Rate limiting by IP and phone number
Route::middleware(['throttle:otp-send:5,1'])->group(function () {
    // OTP sending routes
});

// Custom rate limiter
class OTPRateLimiter
{
    public function tooManyAttempts($key, $maxAttempts = 5): bool
    public function hit($key, $decayMinutes = 60): int
    public function clear($key): void
}
```

#### 3. Analytics and Monitoring
```php
// OTP analytics tracking
class OTPAnalytics
{
    public function trackOTPSent(string $phone, string $purpose): void
    public function trackOTPVerified(string $phone, string $purpose): void
    public function trackOTPFailed(string $phone, string $purpose): void
    public function getOTPStats(string $period = '24h'): array
}
```

## Technical Specifications

### API Response Format
```json
{
  "success": true,
  "message": "OTP sent successfully",
  "data": {
    "request_id": "otp_12345",
    "expires_in": 600,
    "resend_after": 60,
    "phone_masked": "+971****1234"
  }
}
```

### Error Handling
```php
// Standardized error responses
class OTPErrorHandler
{
    const INVALID_PHONE = 'INVALID_PHONE_NUMBER';
    const RATE_LIMITED = 'RATE_LIMITED';
    const OTP_EXPIRED = 'OTP_EXPIRED';
    const INVALID_OTP = 'INVALID_OTP';
    const MAX_ATTEMPTS = 'MAX_ATTEMPTS_EXCEEDED';
}
```

### Configuration Management
```php
// config/customer_auth.php
return [
    'otp' => [
        'length' => 6,
        'expiry_minutes' => 10,
        'max_attempts' => 3,
        'resend_delay_seconds' => 60,
        'rate_limit_per_hour' => 5,
    ],
    'phone' => [
        'country_code' => '+971',
        'validation_regex' => '/^\+971[0-9]{9}$/',
        'allowed_prefixes' => ['50', '51', '52', '54', '55', '56', '58'],
    ],
];
```

## Implementation Timeline

### Week 1-2: Backend Development
- [ ] Create CustomerAuthController
- [ ] Implement OTP service integration
- [ ] Set up database migrations
- [ ] Create API endpoints
- [ ] Implement rate limiting

### Week 3-4: Flutter Integration
- [ ] Create OTP service class
- [ ] Implement UI components
- [ ] Set up state management
- [ ] Add phone number validation
- [ ] Implement error handling

### Week 5-6: Testing & Optimization
- [ ] Unit tests for backend
- [ ] Widget tests for Flutter
- [ ] Integration testing
- [ ] Performance optimization
- [ ] Security audit

### Week 7-8: Deployment & Monitoring
- [ ] Staging deployment
- [ ] Production deployment
- [ ] Analytics implementation
- [ ] Monitoring setup
- [ ] Documentation completion

## Security Considerations

### 1. Phone Number Protection
- Mask phone numbers in logs and responses
- Implement phone number verification before sensitive operations
- Store phone numbers in encrypted format

### 2. OTP Security
- Use cryptographically secure random number generation
- Implement proper OTP expiration
- Clear OTP data after successful verification
- Log all OTP-related activities for audit

### 3. Rate Limiting
- Implement multiple layers of rate limiting
- Use both IP-based and phone-based limits
- Implement progressive delays for repeated failures
- Monitor for suspicious patterns

## Testing Strategy

### 1. Backend Testing
```php
// Feature tests for OTP functionality
class CustomerOTPTest extends TestCase
{
    public function test_send_registration_otp()
    public function test_verify_valid_otp()
    public function test_rate_limiting()
    public function test_otp_expiration()
}
```

### 2. Flutter Testing
```dart
// Widget tests for OTP components
void main() {
  group('OTP Input Widget Tests', () {
    testWidgets('should accept 6 digits', (tester) async {});
    testWidgets('should validate input format', (tester) async {});
  });
}
```

### 3. Integration Testing
- End-to-end customer registration flow
- OTP delivery and verification
- Error handling scenarios
- Performance under load

## Monitoring and Analytics

### 1. Key Metrics
- OTP delivery success rate
- Verification success rate
- Average verification time
- Failed attempt patterns
- Customer conversion rates

### 2. Alerting
- High failure rates
- Unusual traffic patterns
- Service downtime
- Rate limit breaches

### 3. Logging
```php
// Structured logging for OTP events
Log::info('OTP sent', [
    'phone' => $maskedPhone,
    'purpose' => $purpose,
    'request_id' => $requestId,
    'ip' => $request->ip(),
]);
```

## Database Migration Scripts

### 1. Customer Authentication Tables
```sql
-- Migration: 2024_01_01_000001_add_customer_auth_fields.sql
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_verified_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_otp_sent_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS otp_attempts INT DEFAULT 0;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_country_code VARCHAR(5) DEFAULT '+971';

-- Migration: 2024_01_01_000002_create_customer_sessions_table.sql
CREATE TABLE customer_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL,
    otp_request_id VARCHAR(255) NOT NULL UNIQUE,
    purpose ENUM('registration', 'login', 'password_reset', 'phone_verification') NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    verified_at TIMESTAMP NULL,
    attempts INT DEFAULT 0,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_phone (phone),
    INDEX idx_request_id (otp_request_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_purpose (purpose),
    INDEX idx_ip_address (ip_address)
);

-- Migration: 2024_01_01_000003_create_otp_analytics_table.sql
CREATE TABLE otp_analytics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL,
    purpose VARCHAR(50) NOT NULL,
    action ENUM('sent', 'verified', 'failed', 'expired') NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    response_time_ms INT,
    error_code VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_phone (phone),
    INDEX idx_purpose (purpose),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);
```

### 2. Configuration Updates
```php
// config/customer_otp.php
<?php
return [
    'providers' => [
        'default' => env('CUSTOMER_OTP_PROVIDER', 'smartvision'),

        'smartvision' => [
            'api_url' => env('SMARTVISION_API_URL', 'https://api.smartvision.ae'),
            'api_key' => env('SMARTVISION_API_KEY'),
            'sender_id' => env('SMARTVISION_SENDER_ID', 'YourApp'),
        ],

        'firebase' => [
            'project_id' => env('FIREBASE_PROJECT_ID'),
            'web_api_key' => env('FIREBASE_WEB_API_KEY'),
        ],
    ],

    'otp' => [
        'length' => 6,
        'expiry_minutes' => 10,
        'max_attempts' => 3,
        'resend_delay_seconds' => 60,
        'rate_limit_per_hour' => 5,
        'rate_limit_per_day' => 20,
    ],

    'phone' => [
        'country_code' => '+971',
        'validation_regex' => '/^\+971[0-9]{9}$/',
        'allowed_prefixes' => ['50', '51', '52', '54', '55', '56', '58'],
        'mask_format' => '+971****{last_4}',
    ],

    'security' => [
        'max_daily_attempts_per_phone' => 10,
        'max_daily_attempts_per_ip' => 50,
        'suspicious_activity_threshold' => 5,
        'auto_block_duration_hours' => 24,
    ],
];
```

## Flutter Implementation Details

### 1. OTP Models
```dart
// lib/models/otp_models.dart
class OTPRequest {
  final String phoneNumber;
  final String purpose;
  final String? countryCode;

  OTPRequest({
    required this.phoneNumber,
    required this.purpose,
    this.countryCode = '+971',
  });

  Map<String, dynamic> toJson() => {
    'phone': phoneNumber,
    'purpose': purpose,
    'country_code': countryCode,
  };
}

class OTPResponse {
  final bool success;
  final String message;
  final String? requestId;
  final int? expiresIn;
  final int? resendAfter;
  final String? phoneMasked;

  OTPResponse({
    required this.success,
    required this.message,
    this.requestId,
    this.expiresIn,
    this.resendAfter,
    this.phoneMasked,
  });

  factory OTPResponse.fromJson(Map<String, dynamic> json) => OTPResponse(
    success: json['success'] ?? false,
    message: json['message'] ?? '',
    requestId: json['data']?['request_id'],
    expiresIn: json['data']?['expires_in'],
    resendAfter: json['data']?['resend_after'],
    phoneMasked: json['data']?['phone_masked'],
  );
}
```

### 2. OTP Service Implementation
```dart
// lib/services/customer_otp_service.dart
class CustomerOTPService extends GetxService {
  final ApiClient _apiClient = Get.find<ApiClient>();

  Future<OTPResponse> sendOTP(OTPRequest request) async {
    try {
      final response = await _apiClient.post(
        '/customer/auth/send-otp',
        data: request.toJson(),
      );

      return OTPResponse.fromJson(response.data);
    } catch (e) {
      return OTPResponse(
        success: false,
        message: 'Failed to send OTP: ${e.toString()}',
      );
    }
  }

  Future<VerificationResponse> verifyOTP(
    String requestId,
    String otp,
  ) async {
    try {
      final response = await _apiClient.post(
        '/customer/auth/verify-otp',
        data: {
          'request_id': requestId,
          'otp': otp,
        },
      );

      return VerificationResponse.fromJson(response.data);
    } catch (e) {
      return VerificationResponse(
        success: false,
        message: 'Verification failed: ${e.toString()}',
      );
    }
  }
}
```

### 3. OTP UI Components
```dart
// lib/widgets/otp_input_widget.dart
class OTPInputWidget extends StatefulWidget {
  final Function(String) onCompleted;
  final Function(String) onChanged;
  final bool autoFocus;
  final int length;

  const OTPInputWidget({
    Key? key,
    required this.onCompleted,
    required this.onChanged,
    this.autoFocus = true,
    this.length = 6,
  }) : super(key: key);

  @override
  _OTPInputWidgetState createState() => _OTPInputWidgetState();
}

// lib/widgets/phone_input_widget.dart
class PhoneInputWidget extends StatefulWidget {
  final Function(String) onChanged;
  final String? initialValue;
  final String? errorText;
  final bool enabled;

  const PhoneInputWidget({
    Key? key,
    required this.onChanged,
    this.initialValue,
    this.errorText,
    this.enabled = true,
  }) : super(key: key);

  @override
  _PhoneInputWidgetState createState() => _PhoneInputWidgetState();
}
```

## Performance Optimization

### 1. Caching Strategy
```php
// Cache OTP attempts to reduce database queries
Cache::remember("otp_attempts_{$phone}", 3600, function () use ($phone) {
    return CustomerSession::where('phone', $phone)
        ->where('created_at', '>=', now()->subHour())
        ->count();
});

// Cache rate limiting data
Cache::increment("rate_limit_{$ip}", 1, 3600);
```

### 2. Database Optimization
```sql
-- Optimize queries with proper indexing
CREATE INDEX idx_customer_sessions_phone_created ON customer_sessions(phone, created_at);
CREATE INDEX idx_customer_sessions_expires_verified ON customer_sessions(expires_at, verified_at);

-- Partition large tables by date
ALTER TABLE otp_analytics PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### 3. Background Jobs
```php
// Clean up expired OTP sessions
class CleanupExpiredOTPSessions implements ShouldQueue
{
    public function handle()
    {
        CustomerSession::where('expires_at', '<', now())
            ->where('verified_at', null)
            ->delete();
    }
}

// Generate analytics reports
class GenerateOTPAnalytics implements ShouldQueue
{
    public function handle()
    {
        $stats = OTPAnalytics::generateDailyReport();
        // Send to monitoring system
    }
}
```

## Conclusion

This comprehensive migration plan provides a detailed roadmap for repurposing the existing OTP infrastructure for customer authentication in the Flutter app. The modular approach ensures scalability, security, and maintainability while leveraging the proven OTP services already in place.

Key benefits of this approach:
- **Reusability**: Leverages existing, tested OTP infrastructure
- **Scalability**: Designed to handle high-volume customer authentication
- **Security**: Implements multiple layers of protection and monitoring
- **Flexibility**: Supports multiple authentication purposes and providers
- **Maintainability**: Clean separation of concerns and comprehensive documentation

The implementation should be done in phases to ensure proper testing and validation at each step. Regular monitoring and analytics will help optimize the system for better user experience and security.
