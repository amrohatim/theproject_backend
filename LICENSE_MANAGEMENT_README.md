# License Management System

A comprehensive license management system for merchants with automated expiration checking, admin approval workflows, and product/service access restrictions.

## Features

### 🏪 Merchant Features
- **License Upload**: Drag-and-drop PDF license upload interface
- **Expiry Management**: Set and track license expiration dates
- **Status Tracking**: Real-time license verification status
- **Renewal Notifications**: Automatic warnings for expiring licenses
- **Mobile Responsive**: Fully functional on mobile devices

### 👨‍💼 Admin Features
- **Review Dashboard**: Centralized license review interface
- **Bulk Operations**: Approve multiple licenses simultaneously
- **Detailed Review**: View license documents and merchant information
- **Rejection Management**: Reject licenses with detailed reasons
- **Statistics**: Track approval rates and pending reviews

### 🔒 Security & Restrictions
- **Product Access Control**: Block product creation for invalid licenses
- **Service Access Control**: Block service creation for invalid licenses
- **Automatic Expiration**: Daily checks for expired licenses
- **File Validation**: PDF-only uploads with size limits
- **Authorization Checks**: Role-based access control

## Database Schema

### Merchants Table Extensions
```sql
-- License management fields added to merchants table
license_file VARCHAR(255) NULL                    -- Path to license PDF
license_expiry_date DATE NULL                     -- License expiration date
license_status ENUM('verified','checking','expired','rejected') DEFAULT 'checking'
license_verified BOOLEAN DEFAULT FALSE            -- Current validity status
license_rejection_reason TEXT NULL                -- Admin rejection reason
license_uploaded_at TIMESTAMP NULL                -- Upload timestamp
license_approved_at TIMESTAMP NULL                -- Approval timestamp
license_approved_by FOREIGN KEY(users.id) NULL    -- Approving admin
```

## API Endpoints

### Merchant Routes
```php
PUT /merchant/settings/license          // Upload new license
GET /merchant/settings/global           // View license status
```

### Admin Routes
```php
GET /admin/merchant-licenses            // List all licenses
GET /admin/merchant-licenses/{id}       // View license details
POST /admin/merchant-licenses/{id}/approve    // Approve license
POST /admin/merchant-licenses/{id}/reject     // Reject license
POST /admin/merchant-licenses/bulk-approve    // Bulk approve
```

## Installation & Setup

### 1. Run Database Migration
```bash
php artisan migrate
```

### 2. Set Up Scheduled Tasks
Add to your cron job:
```bash
# Check license expiration daily at 2 AM
0 2 * * * cd /path/to/project && php artisan license:check-expiration
```

### 3. Configure File Storage
Ensure the `storage/app/public/merchant-licenses` directory is writable:
```bash
chmod -R 775 storage/app/public/merchant-licenses
```

### 4. Install Browser Testing Dependencies
```bash
npm install
npm run install-browsers
```

## Usage

### For Merchants

1. **Upload License**:
   - Navigate to Settings > Global Settings
   - Scroll to License Management section
   - Drag and drop PDF file or click to browse
   - Set expiration date
   - Click "Upload License"

2. **Check Status**:
   - View current license status in settings
   - Monitor expiration dates
   - Receive renewal warnings

3. **Product/Service Management**:
   - Valid license required for adding products/services
   - Automatic blocking when license is invalid
   - Clear messaging about license requirements

### For Admins

1. **Review Licenses**:
   - Access Admin > Merchant Licenses
   - Filter by status (Pending, Approved, Rejected, Expired)
   - View detailed license information

2. **Approve/Reject**:
   - Click "View" on any license
   - Review merchant information and license document
   - Approve or reject with optional message/reason

3. **Bulk Operations**:
   - Select multiple pending licenses
   - Use "Bulk Approve Selected" for efficiency

## Testing

### Automated Browser Tests

The system includes comprehensive Playwright tests covering:

- ✅ License upload functionality
- ✅ File validation (PDF only, size limits)
- ✅ Drag-and-drop interface
- ✅ Product/service addition restrictions
- ✅ Admin approval workflow
- ✅ License expiration handling
- ✅ Mobile responsiveness
- ✅ Cross-browser compatibility

### Running Tests

```bash
# Run all license management tests
npm run test:license

# Run with browser visible (headed mode)
npm run test:license:headed

# Run on mobile devices
npm run test:mobile

# Run on desktop browsers
npm run test:desktop

# Generate HTML report
npm run test:html-report

# View test report
npm run report
```

