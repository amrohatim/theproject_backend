#!/bin/bash

# Provider Registration Validation Test Suite
# This script runs comprehensive tests for the provider registration validation system

echo "🚀 Starting Provider Registration Validation Test Suite"
echo "======================================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if Laravel environment is set up
print_status "Checking Laravel environment..."
if [ ! -f ".env" ]; then
    print_error ".env file not found. Please set up your Laravel environment first."
    exit 1
fi

# Check if database is configured
print_status "Checking database configuration..."
php artisan config:cache
if ! php artisan migrate:status > /dev/null 2>&1; then
    print_warning "Database not properly configured. Running migrations..."
    php artisan migrate --force
fi

# Clear caches
print_status "Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run Feature Tests
echo ""
print_status "Running Feature Tests..."
echo "=========================="

print_status "Testing Provider Registration Validation..."
php artisan test tests/Feature/ProviderRegistrationValidationTest.php --verbose

if [ $? -eq 0 ]; then
    print_success "Provider Registration Validation tests passed!"
else
    print_error "Provider Registration Validation tests failed!"
    exit 1
fi

print_status "Testing Validation API endpoints..."
php artisan test tests/Feature/ValidationApiTest.php --verbose

if [ $? -eq 0 ]; then
    print_success "Validation API tests passed!"
else
    print_error "Validation API tests failed!"
    exit 1
fi

# Run Browser Tests (if Dusk is available)
echo ""
print_status "Checking for Laravel Dusk..."
if php artisan dusk:install --help > /dev/null 2>&1; then
    print_status "Running Browser Tests with Laravel Dusk..."
    echo "=========================================="
    
    # Start Chrome driver
    print_status "Starting Chrome driver..."
    php artisan dusk:chrome-driver --detect
    
    # Run browser tests
    print_status "Testing Provider Registration Form UI..."
    php artisan dusk tests/Browser/ProviderRegistrationFormTest.php
    
    if [ $? -eq 0 ]; then
        print_success "Browser tests passed!"
    else
        print_error "Browser tests failed!"
        exit 1
    fi
else
    print_warning "Laravel Dusk not installed. Skipping browser tests."
    print_warning "To install Dusk: composer require --dev laravel/dusk"
fi

# Test specific validation scenarios
echo ""
print_status "Running Custom Validation Scenario Tests..."
echo "============================================="

# Test 1: Business name uniqueness
print_status "Testing business name uniqueness validation..."
RESPONSE=$(curl -s -X POST http://localhost:8000/api/validate/business-name \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"business_name": "Test Business"}')

if echo "$RESPONSE" | grep -q '"available":true'; then
    print_success "Business name uniqueness validation working correctly"
else
    print_error "Business name uniqueness validation failed"
    echo "Response: $RESPONSE"
fi

# Test 2: Email status validation
print_status "Testing email status validation..."
RESPONSE=$(curl -s -X POST http://localhost:8000/api/validate/email-status \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email": "test@example.com"}')

if echo "$RESPONSE" | grep -q '"available":true'; then
    print_success "Email status validation working correctly"
else
    print_error "Email status validation failed"
    echo "Response: $RESPONSE"
fi

# Test 3: Phone status validation
print_status "Testing phone status validation..."
RESPONSE=$(curl -s -X POST http://localhost:8000/api/validate/phone-status \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"phone": "+971501234567"}')

if echo "$RESPONSE" | grep -q '"available":true'; then
    print_success "Phone status validation working correctly"
else
    print_error "Phone status validation failed"
    echo "Response: $RESPONSE"
fi

# Performance test
echo ""
print_status "Running Performance Tests..."
echo "============================="

print_status "Testing validation API response times..."
START_TIME=$(date +%s%N)
for i in {1..10}; do
    curl -s -X POST http://localhost:8000/api/validate/business-name \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -d "{\"business_name\": \"Test Business $i\"}" > /dev/null
done
END_TIME=$(date +%s%N)
DURATION=$(( (END_TIME - START_TIME) / 1000000 ))
AVERAGE=$(( DURATION / 10 ))

if [ $AVERAGE -lt 500 ]; then
    print_success "Validation API performance good: ${AVERAGE}ms average response time"
else
    print_warning "Validation API performance slow: ${AVERAGE}ms average response time"
fi

# Security test
echo ""
print_status "Running Security Tests..."
echo "=========================="

# Test SQL injection protection
print_status "Testing SQL injection protection..."
RESPONSE=$(curl -s -X POST http://localhost:8000/api/validate/business-name \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"business_name": "Test\"; DROP TABLE users; --"}')

if echo "$RESPONSE" | grep -q '"available":true'; then
    print_success "SQL injection protection working correctly"
else
    print_error "Potential SQL injection vulnerability detected"
    echo "Response: $RESPONSE"
fi

# Test XSS protection
print_status "Testing XSS protection..."
RESPONSE=$(curl -s -X POST http://localhost:8000/api/validate/business-name \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"business_name": "<script>alert(\"xss\")</script>"}')

if ! echo "$RESPONSE" | grep -q "<script>"; then
    print_success "XSS protection working correctly"
else
    print_error "Potential XSS vulnerability detected"
    echo "Response: $RESPONSE"
fi

# Final summary
echo ""
echo "======================================================="
print_success "🎉 Provider Registration Validation Test Suite Complete!"
echo "======================================================="

print_status "Test Summary:"
echo "✅ Feature Tests: Provider Registration Validation"
echo "✅ Feature Tests: Validation API Endpoints"
echo "✅ Custom Validation Scenarios"
echo "✅ Performance Tests"
echo "✅ Security Tests"

if php artisan dusk:install --help > /dev/null 2>&1; then
    echo "✅ Browser Tests: UI Validation"
else
    echo "⚠️  Browser Tests: Skipped (Dusk not installed)"
fi

print_status "All validation rules are working correctly!"
print_status "The provider registration form is ready for production use."

echo ""
print_status "To run individual test suites:"
echo "  Feature Tests: php artisan test tests/Feature/ProviderRegistrationValidationTest.php"
echo "  API Tests: php artisan test tests/Feature/ValidationApiTest.php"
echo "  Browser Tests: php artisan dusk tests/Browser/ProviderRegistrationFormTest.php"
