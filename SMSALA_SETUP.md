# SMSala SMS OTP Integration Setup

This document provides instructions for setting up SMSala SMS OTP integration for vendor registration phone verification.

## Environment Variables

Add the following environment variables to your `.env` file:

```env
# SMSala SMS Service Configuration
SMSALA_API_ID=SMSALA_DALA3_3862_SMS
SMSALA_API_PASSWORD=Jf8gMgERPiorWrAr
SMSALA_SENDER_ID=DALA3CHIC
SMSALA_BASE_URL=https://api.smsala.com/api
SMSALA_RATE_LIMIT_HOUR=5
SMSALA_RATE_LIMIT_DAY=20
```

## Configuration Details

### API Credentials
- **API ID**: `SMSALA_DALA3_3862_SMS` - Your SMSala API endpoint name
- **API Password**: `Jf8gMgERPiorWrAr` - Your SMSala API token
- **Sender ID**: `DALA3CHIC` - The sender ID that will appear on SMS messages
- **Base URL**: `https://api.smsala.com/api` - SMSala API base URL

### Rate Limiting
- **Hourly Limit**: 5 OTP requests per phone number per hour
- **Daily Limit**: 20 OTP requests per phone number per day

## SMS Message Templates

The system automatically generates SMS messages based on the verification type:

### Registration OTP
```
Your Dala3Chic registration verification code is: 123456. Valid for 10 minutes. Do not share this code.
```

### Login OTP
```
Your Dala3Chic login verification code is: 123456. Valid for 10 minutes. Do not share this code.
```

### Password Reset OTP
```
Your Dala3Chic password reset verification code is: 123456. Valid for 10 minutes. Do not share this code.
```

## API Endpoints

### Web Routes
- `POST /register/send-phone-otp` - Send phone verification OTP
- `POST /register/verify-phone-otp` - Verify phone OTP and create user
- `POST /register/resend-phone-otp` - Resend phone verification OTP

### API Routes
- `POST /api/vendor-registration/send-phone-otp` - Send phone verification OTP
- `POST /api/vendor-registration/verify-phone-otp` - Verify phone OTP and create user
- `POST /api/vendor-registration/resend-phone-otp` - Resend phone verification OTP

## Request/Response Examples

### Send Phone OTP
**Request:**
```json
{
    "registration_token": "abc123def456..."
}
```

**Response:**
```json
{
    "success": true,
    "message": "OTP sent to your phone number.",
    "request_id": "smsala_64f8a1b2c3d4e5f6_1234567890",
    "expires_in": 600
}
```

### Verify Phone OTP
**Request:**
```json
{
    "registration_token": "abc123def456...",
    "otp_code": "123456"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Phone verified successfully. Registration completed!",
    "user_id": 123,
    "next_step": "company_information"
}
```

## Error Handling

### Common Error Responses

#### Invalid Phone Number
```json
{
    "success": false,
    "message": "Invalid phone number format"
}
```

#### Rate Limit Exceeded
```json
{
    "success": false,
    "message": "Too many OTP requests. Please try again in an hour."
}
```

#### Invalid OTP
```json
{
    "success": false,
    "message": "Invalid OTP. Please try again.",
    "attempts_remaining": 2
}
```

#### Expired OTP
```json
{
    "success": false,
    "message": "OTP has expired"
}
```

## Registration Flow

1. **User Registration**: User submits registration form
2. **Email Verification**: User verifies email address
3. **Phone Verification**: 
   - System sends OTP via SMSala
   - User enters OTP code
   - System verifies OTP and creates user account
4. **Next Steps**: User proceeds to company information or license upload

## Logging

The system logs all SMS operations for debugging and monitoring:

- OTP generation and sending attempts
- Verification attempts (success/failure)
- Rate limiting events
- API errors and exceptions

Check Laravel logs for detailed information:
```bash
tail -f storage/logs/laravel.log | grep -i smsala
```

## Testing

### Development Testing
In development, OTP codes are logged to the Laravel log file for testing purposes.

### Production Testing
1. Use a test phone number to verify SMS delivery
2. Check SMSala dashboard for delivery reports
3. Monitor application logs for any errors

## Security Considerations

1. **Rate Limiting**: Implemented to prevent abuse
2. **OTP Expiry**: OTPs expire after 10 minutes
3. **Attempt Limits**: Maximum 3 verification attempts per OTP
4. **Secure Storage**: OTPs are stored in cache with automatic expiry
5. **Input Validation**: All inputs are validated before processing

## Troubleshooting

### SMS Not Received
1. Check phone number format (should include country code)
2. Verify SMSala account balance
3. Check SMSala API credentials
4. Review application logs for errors

### API Errors
1. Verify environment variables are set correctly
2. Check SMSala service status
3. Review network connectivity
4. Check rate limiting status

### Integration Issues
1. Ensure all required services are running
2. Check cache configuration (Redis/Memcached)
3. Verify database connectivity
4. Review Laravel queue configuration if using queued jobs
