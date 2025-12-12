<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContractCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'username' => 'testadmin',
            'email' => 'testadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function it_calculates_monthly_allocations_correctly()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-001',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(12, $contract->monthlyAllocations()->count());
        
        $firstAllocation = $contract->monthlyAllocations()->first();
        $this->assertEquals(100.00, $firstAllocation->allocated_amount);
        $this->assertEquals('USD', $firstAllocation->currency);
    }

    /** @test */
    public function it_generates_monthly_installments_correctly()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-002',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(12, $contract->installments()->count());
        
        $firstInstallment = $contract->installments()->first();
        $this->assertEquals(100.00, $firstInstallment->installment_amount);
    }

    /** @test */
    public function it_generates_quarterly_installments_correctly()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-003',
            'invoice_date' => '2025-01-01',
            'duration_months' => 6,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'quarterly',
            'created_by' => $this->admin->id,
        ]);

        // 6 months / 3 = 2 installments
        $this->assertEquals(2, $contract->installments()->count());
        
        $firstInstallment = $contract->installments()->first();
        $this->assertEquals(600.00, $firstInstallment->installment_amount);
    }

    /** @test */
    public function it_generates_yearly_installments_correctly()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-004',
            'invoice_date' => '2025-01-01',
            'duration_months' => 24,
            'amount' => 2400.00,
            'currency' => 'USD',
            'installment_frequency' => 'yearly',
            'created_by' => $this->admin->id,
        ]);

        // 24 months / 12 = 2 installments
        $this->assertEquals(2, $contract->installments()->count());
        
        $firstInstallment = $contract->installments()->first();
        $this->assertEquals(1200.00, $firstInstallment->installment_amount);
    }

    /** @test */
    public function it_handles_iqd_currency_correctly()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-005',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 12000000.00,
            'currency' => 'IQD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ]);

        $firstAllocation = $contract->monthlyAllocations()->first();
        $this->assertEquals('IQD', $firstAllocation->currency);
        $this->assertEquals(1000000.00, $firstAllocation->allocated_amount);
    }

    /** @test */
    public function it_regenerates_allocations_when_contract_is_updated()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-006',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ]);

        $this->assertEquals(12, $contract->monthlyAllocations()->count());

        // Update duration
        $contract->update(['duration_months' => 6]);

        $this->assertEquals(6, $contract->fresh()->monthlyAllocations()->count());
        
        $firstAllocation = $contract->fresh()->monthlyAllocations()->first();
        $this->assertEquals(200.00, $firstAllocation->allocated_amount);
    }
}

