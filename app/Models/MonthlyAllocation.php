<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyAllocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contract_id',
        'month_date',
        'allocated_amount',
        'currency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'month_date' => 'date',
        'allocated_amount' => 'decimal:2',
    ];

    /**
     * Contract this allocation belongs to
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}

