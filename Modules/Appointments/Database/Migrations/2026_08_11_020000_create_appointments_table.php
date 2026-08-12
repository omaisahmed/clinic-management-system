<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('appointment_type', 30);
            $table->string('status', 30)->default('scheduled');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->unsignedInteger('duration')->default(30);
            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['clinic_id', 'status', 'appointment_date']);
            $table->index('patient_id');
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
