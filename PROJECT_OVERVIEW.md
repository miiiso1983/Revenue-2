# Revenue and Installment Management System - Project Overview

## Executive Summary

This is a complete, production-ready web application built with Laravel 10 for managing sales revenue allocation and installment tracking. The system automatically calculates monthly revenue distributions and installment schedules based on contract parameters, supports multiple currencies, and provides comprehensive reporting capabilities.

## Key Features Implemented

### ✅ Authentication & Authorization
- Secure login system with bcrypt password hashing (cost factor 12)
- Two user roles: Admin (full access) and Guest (read-only)
- Session-based authentication with CSRF protection
- Role-based middleware for route protection
- API authentication via Laravel Sanctum

### ✅ Contract Management
- Full CRUD operations for contracts
- Automatic calculation of monthly revenue allocations
- Automatic generation of installment schedules
- Support for three installment frequencies:
  - Monthly: Equal installments every month
  - Quarterly: Installments every 3 months
  - Yearly: Installments every 12 months
- Multi-currency support (USD and IQD)
- Contract validation with client and server-side checks

### ✅ Revenue Calculation Logic
- **Monthly Allocations**: Total amount divided evenly across contract duration
- **Formula**: `monthly_revenue = amount / duration_months`
- Stored in `monthly_allocations` table with month_date and allocated_amount
- Automatically regenerated when contract is updated

### ✅ Installment Calculation Logic
- **Monthly**: `installment_amount = amount / duration_months`
- **Quarterly**: `installment_amount = amount / ceil(duration_months / 3)`
- **Yearly**: `installment_amount = amount / ceil(duration_months / 12)`
- Due dates set to first day of installment month
- Automatically regenerated when contract is updated

### ✅ Pivot Table Reports
- Dynamic table with clients as rows and months as columns
- Shows both revenue and installments for each client-month intersection
- Filterable by:
  - Date range (start month to end month)
  - Client name
  - Currency (USD/IQD)
  - Application name
- Sortable columns
- Responsive design
- Print functionality
- Export to Excel

### ✅ Bulk Upload System
- Upload CSV or Excel files with multiple contracts
- Real-time validation with preview table
- Shows validation status for each row (✓ valid / ✗ invalid)
- Displays specific error messages for invalid rows
- Allows import of only valid rows
- Sample CSV template provided
- Supports up to 10MB file size

### ✅ Excel Export
- Export pivot reports to XLSX format
- Uses PhpSpreadsheet library
- Formatted output with:
  - Bold headers
  - Auto-width columns
  - Currency formatting
  - Merged cells for title
  - Filter information included

### ✅ Dashboard
- Summary cards showing:
  - Current month revenue (USD and IQD)
  - Current month installments due (USD and IQD)
  - Number of active clients
  - Number of active contracts
  - Total revenue by currency
- Recent contracts table
- Visual statistics with gradient cards

### ✅ Audit Logging
- Tracks all create/update/delete operations
- Stores user_id, action, table_name, record_id
- Captures before (old_values) and after (new_values) as JSON
- Filterable by action, table, user, and date range
- Admin-only access
- Detailed view of changes

### ✅ Security Features
- Bcrypt password hashing with cost factor 12
- CSRF protection on all forms
- XSS prevention via Blade templating
- SQL injection prevention via Eloquent ORM
- Role-based access control
- Secure session configuration
- Input validation and sanitization
- Admin middleware for protected routes

### ✅ API Endpoints
All RESTful API endpoints implemented:
- `POST /api/login` - Authentication
- `POST /api/logout` - End session
- `GET /api/contracts` - List contracts with filters
- `GET /api/contracts/{id}` - Get contract details
- `POST /api/contracts` - Create contract (Admin only)
- `PUT /api/contracts/{id}` - Update contract (Admin only)
- `DELETE /api/contracts/{id}` - Delete contract (Admin only)
- `GET /api/reports/pivot` - Get pivot data as JSON

### ✅ Testing Suite
Comprehensive test coverage:
- **Unit Tests**:
  - Monthly allocation calculations
  - Installment calculations (all frequencies)
  - Currency handling
  - Contract update regeneration
- **Feature Tests**:
  - Contract CRUD operations
  - Authentication and authorization
  - Role-based access control
  - Form validation
- PHPUnit configuration with SQLite in-memory database

## Technical Architecture

