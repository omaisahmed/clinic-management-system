<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignUlid('medicine_id')->nullable()->constrained('medicines')->nullOnDelete();
            $table->string('name', 150);
            $table->string('dosage', 100)->nullable();
            $table->string('frequency', 100)->nullable();
            $table->string('duration', 100)->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();

            $table->index('prescription_id');
            $table->index('medicine_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
