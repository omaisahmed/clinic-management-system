<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('patient_id')->constrained()->cascadeOnDelete();
            $table->string('allergy', 150);
            $table->string('reaction', 255)->nullable();
            $table->string('severity', 20)->default('mild');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_allergies');
    }
};
