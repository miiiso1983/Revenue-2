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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->string('client_name');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->integer('duration_months');
            $table->decimal('amount', 15, 2);
            $table->enum('currency', ['USD', 'IQD']);
            $table->enum('installment_frequency', ['monthly', 'quarterly', 'yearly']);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('client_name');
            $table->index('invoice_date');
            $table->index('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};