### Test Coverage

**Desktop Browsers**: Chrome, Firefox, Safari
**Mobile Devices**: iPhone, Android
**Test Scenarios**: 15+ comprehensive test cases
**Integration Tests**: Complete workflow testing

## Console Commands

### Check License Expiration
```bash
# Check and update expired licenses
php artisan license:check-expiration

# Dry run (preview changes)
php artisan license:check-expiration --dry-run
```

### Cleanup Orphaned Files
```bash
# Remove unused license files
php artisan license:cleanup-files
```

## Service Classes

### LicenseManagementService
Centralized service for license operations:

```php
// Check if merchant can add products/services
$canAdd = $licenseService->canPerformLicenseRequiredActions($merchant);

// Get license status summary
$summary = $licenseService->getLicenseStatusSummary();

// Process license expiration
$licenseService->processLicenseExpiration($merchant);

// Approve/reject licenses
$licenseService->approveLicense($merchant, $admin, $message);
$licenseService->rejectLicense($merchant, $admin, $reason);
```

## Model Methods

### Merchant Model Extensions
```php
// Check license validity
$merchant->hasValidLicense()
$merchant->isLicenseExpired()
$merchant->needsLicenseRenewal()

// Get license information
$merchant->daysUntilLicenseExpiration()
$merchant->getLicenseStatusWithColor()
$merchant->getLicenseActionMessage()

// Scopes
Merchant::withValidLicense()->get()
Merchant::withExpiredLicense()->get()
Merchant::withPendingLicense()->get()
```

## File Structure

```
app/
├── Http/Controllers/
│   ├── Admin/MerchantLicenseController.php
│   └── Merchant/SettingsController.php
├── Http/Middleware/ValidLicenseMiddleware.php
├── Models/Merchant.php (extended)
├── Services/LicenseManagementService.php
└── Console/Commands/CheckLicenseExpiration.php

resources/views/
├── admin/merchant-licenses/
│   ├── index.blade.php
│   └── show.blade.php
└── merchant/settings/global.blade.php (extended)

tests/Browser/
├── LicenseManagementTest.js
├── global-setup.js
└── global-teardown.js

database/migrations/
└── 2025_07_02_113502_add_license_fields_to_merchants_table.php
```

## Security Considerations

- **File Validation**: Only PDF files accepted
- **Size Limits**: 5MB maximum file size
- **Access Control**: Role-based permissions
- **File Storage**: Secure storage in non-public directory
- **Input Validation**: Comprehensive form validation
- **CSRF Protection**: All forms protected
- **Authorization**: Middleware-based access control

## Performance Optimizations

- **Database Indexes**: Optimized queries for license status
- **File Cleanup**: Automated orphaned file removal
- **Caching**: License status caching where appropriate
- **Bulk Operations**: Efficient bulk approval processing

## Monitoring & Logging

- **License Uploads**: Logged with merchant information
- **Approvals/Rejections**: Admin actions logged
- **Expiration Processing**: Daily expiration checks logged
- **Error Handling**: Comprehensive error logging
- **Performance Metrics**: Track approval times and success rates

## Support & Maintenance

### Daily Tasks
- Monitor license expiration command execution
- Review pending license approvals
- Check system logs for errors

### Weekly Tasks
- Clean up orphaned license files
- Review license approval statistics
- Update test data if needed

### Monthly Tasks
- Analyze license renewal patterns
- Review and update validation rules
- Performance optimization review

## Troubleshooting

### Common Issues

1. **File Upload Fails**
   - Check storage permissions
   - Verify file size limits
   - Ensure PDF format

2. **License Status Not Updating**
   - Check scheduled task execution
   - Verify database connectivity
   - Review error logs

3. **Admin Approval Not Working**
   - Verify admin permissions
   - Check middleware configuration
   - Review route definitions

### Debug Commands
```bash
# Check license status for specific merchant
php artisan tinker
>>> $merchant = Merchant::find(1);
>>> $merchant->hasValidLicense();

# Test license expiration command
php artisan license:check-expiration --dry-run

# View recent logs
tail -f storage/logs/laravel.log | grep -i license
```

## Contributing

When contributing to the license management system:

1. **Run Tests**: Ensure all tests pass
2. **Update Documentation**: Keep this README current
3. **Follow Patterns**: Use existing code patterns
4. **Test Coverage**: Add tests for new features
5. **Security Review**: Consider security implications

## License

This license management system is part of the Dala3chic marketplace platform.
