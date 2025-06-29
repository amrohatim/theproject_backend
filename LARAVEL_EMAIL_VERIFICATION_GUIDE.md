# Laravel Email Verification System

## Overview

This document describes the Laravel-based email verification system that replaces the Firebase email verification functionality. The new system provides reliable email verification for vendor and provider registration without SSL certificate dependencies.

## Architecture

### Components

1. **EmailVerificationService** - Core service handling token generation, storage, and validation
2. **EmailVerificationNotification** - Laravel notification for sending verification emails
3. **Database Table** - `email_verification_tokens` for storing verification tokens
4. **Web Routes** - Routes for handling email verification links
5. **API Endpoints** - Maintained existing Firebase API endpoints for backward compatibility

### Flow

1. User completes step 1 of registration (basic information)
2. User clicks "Send Verification Email" in step 2
3. System generates secure token and stores in database
4. System sends email with verification link
5. User clicks link in email
6. System verifies token and marks email as verified
7. User can proceed to step 3 of registration

## Installation & Configuration

### 1. Database Migration

The system uses a dedicated table for storing verification tokens:

```bash
php artisan migrate
```

This creates the `email_verification_tokens` table with the following structure:
- `id` - Primary key
- `email` - Email address being verified
- `token` - Secure verification token (SHA-256 hash)
- `type` - Verification type (vendor_registration, provider_registration, etc.)
- `metadata` - JSON field for additional data
- `expires_at` - Token expiration timestamp
- `verified_at` - Verification completion timestamp
- `created_at` / `updated_at` - Laravel timestamps

### 2. Email Configuration

Update your `.env` file with proper SMTP settings:

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Your App Name"
```

### 3. Queue Configuration (Recommended)

For better performance, configure queues for email sending:

```env
QUEUE_CONNECTION=database
```

Then run the queue worker:

```bash
php artisan queue:work
```

## Usage

### Sending Verification Email

```php
use App\Services\EmailVerificationService;

$emailService = new EmailVerificationService();
$result = $emailService->sendVerificationEmail(
    'user@example.com',
    'vendor_registration',
    ['name' => 'John Doe', 'phone' => '+971501234567']
);

if ($result['success']) {
    // Email sent successfully
    echo $result['message'];
} else {
    // Handle error
    echo $result['message'];
}
```

### Verifying Token

```php
$result = $emailService->verifyToken($token, 'vendor_registration');

if ($result['success'] && $result['verified']) {
    // Email verified successfully
    $email = $result['email'];
    $metadata = $result['metadata'];
} else {
    // Invalid or expired token
    echo $result['message'];
}
```

### Checking Verification Status

```php
$status = $emailService->getVerificationStatus('user@example.com', 'vendor_registration');

if ($status['verified']) {
    echo "Email is verified";
} else if ($status['token_sent']) {
    echo "Verification email sent, awaiting verification";
} else {
    echo "No verification token found";
}
```

## API Endpoints

The system maintains backward compatibility with existing API endpoints:

### Send Verification Email
- **URL**: `/api/vendor/register/send-firebase-email-verification`
- **Method**: POST
- **Headers**: `X-CSRF-TOKEN`
- **Response**: JSON with success status and message

### Check Verification Status
- **URL**: `/api/vendor/register/check-firebase-email-verification`
- **Method**: POST
- **Headers**: `X-CSRF-TOKEN`
- **Response**: JSON with verification status

## Web Routes

### Email Verification Link
- **URL**: `/verify-email/{token}?type={type}`
- **Method**: GET
- **Parameters**: 
  - `token` - Verification token from email
  - `type` - Verification type (vendor_registration, provider_registration)

## Email Templates

The system uses Laravel's built-in mail templates with customization based on verification type:

- **Vendor Registration**: Welcome message with vendor-specific content
- **Provider Registration**: Welcome message with provider-specific content
- **Default**: Generic verification message

## Security Features

1. **Secure Token Generation**: Uses SHA-256 hash of random data
2. **Token Expiration**: Tokens expire after 60 minutes
3. **Single Use**: Tokens are marked as used after verification
4. **Cleanup**: Old tokens are automatically cleaned up
5. **Rate Limiting**: Prevents spam by cleaning old tokens before sending new ones

## Testing

### Unit Tests

Run the email verification service tests:

```bash
php artisan test tests/Feature/EmailVerificationTest.php
```

### Browser Tests

Run end-to-end browser tests:

```bash
php artisan dusk tests/Browser/VendorRegistrationEmailVerificationTest.php
```

## Deployment

### Development Environment

1. Use `MAIL_MAILER=log` to log emails instead of sending them
2. Check `storage/logs/laravel.log` for email content
3. Use the test routes to verify functionality

### Production Environment

1. Configure proper SMTP settings
2. Set up queue workers for email processing
3. Configure proper `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
4. Set up SSL certificates for your domain
5. Monitor email delivery and logs

### Environment-Specific Settings

**Development (.env.local)**:
```env
MAIL_MAILER=log
APP_DEBUG=true
```

**Production (.env.production)**:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server.com
MAIL_PORT=587
MAIL_USERNAME=your-production-email
MAIL_PASSWORD=your-production-password
MAIL_ENCRYPTION=tls
APP_DEBUG=false
```

## Troubleshooting

### Common Issues

1. **Emails not sending**
   - Check SMTP configuration in `.env`
   - Verify SMTP credentials
   - Check Laravel logs: `storage/logs/laravel.log`
   - Ensure queue workers are running if using queues

2. **Verification links not working**
   - Check `APP_URL` in `.env` matches your domain
   - Verify routes are properly registered
   - Check for any middleware blocking the routes

3. **Tokens expiring too quickly**
   - Adjust `TOKEN_EXPIRATION_MINUTES` in `EmailVerificationService`
   - Check server timezone settings

4. **Database errors**
   - Ensure migrations have been run
   - Check database connection settings
   - Verify table permissions

### Debugging

Enable debug mode and check logs:

```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check queue jobs (if using queues)
php artisan queue:failed

# Test email configuration
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

## Maintenance

### Cleanup Expired Tokens

Run this command periodically (e.g., via cron job):

```php
use App\Services\EmailVerificationService;

$emailService = new EmailVerificationService();
$deletedCount = $emailService->cleanupExpiredTokens();
echo "Cleaned up {$deletedCount} expired tokens";
```

### Monitoring

Monitor the following metrics:
- Email delivery success rate
- Token verification rate
- Average time between email send and verification
- Number of expired tokens

## Migration from Firebase

The new system maintains API compatibility, so no frontend changes are required. The migration involves:

1. ✅ Database migration completed
2. ✅ Service implementation completed
3. ✅ Controller updates completed
4. ✅ Email templates created
5. ✅ Routes configured
6. ✅ Tests implemented

### Rollback Plan

If issues arise, you can temporarily revert to Firebase by:
1. Updating the controller methods to use Firebase again
2. Ensuring Firebase credentials are still configured
3. Testing the Firebase flow

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Laravel logs
3. Run the test suite to identify issues
4. Check email provider logs for delivery issues
