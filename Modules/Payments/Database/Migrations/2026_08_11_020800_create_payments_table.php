<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignUlid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method', 30)->default('cash');
            $table->string('reference', 120)->nullable();
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'payment_date']);
            $table->index('patient_id');
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
