<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ExportReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'username' => 'export-admin',
            'email' => 'export-admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function it_exports_separate_invoice_rows_and_handles_month_columns_beyond_z()
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

        $response = $this->actingAs($this->admin)->get(route('export', [
            'start' => '2026-01',
            'end' => '2028-06',
            'data_type' => 'both',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('.xlsx', (string) $response->headers->get('content-disposition'));

        $filePath = $response->baseResponse->getFile()->getPathname();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('Jun 2028', $sheet->getCell('AF4')->getValue());
        $this->assertSame('Acme', $sheet->getCell('A5')->getValue());
        $this->assertSame('INV-001', $sheet->getCell('B5')->getValue());
        $this->assertSame('Acme', $sheet->getCell('A6')->getValue());
        $this->assertSame('INV-002', $sheet->getCell('B6')->getValue());
        $this->assertStringContainsString('Rev: 100.00', (string) $sheet->getCell('C5')->getValue());
        $this->assertStringContainsString('Inst: 100.00', (string) $sheet->getCell('C5')->getValue());
        $this->assertStringContainsString('Rev: 100.00', (string) $sheet->getCell('M6')->getValue());
        $this->assertStringContainsString('Inst: 100.00', (string) $sheet->getCell('M6')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    /** @test */
    public function it_exports_a_single_selected_month_when_period_is_provided()
    {
        $this->createContract([
            'client_name' => 'Acme',
            'invoice_number' => 'INV-NOV',
            'invoice_date' => '2026-11-01',
            'duration_months' => 3,
            'amount' => 300,
        ]);

        $this->createContract([
            'client_name' => 'Beta',
            'invoice_number' => 'INV-DEC',
            'invoice_date' => '2026-12-01',
            'duration_months' => 3,
            'amount' => 300,
        ]);

        $response = $this->actingAs($this->admin)->get(route('export', [
            'period' => '2026-11',
            'data_type' => 'both',
        ]));

        $response->assertOk();

        $filePath = $response->baseResponse->getFile()->getPathname();
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('Period: 2026-11 to 2026-11', $sheet->getCell('A2')->getValue());
        $this->assertSame('Nov 2026', $sheet->getCell('C4')->getValue());
        $this->assertNull($sheet->getCell('D4')->getValue());
        $this->assertSame('Acme', $sheet->getCell('A5')->getValue());
        $this->assertSame('INV-NOV', $sheet->getCell('B5')->getValue());
        $this->assertStringContainsString('Rev: 100.00', (string) $sheet->getCell('C5')->getValue());
        $this->assertStringContainsString('Inst: 100.00', (string) $sheet->getCell('C5')->getValue());
        $this->assertNull($sheet->getCell('A6')->getValue());

        $spreadsheet->disconnectWorksheets();
    }

    private function createContract(array $overrides): Contract
    {
        $adminId = $this->admin->getKey();

        return Contract::create(array_merge([
            'app_name' => 'Export App',
            'client_name' => 'Default Client',
            'invoice_number' => 'INV-DEFAULT',
            'invoice_date' => '2026-01-01',
            'duration_months' => 12,
            'amount' => 1200,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $adminId,
        ], $overrides));
    }
}