# Quick Installation Guide

## Prerequisites

Before you begin, ensure you have the following installed:
- PHP 8.1 or higher
- Composer
- MySQL 8.0+ or MariaDB 10.3+
- Node.js 16.x or higher
- NPM or Yarn

## Step-by-Step Installation

### 1. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Database

Edit the `.env` file and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=revenue_management
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 4. Create Database

Create a new MySQL database:

```sql
CREATE DATABASE revenue_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations and Seeders

```bash
# Run database migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

This will create:
- Database tables
- Two demo users (admin and guest)
- Sample contracts

### 6. Build Frontend Assets

```bash
# For production
npm run build

# For development (with hot reload)
npm run dev
```

### 7. Start the Application

```bash
# Start Laravel development server
php artisan serve
```

The application will be available at: **http://localhost:8000**

## Default Login Credentials

### Admin Account
- **Email**: admin@example.com
- **Password**: password
- **Access**: Full CRUD, bulk upload, audit logs, export

### Guest Account
- **Email**: guest@example.com
- **Password**: password
- **Access**: Read-only access to contracts and reports

## Verification

After installation, verify the system is working:

1. **Login**: Navigate to http://localhost:8000 and login with admin credentials
2. **Dashboard**: Check that the dashboard displays summary statistics
3. **Contracts**: View the sample contracts created by the seeder
4. **Create Contract**: Try creating a new contract
5. **Reports**: View the pivot report
6. **Export**: Test Excel export functionality

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists

### Permission Errors
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

### Missing Dependencies
```bash
# Clear and reinstall
rm -rf vendor node_modules
composer install
npm install
```

### Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Production Deployment

For production deployment:

1. Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Run `php artisan view:cache`
5. Set proper file permissions
6. Configure your web server (Apache/Nginx)
7. Enable HTTPS
8. Set up regular backups

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

## Additional Configuration

### Email Configuration (Optional)
If you want to enable email notifications, configure SMTP in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### Queue Configuration (Optional)
For better performance with bulk uploads:

```env
QUEUE_CONNECTION=database
```

Then run:
```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

## Support

For issues or questions:
1. Check the README.md for detailed documentation
2. Review the troubleshooting section above
3. Check Laravel logs in `storage/logs/laravel.log`
4. Contact the development team

## Next Steps

After successful installation:
1. Change default passwords
2. Create your own user accounts
3. Start adding real contracts
4. Customize the system to your needs
5. Set up regular database backups

