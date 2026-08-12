<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('clinic_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUlid('category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->string('description', 255);
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('payment_method', 30)->default('cash');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['clinic_id', 'expense_date']);
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
