#!/bin/bash

# ========================================
# Cloudways Deployment Script
# Revenue Management System
# ========================================

echo "🚀 Starting Cloudways Deployment..."
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Pull latest code
echo -e "${YELLOW}📥 Step 1: Pulling latest code from GitHub...${NC}"
git pull origin main
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Code pulled successfully${NC}"
else
    echo -e "${RED}❌ Failed to pull code${NC}"
    exit 1
fi
echo ""

# Step 2: Install Composer dependencies
echo -e "${YELLOW}📦 Step 2: Installing Composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Composer dependencies installed${NC}"
else
    echo -e "${RED}❌ Failed to install Composer dependencies${NC}"
    exit 1
fi
echo ""

# Step 3: Install NPM dependencies
echo -e "${YELLOW}📦 Step 3: Installing NPM dependencies...${NC}"
npm install
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ NPM dependencies installed${NC}"
else
    echo -e "${RED}❌ Failed to install NPM dependencies${NC}"
    exit 1
fi
echo ""

# Step 4: Build assets
echo -e "${YELLOW}🔨 Step 4: Building frontend assets...${NC}"
npm run build
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Assets built successfully${NC}"
else
    echo -e "${RED}❌ Failed to build assets${NC}"
    exit 1
fi
echo ""

# Step 5: Run migrations
echo -e "${YELLOW}🗄️  Step 5: Running database migrations...${NC}"
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migrations completed${NC}"
else
    echo -e "${RED}❌ Failed to run migrations${NC}"
    exit 1
fi
echo ""

# Step 6: Clear caches
echo -e "${YELLOW}🧹 Step 6: Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✅ Caches cleared${NC}"
echo ""

# Step 7: Optimize for production
echo -e "${YELLOW}⚡ Step 7: Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
echo -e "${GREEN}✅ Optimization completed${NC}"
echo ""

# Step 8: Set permissions
echo -e "${YELLOW}🔒 Step 8: Setting permissions...${NC}"
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

# Final message
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}🎉 Deployment completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "Your application is now live at:"
echo -e "${YELLOW}https://phpstack-1510634-6068149.cloudwaysapps.com${NC}"
echo ""

