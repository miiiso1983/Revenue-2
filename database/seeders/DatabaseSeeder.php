<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Contract;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create Guest User
        User::create([
            'username' => 'guest',
            'email' => 'guest@example.com',
            'password' => Hash::make('password'),
            'role' => 'guest',
        ]);

        // Create sample contracts
        $adminUser = User::where('role', 'admin')->first();

        Contract::create([
            'app_name' => 'ERP System',
            'client_name' => 'Acme Corporation',
            'invoice_number' => 'INV-2025-001',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 12000.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $adminUser->id,
        ]);

        Contract::create([
            'app_name' => 'CRM Software',
            'client_name' => 'Tech Solutions Ltd',
            'invoice_number' => 'INV-2025-002',
            'invoice_date' => '2025-01-15',
            'duration_months' => 6,
            'amount' => 3600.00,
            'currency' => 'USD',
            'installment_frequency' => 'quarterly',
            'created_by' => $adminUser->id,
        ]);

        Contract::create([
            'app_name' => 'Inventory Management',
            'client_name' => 'Global Traders Inc',
            'invoice_number' => 'INV-2025-003',
            'invoice_date' => '2025-02-01',
            'duration_months' => 24,
            'amount' => 48000000.00,
            'currency' => 'IQD',
            'installment_frequency' => 'yearly',
            'created_by' => $adminUser->id,
        ]);

        Contract::create([
            'app_name' => 'HR Management System',
            'client_name' => 'Acme Corporation',
            'invoice_number' => 'INV-2025-004',
            'invoice_date' => '2025-03-01',
            'duration_months' => 12,
            'amount' => 18000.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $adminUser->id,
        ]);
    }
}

