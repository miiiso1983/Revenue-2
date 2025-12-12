# Revenue and Installment Management System

A comprehensive web-based system built with Laravel for managing sales revenue allocation and installment tracking for contracts. The system supports multi-currency operations (USD and IQD), role-based access control, and provides detailed reporting capabilities.

## Features

### Core Functionality
- **Contract Management**: Create, read, update, and delete contracts with automatic revenue and installment calculations
- **Multi-Currency Support**: Handle both USD and Iraqi Dinar (IQD) currencies
- **Automatic Calculations**: 
  - Monthly revenue allocation across contract duration
  - Installment schedules (monthly, quarterly, yearly)
- **Pivot Table Reports**: Dynamic reports showing revenue and installments by client and month
- **Excel Export**: Export reports to XLSX format with formatting
- **Bulk Upload**: Import multiple contracts via CSV or Excel files with validation preview
- **Audit Logging**: Track all create/update/delete operations with before/after values

### User Roles
- **Admin**: Full CRUD access, bulk upload, audit logs, export capabilities
- **Guest**: Read-only access to contracts and reports

### Security
- Bcrypt password hashing (cost factor 12)
- CSRF protection on all forms
- Role-based access control with middleware
- SQL injection prevention via Eloquent ORM
- XSS protection with Blade templating

## Technology Stack

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Database**: MySQL 8.0+ / MariaDB 10.3+
- **Frontend**: Tailwind CSS, Blade Templates
- **Excel Processing**: PhpSpreadsheet
- **Authentication**: Laravel Sanctum
- **Build Tools**: Vite

## Requirements

- PHP >= 8.1
- Composer
- MySQL 8.0+ or MariaDB 10.3+
- Node.js >= 16.x
- NPM or Yarn

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd revenue-management
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node Dependencies
```bash
npm install
```

### 4. Environment Configuration
```bash
cp .env.example .env
```

Edit `.env` file and configure your database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=revenue_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Run Database Migrations
```bash
php artisan migrate
```

### 7. Seed Database with Sample Data
```bash
php artisan db:seed
```

This creates two demo users:
- **Admin**: admin@example.com / password
- **Guest**: guest@example.com / password

### 8. Build Frontend Assets
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

### 9. Start Development Server
```bash
php artisan serve
```

Visit: http://localhost:8000

## Usage

### Login
Navigate to the login page and use one of the demo accounts:
- Admin: `admin@example.com` / `password`
- Guest: `guest@example.com` / `password`

### Creating a Contract
1. Navigate to **Contracts** > **Add New Contract**
2. Fill in all required fields:
   - Application Name
   - Client Name
   - Invoice Number (must be unique)
   - Invoice Date
   - Duration (1-120 months)
   - Total Amount
   - Currency (USD or IQD)
   - Installment Frequency (monthly/quarterly/yearly)
3. Click **Create Contract**

The system automatically generates:
- Monthly revenue allocations
- Installment schedule based on frequency

### Bulk Upload
1. Navigate to **Bulk Upload**
2. Download the sample CSV template
3. Fill in your contract data following the format
4. Upload the file
5. Review the validation preview
6. Confirm import for valid rows

### Viewing Reports
1. Navigate to **Reports** > **Pivot Report**
2. Set date range and filters (client, currency)
3. View the pivot table showing:
   - Revenue allocations by month
   - Due installments by month
   - Grouped by client
4. Export to Excel or print

### Audit Logs (Admin Only)
1. Navigate to **Audit Logs**
2. Filter by action, table, date range
3. View detailed before/after values for all changes

## API Endpoints

### Authentication
```
POST /api/login
POST /api/logout
```

### Contracts
```
GET    /api/contracts
GET    /api/contracts/{id}
POST   /api/contracts (Admin only)
PUT    /api/contracts/{id} (Admin only)
DELETE /api/contracts/{id} (Admin only)
```

### Reports
```
GET /api/reports/pivot?start=YYYY-MM&end=YYYY-MM&client=&currency=
```

## Testing

Run the test suite:
```bash
php artisan test
```

Or with PHPUnit:
```bash
vendor/bin/phpunit
```

### Test Coverage
- Unit tests for calculation logic
- Feature tests for CRUD operations
- Authentication and authorization tests
- Validation tests

## File Structure

```
revenue-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── ContractController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ReportController.php
│   │   │   ├── BulkUploadController.php
│   │   │   ├── ExportController.php
│   │   │   └── AuditLogController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Contract.php
│       ├── MonthlyAllocation.php
│       ├── Installment.php
│       └── AuditLog.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   └── api.php
└── tests/
    ├── Feature/
    └── Unit/
```

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues, questions, or contributions, please contact the development team.

# Revenue-2
