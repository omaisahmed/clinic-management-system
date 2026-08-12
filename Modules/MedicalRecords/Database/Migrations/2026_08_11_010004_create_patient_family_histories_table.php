<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_family_histories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('patient_id')->constrained()->cascadeOnDelete();
            $table->string('condition', 150);
            $table->string('relation', 80)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_family_histories');
    }
};
