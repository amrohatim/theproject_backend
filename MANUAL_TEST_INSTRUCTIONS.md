# Manual License Upload Test Instructions

## Test Setup

1. **Login as test merchant**:
   - URL: https://dala3chic.com/login
   - Email: merchant@test.com
   - Password: password123

2. **Navigate to license upload page**:
   - Go to: https://dala3chic.com/merchant/settings/global
   - Scroll to "License Management" section

## Test Cases

### Test 1: Basic File Upload
1. Click on the upload area (should trigger file browser)
2. Select any PDF file (under 5MB)
3. Set expiry date to: 2025-12-31
4. Click "Upload License"
5. **Expected**: Success message or specific error (not "Please select a license file")

### Test 2: File Validation
1. Try uploading a non-PDF file
2. **Expected**: Error message about PDF only
3. Try uploading a PDF larger than 5MB
4. **Expected**: Error message about file size

### Test 3: Form Validation
1. Select a PDF file but don't set expiry date
2. **Expected**: Button should remain disabled
3. Set expiry date but remove file
4. **Expected**: Button should become disabled

## Debugging Steps

### Check Browser Console
1. Open browser developer tools (F12)
2. Go to Console tab
3. Look for any JavaScript errors or log messages
4. When selecting a file, you should see: "File selected: {name: 'filename.pdf', size: 12345, type: 'application/pdf'}"
5. When submitting form, you should see: "Form submission validation: {hasFile: true, hasExpiryDate: true, ...}"

### Check Network Tab
1. Open Network tab in developer tools
2. Submit the form
3. Look for a PUT request to `/merchant/settings/license`
4. Check the request payload - should include the file data
5. Check the response - should show success or validation errors

### Check Server Logs
After each test, check the Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Look for:
- `=== LICENSE UPLOAD STARTED ===`
- `Request data: {"has_file":true,...}`
- `=== LicenseManagementService::uploadLicense STARTED ===`

## Expected Behavior

### Successful Upload
1. File is selected and displayed in upload area
2. Form validation passes
3. Loading spinner appears on submit button
4. Page redirects with success message
5. Database is updated with license information
6. File is stored in `storage/app/public/merchant-licenses/`

### Failed Upload
1. Specific error messages (not generic "Please select a license file")
2. Form maintains state (expiry date remains filled)
3. User can retry without losing data

## Troubleshooting

### If file upload doesn't work:
1. Check if file input is properly positioned
2. Verify form has `enctype="multipart/form-data"`
3. Check for JavaScript errors preventing submission
4. Verify server receives the file in request

### If validation always fails:
1. Check if file is actually being sent to server
2. Verify CSRF token is included
3. Check if middleware is blocking the request

### If service fails:
1. Check storage directory permissions
2. Verify database connection
3. Check for PHP errors in logs

## Success Criteria

✅ File can be selected via click
✅ File validation works (type and size)
✅ Form validation prevents invalid submissions
✅ Server receives file data
✅ Database is updated correctly
✅ File is stored in correct location
✅ Appropriate success/error messages are shown

## Current Status

Based on our testing:
- ✅ Controller is being called correctly
- ✅ LicenseManagementService works perfectly
- ✅ Database updates work correctly
- ✅ File storage works correctly
- ❌ Browser file upload is not sending files to server

The issue is specifically with the browser-to-server file transmission, not with the server-side processing.
