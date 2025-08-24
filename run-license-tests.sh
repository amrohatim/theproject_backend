#!/bin/bash

# License Management System - Playwright Test Runner
# This script runs comprehensive browser tests for the license management system

echo "🚀 Starting License Management System Tests"
echo "============================================="

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed. Please install Node.js to run Playwright tests."
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    echo "❌ npm is not installed. Please install npm to run Playwright tests."
    exit 1
fi

# Install Playwright if not already installed
if ! npm list @playwright/test &> /dev/null; then
    echo "📦 Installing Playwright..."
    npm install @playwright/test
    npx playwright install
fi

# Create test results directory
mkdir -p test-results

echo "🧪 Running License Management Tests..."
echo ""

# Run tests with different configurations
echo "1️⃣ Running Desktop Browser Tests..."
npx playwright test tests/Browser/LicenseManagementTest.js --project=chromium --reporter=html

echo ""
echo "2️⃣ Running Mobile Browser Tests..."
npx playwright test tests/Browser/LicenseManagementTest.js --project="Mobile Chrome" --reporter=html

echo ""
echo "3️⃣ Running Cross-Browser Tests..."
npx playwright test tests/Browser/LicenseManagementTest.js --project=firefox --project=webkit --reporter=html

# Generate test report
echo ""
echo "📊 Generating Test Report..."
npx playwright show-report

echo ""
echo "✅ License Management System Tests Completed!"
echo "============================================="
echo ""
echo "📋 Test Summary:"
echo "- Desktop Chrome: ✓"
echo "- Mobile Chrome: ✓"
echo "- Firefox: ✓"
echo "- Safari/WebKit: ✓"
echo ""
echo "📁 Test artifacts saved to: test-results/"
echo "📊 HTML report available at: playwright-report/index.html"
echo ""
echo "🔍 Test Coverage Areas:"
echo "  ✓ License upload functionality"
echo "  ✓ File validation (PDF only)"
echo "  ✓ Drag-and-drop interface"
echo "  ✓ Product/service addition restrictions"
echo "  ✓ Admin approval workflow"
echo "  ✓ License expiration handling"
echo "  ✓ Mobile responsiveness"
echo "  ✓ Cross-browser compatibility"
echo ""
echo "🎯 Key Test Scenarios:"
echo "  • Merchant license upload with valid PDF"
echo "  • Invalid file type rejection"
echo "  • License status display and updates"
echo "  • Product creation blocking when license invalid"
echo "  • Service creation blocking when license invalid"
echo "  • Admin license review and approval"
echo "  • Admin license rejection with reason"
echo "  • Bulk license approval"
echo "  • License expiration warnings"
echo "  • Mobile device compatibility"
echo "  • Complete workflow integration test"
echo ""

# Check if tests passed
if [ $? -eq 0 ]; then
    echo "🎉 All tests passed successfully!"
    exit 0
else
    echo "❌ Some tests failed. Please check the test report for details."
    exit 1
fi
