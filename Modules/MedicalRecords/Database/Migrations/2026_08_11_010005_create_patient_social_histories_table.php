<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_social_histories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('patient_id')->constrained()->cascadeOnDelete();
            $table->boolean('smoking')->default(false);
            $table->boolean('alcohol')->default(false);
            $table->string('occupation')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_social_histories');
    }
};
