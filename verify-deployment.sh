#!/bin/bash

# Production Deployment Verification Script
# For Laravel application deployment on IP: 82.25.109.98

echo "🔍 Production Deployment Verification"
echo "====================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Counters
PASSED=0
FAILED=0
WARNINGS=0

# Function to check and report
check_item() {
    local description="$1"
    local command="$2"
    local expected="$3"
    
    echo -n "Checking $description... "
    
    if eval "$command" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ PASS${NC}"
        ((PASSED++))
    else
        echo -e "${RED}❌ FAIL${NC}"
        ((FAILED++))
    fi
}

check_warning() {
    local description="$1"
    local command="$2"
    
    echo -n "Checking $description... "
    
    if eval "$command" > /dev/null 2>&1; then
        echo -e "${GREEN}✅ OK${NC}"
        ((PASSED++))
    else
        echo -e "${YELLOW}⚠️  WARNING${NC}"
        ((WARNINGS++))
    fi
}

echo "1. System Requirements"
echo "====================="

# Check PHP
check_item "PHP installation" "which php"
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
    echo "   PHP Version: $PHP_VERSION"
fi

# Check Composer
check_item "Composer installation" "which composer"

# Check MySQL
check_item "MySQL installation" "which mysql"

# Check web server
check_warning "Nginx installation" "which nginx"
check_warning "Apache installation" "which apache2"

echo ""
echo "2. File Structure"
echo "================="

# Check Laravel files
check_item "Laravel artisan file" "test -f artisan"
check_item "Composer.json file" "test -f composer.json"
check_item ".env file" "test -f .env"
check_item "Public directory" "test -d public"
check_item "Storage directory" "test -d storage"
check_item "Bootstrap/cache directory" "test -d bootstrap/cache"

echo ""
echo "3. Permissions"
echo "=============="

# Check permissions
check_item "Storage directory writable" "test -w storage"
check_item "Bootstrap/cache writable" "test -w bootstrap/cache"
check_item "Public directory readable" "test -r public"

echo ""
echo "4. Configuration Files"
echo "====================="

# Check configuration files
check_item "Production config file" "test -f config/production.php"
check_item "CORS config file" "test -f config/cors.php"
check_item "Nginx config file" "test -f nginx-production.conf"
check_item "Apache config file" "test -f apache-production.conf"

echo ""
echo "5. Environment Configuration"
echo "==========================="

if [ -f .env ]; then
    # Check .env contents
    check_item "APP_URL set to production IP" "grep -q 'APP_URL=http://82.25.109.98' .env"
    check_item "APP_ENV set to production" "grep -q 'APP_ENV=production' .env"
    check_item "APP_DEBUG disabled" "grep -q 'APP_DEBUG=false' .env"
    check_item "Database configuration present" "grep -q 'DB_DATABASE=' .env"
    check_item "Aramex configuration present" "grep -q 'ARAMEX_ACCOUNT_NUMBER=' .env"
else
    echo -e "${RED}❌ .env file not found${NC}"
    ((FAILED++))
fi

echo ""
echo "6. Storage and Links"
echo "==================="

# Check storage setup
check_item "Storage link exists" "test -L public/storage"
check_item "Products directory exists" "test -d public/products"

echo ""
echo "7. Network Connectivity"
echo "======================="

# Check if we can reach the production IP (if on the same network)
check_warning "Can ping production IP" "ping -c 1 82.25.109.98"

echo ""
echo "8. Deployment Scripts"
echo "===================="

# Check deployment scripts
check_item "Deployment script exists" "test -f deploy-production.php"
check_item "Validation script exists" "test -f validate-production.php"
check_item "Deployment checklist exists" "test -f DEPLOYMENT_CHECKLIST.md"

echo ""
echo "=========================================="
echo "📊 VERIFICATION SUMMARY"
echo "=========================================="
echo ""
echo -e "✅ Passed: ${GREEN}$PASSED${NC}"
echo -e "⚠️  Warnings: ${YELLOW}$WARNINGS${NC}"
echo -e "❌ Failed: ${RED}$FAILED${NC}"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "${GREEN}🎉 All critical checks passed!${NC}"
    echo -e "${GREEN}Your application is ready for production deployment.${NC}"
    echo ""
    echo "Next steps:"
    echo "1. Copy files to your production server"
    echo "2. Follow the DEPLOYMENT_CHECKLIST.md"
    echo "3. Run the deployment script on the server"
else
    echo -e "${RED}❌ Some critical checks failed.${NC}"
    echo -e "${RED}Please fix the issues before deploying.${NC}"
fi

echo ""
echo "📋 For detailed deployment instructions, see:"
echo "   - DEPLOYMENT_CHECKLIST.md"
echo "   - README_PRODUCTION_DEPLOYMENT.md"
echo ""
echo "🚀 Production URL: http://82.25.109.98"
echo "📱 API Base URL: http://82.25.109.98/api"
