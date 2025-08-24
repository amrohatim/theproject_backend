#!/bin/bash

# Product Creation Tests Runner
# This script runs the comprehensive Playwright tests for both merchant and vendor product creation

echo "🧪 Running Product Creation Tests"
echo "=================================="
echo ""

# Ensure the Laravel server is running
echo "🔍 Checking if Laravel server is running..."
if ! curl -s http://localhost:8000 > /dev/null; then
    echo "❌ Laravel server is not running on localhost:8000"
    echo "Please start the server with: php artisan serve"
    exit 1
fi
echo "✅ Laravel server is running"
echo ""

# Create test results directory
mkdir -p test-results
mkdir -p tests/screenshots

echo "🧪 Running Product Creation Tests..."
echo ""

# Run Merchant Product Creation Test
echo "1️⃣ Running Merchant Product Creation Test..."
echo "============================================"
npx playwright test tests/playwright/merchant-product-creation.spec.js --project=chromium --reporter=html

if [ $? -eq 0 ]; then
    echo "✅ Merchant product creation test passed"
else
    echo "❌ Merchant product creation test failed"
fi

echo ""

# Run Vendor Product Creation Test
echo "2️⃣ Running Vendor Product Creation Test..."
echo "=========================================="
npx playwright test tests/playwright/vendor-product-creation.spec.js --project=chromium --reporter=html

if [ $? -eq 0 ]; then
    echo "✅ Vendor product creation test passed"
else
    echo "❌ Vendor product creation test failed"
fi

echo ""

# Run both tests together for comprehensive testing
echo "3️⃣ Running Both Tests Together..."
echo "================================="
npx playwright test tests/playwright/merchant-product-creation.spec.js tests/playwright/vendor-product-creation.spec.js --project=chromium --reporter=html

echo ""
echo "📊 Generating Test Report..."
npx playwright show-report

echo ""
echo "🎉 Product Creation Tests Complete!"
echo ""
echo "📁 Test artifacts saved to:"
echo "   - test-results/ (test results and videos)"
echo "   - tests/screenshots/ (debug screenshots)"
echo "   - playwright-report/ (HTML report)"
echo ""
echo "🔍 Key Test Features:"
echo "   ✅ Comprehensive product creation workflow"
echo "   ✅ Size category validation (size_category_id field)"
echo "   ✅ Three-tier stock validation (general > color > size)"
echo "   ✅ Image upload verification"
echo "   ✅ Database persistence checks"
echo "   ✅ UI verification and display"
echo "   ✅ User ID assignment verification (vendor)"
echo "   ✅ Multi-color and multi-size support"
echo ""
echo "📝 To run individual tests:"
echo "   npx playwright test tests/playwright/merchant-product-creation.spec.js"
echo "   npx playwright test tests/playwright/vendor-product-creation.spec.js"
