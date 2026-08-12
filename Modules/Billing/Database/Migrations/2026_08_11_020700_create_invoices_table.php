<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignUlid('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->string('invoice_number', 30)->unique();
            $table->string('status', 20)->default('draft');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'status']);
            $table->index('patient_id');
            $table->index('visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
