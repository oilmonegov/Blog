#!/bin/bash

# Deployment script for Simple Blog System
# Usage: ./deploy.sh

set -e

echo "🚀 Starting deployment..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}Error: artisan file not found. Are you in the Laravel root directory?${NC}"
    exit 1
fi

# Pull latest code
echo -e "${YELLOW}📥 Pulling latest code...${NC}"
git pull origin main || git pull origin master

# Install PHP dependencies
echo -e "${YELLOW}📦 Installing PHP dependencies...${NC}"
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
echo -e "${YELLOW}📦 Installing Node.js dependencies...${NC}"
npm install

# Build frontend assets
echo -e "${YELLOW}🎨 Building frontend assets...${NC}"
npm run build

# Run migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate --force

# Clear caches
echo -e "${YELLOW}🧹 Clearing caches...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
echo -e "${YELLOW}⚡ Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart queue workers (if using supervisor)
if command -v supervisorctl &> /dev/null; then
    echo -e "${YELLOW}🔄 Restarting queue workers...${NC}"
    sudo supervisorctl restart blog-system-worker:* || echo "No queue workers configured"
fi

# Set permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
chmod -R 775 storage bootstrap/cache || true

echo -e "${GREEN}✅ Deployment complete!${NC}"
echo -e "${GREEN}Don't forget to:${NC}"
echo -e "  - Verify .env file is configured correctly"
echo -e "  - Check application logs for any errors"
echo -e "  - Test the application in a browser"

