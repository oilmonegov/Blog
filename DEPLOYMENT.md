# Deployment Guide

This guide provides step-by-step instructions for deploying the Simple Blog System to a production environment.

## Prerequisites

- PHP >= 8.2 with required extensions
- Composer
- Node.js >= 18.x and npm
- MySQL >= 8.0
- Redis (recommended for production)
- Web server (Nginx or Apache)
- SSL certificate (recommended)

## Pre-Deployment Checklist

- [ ] All tests passing
- [ ] Larastan level 5 passes
- [ ] Code formatted with Pint
- [ ] Environment variables configured
- [ ] Database migrations tested
- [ ] Frontend assets built
- [ ] Security audit completed

## Step 1: Server Setup

### Install PHP Extensions

```bash
# Required PHP extensions
php -m | grep -E "pdo|mbstring|xml|curl|openssl|json|bcmath|fileinfo|gd"
```

### Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Install Node.js and npm

```bash
# Using NodeSource repository (Ubuntu/Debian)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

## Step 2: Application Deployment

### Clone Repository

```bash
cd /var/www
git clone <repository-url> blog-system
cd blog-system
```

### Install Dependencies

```bash
# PHP dependencies (production)
composer install --no-dev --optimize-autoloader

# Node.js dependencies
npm install
```

### Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Configure Environment Variables

Edit `.env` file with production settings:

```env
APP_NAME="Simple Blog System"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... # Generated above
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog_system
DB_USERNAME=your_db_user
DB_PASSWORD=your_secure_password

# Cache & Sessions (Redis)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_smtp_user
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Database Setup

```bash
# Run migrations
php artisan migrate --force

# (Optional) Seed initial data
php artisan db:seed
```

### Build Frontend Assets

```bash
npm run build
```

## Step 3: Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Cache events
php artisan event:cache
```

## Step 4: Set Permissions

```bash
# Set ownership (adjust user/group as needed)
sudo chown -R www-data:www-data /var/www/blog-system

# Set directory permissions
sudo chmod -R 755 /var/www/blog-system
sudo chmod -R 775 /var/www/blog-system/storage
sudo chmod -R 775 /var/www/blog-system/bootstrap/cache
```

## Step 5: Web Server Configuration

### Nginx Configuration

Create `/etc/nginx/sites-available/blog-system`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/blog-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/blog-system /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration

Create `/etc/apache2/sites-available/blog-system.conf`:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/blog-system/public

    <Directory /var/www/blog-system/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/blog-system-error.log
    CustomLog ${APACHE_LOG_DIR}/blog-system-access.log combined
</VirtualHost>
```

Enable the site:

```bash
sudo a2ensite blog-system.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

## Step 6: Redis Configuration

### Install Redis

```bash
# Ubuntu/Debian
sudo apt-get install redis-server

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server
```

### Test Redis Connection

```bash
redis-cli ping
# Should return: PONG
```

## Step 7: Queue Worker (Optional)

If using queues, set up a supervisor process:

Create `/etc/supervisor/conf.d/blog-system-worker.conf`:

```ini
[program:blog-system-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/blog-system/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/blog-system/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start blog-system-worker:*
```

## Step 8: Scheduled Tasks (Cron)

Add Laravel scheduler to crontab:

```bash
sudo crontab -e -u www-data
```

Add:

```
* * * * * cd /var/www/blog-system && php artisan schedule:run >> /dev/null 2>&1
```

## Step 9: Security Hardening

### File Permissions

```bash
# Restrict .env file
chmod 600 .env

# Secure storage
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Firewall

```bash
# Allow HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow SSH (adjust port as needed)
sudo ufw allow 22/tcp

# Enable firewall
sudo ufw enable
```

### SSL Certificate

Use Let's Encrypt for free SSL:

```bash
sudo apt-get install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

## Step 10: Post-Deployment Verification

### Test Application

1. Visit `https://yourdomain.com`
2. Test user registration
3. Test login
4. Test post creation (as admin/author)
5. Test comment submission
6. Verify all routes work

### Monitor Logs

```bash
# Application logs
tail -f storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/error.log

# PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

## Deployment Script

Create `deploy.sh`:

```bash
#!/bin/bash

set -e

echo "Deploying application..."

# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Run migrations
php artisan migrate --force

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Clear application cache
php artisan cache:clear

# Restart queue workers (if using supervisor)
sudo supervisorctl restart blog-system-worker:*

echo "Deployment complete!"
```

Make executable:

```bash
chmod +x deploy.sh
```

## Rollback Procedure

If deployment fails:

```bash
# Revert to previous commit
git reset --hard HEAD~1

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Restore previous database (if needed)
# Use your database backup
```

## Backup Strategy

### Database Backup

```bash
# Create backup script
#!/bin/bash
mysqldump -u your_user -p your_database > /backups/blog-system-$(date +%Y%m%d).sql
```

### File Backup

```bash
# Backup storage and .env
tar -czf /backups/blog-system-files-$(date +%Y%m%d).tar.gz storage .env
```

### Automated Backups

Add to crontab:

```bash
# Daily database backup at 2 AM
0 2 * * * /path/to/backup-db.sh

# Weekly file backup
0 3 * * 0 /path/to/backup-files.sh
```

## Monitoring

### Application Monitoring

- Monitor error logs regularly
- Set up uptime monitoring
- Monitor database performance
- Track Redis memory usage

### Performance Monitoring

- Monitor page load times
- Track database query performance
- Monitor cache hit rates
- Track queue processing times

## Troubleshooting

### Common Issues

1. **500 Error**: Check logs, verify permissions, clear caches
2. **Database Connection Error**: Verify credentials, check MySQL service
3. **Redis Connection Error**: Verify Redis is running, check connection settings
4. **Permission Denied**: Check file/directory permissions
5. **Route Not Found**: Clear route cache: `php artisan route:clear`

### Debug Mode

If needed, temporarily enable debug:

```env
APP_DEBUG=true
```

**Remember to disable after debugging!**

## Maintenance

### Regular Tasks

- Update dependencies monthly
- Review and rotate logs
- Monitor disk space
- Update SSL certificates
- Review security patches

### Updates

```bash
# Update Composer packages
composer update

# Update npm packages
npm update

# Run migrations
php artisan migrate

# Rebuild assets
npm run build

# Clear and recache
php artisan optimize:clear
php artisan optimize
```

## Support

For issues or questions, refer to:
- [Laravel Documentation](https://laravel.com/docs)
- Application logs: `storage/logs/laravel.log`
- Server logs: `/var/log/nginx/` or `/var/log/apache2/`

