<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 150);
            $table->string('generic_name', 150)->nullable();
            $table->string('category', 80)->nullable();
            $table->string('brand', 120)->nullable();
            $table->string('strength', 80)->nullable();
            $table->string('unit', 30)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('reorder_level')->default(10);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'name']);
            $table->index('category');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