### Database Schema
```
users
├── id
├── username (unique)
├── email (unique)
├── password (hashed)
├── role (admin/guest)
└── timestamps

contracts
├── id
├── app_name
├── client_name
├── invoice_number (unique)
├── invoice_date
├── duration_months
├── amount
├── currency (USD/IQD)
├── installment_frequency
├── created_by (FK to users)
└── timestamps

monthly_allocations
├── id
├── contract_id (FK to contracts, cascade delete)
├── month_date (YYYY-MM-01)
├── allocated_amount
├── currency
└── timestamps

installments
├── id
├── contract_id (FK to contracts, cascade delete)
├── due_date
├── installment_amount
├── currency
└── timestamps

audit_logs
├── id
├── user_id (FK to users)
├── action (created/updated/deleted)
├── table_name
├── record_id
├── old_values (JSON)
├── new_values (JSON)
└── created_at
```

### Technology Stack
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Database**: MySQL 8.0+ / MariaDB 10.3+
- **Frontend**: Tailwind CSS 3.x, Blade Templates
- **JavaScript**: Vanilla JS with Vite bundler
- **Excel**: PhpSpreadsheet 1.29+
- **Authentication**: Laravel Sanctum
- **Testing**: PHPUnit 10.x

### Design Patterns Used
- **MVC Architecture**: Separation of concerns
- **Repository Pattern**: Through Eloquent ORM
- **Observer Pattern**: Model events for auto-calculations
- **Middleware Pattern**: Authentication and authorization
- **Service Layer**: Business logic in controllers
- **Factory Pattern**: Database seeders and factories

## File Structure
```
revenue-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ContractController.php
│   │   │   ├── ReportController.php
│   │   │   ├── BulkUploadController.php
│   │   │   ├── ExportController.php
│   │   │   └── AuditLogController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       ├── EncryptCookies.php
│   │       └── VerifyCsrfToken.php
│   └── Models/
│       ├── User.php
│       ├── Contract.php
│       ├── MonthlyAllocation.php
│       ├── Installment.php
│       └── AuditLog.php
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2024_01_01_000001_create_contracts_table.php
│   │   ├── 2024_01_01_000002_create_monthly_allocations_table.php
│   │   ├── 2024_01_01_000003_create_installments_table.php
│   │   └── 2024_01_01_000004_create_audit_logs_table.php
│   ├── seeders/
│   │   └── DatabaseSeeder.php
│   └── schema.sql
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── auth/
│   │   │   └── login.blade.php
│   │   ├── contracts/
│   │   │   ├── index.blade.php
│   │   │   ├── create.blade.php
│   │   │   ├── edit.blade.php
│   │   │   ├── show.blade.php
│   │   │   ├── bulk-upload.blade.php
│   │   │   └── bulk-preview.blade.php
│   │   ├── reports/
│   │   │   └── pivot.blade.php
│   │   ├── audit-logs/
│   │   │   └── index.blade.php
│   │   └── dashboard.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       ├── app.js
│       └── bootstrap.js
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── tests/
│   ├── Feature/
│   │   ├── AuthenticationTest.php
│   │   └── ContractManagementTest.php
│   └── Unit/
│       └── ContractCalculationTest.php
├── public/
│   ├── index.php
│   └── sample-contracts.csv
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── session.php
│   └── sanctum.php
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
├── phpunit.xml
├── README.md
├── INSTALLATION.md
└── PROJECT_OVERVIEW.md
```

## Example Scenarios Verified

### Example 1: Monthly Installments
- Input: amount=1200, duration=12, frequency=monthly
- Output: 12 allocations of $100 each, 12 installments of $100 each

### Example 2: Quarterly Installments
- Input: amount=1200, duration=6, frequency=quarterly
- Output: 6 allocations of $200 each, 2 installments of $600 each

### Example 3: Yearly Installments
- Input: amount=2400, duration=24, frequency=yearly
- Output: 24 allocations of $100 each, 2 installments of $1200 each

## Deployment Checklist

- [x] All migrations created
- [x] All models with relationships
- [x] All controllers with business logic
- [x] All routes (web and API)
- [x] All views with Tailwind CSS
- [x] Authentication system
- [x] Authorization middleware
- [x] Audit logging
- [x] Excel export
- [x] Bulk upload
- [x] Testing suite
- [x] Documentation
- [x] Sample data seeder
- [x] .env.example file
- [x] Installation guide

## Next Steps for Production

1. Set up production database
2. Configure environment variables
3. Run migrations and seeders
4. Build frontend assets
5. Configure web server (Apache/Nginx)
6. Enable HTTPS
7. Set up automated backups
8. Configure monitoring and logging
9. Perform security audit
10. Load test the application

## Support & Maintenance

- All code follows PSR-12 coding standards
- Comprehensive inline documentation
- Error logging to storage/logs/laravel.log
- Extensible architecture for future features
- Clean separation of concerns
- Well-tested core functionality

This system is ready for deployment and use!

