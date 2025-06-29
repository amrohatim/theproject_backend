#!/bin/bash

# Merchant Registration Test Suite Runner
# This script runs comprehensive tests for the merchant registration flow

echo "🚀 Starting Merchant Registration Test Suite"
echo "============================================="

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

# Check if required dependencies are installed
check_dependencies() {
    print_status "Checking dependencies..."
    
    # Check if Playwright is installed
    if ! command -v npx &> /dev/null; then
        print_error "Node.js/npm is not installed. Please install Node.js first."
        exit 1
    fi
    
    # Check if Laravel Dusk is available
    if ! php artisan dusk:install --help &> /dev/null; then
        print_warning "Laravel Dusk is not installed. Installing..."
        composer require --dev laravel/dusk
        php artisan dusk:install
    fi
    
    print_success "Dependencies check completed"
}

# Setup test environment
setup_test_environment() {
    print_status "Setting up test environment..."
    
    # Create test database
    php artisan migrate:fresh --env=testing --seed
    
    # Install Playwright browsers if needed
    if [ ! -d "node_modules/@playwright" ]; then
        print_status "Installing Playwright..."
        npm install @playwright/test
        npx playwright install
    fi
    
    # Ensure test fixtures exist
    mkdir -p tests/fixtures
    if [ ! -f "tests/fixtures/test-image.jpg" ]; then
        echo "iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==" | base64 -d > tests/fixtures/test-image.jpg
    fi
    
    print_success "Test environment setup completed"
}

# Run Laravel Dusk tests
run_dusk_tests() {
    print_status "Running Laravel Dusk tests..."
    
    if php artisan dusk tests/Browser/MerchantRegistrationTest.php; then
        print_success "Laravel Dusk tests passed"
        return 0
    else
        print_error "Laravel Dusk tests failed"
        return 1
    fi
}

# Run Playwright tests
run_playwright_tests() {
    print_status "Running Playwright tests..."
    
    if npx playwright test tests/playwright/merchant-registration.spec.js; then
        print_success "Playwright tests passed"
        return 0
    else
        print_error "Playwright tests failed"
        return 1
    fi
}

# Run API tests
run_api_tests() {
    print_status "Running API tests..."
    
    if php artisan test --filter=MerchantRegistration; then
        print_success "API tests passed"
        return 0
    else
        print_error "API tests failed"
        return 1
    fi
}

# Generate test report
generate_report() {
    print_status "Generating test report..."
    
    echo "📊 Test Results Summary" > test-report.txt
    echo "======================" >> test-report.txt
    echo "Date: $(date)" >> test-report.txt
    echo "Environment: $(php artisan env)" >> test-report.txt
    echo "" >> test-report.txt
    
    if [ $dusk_result -eq 0 ]; then
        echo "✅ Laravel Dusk Tests: PASSED" >> test-report.txt
    else
        echo "❌ Laravel Dusk Tests: FAILED" >> test-report.txt
    fi
    
    if [ $playwright_result -eq 0 ]; then
        echo "✅ Playwright Tests: PASSED" >> test-report.txt
    else
        echo "❌ Playwright Tests: FAILED" >> test-report.txt
    fi
    
    if [ $api_result -eq 0 ]; then
        echo "✅ API Tests: PASSED" >> test-report.txt
    else
        echo "❌ API Tests: FAILED" >> test-report.txt
    fi
    
    echo "" >> test-report.txt
    echo "📋 Test Coverage:" >> test-report.txt
    echo "- Google Maps Integration" >> test-report.txt
    echo "- Location Selection and Clearing" >> test-report.txt
    echo "- Form Validation" >> test-report.txt
    echo "- File Upload Functionality" >> test-report.txt
    echo "- Email Verification Flow" >> test-report.txt
    echo "- OTP Verification Flow" >> test-report.txt
    echo "- License Upload Process" >> test-report.txt
    echo "- Registration Completion" >> test-report.txt
    echo "- Mobile Responsiveness" >> test-report.txt
    echo "- Error Handling and Fallbacks" >> test-report.txt
    
    print_success "Test report generated: test-report.txt"
}

# Main execution
main() {
    check_dependencies
    setup_test_environment
    
    # Initialize result variables
    dusk_result=1
    playwright_result=1
    api_result=1
    
    # Run tests
    run_dusk_tests
    dusk_result=$?
    
    run_playwright_tests
    playwright_result=$?
    
    run_api_tests
    api_result=$?
    
    # Generate report
    generate_report
    
    # Final summary
    echo ""
    echo "🏁 Test Suite Completed"
    echo "======================"
    
    total_tests=3
    passed_tests=0
    
    [ $dusk_result -eq 0 ] && ((passed_tests++))
    [ $playwright_result -eq 0 ] && ((passed_tests++))
    [ $api_result -eq 0 ] && ((passed_tests++))
    
    echo "Tests Passed: $passed_tests/$total_tests"
    
    if [ $passed_tests -eq $total_tests ]; then
        print_success "All tests passed! 🎉"
        exit 0
    else
        print_error "Some tests failed. Please check the logs."
        exit 1
    fi
}

# Run the main function
main "$@"
