<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Installment;
use App\Models\MonthlyAllocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PivotReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'username' => 'pivot-admin',
            'email' => 'pivot-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function it_keeps_same_client_contracts_on_separate_rows_and_builds_correct_totals()
    {
        $this->createContract([
            'client_name' => 'Acme',
            'invoice_number' => 'INV-001',
            'invoice_date' => '2026-01-01',
            'duration_months' => 3,
            'amount' => 300,
        ]);

        $this->createContract([
            'client_name' => 'Acme',
            'invoice_number' => 'INV-002',
            'invoice_date' => '2026-11-01',
            'duration_months' => 2,
            'amount' => 200,
        ]);

        $this->actingAs($this->admin)
            ->get(route('reports.pivot', ['start' => '2026-01', 'end' => '2026-12', 'data_type' => 'revenue']))
            ->assertOk()
            ->assertViewHas('pivotData', function (array $pivotData) {
                $rows = collect($pivotData['clients'])->keyBy(fn (array $row) => $row['invoices'][0] ?? null);

                return count($pivotData['clients']) === 2
                    && round($rows['INV-001']['months']['2026-01-01']['revenue'] ?? 0, 2) === 100.00
                    && round($rows['INV-001']['months']['2026-11-01']['revenue'] ?? 0, 2) === 0.00
                    && round($rows['INV-002']['months']['2026-11-01']['revenue'] ?? 0, 2) === 100.00
                    && round($pivotData['month_totals']['2026-01-01']['all']['revenue'] ?? 0, 2) === 100.00
                    && round($pivotData['month_totals']['2026-11-01']['all']['revenue'] ?? 0, 2) === 100.00;
            });
    }

    /** @test */
    public function it_filters_by_contract_period_overlap_and_sums_duplicate_month_entries()
    {
        $overlapping = $this->createContract([
            'client_name' => 'Overlap Client',
            'invoice_number' => 'INV-OVERLAP',
            'invoice_date' => '2025-11-01',
            'duration_months' => 4,
            'amount' => 400,
        ]);

        $this->createContract([
            'client_name' => 'Expired Client',
            'invoice_number' => 'INV-OLD',
            'invoice_date' => '2025-01-01',
            'duration_months' => 3,
            'amount' => 300,
        ]);

        MonthlyAllocation::create([
            'contract_id' => $overlapping->id,
            'month_date' => '2026-01-01',
            'allocated_amount' => 50,
            'discount_amount' => 5,
            'currency' => 'USD',
        ]);

        Installment::create([
            'contract_id' => $overlapping->id,
            'due_date' => '2026-01-01',
            'installment_amount' => 25,
            'currency' => 'USD',
        ]);

        $this->actingAs($this->admin)
            ->get(route('reports.pivot', ['start' => '2026-01', 'end' => '2026-02', 'data_type' => 'both']))
            ->assertOk()
            ->assertViewHas('pivotData', function (array $pivotData) {
                $row = $pivotData['clients'][0] ?? null;

                return count($pivotData['clients']) === 1
                    && ($row['invoices'][0] ?? null) === 'INV-OVERLAP'
                    && round($row['months']['2026-01-01']['revenue'] ?? 0, 2) === 150.00
                    && round($row['months']['2026-01-01']['discount'] ?? 0, 2) === 5.00
                    && round($row['months']['2026-01-01']['installments'] ?? 0, 2) === 125.00
                    && round($pivotData['month_totals']['2026-01-01']['all']['revenue'] ?? 0, 2) === 150.00;
            });
    }

    private function createContract(array $overrides): Contract
    {
        return Contract::create(array_merge([
            'app_name' => 'Pivot App',
            'client_name' => 'Default Client',
            'invoice_number' => 'INV-DEFAULT',
            'invoice_date' => '2026-01-01',
            'duration_months' => 12,
            'amount' => 1200,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ], $overrides));
    }
}

