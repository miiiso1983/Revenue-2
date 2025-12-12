# 🚀 Cloudways Deployment Guide

## معلومات السيرفر
**URL:** https://phpstack-1510634-6068149.cloudwaysapps.com

---

## 📋 خطوات النشر على Cloudways

### 1️⃣ الاتصال بالسيرفر عبر SSH

```bash
# استخدم بيانات SSH من لوحة تحكم Cloudways
ssh master_username@server_ip
```

### 2️⃣ الانتقال إلى مجلد التطبيق

```bash
cd /home/master_username/applications/app_name/public_html
```

### 3️⃣ استنساخ المشروع من GitHub

```bash
# احذف الملفات الافتراضية
rm -rf *
rm -rf .htaccess

# استنساخ المشروع
git clone https://github.com/miiiso1983/Revenue-2.git .
```

### 4️⃣ تثبيت Dependencies

```bash
# تثبيت Composer dependencies
composer install --optimize-autoloader --no-dev

# تثبيت NPM dependencies
npm install

# بناء الـ assets
npm run build
```

### 5️⃣ إعداد ملف .env

```bash
# نسخ ملف .env
cp .env.example .env

# تعديل ملف .env
nano .env
```

**محتوى ملف .env للـ Cloudways:**

```env
APP_NAME="Revenue Management System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://phpstack-1510634-6068149.cloudwaysapps.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database من لوحة تحكم Cloudways
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cloudways Redis (اختياري)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 6️⃣ توليد Application Key

```bash
php artisan key:generate
```

### 7️⃣ إعداد قاعدة البيانات

```bash
# تشغيل Migrations
php artisan migrate --force

# إضافة البيانات الأولية
php artisan db:seed --force
```

### 8️⃣ ضبط الصلاحيات

```bash
# صلاحيات المجلدات
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# أو حسب مستخدم Cloudways
chown -R master_username:www-data storage bootstrap/cache
```

### 9️⃣ تحسين الأداء

```bash
# Cache الإعدادات
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تحسين Composer autoloader
composer dump-autoload --optimize
```

### 🔟 إعداد .htaccess في public

تأكد من وجود ملف `.htaccess` في مجلد `public`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## ⚙️ إعدادات Cloudways من لوحة التحكم

### 1. Application Settings

1. اذهب إلى **Application Management**
2. اختر تطبيقك
3. في **Application Settings**:
   - **Webroot:** اتركه `public_html` أو غيره إلى `public_html/public`
   - **PHP Version:** 8.1 أو أعلى

### 2. Database Access

1. اذهب إلى **Access Details**
2. انسخ:
   - Database Name
   - Database Username
   - Database Password
3. ضعها في ملف `.env`

### 3. SSL Certificate

1. اذهب إلى **SSL Certificate**
2. فعّل **Let's Encrypt SSL**
3. انتظر حتى يتم التفعيل

---

## 🔒 الأمان

### 1. تعطيل Debug Mode

```env
APP_DEBUG=false
```

### 2. حماية ملف .env

```bash
chmod 600 .env
```

### 3. إخفاء معلومات السيرفر

في `.htaccess` أضف:

```apache
# Hide server information
ServerSignature Off
```

---

## 📝 ملاحظات مهمة

1. **Document Root:** تأكد أن Document Root يشير إلى مجلد `public`
2. **PHP Version:** استخدم PHP 8.1 أو أعلى
3. **Composer:** متوفر افتراضياً في Cloudways
4. **Node.js:** متوفر افتراضياً في Cloudways
5. **Cron Jobs:** قد تحتاج لإعداد Laravel Scheduler

---

## 🔄 تحديث المشروع مستقبلاً

```bash
# الانتقال لمجلد المشروع
cd /home/master_username/applications/app_name/public_html

# سحب آخر التحديثات
git pull origin main

# تحديث Dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# تشغيل Migrations الجديدة
php artisan migrate --force

# مسح وإعادة بناء Cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```


