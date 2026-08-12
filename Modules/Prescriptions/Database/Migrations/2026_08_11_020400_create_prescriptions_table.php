<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignUlid('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('prescription_number', 30)->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'status']);
            $table->index('patient_id');
            $table->index('visit_id');
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
