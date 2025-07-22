# Product Creation Tests - Playwright Automation

This directory contains comprehensive end-to-end tests for product creation functionality using Playwright automation framework.

## Test Files

### 1. `merchant-product-creation.spec.js`
Comprehensive test for merchant product creation with the following features:
- **Product Creation Process**: Complete form filling with validation
- **Size Category Validation**: Ensures `size_category_id` field is properly populated (1=clothes, 2=shoes, 3=hats)
- **Image Upload Testing**: Verifies product images are properly uploaded and stored
- **Database Persistence**: Validates all data is correctly saved to database
- **UI Verification**: Confirms products appear correctly in the products listing
- **Three-tier Stock Validation**: Tests general > color > size stock management

### 2. `vendor-product-creation.spec.js`
End-to-end test for vendor product creation with enhanced features:
- **Complete Workflow Testing**: Full product creation from start to finish
- **User ID Verification**: Ensures `user_id` field is populated with correct vendor ID
- **Multi-color Support**: Tests creation of multiple color variants
- **Comprehensive Size Management**: Tests 12 different sizes across 3 categories
- **Database Verification**: Validates complete data persistence
- **UI Display Testing**: Verifies proper rendering in vendor interface

## Key Features Tested

### Size Categories
- **Clothes (ID: 1)**: XS, S, M, L, XL sizes
- **Shoes (ID: 2)**: EU sizes 40, 41, 42, 43, 44
- **Hats (ID: 3)**: One Size, Adjustable

### Database Validation
- Product table persistence
- Size category ID assignment
- User ID assignment (vendor)
- Image storage verification
- Color and size relationship integrity

### UI Verification
- Product listing display
- Image rendering
- Edit page functionality
- Form validation

## Prerequisites

1. **Laravel Server**: Must be running on `http://localhost:8000`
   ```bash
   php artisan serve
   ```

2. **Database Setup**: Ensure the following tables exist and are seeded:
   - `products`
   - `product_sizes` (with `size_category_id` column)
   - `product_colors`
   - `size_categories`
   - `categories`
   - `branches`

3. **Test Users**: The following users must exist:
   - Merchant: `gogoh3296@gmail.com` / `Fifa2021`
   - Vendor: `gogoh3296@gmail.com` / `Fifa2021`

4. **Playwright Installation**:
   ```bash
   npm install @playwright/test
   npx playwright install
   ```

## Running the Tests

### Individual Tests
```bash
# Run merchant test only
npx playwright test tests/playwright/merchant-product-creation.spec.js

# Run vendor test only
npx playwright test tests/playwright/vendor-product-creation.spec.js
```

### All Tests
```bash
# Run both tests
npx playwright test tests/playwright/merchant-product-creation.spec.js tests/playwright/vendor-product-creation.spec.js

# Or use the provided script
chmod +x run-product-creation-tests.sh
./run-product-creation-tests.sh
```

### With Different Browsers
```bash
# Chrome
npx playwright test --project=chromium

# Firefox
npx playwright test --project=firefox

# Safari
npx playwright test --project=webkit
```

## Test Output

### Screenshots
Debug screenshots are saved to `tests/screenshots/` for troubleshooting.

### Reports
HTML reports are generated in `playwright-report/` directory.

### Console Output
Detailed logging shows each step of the test process with ✅ success indicators.

## Test Structure

Each test follows this pattern:
1. **Setup**: Login and navigation
2. **Form Filling**: Complete product information
3. **Image Upload**: Test file upload functionality
4. **Size/Color Management**: Add multiple variants
5. **Submission**: Submit form and capture response
6. **Verification**: Database and UI validation

## Troubleshooting

### Common Issues
1. **Server Not Running**: Ensure Laravel server is on port 8000
2. **Database Issues**: Check that migrations are run and tables exist
3. **User Authentication**: Verify test users exist and credentials are correct
4. **Image Upload**: Ensure upload directories have proper permissions

### Debug Mode
Add `--debug` flag to run tests in debug mode:
```bash
npx playwright test --debug tests/playwright/merchant-product-creation.spec.js
```

## Maintenance

### Updating Test Data
- Modify product names to include timestamps for uniqueness
- Update user credentials in test files if changed
- Adjust size categories if database schema changes

### Adding New Tests
Follow the existing pattern and include:
- Comprehensive error handling
- Database verification
- UI validation
- Proper cleanup
