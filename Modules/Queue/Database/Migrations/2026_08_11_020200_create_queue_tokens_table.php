<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_tokens', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUlid('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->unsignedInteger('token_number');
            $table->string('status', 20)->default('waiting');
            $table->dateTime('entered_at')->nullable();
            $table->dateTime('called_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'status', 'created_at']);
            $table->index('patient_id');
            $table->index('appointment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_tokens');
    }
};
