# Firebase OTP Setup Guide

This guide explains how to set up Firebase Authentication for OTP (SMS) functionality in the marketplace application.

## Prerequisites

1. A Firebase project with Authentication enabled
2. Firebase Admin SDK service account credentials
3. SMS authentication enabled in Firebase Console

## Step 1: Firebase Project Setup

1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or select existing project `dala3chic-e2b81`
3. Enable Authentication in the Firebase Console:
   - Go to Authentication > Sign-in method
   - Enable "Phone" sign-in provider
   - Configure your authorized domains

## Step 2: Generate Service Account Credentials

1. In Firebase Console, go to Project Settings > Service Accounts
2. Click "Generate new private key"
3. Download the JSON file containing your service account credentials
4. Extract the following values from the JSON file:

```json
{
  "type": "service_account",
  "project_id": "your-project-id",
  "private_key_id": "your-private-key-id",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-xxxxx@your-project.iam.gserviceaccount.com",
  "client_id": "your-client-id",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs/firebase-adminsdk-xxxxx%40your-project.iam.gserviceaccount.com"
}
```

## Step 3: Configure Environment Variables

Update your `.env` file with the Firebase credentials:

```env
# Firebase Configuration
FIREBASE_PROJECT_ID=dala3chic-e2b81
FIREBASE_PRIVATE_KEY_ID=your_private_key_id_here
FIREBASE_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY_HERE\n-----END PRIVATE KEY-----\n"
FIREBASE_CLIENT_EMAIL=firebase-adminsdk-xxxxx@dala3chic-e2b81.iam.gserviceaccount.com
FIREBASE_CLIENT_ID=your_client_id_here
FIREBASE_AUTH_URI=https://accounts.google.com/o/oauth2/auth
FIREBASE_TOKEN_URI=https://oauth2.googleapis.com/token
FIREBASE_AUTH_PROVIDER_X509_CERT_URL=https://www.googleapis.com/oauth2/v1/certs
FIREBASE_CLIENT_X509_CERT_URL=your_client_cert_url_here
FIREBASE_WEB_API_KEY=AIzaSyDyVGqn_0UAOfXEa5tpVnICsUQQDesFIGg
```

**Important Notes:**
- Replace all placeholder values with actual credentials from your service account JSON
- The private key must include the `\n` characters for line breaks
- Wrap the private key in double quotes to preserve formatting

## Step 4: SMS Provider Configuration

Firebase doesn't send SMS directly from server-side. You have several options:

### Option A: Client-Side Firebase Auth (Recommended)
1. Use Firebase Web SDK on the frontend for phone verification
2. Server validates the verification result
3. Most secure and reliable approach

### Option B: Third-Party SMS Service
1. Integrate with services like Twilio, AWS SNS, or local UAE SMS providers
2. Use Firebase for user management, external service for SMS delivery
3. More control over SMS content and delivery

### Option C: Firebase Cloud Functions
1. Create Cloud Functions to handle SMS sending
2. Trigger functions from your Laravel backend
3. Functions can integrate with SMS providers

## Step 5: Testing Configuration

The application includes a testing mode that logs OTP codes instead of sending SMS:

1. **Development Mode**: When `APP_ENV=local`, OTP codes are logged to Laravel logs
2. **Production Mode**: When Firebase credentials are properly configured, real SMS sending is attempted
3. **Fallback Mode**: If Firebase credentials are missing, falls back to testing mode

## Step 6: Verify Setup

1. Check Laravel logs for Firebase initialization messages
2. Test OTP sending in development mode
3. Verify OTP codes appear in logs: `tail -f storage/logs/laravel.log`

## Troubleshooting

### Common Issues:

1. **Firebase Auth not initialized**
   - Check if all required environment variables are set
   - Verify private key formatting (must include `\n` characters)

2. **Invalid credentials**
   - Ensure service account has proper permissions
   - Verify project ID matches your Firebase project

3. **SMS not sending**
   - Check Firebase Console for SMS quota limits
   - Verify phone number format (+971XXXXXXXXX)
   - Check Firebase Authentication logs

### Debug Commands:

```bash
# Check environment variables
php artisan tinker
>>> config('services.firebase')

# Test Firebase connection
php artisan tinker
>>> app(App\Services\FirebaseOTPService::class)->sendOTP('+971501234567')
```

## Security Considerations

1. **Never commit service account credentials to version control**
2. **Use environment variables for all sensitive data**
3. **Implement rate limiting for OTP requests**
4. **Set appropriate Firebase security rules**
5. **Monitor Firebase usage and costs**

## Production Deployment

1. Set `APP_ENV=production` in production environment
2. Configure proper Firebase credentials
3. Set up monitoring for OTP delivery success rates
4. Implement proper error handling and user feedback
