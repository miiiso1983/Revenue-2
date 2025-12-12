#!/bin/bash

# ========================================
# Cloudways Initial Setup Script
# Revenue Management System
# ========================================

echo "🚀 Starting Initial Cloudways Setup..."
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Step 1: Check if .env exists
echo -e "${YELLOW}📋 Step 1: Checking .env file...${NC}"
if [ ! -f .env ]; then
    if [ -f .env.production ]; then
        echo -e "${BLUE}Creating .env file from .env.production (with database credentials)...${NC}"
        cp .env.production .env
        echo -e "${GREEN}✅ .env file created with database credentials${NC}"
    elif [ -f .env.cloudways ]; then
        echo -e "${BLUE}Creating .env file from .env.cloudways...${NC}"
        cp .env.cloudways .env
        echo -e "${GREEN}✅ .env file created${NC}"
        echo -e "${RED}⚠️  IMPORTANT: Edit .env file and add your database credentials!${NC}"
        echo ""
        echo -e "${YELLOW}Get database credentials from Cloudways:${NC}"
        echo "1. Go to Cloudways Dashboard"
        echo "2. Select your application"
        echo "3. Go to 'Access Details'"
        echo "4. Copy Database Name, Username, and Password"
        echo ""
        read -p "Press Enter after you've updated the .env file..."
    else
        echo -e "${RED}❌ No .env template found${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✅ .env file already exists${NC}"
fi
echo ""

# Step 2: Generate Application Key
echo -e "${YELLOW}🔑 Step 2: Generating application key...${NC}"
php artisan key:generate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Application key generated${NC}"
else
    echo -e "${RED}❌ Failed to generate application key${NC}"
    exit 1
fi
echo ""

# Step 3: Install Composer dependencies
echo -e "${YELLOW}📦 Step 3: Installing Composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Composer dependencies installed${NC}"
else
    echo -e "${RED}❌ Failed to install Composer dependencies${NC}"
    exit 1
fi
echo ""

# Step 4: Install NPM dependencies
echo -e "${YELLOW}📦 Step 4: Installing NPM dependencies...${NC}"
npm install
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ NPM dependencies installed${NC}"
else
    echo -e "${RED}❌ Failed to install NPM dependencies${NC}"
    exit 1
fi
echo ""

# Step 5: Build assets
echo -e "${YELLOW}🔨 Step 5: Building frontend assets...${NC}"
npm run build
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Assets built successfully${NC}"
else
    echo -e "${RED}❌ Failed to build assets${NC}"
    exit 1
fi
echo ""

# Step 6: Run migrations
echo -e "${YELLOW}🗄️  Step 6: Running database migrations...${NC}"
php artisan migrate --force
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migrations completed${NC}"
else
    echo -e "${RED}❌ Failed to run migrations${NC}"
    echo -e "${RED}Please check your database credentials in .env file${NC}"
    exit 1
fi
echo ""

# Step 7: Seed database
echo -e "${YELLOW}🌱 Step 7: Seeding database with initial data...${NC}"
read -p "Do you want to seed the database with sample data? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan db:seed --force
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✅ Database seeded successfully${NC}"
        echo ""
        echo -e "${BLUE}Default users created:${NC}"
        echo "Admin: admin@example.com / password"
        echo "Guest: guest@example.com / password"
    else
        echo -e "${RED}❌ Failed to seed database${NC}"
    fi
else
    echo -e "${YELLOW}⏭️  Skipping database seeding${NC}"
fi
echo ""

# Step 8: Set permissions
echo -e "${YELLOW}🔒 Step 8: Setting permissions...${NC}"
chmod -R 755 storage bootstrap/cache
chmod 600 .env
echo -e "${GREEN}✅ Permissions set${NC}"
echo ""

# Step 9: Optimize for production
echo -e "${YELLOW}⚡ Step 9: Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
echo -e "${GREEN}✅ Optimization completed${NC}"
echo ""

# Step 10: Create symbolic link for storage
echo -e "${YELLOW}🔗 Step 10: Creating storage symbolic link...${NC}"
php artisan storage:link
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Storage link created${NC}"
else
    echo -e "${YELLOW}⚠️  Storage link may already exist${NC}"
fi
echo ""

# Final message
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}🎉 Initial setup completed successfully!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "Your application is now ready at:"
echo -e "${YELLOW}https://phpstack-1510634-6068149.cloudwaysapps.com${NC}"
echo ""
echo -e "${BLUE}Next steps:${NC}"
echo "1. Visit your application URL"
echo "2. Login with: admin@example.com / password"
echo "3. Change the default password"
echo "4. Start using the system!"
echo ""

