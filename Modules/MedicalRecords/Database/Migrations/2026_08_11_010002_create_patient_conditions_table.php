<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_conditions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('patient_id')->constrained()->cascadeOnDelete();
            $table->string('condition', 150);
            $table->date('diagnosis_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_conditions');
    }
};
