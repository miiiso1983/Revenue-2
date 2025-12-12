# Quick Reference Guide

## Installation (5 Minutes)

```bash
# 1. Install dependencies
composer install && npm install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Configure database in .env
DB_DATABASE=revenue_management
DB_USERNAME=root
DB_PASSWORD=your_password

# 4. Setup database
php artisan migrate
php artisan db:seed

# 5. Build and run
npm run build
php artisan serve
```

Visit: http://localhost:8000

## Default Credentials

| Role  | Email                | Password |
|-------|---------------------|----------|
| Admin | admin@example.com   | password |
| Guest | guest@example.com   | password |

## Key Calculations

### Monthly Revenue Allocation
```
allocated_amount = total_amount / duration_months
```

### Installments
- **Monthly**: `amount / duration_months`
- **Quarterly**: `amount / ceil(duration_months / 3)`
- **Yearly**: `amount / ceil(duration_months / 12)`

## Common Commands

```bash
# Run tests
php artisan test

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database
php artisan migrate:fresh --seed  # Reset database
php artisan migrate:rollback      # Undo last migration

# Development
npm run dev                       # Hot reload
npm run build                     # Production build
```

## API Endpoints

### Authentication
```bash
# Login
POST /api/login
{
  "email": "admin@example.com",
  "password": "password"
}

# Logout
POST /api/logout
```

### Contracts
```bash
# List all contracts
GET /api/contracts?client=&currency=USD&page=1

# Get contract
GET /api/contracts/{id}

# Create contract (Admin only)
POST /api/contracts
{
  "app_name": "My App",
  "client_name": "Client Name",
  "invoice_number": "INV-001",
  "invoice_date": "2025-01-01",
  "duration_months": 12,
  "amount": 1200.00,
  "currency": "USD",
  "installment_frequency": "monthly"
}

# Update contract (Admin only)
PUT /api/contracts/{id}

# Delete contract (Admin only)
DELETE /api/contracts/{id}
```

### Reports
```bash
# Pivot report
GET /api/reports/pivot?start=2025-01&end=2025-12&client=&currency=USD
```

## File Locations

| Component | Path |
|-----------|------|
| Controllers | `app/Http/Controllers/` |
| Models | `app/Models/` |
| Views | `resources/views/` |
| Routes | `routes/web.php`, `routes/api.php` |
| Migrations | `database/migrations/` |
| Tests | `tests/Feature/`, `tests/Unit/` |
| Config | `config/` |
| Assets | `resources/css/`, `resources/js/` |

## User Permissions

| Feature | Admin | Guest |
|---------|-------|-------|
| View Contracts | ✅ | ✅ |
| Create Contracts | ✅ | ❌ |
| Edit Contracts | ✅ | ❌ |
| Delete Contracts | ✅ | ❌ |
| View Reports | ✅ | ✅ |
| Export Excel | ✅ | ✅ |
| Bulk Upload | ✅ | ❌ |
| Audit Logs | ✅ | ❌ |

## Bulk Upload CSV Format

```csv
app_name,client_name,invoice_number,invoice_date,duration_months,amount,currency,installment_frequency
My App,Client A,INV-001,2025-01-01,12,1200.00,USD,monthly
Another App,Client B,INV-002,2025-02-01,6,600.00,IQD,quarterly
```

Download template: http://localhost:8000/sample-contracts.csv

## Database Tables

- `users` - User accounts
- `contracts` - Contract records
- `monthly_allocations` - Revenue allocations
- `installments` - Installment schedules
- `audit_logs` - Change history

## Troubleshooting

### Database Connection Error
```bash
# Check MySQL is running
sudo systemctl status mysql

# Verify credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
```

### Permission Errors
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Missing Dependencies
```bash
composer update
npm install
```

### Assets Not Loading
```bash
npm run build
php artisan config:clear
```

## Support

- **Documentation**: See README.md
- **Installation**: See INSTALLATION.md
- **Architecture**: See PROJECT_OVERVIEW.md
- **Logs**: Check `storage/logs/laravel.log`

## Quick Tips

1. **Change Password**: Update in database or use `bcrypt('newpassword')` in tinker
2. **Add User**: Use `php artisan tinker` and create via User model
3. **Reset Database**: `php artisan migrate:fresh --seed`
4. **Debug Mode**: Set `APP_DEBUG=true` in .env (development only)
5. **Cache Config**: Run `php artisan config:cache` in production

## Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Change `APP_KEY`
- [ ] Update database credentials
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `npm run build`
- [ ] Set proper file permissions
- [ ] Enable HTTPS
- [ ] Configure backups
- [ ] Change default passwords

---

**Need Help?** Check the full documentation in README.md and INSTALLATION.md

