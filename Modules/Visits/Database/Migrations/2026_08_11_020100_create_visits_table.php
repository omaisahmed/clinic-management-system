<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->string('visit_number', 30)->unique();
            $table->date('visit_date');
            $table->string('status', 20)->default('in_progress');
            $table->text('chief_complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->string('blood_pressure', 20)->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();
            $table->unsignedSmallInteger('respiratory_rate')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'visit_date', 'status']);
            $table->index('patient_id');
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
