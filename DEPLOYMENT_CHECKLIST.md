# 🚀 Production Deployment Checklist for IP: 82.25.109.98

## 📋 Pre-Deployment Requirements

### System Requirements
- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] MySQL/MariaDB installed and running
- [ ] Web server (Nginx/Apache) installed
- [ ] Git installed

### Server Setup Commands

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-mbstring php8.2-gd php8.2-bcmath -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install MySQL
sudo apt install mysql-server -y

# Install Nginx
sudo apt install nginx -y
```

## 🔧 Deployment Steps

### 1. Clone Repository
```bash
cd /var/www/
<<<<<<< HEAD
sudo git clone https://github.com/roothatim/theproject_backend.git laravel-app
=======
sudo git clone https://github.com/amrohatim/theproject_backend.git laravel-app
>>>>>>> 6784681faf1a8a3946f12e302fe84ec74113ed6a
cd laravel-app
sudo chown -R www-data:www-data .
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Environment Configuration
```bash
# Copy the production .env file (already created)
# The .env file is already configured for production

# Generate application key
php artisan key:generate --force
```

### 4. Database Setup
```bash
# Create database
mysql -u root -p
<<<<<<< HEAD
CREATE DATABASE tower;
CREATE USER 'root'@'localhost' IDENTIFIED BY 'fifa2021';
GRANT ALL PRIVILEGES ON tower.* TO 'root'@'localhost';
=======
CREATE DATABASE your_production_database;
CREATE USER 'your_db_username'@'localhost' IDENTIFIED BY 'your_secure_db_password';
GRANT ALL PRIVILEGES ON your_production_database.* TO 'your_db_username'@'localhost';
>>>>>>> 6784681faf1a8a3946f12e302fe84ec74113ed6a
FLUSH PRIVILEGES;
EXIT;

# Update .env with your actual database credentials
nano .env
# Update these lines:
# DB_DATABASE=your_production_database
# DB_USERNAME=your_db_username
# DB_PASSWORD=your_secure_db_password
```

### 5. Run Migrations
```bash
php artisan migrate --force
```

### 6. Storage Setup
```bash
# Create storage link
php artisan storage:link

# Create products directory
mkdir -p public/products
chmod 755 public/products

# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 7. Cache Configuration
```bash
# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8. Web Server Configuration

#### For Nginx:
```bash
# Copy the nginx configuration
sudo cp nginx-production.conf /etc/nginx/sites-available/laravel
sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/

# Update the configuration file
sudo nano /etc/nginx/sites-available/laravel
# Change: root /path/to/your/laravel/public;
# To:     root /var/www/laravel-app/public;

# Test and restart Nginx
sudo nginx -t
sudo systemctl restart nginx
```

#### For Apache:
```bash
# Copy the apache configuration
sudo cp apache-production.conf /etc/apache2/sites-available/laravel.conf

# Update the configuration file
sudo nano /etc/apache2/sites-available/laravel.conf
# Change: DocumentRoot /path/to/your/laravel/public
# To:     DocumentRoot /var/www/laravel-app/public

# Enable modules and site
sudo a2enmod rewrite headers expires deflate
sudo a2ensite laravel.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

## ✅ Validation Steps

### 1. Run Validation Script
```bash
php validate-production.php
```

### 2. Test Basic Connectivity
```bash
curl -I http://82.25.109.98
```

### 3. Test API Endpoints
```bash
curl -X GET http://82.25.109.98/api/products
```

### 4. Test CORS
```bash
curl -H "Origin: http://82.25.109.98" \
     -H "Access-Control-Request-Method: GET" \
     -H "Access-Control-Request-Headers: X-Requested-With" \
     -X OPTIONS \
     http://82.25.109.98/api/products
```

## 🔒 Security Configuration

### 1. Firewall Setup
```bash
sudo ufw allow 22    # SSH
sudo ufw allow 80    # HTTP
sudo ufw allow 443   # HTTPS (when SSL is configured)
sudo ufw enable
```

### 2. SSL Certificate (Recommended)
```bash
# Install Certbot for Let's Encrypt
sudo apt install certbot python3-certbot-nginx -y

# Get SSL certificate (replace with your domain)
sudo certbot --nginx -d yourdomain.com

# Or for IP-based setup, you'll need to purchase an SSL certificate
```

## 📧 Configuration Updates

### 1. Update Mail Settings
Edit `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host.com
MAIL_PORT=587
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Your App Name"
```

### 2. Update Aramex Shipping Settings
Edit `.env` file:
```env
ARAMEX_ACCOUNT_NUMBER=your_production_account_number
ARAMEX_USERNAME=your_production_username
ARAMEX_PASSWORD=your_production_password
ARAMEX_ACCOUNT_PIN=your_production_pin

ARAMEX_SHIPPER_NAME="Your Business Name"
ARAMEX_SHIPPER_COMPANY="Your Company Name"
ARAMEX_SHIPPER_PHONE="+971501234567"
ARAMEX_SHIPPER_EMAIL="shipping@yourdomain.com"
ARAMEX_SHIPPER_ADDRESS_LINE1="Your Business Address"
ARAMEX_SHIPPER_CITY="Your City"
ARAMEX_SHIPPER_COUNTRY_CODE="AE"
```

## 🔄 Post-Deployment

### 1. Clear and Rebuild Caches
```bash
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Monitor Logs
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/laravel_error.log

# Apache logs
sudo tail -f /var/log/apache2/laravel_error.log
```

### 3. Set Up Monitoring
```bash
# Create a simple health check script
echo '#!/bin/bash
curl -f http://82.25.109.98/up || echo "Application is down!"' > /usr/local/bin/health-check.sh
chmod +x /usr/local/bin/health-check.sh

# Add to crontab for monitoring
echo "*/5 * * * * /usr/local/bin/health-check.sh" | crontab -
```

## 🎯 Final Verification

- [ ] Application accessible at http://82.25.109.98
- [ ] API endpoints working at http://82.25.109.98/api
- [ ] Images loading from http://82.25.109.98/storage and http://82.25.109.98/products
- [ ] Database connection working
- [ ] Mail configuration tested
- [ ] Aramex integration configured
- [ ] CORS working for Flutter app
- [ ] Logs are being written
- [ ] File permissions correct
- [ ] SSL certificate installed (if applicable)

## 🆘 Troubleshooting

### Common Issues:
1. **500 Error**: Check file permissions and Laravel logs
2. **Database Connection**: Verify credentials and MySQL service
3. **Images Not Loading**: Check storage link and permissions
4. **CORS Issues**: Verify CORS configuration and allowed origins

### Quick Fixes:
```bash
# Reset permissions
sudo chown -R www-data:www-data /var/www/laravel-app
sudo chmod -R 755 /var/www/laravel-app
sudo chmod -R 775 /var/www/laravel-app/storage
sudo chmod -R 775 /var/www/laravel-app/bootstrap/cache

# Recreate storage link
php artisan storage:link

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📞 Support Resources

- Laravel Documentation: https://laravel.com/docs
- Deployment Guide: README_PRODUCTION_DEPLOYMENT.md
<<<<<<< HEAD
- Shipping Integration: README_SHIPPING.md
=======
- Shipping Integration: README_SHIPPING.md
>>>>>>> 6784681faf1a8a3946f12e302fe84ec74113ed6a
