# Merchant Stock Validation Tests

This directory contains comprehensive browser automation tests for the merchant stock validation system implemented in the product creation and edit forms.

## Overview

The stock validation system implements a three-level hierarchy:
1. **General Stock** (top level) - total available inventory
2. **Product Colors Stock** - must not exceed general stock
3. **Product Sizes Stock** - must not exceed product color stock

## Test Coverage

### Core Functionality Tests
- ✅ General stock validation against color stock totals
- ✅ Color stock validation against size stock totals
- ✅ Real-time auto-correction of invalid stock levels
- ✅ Negative stock value prevention
- ✅ Visual feedback system (border colors, animations)
- ✅ Alert message system with proper styling

### User Experience Tests
- ✅ Stock summary display on input focus
- ✅ Form submission validation and error prevention
- ✅ Dynamic color/size addition validation
- ✅ Mobile responsiveness across viewports

### Edge Cases & Performance
- ✅ Rapid input changes handling
- ✅ Zero and empty value handling
- ✅ Multiple color/size combinations
- ✅ Browser compatibility testing

## Setup Instructions

### Prerequisites
- Node.js (v14 or higher)
- Access to the merchant dashboard at https://dala3chic.com
- Valid merchant account credentials

### Installation

1. Navigate to the tests directory:
```bash
cd tests
```

2. Install dependencies:
```bash
npm install
```

3. Install Playwright browsers:
```bash
npm run install-browsers
```

### Configuration

Set environment variables for merchant credentials:
```bash
export MERCHANT_EMAIL="your-merchant@email.com"
export MERCHANT_PASSWORD="your-password"
```

Or create a `.env` file in the tests directory:
```
MERCHANT_EMAIL=your-merchant@email.com
MERCHANT_PASSWORD=your-password
```

## Running Tests

### Option 1: Simple Test Runner (Recommended)
```bash
npm test
```

This runs the simplified test runner with visual feedback and detailed reporting.

### Option 2: Full Playwright Test Suite
```bash
npm run test:playwright
```

This runs the comprehensive Playwright test suite with advanced features.

### Option 3: Direct Execution
```bash
node run-stock-validation-tests.js
```

## Test Scenarios

### 1. General Stock Validation
- Sets general stock to 100
- Adds color stocks totaling more than 100
- Verifies auto-correction and alert messages

### 2. Color Stock Validation
- Tests color stock limits against general stock
- Verifies proportional adjustment when limits exceeded
- Checks visual feedback during corrections

### 3. Size Stock Validation
- Tests size stock limits against color stock
- Verifies real-time validation in dynamic forms
- Checks allocation tracking accuracy

### 4. Visual Feedback System
- Tests border color changes during validation
- Verifies alert message appearance and styling
- Checks animation timing and transitions

### 5. Form Submission Prevention
- Tests form blocking with validation errors
- Verifies validation summary display
- Checks error message accuracy

### 6. Mobile Responsiveness
- Tests validation on mobile viewports (375x667)
- Verifies alert message sizing and positioning
- Checks touch interaction compatibility

## Expected Results

All tests should pass with the following behaviors:

1. **Auto-correction**: Invalid stock values are automatically adjusted
2. **Visual Feedback**: Inputs show yellow→green border transitions
3. **Alert Messages**: Clear, informative messages appear for 4 seconds
4. **Form Protection**: Invalid forms cannot be submitted
5. **Real-time Updates**: Validation occurs on input/blur events
6. **Mobile Compatibility**: All features work on mobile devices

## Troubleshooting

### Common Issues

1. **Login Failures**
   - Verify merchant credentials are correct
   - Check if 2FA is enabled (may need to disable for testing)
   - Ensure merchant account has product creation permissions

2. **Test Timeouts**
   - Increase timeout values in test configuration
   - Check network connectivity to https://dala3chic.com
   - Verify server response times

3. **Element Not Found**
   - Check if form structure has changed
   - Verify CSS selectors are still valid
   - Update selectors if necessary

4. **Validation Not Working**
   - Ensure merchant-stock-validation.js is loaded
   - Check browser console for JavaScript errors
   - Verify event listeners are properly attached

### Debug Mode

Run tests with debug output:
```bash
DEBUG=1 node run-stock-validation-tests.js
```

Run with browser visible (non-headless):
```bash
HEADLESS=false node run-stock-validation-tests.js
```

## File Structure

```
tests/
├── Browser/
│   └── MerchantStockValidationTest.js    # Full Playwright test suite
├── run-stock-validation-tests.js         # Simplified test runner
├── package.json                          # Dependencies and scripts
└── README.md                            # This file
```

## Contributing

When adding new tests:

1. Follow the existing test structure
2. Add descriptive test names and comments
3. Include both positive and negative test cases
4. Test across different viewports
5. Update this README with new test scenarios

## Support

For issues with the stock validation system or tests:

1. Check browser console for JavaScript errors
2. Verify all required JavaScript files are loaded
3. Test manually in the browser first
4. Review test logs for specific failure points

## Performance Notes

- Tests run with 500ms slow motion for better visibility
- Each test includes appropriate wait times for animations
- Mobile tests use standard viewport sizes (375x667)
- Network idle state is awaited before starting tests
