<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function dashboard_uses_selected_month_for_revenue_and_installments()
    {
        $adminId = $this->admin->getKey();

        Contract::create([
            'app_name' => 'Teami',
            'client_name' => 'Acme',
            'invoice_number' => 'USD-YEARLY',
            'invoice_date' => '2026-11-01',
            'duration_months' => 12,
            'amount' => 2400,
            'currency' => 'USD',
            'installment_frequency' => 'yearly',
            'created_by' => $adminId,
        ]);

        Contract::create([
            'app_name' => 'Teami',
            'client_name' => 'Beta',
            'invoice_number' => 'USD-MONTHLY',
            'invoice_date' => '2026-10-01',
            'duration_months' => 12,
            'amount' => 1200,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $adminId,
        ]);

        Contract::create([
            'app_name' => 'Teami',
            'client_name' => 'Gamma',
            'invoice_number' => 'IQD-MONTHLY',
            'invoice_date' => '2026-11-01',
            'duration_months' => 3,
            'amount' => 3000,
            'currency' => 'IQD',
            'installment_frequency' => 'monthly',
            'created_by' => $adminId,
        ]);

        $this->actingAs($this->admin)
            ->get(route('dashboard', ['period' => '2026-11']))
            ->assertOk()
            ->assertViewHas('selectedPeriod', '2026-11')
            ->assertViewHas('selectedMonthRevenueUSD', fn ($value) => round((float) $value, 2) === 300.00)
            ->assertViewHas('selectedMonthRevenueIQD', fn ($value) => round((float) $value, 2) === 1000.00)
            ->assertViewHas('selectedMonthInstallmentsUSD', fn ($value) => round((float) $value, 2) === 2500.00)
            ->assertViewHas('selectedMonthInstallmentsIQD', fn ($value) => round((float) $value, 2) === 1000.00);
    }
}