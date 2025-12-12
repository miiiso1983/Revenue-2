<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monthly_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->date('month_date'); // Format: YYYY-MM-01
            $table->decimal('allocated_amount', 15, 2);
            $table->enum('currency', ['USD', 'IQD']);
            $table->timestamps();
            
            // Indexes for efficient querying
            $table->index(['contract_id', 'month_date']);
            $table->index('month_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_allocations');
    }
};

