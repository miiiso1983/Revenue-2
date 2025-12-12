<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Contract extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_name',
        'client_name',
        'invoice_number',
        'invoice_date',
        'duration_months',
        'amount',
        'currency',
        'installment_frequency',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'invoice_date' => 'date',
        'amount' => 'decimal:2',
        'duration_months' => 'integer',
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate allocations and installments when contract is created
        static::created(function ($contract) {
            $contract->generateMonthlyAllocations();
            $contract->generateInstallments();
        });

        // Regenerate allocations and installments when contract is updated
        static::updated(function ($contract) {
            if ($contract->isDirty(['amount', 'duration_months', 'invoice_date', 'installment_frequency'])) {
                $contract->monthlyAllocations()->delete();
                $contract->installments()->delete();
                $contract->generateMonthlyAllocations();
                $contract->generateInstallments();
            }
        });
    }

    /**
     * Monthly allocations for this contract
     */
    public function monthlyAllocations()
    {
        return $this->hasMany(MonthlyAllocation::class);
    }

    /**
     * Installments for this contract
     */
    public function installments()
    {
        return $this->hasMany(Installment::class);
    }

    /**
     * User who created this contract
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate monthly revenue allocations
     */
    public function generateMonthlyAllocations(): void
    {
        $monthlyAmount = $this->amount / $this->duration_months;
        $startDate = Carbon::parse($this->invoice_date)->startOfMonth();

        for ($i = 0; $i < $this->duration_months; $i++) {
            $monthDate = $startDate->copy()->addMonths($i);
            
            MonthlyAllocation::create([
                'contract_id' => $this->id,
                'month_date' => $monthDate->format('Y-m-01'),
                'allocated_amount' => round($monthlyAmount, 2),
                'currency' => $this->currency,
            ]);
        }
    }

    /**
     * Generate installments based on frequency
     */
    public function generateInstallments(): void
    {
        $startDate = Carbon::parse($this->invoice_date)->startOfMonth();
        
        switch ($this->installment_frequency) {
            case 'monthly':
                $this->generateMonthlyInstallments($startDate);
                break;
            case 'quarterly':
                $this->generateQuarterlyInstallments($startDate);
                break;
            case 'yearly':
                $this->generateYearlyInstallments($startDate);
                break;
        }
    }

    /**
     * Generate monthly installments
     */
    private function generateMonthlyInstallments(Carbon $startDate): void
    {
        $installmentAmount = $this->amount / $this->duration_months;

        for ($i = 0; $i < $this->duration_months; $i++) {
            Installment::create([
                'contract_id' => $this->id,
                'due_date' => $startDate->copy()->addMonths($i)->format('Y-m-01'),
                'installment_amount' => round($installmentAmount, 2),
                'currency' => $this->currency,
            ]);
        }
    }

    /**
     * Generate quarterly installments
     */
    private function generateQuarterlyInstallments(Carbon $startDate): void
    {
        $numberOfInstallments = ceil($this->duration_months / 3);
        $installmentAmount = $this->amount / $numberOfInstallments;

        for ($i = 0; $i < $numberOfInstallments; $i++) {
            Installment::create([
                'contract_id' => $this->id,
                'due_date' => $startDate->copy()->addMonths($i * 3)->format('Y-m-01'),
                'installment_amount' => round($installmentAmount, 2),
                'currency' => $this->currency,
            ]);
        }
    }

    /**
     * Generate yearly installments
     */
    private function generateYearlyInstallments(Carbon $startDate): void
    {
        $numberOfInstallments = ceil($this->duration_months / 12);
        $installmentAmount = $this->amount / $numberOfInstallments;

        for ($i = 0; $i < $numberOfInstallments; $i++) {
            Installment::create([
                'contract_id' => $this->id,
                'due_date' => $startDate->copy()->addMonths($i * 12)->format('Y-m-01'),
                'installment_amount' => round($installmentAmount, 2),
                'currency' => $this->currency,
            ]);
        }
    }
}

