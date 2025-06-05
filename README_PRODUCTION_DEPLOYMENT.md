# Production Deployment Guide

This guide provides step-by-step instructions for deploying your Laravel application to production on server IP: **82.25.109.98**.

## 🚀 Quick Start

1. **Run the deployment script:**
   ```bash
   php deploy-production.php
   ```

2. **Validate your configuration:**
   ```bash
   php validate-production.php
   ```

3. **Configure your web server** (see Web Server Configuration section below)

## 📋 Pre-Deployment Checklist

### ✅ Environment Configuration
- [x] `.env` file created with production settings
- [x] `APP_URL` set to `http://82.25.109.98`
- [x] `APP_ENV` set to `production`
- [x] `APP_DEBUG` set to `false`
- [ ] Database credentials configured
- [ ] Mail settings configured
- [ ] Aramex shipping credentials configured

### ✅ Security Configuration
- [x] CORS configured for production IP
- [x] Debug mode disabled
- [x] Secure session settings
- [ ] SSL certificate installed (recommended)
- [ ] Firewall configured

### ✅ Performance Configuration
- [x] Configuration caching enabled
- [x] Route caching enabled
- [x] View caching enabled
- [x] Autoloader optimization

## 🔧 Manual Configuration Steps

### 1. Database Configuration

Update these values in your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_database
DB_USERNAME=your_db_username
DB_PASSWORD=your_secure_db_password
```

### 2. Mail Configuration

Configure your production mail settings:

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

### 3. Aramex Shipping Configuration

Update with your production Aramex credentials:

```env
ARAMEX_ACCOUNT_NUMBER=your_production_account_number
ARAMEX_USERNAME=your_production_username
ARAMEX_PASSWORD=your_production_password
ARAMEX_ACCOUNT_PIN=your_production_pin

# Update shipper information
ARAMEX_SHIPPER_NAME="Your Business Name"
ARAMEX_SHIPPER_COMPANY="Your Company Name"
ARAMEX_SHIPPER_PHONE="+971501234567"
ARAMEX_SHIPPER_EMAIL="shipping@yourdomain.com"
ARAMEX_SHIPPER_ADDRESS_LINE1="Your Business Address"
ARAMEX_SHIPPER_CITY="Your City"
ARAMEX_SHIPPER_COUNTRY_CODE="AE"
```

## 🌐 Web Server Configuration

### Option 1: Nginx (Recommended)

1. Copy the provided `nginx-production.conf` to your Nginx sites directory
2. Update the `root` path to point to your Laravel `public` directory
3. Enable the site and restart Nginx

```bash
sudo cp nginx-production.conf /etc/nginx/sites-available/laravel
sudo ln -s /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### Option 2: Apache

1. Copy the provided `apache-production.conf` to your Apache sites directory
2. Update the `DocumentRoot` path to point to your Laravel `public` directory
3. Enable required modules and the site

```bash
sudo a2enmod rewrite headers expires deflate
sudo cp apache-production.conf /etc/apache2/sites-available/laravel.conf
sudo a2ensite laravel.conf
sudo systemctl restart apache2
```

## 🔒 Security Recommendations

### 1. SSL Certificate (Highly Recommended)

Install an SSL certificate and update your configuration:

```env
APP_URL=https://82.25.109.98
```

Update CORS origins to include HTTPS:
```php
'allowed_origins' => [
    'https://82.25.109.98',
    'http://82.25.109.98',  // Keep for development
],
```

### 2. Firewall Configuration

Configure your firewall to only allow necessary ports:

```bash
# Allow SSH (if needed)
sudo ufw allow 22

# Allow HTTP and HTTPS
sudo ufw allow 80
sudo ufw allow 443

# Enable firewall
sudo ufw enable
```

### 3. File Permissions

Ensure proper file permissions:

```bash
sudo chown -R www-data:www-data /path/to/your/laravel
sudo chmod -R 755 /path/to/your/laravel
sudo chmod -R 775 /path/to/your/laravel/storage
sudo chmod -R 775 /path/to/your/laravel/bootstrap/cache
```

## 📱 API Endpoints

Your API will be available at:
- Base URL: `http://82.25.109.98/api`
- Example: `http://82.25.109.98/api/products`

## 🖼️ Image and Asset URLs

- Storage files: `http://82.25.109.98/storage/`
- Product images: `http://82.25.109.98/products/`
- Public assets: `http://82.25.109.98/`

## 🧪 Testing Your Deployment

### 1. Basic Connectivity Test

```bash
curl -I http://82.25.109.98
```

### 2. API Test

```bash
curl -X GET http://82.25.109.98/api/products
```

### 3. CORS Test

```bash
curl -H "Origin: http://82.25.109.98" \
     -H "Access-Control-Request-Method: GET" \
     -H "Access-Control-Request-Headers: X-Requested-With" \
     -X OPTIONS \
     http://82.25.109.98/api/products
```

## 🔧 Maintenance Commands

### Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rebuild Caches

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Update Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

## 📊 Monitoring

### Log Files

Monitor these log files:
- Laravel logs: `storage/logs/laravel.log`
- Web server logs: `/var/log/nginx/` or `/var/log/apache2/`

### Health Check

Your application includes a health check endpoint:
- URL: `http://82.25.109.98/up`

## 🆘 Troubleshooting

### Common Issues

1. **500 Internal Server Error**
   - Check file permissions
   - Verify `.env` file exists and is readable
   - Check web server error logs

2. **CORS Issues**
   - Verify CORS configuration in `config/cors.php`
   - Check custom CORS middleware

3. **Images Not Loading**
   - Ensure storage link exists: `php artisan storage:link`
   - Check file permissions on storage directories
   - Verify `APP_URL` is correct

4. **Database Connection Issues**
   - Verify database credentials in `.env`
   - Ensure database server is running
   - Check firewall rules for database port

## 📞 Support

For additional support, refer to:
- Laravel Documentation: https://laravel.com/docs
- Aramex API Documentation: https://www.aramex.com/developers
- Server logs and error messages
