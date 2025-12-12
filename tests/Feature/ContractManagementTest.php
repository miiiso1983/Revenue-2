<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContractManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $guest;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->guest = User::create([
            'username' => 'guest',
            'email' => 'guest@example.com',
            'password' => bcrypt('password'),
            'role' => 'guest',
        ]);
    }

    /** @test */
    public function admin_can_create_contract()
    {
        $response = $this->actingAs($this->admin)->post(route('contracts.store'), [
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-001',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
        ]);

        $response->assertRedirect(route('contracts.index'));
        $this->assertDatabaseHas('contracts', [
            'invoice_number' => 'TEST-001',
        ]);
    }

    /** @test */
    public function guest_cannot_create_contract()
    {
        $response = $this->actingAs($this->guest)->post(route('contracts.store'), [
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-002',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('contracts', [
            'invoice_number' => 'TEST-002',
        ]);
    }

    /** @test */
    public function admin_can_update_contract()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-003',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->put(route('contracts.update', $contract), [
            'app_name' => 'Updated App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-003',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
        ]);

        $response->assertRedirect(route('contracts.index'));
        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'app_name' => 'Updated App',
        ]);
    }

    /** @test */
    public function admin_can_delete_contract()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-004',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('contracts.destroy', $contract));

        $response->assertRedirect(route('contracts.index'));
        $this->assertDatabaseMissing('contracts', [
            'id' => $contract->id,
        ]);
    }

    /** @test */
    public function contract_validation_works()
    {
        $response = $this->actingAs($this->admin)->post(route('contracts.store'), [
            'app_name' => '',
            'client_name' => '',
            'invoice_number' => '',
            'invoice_date' => '',
            'duration_months' => '',
            'amount' => '',
            'currency' => '',
            'installment_frequency' => '',
        ]);

        $response->assertSessionHasErrors([
            'app_name',
            'client_name',
            'invoice_number',
            'invoice_date',
            'duration_months',
            'amount',
            'currency',
            'installment_frequency',
        ]);
    }

    /** @test */
    public function both_admin_and_guest_can_view_contracts()
    {
        $contract = Contract::create([
            'app_name' => 'Test App',
            'client_name' => 'Test Client',
            'invoice_number' => 'TEST-005',
            'invoice_date' => '2025-01-01',
            'duration_months' => 12,
            'amount' => 1200.00,
            'currency' => 'USD',
            'installment_frequency' => 'monthly',
            'created_by' => $this->admin->id,
        ]);

        $adminResponse = $this->actingAs($this->admin)->get(route('contracts.show', $contract));
        $adminResponse->assertStatus(200);

        $guestResponse = $this->actingAs($this->guest)->get(route('contracts.show', $contract));
        $guestResponse->assertStatus(200);
    }
}

