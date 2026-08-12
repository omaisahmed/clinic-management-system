<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignUlid('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->string('test_name', 150);
            $table->string('category', 80)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('status', 20)->default('requested');
            $table->string('sample_type', 80)->nullable();
            $table->date('collection_date')->nullable();
            $table->text('result')->nullable();
            $table->date('result_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'status']);
            $table->index('patient_id');
            $table->index('visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
