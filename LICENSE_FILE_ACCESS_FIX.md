# License File Access Fix

## Problem
Merchant license PDF files were returning 404 errors when accessed directly via URL (e.g., `https://dala3chic.com/storage/merchant-licenses/license_2_1751471042.pdf`).

## Root Cause Analysis
1. **Broken Storage Symbolic Link**: The `public/storage` was a directory instead of a symbolic link to `storage/app/public`
2. **Server-Level Restrictions**: The shared hosting environment has server-level restrictions preventing direct access to files in the storage directory (returns 403 Forbidden)

## Solution Implemented

### 1. Fixed Storage Symbolic Link
- Removed the incorrect `public/storage` directory
- Recreated the proper symbolic link using `php artisan storage:link`
- Verified the link points to the correct location: `storage/app/public`

### 2. Created Laravel Routes for File Serving
Added new routes in `routes/web.php`:
```php
Route::get('/merchant-licenses/{id}/download', [MerchantLicenseController::class, 'downloadLicense'])->name('merchant-licenses.download');
Route::get('/merchant-licenses/{id}/view', [MerchantLicenseController::class, 'viewLicense'])->name('merchant-licenses.view');
```

### 3. Added Controller Methods
Added two new methods to `App\Http\Controllers\Admin\MerchantLicenseController`:

#### `downloadLicense($id)`
- Serves license files as downloads
- Includes proper authentication checks
- Returns file with appropriate headers for download

#### `viewLicense($id)`
- Serves license files for inline viewing (e.g., in iframes)
- Sets `Content-Type: application/pdf`
- Sets `Content-Disposition: inline` for browser viewing

### 4. Updated Model URL Generation
Modified `App\Models\Merchant::getLicenseFileUrlAttribute()`:
- Changed from direct storage URLs to Laravel route URLs
- Now returns: `route('admin.merchant-licenses.view', $this->id)`

### 5. Updated Admin Views
Updated the following view files:
- `resources/views/admin/merchant-licenses/index.blade.php`
- `resources/views/admin/merchant-licenses/show.blade.php`

Changes made:
- Download links now use `route('admin.merchant-licenses.download', $merchant->id)`
- View links now use `route('admin.merchant-licenses.view', $merchant->id)`
- PDF iframe sources updated to use the new view route

## Benefits of This Solution

### 1. Security
- Files are served through authenticated Laravel routes
- Proper access control and authorization checks
- No direct file system access required

### 2. Reliability
- Works regardless of web server configuration
- Bypasses shared hosting restrictions
- Consistent behavior across different environments

### 3. Maintainability
- Centralized file serving logic
- Easy to add additional security checks or logging
- Can implement features like download tracking

### 4. Flexibility
- Can easily add features like watermarking
- Can implement different access levels
- Can add audit trails for file access

## File Access URLs

### Before (Direct Storage - Not Working)
```
https://dala3chic.com/storage/merchant-licenses/license_2_1751471042.pdf
```

### After (Laravel Routes - Working)
```
View:     https://dala3chic.com/admin/merchant-licenses/2/view
Download: https://dala3chic.com/admin/merchant-licenses/2/download
```

## Testing Results

✅ Storage symbolic link properly configured  
✅ License files exist and are readable  
✅ New routes are registered and functional  
✅ Controller methods work correctly  
✅ Admin interface updated to use new routes  
✅ Authentication protection in place  
✅ PDF viewing and downloading work in admin interface  

## Server Configuration Note

The shared hosting environment has server-level restrictions that return 403 Forbidden for direct access to storage files. This is a security feature and the Laravel route-based solution is the recommended approach for such environments.

## Future Enhancements

1. **Download Tracking**: Log when license files are accessed
2. **Access Control**: Implement role-based access to specific license files
3. **File Versioning**: Support multiple versions of license files
4. **Audit Trail**: Track who accessed which files and when
5. **Watermarking**: Add dynamic watermarks to viewed PDFs

## Verification

Run the following command to verify the fix:
```bash
php artisan route:list | grep merchant-licenses
```

Expected output should include:
- `admin.merchant-licenses.download`
- `admin.merchant-licenses.view`

The admin license management interface should now properly display and allow downloading of license files without any 404 errors.
